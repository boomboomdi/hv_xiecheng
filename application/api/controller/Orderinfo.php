<?php

namespace app\api\controller;


use app\admin\model\WriteoffModel;
use Couchbase\IndexFailureException;
use phpseclib\Crypt\AES;
use think\Controller;
use think\Db;
use app\common\model\CamiChannelModel;
use app\common\model\CardzhouyiModel;
use app\common\model\OrderhexiaoModel;
use app\common\model\OrderModel;
use app\api\validate\OrderinfoValidate;
use app\api\validate\CheckPhoneAmountNotifyValidate;
use think\Request;
use app\common\model\SystemConfigModel;

use tool\Log;
use think\Validate;
use app\common\Redis;

header('Access-Control-Allow-Origin:*');
header("Access-Control-Allow-Credentials:true");
header("Access-Control-Allow-Headers: Origin, X-Requested-With, Content-Type, Accept,Authorization");
header('Access-Control-Allow-Methods:GET,POST,PUT,DELETE,OPTIONS,PATCH');

class Orderinfo extends Controller
{


    /**
     * 正式入口/卡密正式路口
     * @param Request $request
     * @return void
     */
    public function order(Request $request)
    {

        $data = @file_get_contents('php://input');
        $message = json_decode($data, true);

//        var_dump($message);exit;
        $db = new Db();
        try {
            logs(json_encode(['message' => $message, 'line' => $message]), 'order_fist');
            $validate = new OrderinfoValidate();
            if (!$validate->check($message)) {
                return apiJsonReturn(-1, '', $validate->getError());
            }
            $db = new Db();
            //merchant_sign  //商户标识
            $token = $db::table('bsa_merchant')->where('merchant_sign', '=', $message['merchant_sign'])->find()['token'];
            if (empty($token)) {
                return apiJsonReturn(-2, "商户验证失败！");
            }
            $sig = md5($message['merchant_sign'] . $message['order_no'] . $message['amount'] . $message['time'] . $token);
//
//
            if ($message['merchant_sign'] != 'ceshi') {
                if ($sig != $message['sign']) {
                    logs(json_encode(['orderParam' => $message, 'doMd5' => $sig]), 'orderParamSignFail');
                    return apiJsonReturn(-3, "验签失败！");
                }
            }
            $orderFind = $db::table('bsa_order')->where('order_no', '=', $message['order_no'])->count();
            if ($orderFind > 0) {
                return apiJsonReturn(-4, "order_no existed 订单号单号重复！");
            }

            // 根据user_id  未付款次数 限制下单 end

            $orderMe = md5(uniqid() . getMillisecond());
            $orderFind = $db::table('bsa_order')->where('order_me', '=', $orderMe)->find();
            if (!empty($orderFind)) {
                $orderMe = md5(uniqid() . getMillisecond());
            }
            $orderNoFind = $db::table('bsa_order')->where('order_no', '=', $message['order_no'])->find();
            if (!empty($orderNoFind)) {
                return apiJsonReturn(-5, "该订单号已存在！");
            }
            //order_no   //商户订单号
            //payment   //支付方式（alipay：支付宝，wechat：微信）
            //notify_url  //回调通知地址
            //sign  //签名
            //cami_type_sign  Walmart   沃尔玛
            //沃尔玛  Walmart
            //查找通道  bsa_cami_write   by  card_type,pay_type,
            //根据卡种 、分配订单。
            //1、插入初始订单信息  --
            //拼接参数
            $insertOrderData['merchant_sign'] = $message['merchant_sign'];  //商户
            $insertOrderData['amount'] = $message['amount']; //支付金额
            $insertOrderData['order_no'] = $message['order_no'];  //商户订单号
            $insertOrderData['cami_type_sign'] = $message['cami_type_sign'];  //卡密标识
            ;  // 0、等待下单 1、支付成功（下单成功）！2、支付失败（下单成功）！3、下单失败！4、等待支付（下单成功）！5、已手动回调。
            $insertOrderData['order_status'] = 0; //状态
            $insertOrderData['order_me'] = $orderMe; //本平台订单号
            $insertOrderData['payable_amount'] = $message['amount'];  //应付金额
            $insertOrderData['payment'] = $message['payment']; //alipay
            $insertOrderData['add_time'] = time();  //入库时间
            $insertOrderData['notify_url'] = $message['notify_url']; //下单回调地址 notify url
            $insertOrderData['order_desc'] = "等待匹配!"; //订单描述
            $centent = "下单金额：" . $insertOrderData['amount'] . ":" . $insertOrderData['cami_type_sign'] . $insertOrderData['order_desc'];
            $orderModel = new OrderModel();
            $createOrderOne = $orderModel->addOrder($insertOrderData);
            if (!isset($createOrderOne['code']) || $createOrderOne['code'] != 0) {
                logs(json_encode(['action' => 'getUseHxOrderRes',
                    'insertOrderData' => $insertOrderData,
                    'createOrderOne' => $createOrderOne,
                    'lastSal' => $db::order("bsa_order")->getLastSql()
                ]), 'addOrderFail_log');
                return apiJsonReturn(-101, "下单有误！");
            }
//            $logData = var_export($param, true);
            Log::OrderLog($insertOrderData['merchant_sign'], $insertOrderData['order_no'], $centent);

            //2、绑定通道：
            //a、更新额度
            //b、更新订单
            //3、返回引导页面

            $camiChannelModel = new CamiChannelModel();
            $searchData['cami_type_sign'] = $message['cami_type_sign'];  //卡密类型
            $searchData['payment'] = $message['payment']; //支付方式
            $searchData['amount'] = $message['amount']; //订单金额
            $useCamiChannel = $camiChannelModel->getUseCamiChannel($searchData);
            if ($useCamiChannel['code'] != 0 || empty($useCamiChannel['data'])) {
                $content = $useCamiChannel['msg'];
                Log::OrderLog($insertOrderData['merchant_sign'], $insertOrderData['order_no'], $content);
                $orderWhere1['order_me'] = $insertOrderData['order_me'];

                $update1['order_status'] = 3;  //订单状态
                $update1['order_desc'] = "匹配核销通通道：" . $useCamiChannel['msg'];  //
                //修改订单状态为下单失败
                $db::table("bsa_order")->where($orderWhere1)->update($update1);
                return apiJsonReturn(-102, "下单失败,无可用通道！");
            }

            //绑定核销服务商

            //创建绑定核销服务商订单  -
            $orderLimitTime = SystemConfigModel::getOrderLockTime();
            $useCamiChannelData = $useCamiChannel['data'];
            $db::startTrans();
            //冻结 核销服务商订单金额  >write_off => 增加冻结金额 -》减少可用金额   >100更新绑定服务商状态
            $bsaWriteOff = $db::table("bsa_write_off")
                ->where('write_off_id', $useCamiChannelData['write_off_id'])
                ->lock(true)
                ->find();
            if (!$bsaWriteOff) {
                $db::rollback();
                $failOrderWhere['order_me'] = $insertOrderData['order_me'];
                $failOrderUpdate['order_status'] = 3;  //下单失败-等待访问
                $failOrderUpdate['order_desc'] = "下单失败，无可匹配核销商-201";  //下单失败
                $db::table("bsa_order")->where($failOrderWhere)->update($failOrderUpdate);
                return apiJsonReturn(-201, "无可匹配订单！");
            }

            //更新核销商金额
            //冻结金额
            $freezeAmount = ($insertOrderData['amount'] * (1 - $useCamiChannelData['rate']));
            $writeOffModel = new WriteoffModel();

            //可用金额减少   use_amount--
            //冻结金额减少   freeze_amount--
            $updateWriteOff = $writeOffModel
                ->execute("UPDATE bsa_write_off  SET 
                           use_amount = use_amount - " . (number_format($freezeAmount, 3)) . " ,
                           freeze_amount = freeze_amount + " . (number_format($freezeAmount, 3)) . " 
                         WHERE  write_off_id = " . $useCamiChannelData['write_off_id']);
            if ($updateWriteOff != 1) {
                $db::rollback();
                $failOrderWhere['order_me'] = $insertOrderData['order_me'];
                $failOrderUpdate['order_status'] = 3;  //下单失败-等待访问
                $failOrderUpdate['order_desc'] = "下单失败，更新核销商金额有误！-203";
                $db::table("bsa_order")->where($failOrderWhere)->update($failOrderUpdate);
                logs(json_encode([
                    'action' => 'updateMatch',
                    'updateCamiChannelWhere' => $useCamiChannelData['write_off_id'],
                    'updateCamiChannelData' => $freezeAmount,
                    'updateSql' => $db::table("bsa_write_off")->getLastSql(),
                    'updateMatchSuccessRes' => $updateWriteOff,
                ]), 'updateWriteOffStatusFail');
                return apiJsonReturn(-203, "无可匹配订单！");
            }
            $bsaWriteOff = $db::table("bsa_write_off")
                ->where('write_off_id', '=', $useCamiChannelData['write_off_id'])
                ->find();
            //如果核销服务商余额小于100 关闭其匹配订单功能
            if ($bsaWriteOff['use_amount'] < 100) {
                $updateWriteData['status'] = 2;//核销服务商状态关闭。
                //核销通道状态关闭
                $updateCamiChannelWhere['write_off_id'] = $useCamiChannelData['write_off_id'];
                $updateCamiChannelData['order_status'] = 2;
                $closeStatus = $db::table('bsa_cami_write')->where($updateCamiChannelWhere)->update($updateCamiChannelData);
                if (!$closeStatus) {
                    $db::rollback();
                    logs(json_encode([
                        'action' => 'updateMatch',
                        'updateCamiChannelWhere' => $updateCamiChannelWhere,
                        'updateCamiChannelData' => $updateCamiChannelData,
                        'updateMatchSuccessRes' => $closeStatus,
                    ]), 'updateCamiWriteStatusFail');
                    return apiJsonReturn(-202, '', '下单频繁，请稍后再下-5！');
                }
            }

            $url = $request->domain() . "/api/orderinfo/info2" . "?order=" . $insertOrderData['order_me'];
            if (isset($message['cami_type_sign']) && !empty($message['cami_type_sign'])) {
                if ($message['cami_type_sign'] == 'xiecheng') {
                    $url = $request->domain() . "/api/orderinfo/info2" . "?order=" . $insertOrderData['order_me'] . "&cami_type_name=xiecheng";
                }
                if ($message['cami_type_sign'] == 'Walmart') {
                    $url = $request->domain() . "/api/orderinfo/info" . "?order=" . $insertOrderData['order_me'] . "&cami_type_name=Walmart";
                }
            }
            //修改订单状态 //下单成功
            $successOrderWhere['order_me'] = $insertOrderData['order_me'];
            $successOrderUpdate['order_status'] = 4;  //下单成功-等待访问
            $successOrderUpdate['rate'] = $useCamiChannelData['rate'];  //费率
            $successOrderUpdate['write_off_sign'] = $bsaWriteOff['write_off_sign'];   //匹配核销单核销商标识
            $orderLimitTime = SystemConfigModel::getOrderLockTime();
            $successOrderUpdate['order_limit_time'] = (time() + $orderLimitTime);  //订单表 $orderLimitTime 订单限制展示时间
            $successOrderUpdate['limit_time'] = (time() + $orderLimitTime);  //订单表 订单冻结回调时间
            $successOrderUpdate['operator'] = $useCamiChannelData['cami_type_sign']; //移动联通电信  /沃尔玛京东E卡。。。
            $successOrderUpdate['qr_url'] = $url; //支付订单
            $successOrderUpdate['order_desc'] = "等待访问!"; //订单描述
            $updateSuccessOrderRes = $orderModel->where($successOrderWhere)->update($successOrderUpdate);

            if (!$updateSuccessOrderRes) {
                Log::OrderLog($insertOrderData['merchant_sign'], $insertOrderData['order_no'], "下单失败！联系技术：301");
                logs(json_encode([
                    'action' => 'updateSuccessOrder',
                    'Where' => $successOrderWhere,
                    'updateMatch' => $successOrderUpdate,
                    'updateSql' => $db::table("bsa_order")->getLastSql(),
                    'updateMatchSuccessRes' => $updateSuccessOrderRes,
                ]), 'updateUpdateSuccessOrderFail');
                $db::rollback();
                return apiJsonReturn(-204, '', '下单频繁，联系技术：-204！');
            }

            Log::OrderLog($insertOrderData['merchant_sign'], $insertOrderData['order_no'], "下单匹配成功！" . $url);
            $db::commit();
            return json(msg(200, $url, "下单成功"));
        } catch (\Error $error) {

            logs(json_encode(['file' => $error->getFile(),
                'line' => $error->getLine(), 'errorMessage' => $error->getMessage()
            ]), 'orderError');
            return json(msg(-22, '', "接口异常!-22"));
        } catch (\Exception $exception) {
            logs(json_encode(['file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'errorMessage' => $exception->getMessage(),
                'lastSql' => $db::table('bsa_order')->getLastSql(),
            ]), 'orderException');
            return json(msg(-11, '', "接口异常!-11"));
        }
    }


    /**
     * 玩家点击支付方式
     * @return \think\response\Json
     */
    public function addOrderPayType()
    {
        $data = @file_get_contents('php://input');
        $param = json_decode($data, true);
        logs(json_encode(['param' => $param]), 'addOrderPayType');
        if (!isset($param['payType']) || empty($param['payType'])) {
            return json(msg(-1, '', "payType error"));
        }
        if (!isset($param['order_no']) || empty($param['order_no'])) {
            return json(msg(-1, '', "order_no error"));
        }
        try {
            $orderShowTime = SystemConfigModel::getOrderShowTime();
            $db = new Db();
//            $orderModel = new OrderModel();
            $orderInfo = $db::table("bsa_order")
                ->where("order_no", "=", $param['order_no'])
                ->find();
            if (empty($orderInfo)) {
                return json(msg(-1, '', "order error"));
            }
            if (($orderInfo['add_time'] + $orderShowTime) < time()) {
                return json(msg(-2, '', "order time out"));
            }
            $updateData['user_ip'] = getLocationByIp(request()->ip());
            $updateData['click_time'] = time();
            $updateData['pay_name'] = htmlspecialchars($param['payType']);
            $db::table("bsa_order")
                ->where("order_no", "=", $param['order_no'])
                ->update($updateData);
            return json(msg(0, '', "success"));
        } catch (\Error $error) {
            logs(json_encode(['file' => $error->getFile(),
                'line' => $error->getLine(), 'errorMessage' => $error->getMessage()
            ]), 'addOrderPayTypeError');
            return json(msg(-22, '', "Error-22"));
        } catch (\Exception $exception) {
            logs(json_encode(['file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'errorMessage' => $exception->getMessage(),
                'lastSql' => $db::table('bsa_order')->getLastSql(),
            ]), 'addOrderPayTypeException');
            return json(msg(-11, '', "Exception-11"));
        }
    }

    /**
     * 卡密引导页
     * @return void
     */
    public function info(Request $request)
    {
        $message = $request->param();
        $db = new Db();
        try {
            if (!isset($message['order']) || empty($message['order'])) {
                echo "请重新下单";
                exit;
            }
            $orderWhere['order_status'] = 4;  //下单成功-等待访问
            $orderWhere['order_me'] = $message['order'];  //下单成功-等待访问
            $orderModel = new OrderModel();
            $orderInfo = $orderModel
                ->where($orderWhere)
                ->find();
            if (empty($orderInfo)) {
                logs(json_encode([
                    'action' => 'info',
                    'message' => $message,
                    'lockRes' => $orderInfo,
                ]), 'orderInfoFail');
                echo '访问繁忙，重新下单！';
                exit;
            }
            $orderWhere['order_status'] = 4;  //下单成功-等待访问
            $orderWhere['order_me'] = $message['order'];  //下单成功-等待访问

            $db = new Db();
            $orderModel = new OrderModel();
            $orderInfo = $orderModel
                ->where($orderWhere)
                ->find();
            if (empty($orderInfo)) {
                logs(json_encode([
                    'action' => 'info',
                    'message' => $message,
                    'lockRes' => $orderInfo,
                ]), 'orderInfoFail');
                echo '访问繁忙，重新下单！';
            }
            //可支付状态
            if ($orderInfo['order_status'] != 4) {
                echo "请重新下单!!!!" . $orderInfo['order_status'];
                exit;
            }
            $orderShowTime = SystemConfigModel::getOrderShowTime();
            $endTime = $orderInfo['add_time'] + $orderShowTime;
            $now = time();
            $countdownTime = $endTime - $now;
            if ($countdownTime < 0) {
                echo "订单超时，请重新下单！";
                exit;
            }
            $orderInfo['camiTypeName'] = $db::table("bsa_cami_type")->where('cami_type_sign', $orderInfo['operator'])->find()['cami_type_username'];
//            $jdUrl = "https://so.m.jd.com/ware/search.action?keyword=%E6%B2%83%E5%B0%94%E7%8E%9B%E5%8D%A1%E5%AF%86" . (int)$orderInfo['amount'] . "&searchFrom=search&sf=11&as=1";
            $jdUrl = "openapp.jdmobile://virtual?params=%7B%22des%22%3A%22productList%22%2C%22keyWord%22%3A%22%E6%B2%83%E5%B0%94%E7%8E%9B%E5%8D%A1%E7%94%B5%E5%AD%90%E5%8D%A1".(int)$orderInfo['amount']."%22%2C%22from%22%3A%22search%22%2C%22category%22%3A%22jump%22%2C%22sourceType%22%3A%22JSHOP_SOURCE_TYPE%22%2C%22sourceValue%22%3A%22JSHOP_SOURCE_VALUE%22%2C%22M_sourceFrom%22%3A%22so%22%2C%22msf_type%22%3A%22auto%22%2C%22m_param%22%3A%7B%22m_source%22%3A%220%22%2C%22event_series%22%3A%7B%7D%2C%22jda%22%3A%22122270672.1671255236227584470994.1671255236.1682483782.1683211206.17%22%2C%22usc%22%3A%22card-dp.kulinan.shop%22%2C%22ucp%22%3A%22-%22%2C%22umd%22%3A%22referral%22%2C%22utr%22%3A%22-%22%2C%22jdv%22%3A%22122270672%7Ccard-dp.kulinan.shop%7C-%7Creferral%7C-%7C1683211206381%22%2C%22ref%22%3A%22https%3A%2F%2Fsou.m.jd.com%2Fbases%2Fm%2FsearchKeyword.htm%3Fkeyword%3D%25E6%25B2%2583%25E5%25B0%2594%25E7%258E%259B%25E5%258D%25A1%25E7%2594%25B5%25E5%25AD%2590%25E5%258D%25A1".(int)$orderInfo['amount']."%26searchFrom%3Dsearch%26sf%3D14%26as%3D0%22%2C%22psn%22%3A%221671255236227584470994%7C17%22%2C%22psq%22%3A4%2C%22unpl%22%3A%22%22%2C%22pc_source%22%3A%22%22%2C%22mba_muid%22%3A%221671255236227584470994%22%2C%22mba_sid%22%3A%2216832112063878582766825417500%22%2C%22std%22%3A%22MO-J2011-1%22%2C%22par%22%3A%22keyword%3D%25E6%25B2%2583%25E5%25B0%2594%25E7%258E%259B%25E5%258D%25A1%25E7%2594%25B5%25E5%25AD%2590%25E5%258D%25A1".(int)$orderInfo['amount']."%26searchFrom%3Dsearch%26sf%3D14%26as%3D0%22%2C%22event_id%22%3A%22MDownLoadFloat_AppArouse%22%2C%22mt_xid%22%3A%22%22%2C%22mt_subsite%22%3A%22%22%2C%22YINLIUhuanqi%22%3A%22https%3A%2F%2Fsou.m.jd.com%2Fbases%2Fm%2FsearchKeyword.htm%3Fkeyword%3D%25E6%25B2%2583%25E5%25B0%2594%25E7%258E%259B%25E5%258D%25A1%25E7%2594%25B5%25E5%25AD%2590%25E5%258D%25A1".(int)$orderInfo['amount']."%26searchFrom%3Dsearch%26sf%3D14%26as%3D0%22%7D%2C%22SE%22%3A%7B%22mt_subsite%22%3A%22%22%2C%22__jdv%22%3A%22122270672%7Ccard-dp.kulinan.shop%7C-%7Creferral%7C-%7C1683211206381%22%2C%22unpl%22%3A%22%22%2C%22__jda%22%3A%22122270672.1671255236227584470994.1671255236.1682483782.1683211206.17%22%7D%7D";
//            $taobaoUrl = "https://main.m.taobao.com/search/index.html?spm=a215s.7406091.topbar.1.560c6770snz1OF&pageType=3&q=%E6%B2%83%E5%B0%94%E7%8E%9B%E7%94%B5%E5%AD%90%E5%8D%A1". (int)$orderInfo['amount'];
            $taobaoUrl = "tbopen://m.taobao.com/tbopen/index.html?h5Url=https%3A%2F%2Fmain.m.taobao.com%2Fsearch%2Findex.html%3Fspm%3Da215s.7406091.topbar.1.560c6770snz1OF%26pageType%3D3%26q%3D%25E6%25B2%2583%25E5%25B0%2594%25E7%258E%259B%25E7%2594%25B5%25E5%25AD%2590%25E5%258D%25A1". (int)$orderInfo['amount'];
            $this->assign('orderData', $orderInfo);
            $this->assign('jdUrl', $jdUrl);
            $this->assign('taobaoUrl', $taobaoUrl);
            return $this->fetch('info1');
        } catch (\Error $error) {
            logs(json_encode(['file' => $error->getFile(),
                'line' => $error->getLine(), 'errorMessage' => $error->getMessage()
            ]), 'infoError');
            return json(msg(-22, '', "Error-22"));
        } catch (\Exception $exception) {
            logs(json_encode(['file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'errorMessage' => $exception->getMessage(),
                'lastSql' => $db::table('bsa_order')->getLastSql(),
            ]), 'infoErrorException');
        }

    }


    /**
     * 携程订单页
     * @return void
     */
    public function info2(Request $request)
    {

        $db = new Db();
        try {

            $message = $request->param();
            if (!isset($message['order']) || empty($message['order'])) {
                echo "请重新下单";
                exit;
            }
            $orderWhere['order_status'] = 4;  //下单成功-等待访问
            $orderWhere['order_me'] = $message['order'];  //下单成功-等待访问

            $orderModel = new OrderModel();
            $orderInfo = $orderModel
                ->where($orderWhere)
                ->find();
            if (empty($orderInfo)) {
                logs(json_encode([
                    'action' => 'info',
                    'message' => $message,
                    'lockRes' => $orderInfo,
                ]), 'orderInfoFail');
                echo '访问繁忙，重新下单！';
                exit;
            }
            //可支付状态
            if ($orderInfo['order_status'] != 4) {
                echo "请重新下单!!!!" . $orderInfo['order_status'];
                exit;
            }
            $orderShowTime = SystemConfigModel::getOrderShowTime();
            $endTime = $orderInfo['add_time'] + $orderShowTime;
            $now = time();
            $countdownTime = $endTime - $now;
            if ($countdownTime < 0) {
                echo "订单超时，请重新下单！";
                exit;
            }
            $orderInfo['camiTypeName'] = "携程电子卡";
            $this->assign('orderData', $orderInfo);
            return $this->fetch('info2');
        } catch (\Error $error) {
            logs(json_encode(['file' => $error->getFile(),
                'line' => $error->getLine(), 'errorMessage' => $error->getMessage()
            ]), 'infoError');

            echo "订单页繁忙！Error-22";
        } catch (\Exception $exception) {
            logs(json_encode(['file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'errorMessage' => $exception->getMessage(),
                'lastSql' => $db::table('bsa_order')->getLastSql(),
            ]), 'infoErrorException');
            echo "订单页繁忙! exception-11";
        }
    }

    /**
     * @param Request $request
     * @return void
     * @throws \think\db\exception\DataNotFoundException
     * @throws \think\db\exception\ModelNotFoundException
     * @throws \think\exception\DbException
     */
    public function getInfo(Request $request)
    {
        $db = new Db();
        try {

            $message = $request->param();
            if (!isset($message['id']) || empty($message['id'])) {
                return json(['code' => -1, 'msg' => '数据无效！', 'data' => []]);
            }
            $orderWhere['order_status'] = 4;  //下单成功-等待访问
            $orderWhere['order_me'] = $message['order'];  //下单成功-等待访问

            $orderModel = new OrderModel();
            $orderInfo = $orderModel
                ->where($orderWhere)
                ->find();
            if (empty($orderInfo)) {
                logs(json_encode([
                    'action' => 'info',
                    'message' => $message,
                    'ip' => $request->ip(),
                    'lockRes' => $orderInfo,
                ]), 'orderInfoFail');
                return json(['code' => -1000, 'msg' => '请重新下单！', 'data' => []]);
            }
            //可支付状态
            if ($orderInfo['order_status'] != 4) {
                return json(['code' => -2, 'msg' => '请重新下单！', 'data' => []]);
            }
            $orderShowTime = SystemConfigModel::getOrderShowTime();
            $endTime = $orderInfo['add_time'] + $orderShowTime;
            $now = time();
            $countdownTime = $endTime - $now;
            if ($countdownTime < 0) {
                return json(['code' => -3, 'msg' => '订单冻结！', 'data' => []]);
            }

            return json(['code' => 0, 'msg' => '可支付！', 'data' => []]);
        } catch (\Error $error) {
            logs(json_encode(['file' => $error->getFile(),
                'line' => $error->getLine(), 'errorMessage' => $error->getMessage()
            ]), 'getInfoError');

            return json(['code' => -11, 'msg' => '订单异常不可支付！', 'data' => []]);
        } catch (\Exception $exception) {
            logs(json_encode(['file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'errorMessage' => $exception->getMessage(),
                'lastSql' => $db::table('bsa_order')->getLastSql(),
            ]), 'getInfoErrorException');
            return json(['code' => -3, 'msg' => '订单冻结！', 'data' => []]);
        }

        return json(['code' => -333, 'msg' => '失败！', 'data' => []]);
    }

    /**
     *
     * orderNo: orderNos,
     * acceptCardNo: cardInfo,
     * acceptCard: pwd
     * 客户上传卡密
     * @return void
     */
    public function uploadCard(Request $request)
    {
        if (request()->isPost()) {
            $param = input('post.');
            $orderModel = new OrderModel();
            if (!isset($param['orderNo']) || !isset($param['acceptCardNo']) || !isset($param['acceptCard'])) {
                return json(['code' => -1000, 'msg' => '数据无效！', 'data' => []]);
            }
            logs(json_encode(['message' => $param, 'line' => 366]), 'uploadCard_fist');
            $where['order_me'] = $param['orderNo'];
            //查询订单状态
            $orderData = $orderModel->where($where)->find();
            if (empty($orderData)) {
                return json(['code' => -1, 'msg' => '上传无此订单！', 'data' => []]);
            }
            if (!empty($orderData['cami_account'])) {
                return json(['code' => -3, 'msg' => '订单正在正在核销中，请勿重新提交！', 'data' => []]);
            }

            if (time() > $orderData['order_limit_time']) {
                return json(['code' => -3, 'msg' => '订单超时，请重新下单！', 'data' => []]);
            }

            try {
                //卡密
                $hasWhere['cami_account'] = $param['acceptCardNo'];
                $hasWhere['cami_password'] = $param['acceptCard'];
                $hasWhere['order_status'] = 1;
                $isHasOrderData = $orderModel->where($hasWhere)->find();

                if ($isHasOrderData) {
                    return json(['code' => -12, 'msg' => '卡密已上传，请勿重新提交提交！', 'data' => []]);
                }
                $updateData['cami_account'] = $param['acceptCardNo'];
                $updateData['cami_password'] = $param['acceptCard'];
                $updateData['order_desc'] = "上传卡密成功，正在请求核销";
                $update = $orderModel->where($where)->update($updateData);
                if (!$update) {
                    return json(['code' => -22, 'msg' => '提交失败，请截图联系客服！', 'data' => []]);
                }

                $appKey = "qG4UnbXxzgxdI6VU";
                $secret = "X5WwO3OlrGNFTXn35Dut2MBqJFZLl9NU";
                $encryptPassword = "VhClL3zB55pfCN8mdIJpt9B3VwLNCRMd";
                $url = "http://114.67.177.36:38088/uploadCard";
                $notifyUrl = $request->domain() . "/api/cardinfo/cardUploadNotify";;
//        {"cardList":[{"cardName":"0aaa","cardPass":"3456"}],"notifyUrl":"http://localhost/test","timestamp":1681735480158}
                // 创建有序字典
                $objectMap = array();
                $objectMap["notifyUrl"] = $notifyUrl;
                $objectMap["timestamp"] = getMillisecond();
                $cardList = array();
                $obj["cardName"] = $updateData['cami_account'];
                $obj["cardPass"] = $updateData['cami_password'];
                $cardList[] = $obj;
                usort($cardList, function ($a, $b) {
                    return strcmp($a["cardName"], $b["cardName"]);
                });
                $objectMap["cardList"] = $cardList;

                // 对字典进行排序并转换为JSON字符串
                ksort($objectMap);
                $jsonStr = json_encode($objectMap, JSON_UNESCAPED_SLASHES);
                // 拼接签名字符串并计算MD5
                $textStr = "{$appKey}{$jsonStr}{$secret}";
                $sign = md5($textStr);
                // 将签名添加到字典中
                $objectMap["sign"] = $sign;
                $jsonStr = json_encode($objectMap, JSON_UNESCAPED_SLASHES);

                $cipher = new AES(1);
                $cipher->setKey($encryptPassword);
                $encryptedData = $cipher->encrypt($jsonStr);

                $data = gzencode($encryptedData);
                // 发送HTTP POST请求并输出响应结果
                $headers = array("appKey: {$appKey}", "Content-Type: application/octet-stream", "Content-Encoding: gzip");
                $options = array('http' => array('method' => 'POST', 'header' => implode("\r\n", $headers), 'content' => $data));

                $response = file_get_contents($url, false, stream_context_create($options));

                $responseData = json_decode($response, true);

                $updateData2['upload_status'] = 1;
                $updateData2['check_result'] = $responseData['msg'];
                $updateData2['order_pay'] = $responseData['traceId'];

                $account = "无关键字段返回！";
                if (isset($responseData['data']['uploadId'])) {
                    $account = $responseData['data']['uploadId'];
                }
                $updateData2['account'] = $account;
                $updateData2['upload_time'] = date("Y-m-d H:i:s", time());
                if ($responseData['code'] != 200) {
                    $updateData2['upload_status'] = 2;
                    $updateData2['order_desc'] = "上传请求返回：" . $responseData['msg'];
                }

                $update2 = $orderModel->where($where)->update($updateData2);
                if (!$update2) {
                    return json(['code' => -5, 'msg' => '提交失败，请重新下单提交！', 'data' => []]);
                }
                logs(json_encode(['message' => $param, 'uploadData' => $objectMap, 'response' => $responseData]), 'uploadCard_first');
                //请求核销通道
                return json(['code' => 0, 'msg' => '上传成功，正在处理', 'data' => []]);
            } catch (\Exception $exception) {
                logs(json_encode(['param' => $param,
                    'file' => $exception->getFile(),
                    'line' => $exception->getLine(),
                    'errorMessage' => $exception->getMessage()]), 'uploadCardException');

                return json(['code' => -11, 'msg' => 'uploadCard exception!' . $exception->getMessage()]);
            } catch (\Error $error) {
                logs(json_encode(['param' => $param,
                    'file' => $error->getFile(),
                    'line' => $error->getLine(),
                    'errorMessage' => $error->getMessage()]), 'uploadCardError');

                return json(['code' => -22, 'msg' => 'uploadCard error!' . $error->getMessage()]);
            }
        }
    }


    /**
     *
     * orderNo: orderNos,
     * acceptCardNo: cardInfo,
     * acceptCard: pwd
     * 客户上传卡密   沃尔玛
     * @return void
     */
    public function uploadCardTwo(Request $request)
    {
        if (request()->isPost()) {
            $param = input('post.');
            $orderModel = new OrderModel();
            if (!isset($param['orderNo']) || !isset($param['acceptCardNo']) || !isset($param['acceptCard'])) {
                return json(['code' => -1000, 'msg' => '数据无效！', 'data' => []]);
            }
            logs(json_encode(['message' => $param, 'line' => 366]), 'uploadCard_fist');
            $where['order_me'] = $param['orderNo'];
            //查询订单状态
            $orderData = $orderModel->where($where)->find();
            if (empty($orderData)) {
                return json(['code' => -1, 'msg' => '上传无此订单！', 'data' => []]);
            }
            if (!empty($orderData['cami_account'])) {
                return json(['code' => -3, 'msg' => '订单正在正在核销中，请勿重新提交！', 'data' => []]);
            }

            if (time() > $orderData['order_limit_time']) {
                return json(['code' => -3, 'msg' => '订单超时，请重新下单！', 'data' => []]);
            }
            if (empty($param['acceptCardNo'])) {
                return json(['code' => -1, 'msg' => '卡号错误！', 'data' => []]);
            }
            if (empty($param['acceptCard'])) {
                return json(['code' => -1, 'msg' => '卡号错误！', 'data' => []]);
            }
            try {
                //卡密
                $hasWhere['cami_account'] = $param['acceptCardNo'];
                $hasWhere['cami_password'] = $param['acceptCard'];
                $hasWhere['order_status'] = 1;

                $isHasOrderData = $orderModel->where($hasWhere)->find();

                if ($isHasOrderData) {
                    return json(['code' => -12, 'msg' => '卡密已上传，请勿重新提交提交！', 'data' => []]);
                }
                $updateData['cami_account'] = $param['acceptCardNo'];
                $updateData['cami_password'] = $param['acceptCard'];
                $updateData['order_desc'] = "上传卡密成功，正在请求核销";
                $update = $orderModel->where($where)->update($updateData);
                if (!$update) {
                    return json(['code' => -22, 'msg' => '提交失败，请截图联系客服！', 'data' => []]);
                }
                $ZhouyiModel = new CardzhouyiModel();

                $cardData['cami_account'] = $param['acceptCardNo'];
                $cardData['cami_password'] = $param['acceptCard'];
                $cardData['orderNo'] = $where['order_me'];
                $cardData['bizNotifyUrl'] = $request->domain() . "/api/cardinfo/cardNotify";
                $cardData['amount'] = $orderData['amount'];
                $response = $ZhouyiModel->upload($cardData);


                $updateData2['upload_status'] = 1;
                $updateData2['check_result'] = $response['msg'];

                $updateData2['upload_time'] = date("Y-m-d H:i:s", time());
                if ($response['code'] != 0) {
                    $updateData2['upload_status'] = 2;
                    $updateData2['order_desc'] = "上传请求返回：" . $response['msg'];
                }


                $update2 = $orderModel->where($where)->update($updateData2);
                if (!$update2) {
                    return json(['code' => -5, 'msg' => '提交失败，请重新下单提交！', 'data' => []]);
                }
                logs(json_encode(['message' => $param, 'uploadData' => $cardData, 'response' => $response]), 'uploadCard_first');
                //请求核销通道
                return json(['code' => $response['code'], 'msg' => $response['msg'], 'data' => []]);
            } catch (\Exception $exception) {
                logs(json_encode(['param' => $param,
                    'file' => $exception->getFile(),
                    'line' => $exception->getLine(),
                    'errorMessage' => $exception->getMessage()]), 'uploadCard2Exception');

                return json(['code' => -112, 'msg' => 'uploadCard exception!' . $exception->getMessage()]);
            } catch (\Error $error) {
                logs(json_encode(['param' => $param,
                    'file' => $error->getFile(),
                    'line' => $error->getLine(),
                    'errorMessage' => $error->getMessage()]), 'uploadCard2Error');

                return json(['code' => -22, 'msg' => 'uploadCard error!' . $error->getMessage()]);
            }
        }
    }


    /**
     * 引导页面查询订单状态
     */
    public function getOrderInfo(Request $request)
    {
        $data = @file_get_contents('php://input');
        $message = json_decode($data, true);

        $orderShowTime = SystemConfigModel::getOrderShowTime();
        logs(json_encode([
            'action' => 'getOrderInfo',
            'message' => $message
        ]), 'getOrderInfo');
        if (!isset($message['order_no']) || empty($message['order_no'])) {
            return json(msg(-1, '', '单号有误！'));
        }
        $db = new Db();
        $orderModel = new OrderModel();
//        $where['order_no'] = $message['order_no'];
        $orderInfo = $db::table("bsa_order")
            ->where("order_no", "=", $message['order_no'])->find();
        if (empty($orderInfo)) {
            logs(json_encode([
                'action' => 'lockFail',
                'message' => $message,
                'lockRes' => $orderInfo,
            ]), 'getOrderInfoFail');
            return json(msg(-2, '', '访问繁忙，重新下单！'));
        }
        try {
            if ($orderInfo['order_status'] == 7) {

                logs(json_encode([
                    'action' => 'doMatching',
                    'message' => $message,
                ]), 'getOrderInfodoMatching');
                for ($i = 0; $i < 5; $i++) {
                    sleep(3);
                    $orderInfo = $db::table("bsa_order")->where("order_no", "=", $orderInfo['order_no'])->find();
                    if ($orderInfo['order_status'] == 4) {
                        if (($orderInfo['add_time'] + $orderShowTime) < time()) {
                            return json(msg(-5, '', '订单超时，请重新下单'));
                            break;
                        }
                        $returnData['phone'] = $orderInfo['account'];
                        $returnData['amount'] = $orderInfo['amount'];
                        $limitTime = (($orderInfo['add_time'] + $orderShowTime) - time());
                        $returnData['limitTime'] = (int)($limitTime);
//                $imgUrl = "http://175.178.195.147:9090/upload/tengxun.jpg";

                        $imgUrl = $request->domain() . "/upload/weixin517.jpg";
//                $imgUrl = urlencode($imgUrl);
                        $returnData['imgUrl'] = $imgUrl;
                        return json(msg(0, $returnData, "success"));

                    } else if ($orderInfo['order_status'] == 3) {
                        return json(msg(-3, '', '匹配繁忙，重新下单！'));
                    } else {
                        sleep(1);
                        continue;
                    }
                }
                return json(msg(-9, "", "网络异常，请刷新页面"));
            }
            if ($orderInfo['order_status'] == 0) {

                //展示时间 getOrderShowTime    getAutoCheckOrderTime
                if (time() > ($orderInfo['add_time'] + $orderShowTime)) {
                    return json(msg(-5, '', '订单超时，请重新下单'));
                }
                $db::startTrans();
                $orderInfo = $db::table("bsa_order")
                    ->where("order_no", "=", $orderInfo['order_no'])
                    ->where("order_status", "=", 0)
                    ->lock(true)
                    ->find();

                $orderHxLockTime = SystemConfigModel::getOrderHxLockTime();
                if (!$orderInfo) {
                    logs(json_encode([
                        'action' => 'lockFail',
                        'message' => $message,
                        'lockRes' => $orderInfo,
                    ]), 'getOrderInfoFail');
                    $db::rollback();
                    return json(msg(-6, '', '请刷新或重新下单！-4'));
                }
                //更新为下当中状态
                $doMatch['order_status'] = 7;
                $db::table("bsa_order")->where("order_no", "=", $orderInfo['order_no'])->update($doMatch);
                $db::commit();
                //2、请求核销单
                $orderHXModel = new OrderhexiaoModel();
                $getUseHxOrderRes = $orderHXModel->getUseHxOrderNew($orderInfo);

                if (!isset($getUseHxOrderRes['code']) || $getUseHxOrderRes['code'] != 0) {
                    if (is_array($getUseHxOrderRes['data'])) {
                        $getUseHxOrderRes['data'] = json_encode($getUseHxOrderRes['data']);
                    }
                    logs(json_encode([
                        'action' => 'getUseHxOrderFail',
                        'insertOrderData' => $orderInfo,
                        'getUseHxOrderRes' => $getUseHxOrderRes
                    ]), 'getOrderInfoAmount_log');
                    //修改订单为下单失败状态。
                    $updateOrderStatus['order_status'] = 3;
                    $updateOrderStatus['last_use_time'] = time();
                    $updateOrderStatus['check_result'] = $getUseHxOrderRes['data'];
                    $updateOrderStatus['order_desc'] = "下单失败|-|" . $getUseHxOrderRes['msg'];
                    $updateMatchRes = $orderModel->where('order_no', $orderInfo['order_no'])->update($updateOrderStatus);
                    if (!$updateMatchRes) {
                        logs(json_encode([
                            'action' => 'updateMatchRes',
                            'message' => $message,
                            'updateMatchRes' => $updateMatchRes,
                        ]), 'getOrderInfoFail');
                        return json(msg(-5, '', '下单繁忙'));
                    }
                    return json(msg(-5, '', "下单繁忙，可重新下单！"));
                }
                //有没有之前没有支付的订单匹配的上金额  start
//                $hasPayOrderData = $orderModel
//                    ->where("account", '=', $orderInfo['account'])
//                    ->where("order_pay", '=', $orderInfo['order_pay'])
//                    ->where("order_no", '<>', $orderInfo['order_no'])
//                    ->where("order_status", '<>', 1)
//                    ->where("pay_status", '<>', 1)
//                    ->where("end_check_amount", '<', $getUseHxOrderRes['data']['last_check_amount'] + 20)
//                    ->where("add_time", '>', time() - ($orderHxLockTime + 3600))
//                    ->order('add_time desc')
//                    ->find();
//                if (!empty($hasPayOrderData)) {
//                    logs(json_encode([
//                        "nowTime" => date("Y-m-d H:i:s", time()),
//                        'findLoseOrder' => $hasPayOrderData['order_no'],
//                        'add_time' => $hasPayOrderData['add_time'],
//                        'account' => $hasPayOrderData['account'],
//
//                    ]), 'AfindHasPayOrderAndNotify');
//                    //当前订单更改为下单失败状态
//                    $updateOrderStatus['order_status'] = 3;
//                    $updateOrderStatus['last_use_time'] = time();
//                    $updateOrderStatus['check_result'] = "发现掉单此单失败" . json_encode($hasPayOrderData['order_no']);
//                    $updateOrderStatus['order_desc'] = "发现掉单|此单下单失败！";
//                    $updateMatchRes = $orderModel->where('order_no', $orderInfo['order_no'])->update($updateOrderStatus);
//                    if (!$updateMatchRes) {
//                        logs(json_encode([
//                            'action' => 'updateMatchRes',
//                            'message' => $message,
//                            'updateMatchRes' => $updateMatchRes,
//                        ]), 'hasPayOrderUpdateFail');
//                        return json(msg(-31, '', '下单失败,请重新下单！-31'));
//                    }
//                    //修改订单为下单失败状态。  end
//
//                    //给之前的订单回调 start
//                    $orderHXModel = new OrderhexiaoModel();
//                    $updateOrderWhere['order_no'] = $hasPayOrderData['order_no'];
//                    $updateOrderWhere['account'] = $hasPayOrderData['account'];
//                    //订单表
//                    $localUpdateRes = $orderHXModel->loseOrderLocalUpdateNew($hasPayOrderData, 3, $getUseHxOrderRes['data']['last_check_amount']);
//                    logs(json_encode([
//                        "time" => date("Y-m-d H:i:s", time()),
//                        'findLoseOrder' => $hasPayOrderData['order_no'],
//                        'updateOrderWhere' => $updateOrderWhere,
//                        'account' => $hasPayOrderData['account'],
//                        'localUpdateRes' => $localUpdateRes
//                    ]), 'AfindHasPayOrderAndNotify');
//                    if (!isset($localUpdate['code']) || $localUpdate['code'] != 0) {
//                        return json(msg(32, '', '下单失败，请重新下单2！'));
//                    }
//                    //给之前的订单回调 end
//                    return json(msg(32, '', '下单失败，请重新下单1'));
//                }
                //有没有之前没有支付的订单匹配的上金额  end

                $updateOrderStatus['order_status'] = 4;   //等待支付状态
                $updateOrderStatus['check_times'] = 1;   //下单成功就查询一次
                $updateOrderStatus['start_check_amount'] = $getUseHxOrderRes['data']['last_check_amount'];  //开单余额
                $updateOrderStatus['last_check_amount'] = $getUseHxOrderRes['data']['last_check_amount'];  //第一次查询余额
                $updateOrderStatus['end_check_amount'] = $getUseHxOrderRes['data']['last_check_amount'] + $orderInfo['amount'];  //应到余额
                $updateOrderStatus['order_desc'] = "下单成功,等待支付！";
                if ($orderInfo['operator'] == '移动') {
                    $updateOrderStatus['next_check_time'] = $orderInfo['next_check_time'] + 90;
                }
//                $url = "http://175.178.241.238/pay/#/huafei";
                $url = "http://175.178.241.238/pay/#/kindsRoll";
//                if (isset($orderInfo['payment']) && $orderInfo['payment'] == "alipay") {
//                    $url = "http://175.178.241.238/pay/#/huafeiNewZfb";
//                }
//            订单号order_id   金额 amount   手机号 phone  二维码链接 img_url    有效时间 limit_time 秒
//            $imgUrl = "http://175.178.195.147:9090/upload/huafei.jpg";
//                $imgUrl = "http://175.178.195.147:9090/upload/tengxun.jpg";
                $imgUrl = $request->domain() . "/upload/weixin517.jpg";
//                $imgUrl = urlencode($imgUrl);

                $limitTime = (($orderInfo['add_time'] + $orderShowTime) - time());
                $url = $url . "?order_id=" . $message['order_no'] . "&amount=" . $orderInfo['amount'] . "&phone=" . $orderInfo['account'] . "&img_url=" . $imgUrl . "&limit_time=" . $limitTime;
                $updateOrderStatus['qr_url'] = $url;   //支付订单
//            $localOrderUpdateRes = $orderModel->localUpdateOrder($updateWhere, $updateOrderStatus);
                $localOrderUpdateRes = $db::table("bsa_order")
                    ->where('id', '=', $orderInfo['id'])
                    ->where('order_no', '=', $orderInfo['order_no'])
                    ->update($updateOrderStatus);
                if (!$localOrderUpdateRes) {
                    logs(json_encode([
                        'action' => 'localOrderUpdate',
                        'message' => $message,
                        'localOrderUpdateRes' => $localOrderUpdateRes,
                    ]), 'getOrderInfoFail');
                    $updateOrderStatus['order_status'] = 3;
                    $updateOrderStatus['last_use_time'] = time();
                    $updateOrderStatus['order_desc'] = "下单失败|" . "localOrderUpdateFail";
                    $orderModel->where('order_no', $orderInfo['order_no'])->update($updateOrderStatus);
                    return json(msg(-7, '', '下单繁忙'));
                }
                $returnData['phone'] = $orderInfo['account'];
                $returnData['amount'] = $orderInfo['amount'];
                $returnData['limitTime'] = (int)($limitTime);
                $returnData['imgUrl'] = $imgUrl;
                return json(msg(0, $returnData, 'order_success'));
            } else {
                if (time() > ($orderInfo['add_time'] + $orderShowTime)) {
                    return json(msg(-5, '', '订单超时，请重新下单'));
                }
                if ($orderInfo['order_status'] != 4) {
                    return json(msg(-5, '', '订单状态有误，请重新下单！'));
                }

                $returnData['phone'] = $orderInfo['account'];
                $returnData['amount'] = $orderInfo['amount'];
                $limitTime = (($orderInfo['add_time'] + $orderShowTime) - time());
                $returnData['limitTime'] = (int)($limitTime);
//                $imgUrl = "http://175.178.195.147:9090/upload/tengxun.jpg";

                $imgUrl = $request->domain() . "/upload/weixin517.jpg";
//                $imgUrl = urlencode($imgUrl);
                $returnData['imgUrl'] = $imgUrl;
                return json(msg(0, $returnData, "success"));
            }
        } catch (\Exception $exception) {
            logs(json_encode(['param' => $message,
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'errorMessage' => $exception->getMessage()]), 'orderInfoException');
            return apiJsonReturn(-11, "orderInfo exception!" . $exception->getMessage());
        } catch (\Error $error) {
            logs(json_encode(['param' => $message,
                'file' => $error->getFile(),
                'line' => $error->getLine(),
                'errorMessage' => $error->getMessage()]), 'orderInfoError');
            return json(msg(-22, '', 'orderInfo error!' . $error->getMessage()));
        }
    }

    //结果回调
    public function checkPhoneAmountNotify0076(Request $request)
    {
        session_write_close();
        $data = @file_get_contents('php://input');
        $message = json_decode($data, true);
        try {
            logs(json_encode([
                'param' => $message,
                'ip' => $request->ip(),
                'startTime' => date("Y-m-d H:i:s", time())
            ]), 'checkPhoneAmountNotify0076');
            $validate = new CheckPhoneAmountNotifyValidate();
            if (!$validate->check($message)) {
                return apiJsonReturn(-1, '', $validate->getError());
            }
            $orderModel = new OrderModel();
            $orderWhere['order_no'] = $message['order_no'];  //四方单号
            $orderWhere['account'] = $message['phone'];   //订单匹配手机号
            $orderInfo = $orderModel->where($orderWhere)->find();


            if (empty($orderInfo)) {
                return json(msg(-2, '', '无此订单！'));
            }
            logs(json_encode([
                "time" => date("Y-m-d H:i:s", time()),
                "orderInfo" => $orderInfo,
                'param' => $message
            ]), 'MatchOrderFailCheckPhoneAmountNotify0076');
            if ($orderInfo['order_status'] == 1) {
                return json(msg(-3, '', '订单已支付！'));
            }
            $db = new Db();
            $checkResult = "第" . ($orderInfo['check_times'] + 1) . "次查询结果" . $message['amount'] . "(" . date("Y-m-d H:i:s") . ")";

            $nextCheckTime = $orderInfo['next_check_time'] + 300;  //设置第三次往后的查询时间
            $autoCheckOrderTime = SystemConfigModel::getAutoCheckOrderTime();
            if (is_int($autoCheckOrderTime)) {
                $nextCheckTime = $orderInfo['next_check_time'] + $autoCheckOrderTime;
            }
            if ($message['check_status'] != 1) {
                $updateCheckTimesRes = $db::table("bsa_order")->where($orderWhere)
                    ->update([
                        "check_status" => 0,  //查询结束
//                        "check_times" => $orderInfo['check_times'] + 1,
                        "next_check_time" => $orderInfo['next_check_time'] + 20,
                        "order_desc" => $checkResult,
                        "check_result" => $checkResult,
                    ]);
                logs(json_encode(['phone' => $orderInfo['account'],
                    "order_no" => $orderInfo['order_no'],
                    "notifyTime" => date("Y-m-d H:i:s", time()),
                    "updateCheckTimesRes" => $updateCheckTimesRes
                ]), '0076updateCheckPhoneAmountFail');
                return json(msg(1, '', '接收成功,更新成功1'));
            }
            //查询成功
            $orderWhere['order_no'] = $orderInfo['order_no'];
            $orderUpdate['check_times'] = $orderInfo['check_times'] + 1;
            $orderUpdate['check_status'] = 0;   //可在查询状态
            $orderUpdate['last_check_amount'] = $message['amount'];
            $orderUpdate['next_check_time'] = $nextCheckTime;
            $orderUpdate['check_result'] = $checkResult;
            $updateCheck = $db::table("bsa_order")->where($orderWhere)
                ->update($orderUpdate);
            if (!$updateCheck) {
                logs(json_encode(["time" => date("Y-m-d H:i:s", time()),
                    'action' => "checkNotifySuccess",
                    'message' => json_encode($message),
                    "updateCheck" => $updateCheck
                ]), '0076updateCheckPhoneAmountFail');
            }
            //1、支付到账
            if ($message['amount'] > ($orderInfo['end_check_amount'] - 20)) {
                //本地更新
                $orderHXModel = new OrderhexiaoModel();
                $updateOrderWhere['order_no'] = $orderInfo['order_no'];
                $updateOrderWhere['account'] = $orderInfo['account'];
                $orderHXData = $orderHXModel->where($orderWhere)->find();
                $localUpdateRes = $orderHXModel->orderLocalUpdate($orderInfo);
                logs(json_encode(["time" => date("Y-m-d H:i:s", time()),
                    'updateOrderWhere' => $updateOrderWhere,
                    'account' => $orderHXData['account'],
                    'localUpdateRes' => $localUpdateRes
                ]), '0076updateCheckPhoneAmountLocalUpdate');
                if (!isset($localUpdate['code']) || $localUpdate['code'] != 0) {
                    return json(msg(1, '', '接收成功,更新失败！'));
                }
                return json(msg(1, '', '接收成功,更新成功！'));
            }
            return json(msg(1, '', '接收成功,匹配失败！'));
        } catch (\Exception $exception) {
            logs(json_encode(['param' => $message,
                'file' => $exception->getFile(),
                'line' => $exception->getLine(),
                'errorMessage' => $exception->getMessage()]), 'checkPhoneAmountNotify0076Exception');
            return json(msg(-11, '', '接收异常！'));
        } catch (\Error $error) {
            logs(json_encode(['param' => $message,
                'file' => $error->getFile(),
                'line' => $error->getLine(),
                'errorMessage' => $error->getMessage()]), 'checkPhoneAmountNotify0076Error');
            return json(msg(-22, '', "接收错误！"));
        }
    }


}