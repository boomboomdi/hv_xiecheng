<?php
/**
 * Created by PhpStorm.
 * User: 75763
 * Date: 2018/12/15
 * Time: 19:53
 */

namespace app\api\controller;

use think\Db;
use think\Controller;
use think\Request;
use app\common\model\OrderModel;
use app\common\model\SystemConfigModel;
use app\common\model\Cardxiecheng;
use tool\Log;

use phpseclib\Crypt\AES;

class Ceshi extends Controller
{

    public function aa1(Request $request)
    {
        $data = @file_get_contents('php://input');
        $message = json_decode($data, true);//获取 调用信息
        $appKey = "qG4UnbXxzgxdI6VU";
        $secret = "X5WwO3OlrGNFTXn35Dut2MBqJFZLl9NU";
        $encryptPassword = "VhClL3zB55pfCN8mdIJpt9B3VwLNCRMd";
        $headers = $request->header();
        $objectMap = $message;
        if (is_array($objectMap['cardList'])) {
//            var_dump($objectMap['cardList'][]);exit;
            foreach ($objectMap['cardList'] as $k => $v) {
//                var_dump($k);exit;
                ksort($objectMap['cardList'][$k]);
            }
        }

        ksort($objectMap);
//        var_dump($objectMap);

        $objectMap = json_encode($objectMap);
        return $objectMap;exit;
//        exit;
        unset ($objectMap['sign']);
        // 对字典进行排序并转换为JSON字符串
        $checkSignData = $objectMap;
        if (is_array($checkSignData['cardList'])) {
            //删除原数组cardList数据
            unset($objectMap['cardList']);
            $checkSignCardListData = $checkSignData['cardList'];
            foreach ($checkSignCardListData as $k => $v) {
                unset($checkSignCardListData[$k]);
                $sortData = $v;
                ksort($sortData);
                $checkSignCardListData[] = $sortData;
            }
            ksort($checkSignCardListData);
            //填充新的cardList数据
            $objectMap['cardList'] = $checkSignCardListData;
        }
    }

    public function aa2(Request $request)
    {
        $data = @file_get_contents('php://input');
        $message = json_decode($data, true);//获取 调用信息
        $sign = urldecode(http_build_query($message,"&"));
        $sign = str_replace(' ','',$sign);
//        $sign = http_build_query($message,"&");
//        $sign = http_build_query($message,"&");
        var_dump($sign);exit;
        $objectMap = $message;
        if (is_array($objectMap['cardList'])) {
//            var_dump($objectMap['cardList'][]);exit;
            foreach ($objectMap['cardList'] as $k => $v) {
//                var_dump($k);exit;
                ksort($objectMap['cardList'][$k]);
            }
        }

        ksort($objectMap);
        $jsonStr = json_encode($objectMap, JSON_UNESCAPED_SLASHES);

        return $jsonStr;
    }
    //下单
    public function ddd1()
    {
//        echo "aaa";exit;
        $db = new Db();
        $orderWhere['order_no'] = "BJ202403101441460010796";
        $writeWhere['write_off_sign'] = "ZHANGSANHEXIAO";
        $order = $db::table("bsa_order")->where($orderWhere)->find();
//        var_dump($order['rate']);exit;
        $bsaWriteOffData = $db::table("bsa_write_off")->where($writeWhere)->find();
        $freezeAmount = (100.00 * (1 - 0.05));
        $db::table("bsa_write_off")
            ->execute("UPDATE bsa_write_off  SET 
                                            use_amount = use_amount - " . (number_format($freezeAmount, 3)) . " ,
                                            freeze_amount = freeze_amount + " . (number_format($freezeAmount, 3)) . " 
                                            WHERE  write_off_id = " . $bsaWriteOffData['write_off_id']);

        var_dump($db::table("bsa_write_off")->getLastSql());
        exit;

    }

    //支付失败
    public function eee()
    {
        $db = new Db();
        $orderWhere['order_no'] = "BJ202403101441460010796";
        $writeWhere['write_off_sign'] = "ZHANGSANHEXIAO";
        $order = $db::table("bsa_order")->where($orderWhere)->find();
//        var_dump($order['rate']);exit;
        $bsaWriteOffData = $db::table("bsa_write_off")->where($writeWhere)->find();
        $freezeAmount = (100.00 * (1 - 0.05));
        $updateWriteOff = $db::table("bsa_write_off")
            ->execute("UPDATE bsa_write_off  SET 
                                            use_amount = use_amount + " . (number_format($freezeAmount, 3)) . " ,
                                            freeze_amount = freeze_amount - " . (number_format($freezeAmount, 3)) . " 
                                            WHERE  write_off_id = " . $bsaWriteOffData['write_off_id']);
        var_dump($db::table("bsa_write_off")->getLastSql());
        exit;

    }

    //支付成功
    public function fff()
    {
        $db = new Db();
        $orderWhere['order_no'] = "BJ202403101441460010796";
        $writeWhere['write_off_sign'] = "ZHANGSANHEXIAO";
        $order = $db::table("bsa_order")->where($orderWhere)->find();
//        var_dump($order['rate']);exit;
        $bsaWriteOffData = $db::table("bsa_write_off")->where($writeWhere)->find();
        $freezeAmount = (100.00 * (1 - 0.05));
        $updateWriteOff = $db::table("bsa_write_off")
            ->execute("UPDATE bsa_write_off  SET 
                                            freeze_amount = freeze_amount - " . (number_format($freezeAmount, 3)) . " ,
                                            write_off_deposit = write_off_deposit - " . (number_format($freezeAmount, 3)) . " 
                                            WHERE  write_off_id = " . $bsaWriteOffData['write_off_id']);
        var_dump($db::table("bsa_write_off")->getLastSql());
        exit;

    }

    public function cc()
    {
        $aaa = array();
        for ($i = 0; $i < 3; ++$i) {
            $aaa[] = $i;
        }
        foreach ($aaa as $k => $v) {
            if ($v) {

            }

        }
    }

    public function bb()
    {
        $appKey = "qG4UnbXxzgxdI6VU";

        $url = "http://114.67.177.36:38088/queryCard?uploadId=" . 74358;  //uploadId
        $headers = array("appKey: {$appKey}");
        $options = array('http' => array('method' => 'get', 'header' => implode("\r\n", $headers)));

//        $response = cUrlGetData($url, $headers);
        $response = httpGET2($url, $headers);
//        $response = file_get_contents($url, false, stream_context_create($options));
        var_dump($response);
        exit;
    }


    //请求网关
    private $gateway = "http://114.67.177.36:38088/uploadCard";
//
    //分配的商户key
    private $appKey = "qG4UnbXxzgxdI6VU";
    //秘钥
    private $secretKey = "X5WwO3OlrGNFTXn35Dut2MBqJFZLl9NU";
    //加密密码
    private $encryptPassword = "VhClL3zB55pfCN8mdIJpt9B3VwLNCRMd";

    /**
     * 上传卡密
     * @return void
     */
    public function uploadCami()
    {
        // 创建有序字典
        $objectMap = array();
        $objectMap["notifyUrl"] = "http://localhost/test";
        $objectMap["timestamp"] = 1681735480158;
        $cardList = array();
        for ($i = 0; $i < 3; ++$i) {
            $obj = array();
            $obj["cardName"] = "{$i}aaa";
            $obj["cardPass"] = "3456";
            $cardList[] = $obj;
        }
        $cardModel = new Cardxiecheng();
        $cardModel->uploadCard($cardList);

    }

    //请求卡密
    public function aa()
    {

//        require_once('./vandor/phpseclib/Crypt/AES.php'); $aes = new phpseclib\Crypt\AES(CRYPT_AES_MODE_ECB);
//        $appKey = "";
//        $secret = "";
//        $encryptPassword = "";
        $appKey = "qG4UnbXxzgxdI6VU";
        $secret = "X5WwO3OlrGNFTXn35Dut2MBqJFZLl9NU";
        $encryptPassword = "VhClL3zB55pfCN8mdIJpt9B3VwLNCRMd";
//        {"cardList":[{"cardName":"0aaa","cardPass":"3456"}],"notifyUrl":"http://localhost/test","timestamp":1681735480158}
        // 创建有序字典
        $objectMap = array();
        $objectMap["notifyUrl"] = "http://localhost/test";
        $objectMap["timestamp"] = 1681735480158;
        $cardList = array();
        for ($i = 0; $i < 3; ++$i) {
            $obj = array();
            $obj["cardName"] = "{$i}aaa";
            $obj["cardPass"] = "3456";
            $cardList[] = $obj;
        }
//        var_dump($cardList);exit;
        usort($cardList, function ($a, $b) {
            return strcmp($a["cardName"], $b["cardName"]);
        });
        $objectMap["cardList"] = $cardList;

        // 对字典进行排序并转换为JSON字符串
        ksort($objectMap);
        $jsonStr = json_encode($objectMap, JSON_UNESCAPED_SLASHES);
//        echo "\n";
        // 拼接签名字符串并计算MD5
        $textStr = "{$appKey}{$jsonStr}{$secret}";
//        echo "\n";
        $sign = md5($textStr);
//        echo "\n";
        // 将签名添加到字典中
        $objectMap["sign"] = $sign;
        $jsonStr = json_encode($objectMap, JSON_UNESCAPED_SLASHES);
//        echo "请求内容明文: ", $jsonStr, "\n";

        $cipher = new AES(1);
        $cipher->setKey($encryptPassword);
        $encryptedData = $cipher->encrypt($jsonStr);

        $data = gzencode($encryptedData);
//        var_dump($data);
//        exit;
        // 发送HTTP POST请求并输出响应结果
        $url = "http://114.67.177.36:38088/uploadCard";
        $headers = array("appKey: {$appKey}", "Content-Type: application/octet-stream", "Content-Encoding: gzip");
        $options = array('http' => array('method' => 'POST', 'header' => implode("\r\n", $headers), 'content' => $data));

        $response = file_get_contents($url, false, stream_context_create($options));

        logs(json_encode(['uploadData' => $objectMap, 'response' => json_decode($response, true)]), 'uploadCard_xc_cs');
        echo "请求结果: ", $response, "\n";
    }

    public function kami()
    {
        return $this->fetch();
    }

    public function demo1()
    {
        return $this->fetch();
    }

    public function shop()
    {
        $orderData['img'] = "";
        $orderData['orderNo'] = 123456123456;
        $orderData['orderMe'] = 654321654321;
        $orderData['camiType'] = "walmart";
        $orderData['camiTypeName'] = "沃尔玛电子卡";
        $orderData['amount'] = 100;
        $orderData['endTime'] = time() + 600;
        $this->assign('orderData', $orderData);
        return $this->fetch();
    }

    public function shop1()
    {

        $orderData['img'] = "";
        $orderData['orderNo'] = 123456123456;
        $orderData['orderMe'] = 654321654321;
        $orderData['camiType'] = "walmart";
        $orderData['camiTypeName'] = "沃尔玛电子卡";
        $orderData['amount'] = 100;
        $orderData['endTime'] = time() + 600;
        $this->assign('orderData', $orderData);
        return $this->fetch();
    }

    public function shop2()
    {

        $orderData['img'] = "";
        $orderData['orderNo'] = 123456123456;
        $orderData['orderMe'] = 654321654321;
        $orderData['camiType'] = "walmart";
        $orderData['camiTypeName'] = "沃尔玛电子卡";
        $orderData['amount'] = 100;
        $orderData['endTime'] = time() + 600;
        $this->assign('orderData', $orderData);
        return $this->fetch();
    }

    //查询订单状态
    public function getOrderInfo()
    {

    }


}