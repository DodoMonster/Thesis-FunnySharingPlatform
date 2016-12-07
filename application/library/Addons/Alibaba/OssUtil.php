<?php
namespace Addons\Alibaba;
require_once 'oss/sdk.class.php';
require_once 'oss/util/oss_util.class.php';
class OssUtil {
    const endpoint = OSS_ENDPOINT;
    const accessKeyId = OSS_ACCESS_ID;
    const accesKeySecret = OSS_ACCESS_KEY;
    const bucket = OSS_TEST_BUCKET;

    public static function get_oss_client() {
        $oss = new \ALIOSS(self::accessKeyId, self::accesKeySecret, self::endpoint);
        return $oss;
    }

    public static function my_echo($msg) {
        $new_line = " \n";
        echo $msg . $new_line;
    }
    public static function create_bucket() {
        $oss = self::get_oss_client();
        $bucket = self::get_bucket_name();
        $acl = ALIOSS::OSS_ACL_TYPE_PUBLIC_READ;
        $res = $oss->create_bucket($bucket, $acl);
        $msg = "创建bucket " . $bucket;
        OSSUtil::print_res($res, $msg);
    }
    
    //TODO
    public static function upload_local_pic($file_name,$file_path){
        $bucket = self::bucket;
        $oss = self::get_oss_client();
        $res = $oss->upload_file_by_file($bucket, $file_name,$file_path);
        //$msg = "上传本地文件 :" . $file_path . " 到 /" . $bucket . "/" . $object;
        //OSSUtil::print_res($res, $msg);
    }

    //state:1,base64解码，2不解码
    public static function upload_content_pic($file_name,$content,$state=1){
        $bucket = self::bucket;
        $oss = self::get_oss_client();
        if($state==1){
            $base64_body = substr(strstr($content,','),1);
            $data= base64_decode($base64_body);
        }else{
            $data=$content;
        }

        $upload_file_options = array(  
            'content' => $data,
            'length' => strlen($data)
        ); 

        $res = $oss->upload_file_by_content($bucket, $file_name,$upload_file_options);
        //$msg = "上传本地文件 :" . $file_name . " 到 /" . $bucket . "/";
        return $res;
        //OSSUtil::print_res($res, $msg);
    }
}
