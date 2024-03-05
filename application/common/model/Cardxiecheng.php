<?php

namespace app\common\model;

use app\common\Aes;
class Cardxiecheng
{
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
     * 创建上传卡密
     * @param notifyUrl  回调通知地址  http://baidu.com
     * @param timestamp  请求时间戳   1679120877260
     * @param sign
     * @param cardList
     * @return void
     */
    public function uploadCard($uploadData)
    {
        $objectMap["cardList"] = $uploadData;

        // 对字典进行排序并转换为JSON字符串
        ksort($objectMap);
        $jsonStr = json_encode($objectMap, JSON_UNESCAPED_SLASHES);
        echo "\n";
        // 拼接签名字符串并计算MD5
        echo $textStr = "{$this->appKey}{$jsonStr}{$this->secretKey}";
        echo "\n";

        echo $sign = md5($textStr);
        echo "\n";
        // 将签名添加到字典中
        $objectMap["sign"] = $sign;
        $jsonStr = json_encode($objectMap, JSON_UNESCAPED_SLASHES);
        echo "请求内容明文: ", $jsonStr, "\n";

        $key = $this->encryptPassword;
        $cipher = new Aes($key);
        $encryptedData = $cipher->encode($jsonStr);
//        $encryptedData = $cipher->encrypt($jsonStr);
        $data = gzencode($encryptedData);
        var_dump($data);exit;
//        $cipher = new Aes(AES::MODE_ECB);
//        $cipher->setKey($this->encryptPassword);
//        $encryptedData = $cipher->encrypt($jsonStr);
//
//        $data = gzencode($encryptedData);
        // 发送HTTP POST请求并输出响应结果
        $url = "http://114.67.177.36:38088/uploadCard";
        $headers = array("appKey: {$this->appKey}", "Content-Type: application/octet-stream", "Content-Encoding: gzip");
        $options = array('http' => array('method' => 'POST', 'header' => implode("\r\n", $headers), 'content' => $data));

        $response = file_get_contents($url, false, stream_context_create($options));
        echo "请求结果: ", $response, "\n";

    }


}