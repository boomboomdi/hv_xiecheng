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
    public function bb()
    {
        $appKey = "qG4UnbXxzgxdI6VU";

        $url = "http://114.67.177.36:38088/queryCard?uploadId=" . 74339;  //uploadId
        $headers = array("appKey: {$appKey}");
        $options = array('http' => array('method' => 'get', 'header' => implode("\r\n", $headers)));

        $response = httpGET2($url, $headers);
//        $response = http_get($url, $options);
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