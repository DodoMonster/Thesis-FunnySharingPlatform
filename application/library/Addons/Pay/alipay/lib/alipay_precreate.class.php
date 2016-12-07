<?php

require_once("alipay_core.function.php");
require_once("alipay_rsa.function.php");

class AlipayPrecreate {

    var $alipay_config;
    var $gatewayUrl = 'https://openapi.alipay.com/gateway.do';
    var $signType='RSA';
    var $postCharset="UTF-8";
    var $fileCharset="UTF-8";
    var $rsaPrivateKeyFilePath='';
    var $rsaPrivateKey='';

    function __construct($alipay_config) {
        $this->alipay_config = $alipay_config;
        $this->rsaPrivateKeyFilePath=$alipay_config['private_key_path'];
    }

    /**
     * 生成签名结果
     * @param $para_sort 已排序要签名的数组
     * return 签名结果字符串
     */
    function buildRequestMysign($para_sort) {
        //把数组所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串
        $prestr = createLinkstring($para_sort);

        $mysign = "";
        switch (strtoupper(trim($this->alipay_config['sign_type']))) {
            case "RSA" :
                $mysign = rsaSign($prestr, trim($this->alipay_config['private_key_path']));
                break;
            default :
                $mysign = "";
        }
        return $mysign;
    }

    /**
     * 生成要请求给支付宝的参数数组
     * @param $para_temp 请求前的参数数组
     * @return 要请求的参数数组
     */
    function buildRequestPara($para_temp) {
        //除去待签名参数数组中的空值和签名参数
        $para_filter = paraFilter($para_temp);

        //对待签名参数数组排序
        //$para_sort = argSort($para_filter);
        $para_sort = ksort($para_filter);

        //生成签名结果
        $mysign = $this->buildRequestMysign($para_sort);

        //签名结果与签名方式加入请求提交参数组中
        $para_sort['sign'] = $mysign;
        $para_sort['sign_type'] = strtoupper(trim($this->alipay_config['sign_type']));
        print_r($para_sort);
        

        return $para_sort;
    }

    /**
     * 生成要请求给支付宝的参数数组
     * @param $para_temp 请求前的参数数组
     * @return 要请求的参数数组字符串
     */
    function buildRequestParaToString($apiParams,$sysParams) {
        /*//待请求参数数组
        $para = $this->buildRequestPara($para_temp);
        //把参数组中所有元素，按照“参数=参数值”的模式用“&”字符拼接成字符串，并对字符串做urlencode编码
        $request_data = createLinkstringUrlencode($para);
        return $request_data;*/
        
        //签名
        $sysParams["sign"] = $this->generateSign(array_merge($apiParams, $sysParams), $this->signType);
        //$sysParams["sign_type"]='RSA';

        //系统参数放入GET请求串
        $requestUrl = $this->gatewayUrl . "?";
        foreach ($sysParams as $sysParamKey => $sysParamValue) {
            $requestUrl .= "$sysParamKey=" . urlencode($this->characet($sysParamValue, $this->postCharset)) . "&";
        }
        $requestUrl = substr($requestUrl, 0, -1);
      
        return $requestUrl;
    }
    
    protected function checkEmpty($value) {
        if (!isset($value))
            return true;
        if ($value === null)
            return true;
        if (trim($value) === "")
            return true;

        return false;
    }
    
    public function generateSign($params, $signType = "RSA") {
        return $this->sign($this->getSignContent($params), $signType);
    }
    
    protected function getSignContent($params) {
        ksort($params);

        $stringToBeSigned = "";
        $i = 0;
        foreach ($params as $k => $v) {
            if (false === $this->checkEmpty($v) && "@" != substr($v, 0, 1)) {

                // 转换成目标字符集
                $v = $this->characet($v, $this->postCharset);

                if ($i == 0) {
                    $stringToBeSigned .= "$k" . "=" . "$v";
                } else {
                    $stringToBeSigned .= "&" . "$k" . "=" . "$v";
                }
                $i++;
            }
        }

        unset($k, $v);
        return $stringToBeSigned;
    }
    
    function characet($data, $targetCharset) {
        if (!empty($data)) {
            $fileType = $this->fileCharset;
            if (strcasecmp($fileType, $targetCharset) != 0) {
                $data = mb_convert_encoding($data, $targetCharset);
            }
        }
        return $data;
    }
    
    protected function sign($data, $signType = "RSA") {
        if ($this->checkEmpty($this->rsaPrivateKeyFilePath)) {
            $priKey = $this->rsaPrivateKey;
            $res = "-----BEGIN RSA PRIVATE KEY-----\n" .
                    wordwrap($priKey, 64, "\n", true) .
                    "\n-----END RSA PRIVATE KEY-----";
        } else {
            $priKey = file_get_contents($this->rsaPrivateKeyFilePath);
            $res = openssl_get_privatekey($priKey);
        }

        ($res) or die('您使用的私钥格式错误，请检查RSA私钥配置');

        if ("RSA2" == $signType) {
            openssl_sign($data, $sign, $res, OPENSSL_ALGO_SHA256);
        } else {
            openssl_sign($data, $sign, $res);
        }

        if (!$this->checkEmpty($this->rsaPrivateKeyFilePath)) {
            openssl_free_key($res);
        }
        $sign = base64_encode($sign);
        return $sign;
    }
    
    
}

?>