<?php
class StatController extends \Core\BaseControllers {
    
    //统计下载相关//1,click;2,complete;3,install;4,enter
    public function statDownloadAction(){
        $post['game_id']=isset($this->_postData['game_id']) ? $this->_postData['game_id']: (isset($this->_getData['game_id']) ? $this->_getData['game_id']: '');
        $post['channel_id']=isset($this->_postData['channel_id']) ? $this->_postData['channel_id']: (isset($this->_getData['channel_id']) ? $this->_getData['channel_id']: '');
        $post['type']=isset($this->_postData['type']) ? intval($this->_postData['type']): (isset($this->_getData['type']) ? intval($this->_getData['type']): 0);
        $post['is_first']=isset($this->_postData['is_first']) ? $this->_postData['is_first']: (isset($this->_getData['is_first']) ? $this->_getData['is_first']: '');
        $post['device_id']=isset($this->_postData['device_id']) ? $this->_postData['device_id']: (isset($this->_getData['device_id']) ? $this->_getData['device_id']: '');
        $post['idfa']=isset($this->_postData['idfa']) ? $this->_postData['idfa']: (isset($this->_getData['idfa']) ? $this->_getData['idfa']: '');
        $post['imei']=isset($this->_postData['imei']) ? $this->_postData['imei']: (isset($this->_getData['imei']) ? $this->_getData['imei']: '');
        $post['os']=isset($this->_postData['os']) ? $this->_postData['os']: (isset($this->_getData['os']) ? $this->_getData['os']: '');
        $post['http_user_agent']=isset($this->_postData['http_user_agent']) ? $this->_postData['http_user_agent']: (isset($this->_getData['http_user_agent']) ? $this->_getData['http_user_agent']: $this->_httpUserAgent);
        $post['http_referer']=isset($this->_postData['http_referer']) ? $this->_postData['http_referer']: (isset($this->_getData['http_referer']) ? $this->_getData['http_referer']: $this->_httpReferer);
        $post['ip']=isset($this->_postData['ip']) ? $this->_postData['ip']: (isset($this->_getData['ip']) ? $this->_getData['ip']: $this->_realIp);
        $model= new \Log\StatModel();
        $data=$model->statDownload($post);
        $this->returnValue($data);
    }
    
    //ios下载去重
    public function statIosDistinctAction(){
        //appid : 产品id
        //idfa : IDFA：用户标识，需要查询的IDFA拼接的字符串，英文逗号间隔； 
        //http://url?appid=xxxxx&idfa=5A58EF1E-EEF2-478D-94EE-709B98407589,A0A82816-3383-437B-A535-F910162A7097, A0A82816-3383-437B-A535-F910162A7098
        //返回JSON结果：
        //{"5A58EF1E-EEF2-478D-94EE-709B98407589":"1","A0A82816-3383-437B-A535-F910162A7097":"0","A0A82816-3383-437B-A535-F910162A7098":"1"}
        $post['app_id']=isset($this->_postData['app_id']) ? $this->_postData['app_id']:"";
        $post['idfa']=isset($this->_postData['idfa']) ? $this->_postData['idfa']:"";
        $model= new \Log\StatModel();
        $data=$model->statIosDistinct($post);
        echo json_encode($data);
        exit;
    }
    
    //ios上报
    public function statIosReportAction(){
        $post['ip']=isset($this->_postData['ip']) ? $this->_postData['ip']:"";
        $post['idfa']=isset($this->_postData['idfa']) ? $this->_postData['idfa']:"";
        $post['mac']=isset($this->_postData['mac']) ? $this->_postData['mac']:"";
        $post['source']=isset($this->_postData['source']) ? $this->_postData['source']:"";
        $post['app_id']=isset($this->_postData['app_id']) ? $this->_postData['app_id']:"";
        $post['game_id']=11114;
        $model= new \Log\StatModel();
        $data=$model->statIosReport($post);
        echo $data;
        exit;
    }
    
    //广告主上报接口(没有主动回调接口)
    public function statReportAction(){
        $post['game_id']=isset($this->_postData['game_id']) ? $this->_postData['game_id']:(isset($this->_getData['game_id']) ? $this->_getData['game_id']: '');
        $post['channel_id']=isset($this->_postData['channel_id']) ? $this->_postData['channel_id']:(isset($this->_getData['channel_id']) ? $this->_getData['channel_id']: '');
        $post['device_type']=isset($this->_postData['device_type']) ? $this->_postData['device_type']:(isset($this->_getData['device_type']) ? $this->_getData['device_type']: '');//1ios.2android
        //$post['device_id']=isset($this->_postData['device_id']) ? $this->_postData['device_id']:(isset($this->_getData['device_id']) ? $this->_getData['device_id']: '');
        $post['idfa']=isset($this->_postData['idfa']) ? $this->_postData['idfa']:(isset($this->_getData['idfa']) ? $this->_getData['idfa']: '');
        $post['imei']=isset($this->_postData['imei']) ? $this->_postData['imei']: (isset($this->_getData['imei']) ? $this->_getData['imei']: '');
        $post['mac']=isset($this->_postData['mac']) ? $this->_postData['mac']: (isset($this->_getData['mac']) ? $this->_getData['mac']: '');
        $model= new \Log\StatModel();
        $data=$model->statReport($post);
        $this->returnValue($data);
    }
    
    //广告主上报回调接口接口
    public function stateReportCallbackAction(){
        //分哪个广告主的回调
    }
    
    
    //获取用户登录日志(保存到相应目录)
    public function getUserLoginLogAction(){
        $model= new \Log\StatModel();
        $model->getUserLoginLog();
    }
    
    //获取用户支付日志(保存到相应目录)
    public function getUserPayLogAction(){
        $model= new \Log\StatModel();
        $model->getUserPayLog();
    }
    
    //获取用户支付通知日志(保存到相应目录)
    public function getUserPayNotifyLogAction(){
        $model= new \Log\StatModel();
        $model->getUserPayNotifyLog();
    }
    
    //获取用户支付日志(保存到相应目录)
    public function getUserPayLog1Action(){
        $date=isset($this->_postData['date']) ? $this->_postData['date']:(isset($this->_getData['date']) ? $this->_getData['date']: '');
        $model= new \Log\StatModel();
        $model->getUserPayLog1($date);
    }
    
    //获取用户支付通知日志(保存到相应目录)
    public function getUserPayNotifyLog1Action(){
        $date=isset($this->_postData['date']) ? $this->_postData['date']:(isset($this->_getData['date']) ? $this->_getData['date']: '');
        $date = '2016-11-22';
        echo $date;
        $model= new \Log\StatModel();
        $model->getUserPayNotifyLog1($date);
    }

    //定时删除日志表信息
    public function timeToDeleteAction() {
        $model = new \Log\LogModel();
        $model->timeToDelete();
    }
}
