<?php
namespace Addons\Qrcode;
include_once "qrcode/qrlib.php";
class QrcodeUtil {
    public static function show($url){
        \QRcode::png($url, FALSE, 'Q', 20, 2);
    }

    // 生产中间有logo的二维码
    public function logoShow($url){
        header("Content-type: image/png; charset=utf-8");
        \QRcode::png($url, 'data/qrcode.png', 'Q', 5, 2);
        //$logo = 'http://wanyouxi1.ufile.ucloud.com.cn/2016/09/13/2016091310595590958.png';//准备好的logo图片
        $logo = 'http://wanyouxi1.ufile.ucloud.com.cn/2016/10/09/2016100903134354728.png';      // 国庆logo
        $QR = 'data/qrcode.png';//已经生成的原始二维码图
        if ($logo !== FALSE) {
            $QR = imagecreatefromstring(file_get_contents($QR));
            $logo = imagecreatefromstring(file_get_contents($logo));
            $QR_width = imagesx($QR);//二维码图片宽度
            $logo_width = imagesx($logo);//logo图片宽度
            $logo_height = imagesy($logo);//logo图片高度
            $logo_qr_width = $QR_width / 4;
            $scale = $logo_width/$logo_qr_width;
            $logo_qr_height = $logo_height/$scale;
            $from_width = ($QR_width - $logo_qr_width) / 2;
            //重新组合图片并调整大小
            imagecopyresampled($QR, $logo, $from_width, $from_width, 0, 0, $logo_qr_width,
                $logo_qr_height, $logo_width, $logo_height);
        }
        //输出图片
        imagepng($QR);
    }

    //
    public static function returnQrcode($url){
        \QRcode::png($url, 'data/pay_qrcode.png', 'Q', 5, 2);
        echo 'data/pay_qrcode.png';
    }
}
