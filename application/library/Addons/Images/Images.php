<?php
namespace Addons\Images;
class Images{
    //private $imgUrl='http://img1.dazhua.net/';
    public function getImageInfo($img) {
        $imageInfo = getimagesize($img);
        if ($imageInfo !== false) {
            //$imageType = strtolower(substr(image_type_to_extension($imageInfo[2]), 1));
            //$imageSize = @filesize($img);
            //$info = array("width" => $imageInfo[0],"height" => $imageInfo[1],"type" => $imageType,"size" => $imageSize,"mime" => $imageInfo['mime']);
            $info = array("width" => $imageInfo[0],"height" => $imageInfo[1]);
            return $info;
        } else {
            return false;
        }
    }
    
    public function getImageInfoDetail($img) {
        $imageInfo = getimagesize($img);
        if ($imageInfo !== false) {
            $imageType = strtolower(substr(image_type_to_extension($imageInfo[2]), 1));
            $imageSize = @filesize($img);
            $info = array("width" => $imageInfo[0],"height" => $imageInfo[1],"type" => $imageType,"size" => $imageSize,"mime" => $imageInfo['mime']);
            return $info;
        } else {
            return false;
        }
    }
    
    //上传阿里云//pic[]
    /*public function uploadAliOss(){
        if(empty($_FILES["pic"]["name"])){
            \Core\BaseErrors::ParamerterError();
        }
        $files=array();
        $allowType=array('jpg','jpeg','gif','png','JPG','JPEG','GIF','PNG');
        $time=time();
        $year=date('Y',$time);
        $month=date('m',$time);
        $day=date('d',$time);
        $tmp = range(10,99);
        $randArray=array_rand($tmp,count($_FILES["pic"]["name"]));
        for ($i = 0; $i < count($_FILES["pic"]["name"]); $i++) {
            if(!empty($_FILES["pic"]["name"][$i])){
                $arr = explode(".", $_FILES["pic"]["name"][$i]);
                $ext = strtolower($arr[count($arr) - 1]);//TODO转小写
                if(in_array($ext, $allowType)){//&& $fileInfo['size']<=304800 
                    $fileinfo['remote_filename']=$year.'/'.$month.'/'.$day.'/'.date('Ymdhis',time()).rand(10000,99999).$randArray[$i].'.'.$ext;
                    $files[]=$fileinfo;
                    \Addons\Alibaba\OssUtil::upload_local_pic($fileinfo['remote_filename'],$_FILES['pic']['tmp_name'][$i]);
                }
            }
        }
        if ($files) {
            $imagesUrl=array();
            foreach ($files as $v){
                $imagesUrl[]=$v['remote_filename'];
            }
            return array('code'=>200,'message'=>'Success','data'=>array('images'=>$imagesUrl));            
        }else{
            return array('code'=>4037,'message'=>'Failed','data'=>new \stdClass());
        }
    */
    
    //上传阿里云//pic[]
    public function uploadImageAliOss($pic){
        if(empty($_FILES[$pic]["name"])){
            \Core\BaseErrors::ParamerterError();
        }
        $files=array();
        $allowType=array('jpg','jpeg','gif','png','JPG','JPEG','GIF','PNG');
        $time=time();
        $year=date('Y',$time);
        $month=date('m',$time);
        $day=date('d',$time);
        $tmp = range(10,99);
        $randArray=array_rand($tmp,count($_FILES[$pic]["name"]));
        for ($i = 0; $i < count($_FILES[$pic]["name"]); $i++) {
            if(!empty($_FILES[$pic]["name"][$i])){
                $arr = explode(".", $_FILES[$pic]["name"][$i]);
                $ext = strtolower($arr[count($arr) - 1]);//TODO转小写
                if(in_array($ext, $allowType)){//&& $fileInfo['size']<=304800 
                    $fileinfo['remote_filename']=$year.'/'.$month.'/'.$day.'/'.date('Ymdhis',time()).rand(10000,99999).$randArray[$i].'.'.$ext;
                    $files[]=$fileinfo;
                    \Addons\Alibaba\OssUtil::upload_local_pic($fileinfo['remote_filename'],$_FILES[$pic]['tmp_name'][$i]);
                }
            }
        }
        if ($files) {
            $imagesUrl=array();
            foreach ($files as $v){
                $imagesUrl[]=$v['remote_filename'];
            }
            return array('code'=>200,'message'=>'Success','data'=>array('images'=>$imagesUrl));            
        }else{
            return array('code'=>4037,'message'=>'Failed','data'=>new \stdClass());
        }
    }

    //上传文件内容到阿里云//pic[]//state:1,base64解码，2不解码
    public function uploadImageContentAliOss($content, $subfolder = '',$state=1){
        
        $files=array();
        $ext = 'jpg';
        $time=time();
        $year=date('Y',$time);
        $month=date('m',$time);
        $day=date('d',$time);
        $tmp = range(10,99);
        $randArray=array_rand($tmp, 5);

        $fileinfo['remote_filename']=$year.'/'.$month.'/'.$day.'/'.$subfolder.date('Ymdhis',time()).rand(10000,99999).'.'.$ext;

        $res = \Addons\Alibaba\OssUtil::upload_content_pic($fileinfo['remote_filename'],$content,$state);

        if ($res->status == '200') {
            return array('code'=>200,'message'=>'Success','data'=>array('images'=>$fileinfo['remote_filename']));            
        }else{
            return array('code'=>4037,'message'=>'Failed','data'=>new \stdClass());
        }
    }
    
    //上传文件
    public function uploadFileByUcloud($pic){
        require_once 'lib/ucloud/proxy.php';
        require_once 'lib/ucloud/conf.php';
        require_once 'lib/ucloud/http.php';
        require_once 'lib/ucloud/digest.php';
        require_once 'lib/ucloud/utils.php';
        require_once 'lib/ucloud/mimetypes.php';
        
        $bucket = "wanyouxi1";
        if(empty($_FILES[$pic]["name"])){
            \Core\BaseErrors::ParamerterError();
        }
        $files=array();
        $allowType=array('jpg','jpeg','gif','png','JPG','JPEG','GIF','PNG');
        $time=time();
        $year=date('Y',$time);
        $month=date('m',$time);
        $day=date('d',$time);
        $tmp = range(10,99);
        $randArray=array_rand($tmp,count($_FILES[$pic]["name"]));
        for ($i = 0; $i < count($_FILES[$pic]["name"]); $i++) {
            if(!empty($_FILES[$pic]["name"][$i])){
                $arr = explode(".", $_FILES[$pic]["name"][$i]);
                $ext = strtolower($arr[count($arr) - 1]);//TODO转小写
                if(in_array($ext, $allowType)){//&& $fileInfo['size']<=304800 
                    $fileinfo['remote_filename']=$year.'/'.$month.'/'.$day.'/'.date('Ymdhis',time()).rand(10000,99999).$randArray[$i].'.'.$ext;
                    $files[]=$fileinfo;
                    //list($data, $err) = \UCloud_PutFile($bucket, $fileinfo['remote_filename'], $_FILES[$pic]['tmp_name'][$i]);
                    list($data, $err) = UCloud_MultipartForm($bucket, $fileinfo['remote_filename'], $_FILES[$pic]['tmp_name'][$i]);
                }
            }
        }
        
        if($err){
            return array('code'=>4037,'message'=>'Failed','data'=>new \stdClass());
        }
        if ($files) {
            $imagesUrl=array();
            foreach ($files as $v){
                $imagesUrl[]=$v['remote_filename'];
            }
            return array('code'=>200,'message'=>'Success','data'=>array('images'=>$imagesUrl));            
        }
    }
    
    //上传文件
    public function remoteFileUploadByUcloud($file){
        require_once 'lib/ucloud/proxy.php';
        require_once 'lib/ucloud/conf.php';
        require_once 'lib/ucloud/http.php';
        require_once 'lib/ucloud/digest.php';
        require_once 'lib/ucloud/utils.php';
        require_once 'lib/ucloud/mimetypes.php';
        $bucket = "wanyouxi1";
        $time=time();
        $year=date('Y',$time);
        $month=date('m',$time);
        $day=date('d',$time);
        $remotefile=$year.'/'.$month.'/'.$day.'/'.date('Ymdhis',time()).rand(10000,99999).'.'.'jpg';
        list($data, $err) = UCloud_PutFile($bucket,$remotefile, $file);
        if ($err) {
        }
        return $remotefile;
    }

    // 大文件上传
    public function uploadFile($file){
        require_once 'lib/ucloud/proxy.php';
        require_once 'lib/ucloud/conf.php';
        require_once 'lib/ucloud/http.php';
        require_once 'lib/ucloud/digest.php';
        require_once 'lib/ucloud/utils.php';
        require_once 'lib/ucloud/mimetypes.php';

        $bucket = "wanyouxi1";
        if(empty($_FILES[$file]["name"])){
            \Core\BaseErrors::ParamerterError();
        }
        $time=time();
        $year=date('Y',$time);
        $month=date('m',$time);
        $day=date('d',$time);
        $arr = explode(".", $_FILES[$file]["name"]);
        $ext = strtolower($arr[count($arr) - 1]);//TODO转小写
        $fileinfo['remote_filename']=$year.'/'.$month.'/'.$day.'/'.date('Ymdhis',time()).rand(10000,99999).'.'.$ext;
        list($data, $err) = UCloud_MultipartForm($bucket, $fileinfo['remote_filename'], $_FILES[$file]['tmp_name']);
        if($err){
            return array('code'=>4037,'message'=>'Failed','data'=>new \stdClass());
        }
        if ($fileinfo) {
            return array('code'=>200,'message'=>'Success','data'=>array('file'=>$fileinfo['remote_filename']));
        }
    }
        
}



