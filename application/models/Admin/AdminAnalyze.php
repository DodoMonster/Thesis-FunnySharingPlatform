<?php
namespace Admin;
class AdminAnalyzeModel extends \Core\BaseModels {
    
    //下载数统计channelid,3android,4ios,5web
    public function analyzeDownload($gameId,$datetime){
        $options['table']='log_game_download';
        $options['where']=['game_id'=>'?'];
        $options['param']=[$gameId];
        $list=$this->db->select($options);
        foreach ($list as $k=>$v){
            $list1[$v['channel_id']]=$list[$k];
        }
        $data['web_download']=$list1;
        $download=[];
        $allday['home']=0;
        $allday['ios']=0;
        $allday['android']=0;
        $allday['web']=0;
        $allday['all']=0;
        $date=!empty($datetime)?$datetime:date("Y-m-d",  time());//
        for ($i=0;$i<=23;$i++){
            $startTime= strtotime($date)+$i*3600;
            $endTime=$startTime+3600;
            //ios
            $options1['table']='log_game_download_detail';
            $options1['field']='id';
            $options1['where']=['game_id'=>'?','channel_id'=>'?','type'=>'?','ctime'=>['BETWEEN',['?','?']]];
            $options1['param']=[$gameId,4,1,$startTime,$endTime];
            $iosDld=$this->db->count($options1);
            //android
            $options1['param']=[$gameId,3,1,$startTime,$endTime];
            $androidDld=$this->db->count($options1);
            //web
            $options1['param']=[$gameId,5,1,$startTime,$endTime];
            $webDld=$this->db->count($options1);
            
            //click
            $options2['table']='log_game_download_pv';
            $options2['field']='id';
            $options2['where']=['game_id'=>'?','channel_id'=>'?','type'=>'?','ctime'=>['BETWEEN',['?','?']]];
            $options2['param']=[$gameId,5,5,$startTime,$endTime];
            $homeId=$this->db->count($options2);
            
            $download[$i]['time']=$i.'-'.($i+1).'点';
            $download[$i]['home']=$homeId;
            $download[$i]['ios']=$iosDld;
            $download[$i]['android']=$androidDld;
            $download[$i]['web']=$webDld;
            $download[$i]['all']=$iosDld+$androidDld+$webDld;
            
            $allday['home']+=$download[$i]['home'];
            $allday['ios']+=$download[$i]['ios'];
            $allday['android']+=$download[$i]['android'];
            $allday['web']+=$download[$i]['web'];
            $allday['all']+=$download[$i]['all'];
        }
        $download['allday']['time']='全天';
        $download['allday']['home']=$allday['home'];
        $download['allday']['ios']=$allday['ios'];
        $download['allday']['android']=$allday['android'];
        $download['allday']['web']=$allday['web'];
        $download['allday']['all']=$allday['all'];
        $data['download_list']=$download;        
        return $this->returnResult(200,$data);
    }
    
    //ios刷榜分析
    public function analyzeIosDownload($gameId,$datetime){
        $oneday=[];
        $allday['report']=0;
        $allday['report_real']=0;
        $allday['real']=0;
        $allday['create']=0;
        $date=!empty($datetime)?$datetime:date("Y-m-d",  time());//
        for ($i=0;$i<=23;$i++){
            $startTime= strtotime($date)+$i*3600;
            $endTime=$startTime+3600;      
            //上报
            $options1['table']='log_ios_report_idfa';
            $options1['field']='idfa';
            $options1['where']=['game_id'=>'?','ctime'=>['BETWEEN',['?','?']]];
            $options1['param']=[$gameId,$startTime,$endTime];
            $sql=$this->db->buildselect($options1);
            $idfas=$this->db->count($options1);
            
            //上报同一时间段打开游戏数
            $options3['table']='log_game_download_detail';
            //$options3['field']='id';
            //$options3['group']='idfa';
            $options3['field']='distinct(idfa)';
            $options3['where']=['channel_id'=>'?','type'=>'?','idfa'=>['IN',$sql],'ctime'=>['BETWEEN',['?','?']]];
            $options3['param']=[4,3,$gameId,$startTime,$endTime,$startTime,$endTime];
            $reportRealIdfa=$this->db->count($options3);
            
            //最终实际打开游戏数
            $options3['table']='log_game_download_detail';
            //$options3['field']='id';
            //$options3['group']='idfa';
            $options3['field']='distinct(idfa)';
            $options3['where']=['channel_id'=>'?','type'=>'?','idfa'=>['IN',$sql]];
            $options3['param']=[4,3,$gameId,$startTime,$endTime];
            $realIdfa=$this->db->count($options3);
            
            //最终实际进入游戏
            $options4['table']='log_game_download_detail';
            $options4['field']='distinct(idfa)';
            $options4['where']=['channel_id'=>'?','type'=>'?','idfa'=>['IN',$sql]];
            $options4['param']=[4,4,$gameId,$startTime,$endTime];
            $createIdfa=$this->db->count($options4);
                                         
            $oneday[$i]['time']=$i.'-'.($i+1).'点';
            $oneday[$i]['report']=$idfas;
            $oneday[$i]['report_real']=$reportRealIdfa;
            $oneday[$i]['real']=$realIdfa;
            $oneday[$i]['create']=$createIdfa;
            
            $allday['report']+=$oneday[$i]['report'];
            $allday['report_real']+=$oneday[$i]['report_real'];
            $allday['real']+=$oneday[$i]['real'];
            $allday['create']+=$oneday[$i]['create'];
        }
        
        $oneday['allday']['time']='全天';
        $oneday['allday']['report']=$allday['report'];
        $oneday['allday']['report_real']=$allday['report_real'];
        $oneday['allday']['real']=$allday['real'];
        $oneday['allday']['create']=$allday['create'];
        $data['oneday_list']=$oneday;//一天列表数
        
       
        $options8['table']='log_ios_report_idfa';
        $options8['field']='idfa';
        $options8['where']=['cdate'=>'?'];
        $options8['param']=[$date];
        $sql8=$this->db->buildselect($options8);
        
         //激活总数
        $options5['table']='log_game_download_detail';
        $options5['field']='distinct(idfa)';
        $options5['where']=['channel_id'=>'?','type'=>'?','cdate'=>'?'];
        $options5['param']=[4,3,$date];
        $allinnum=$this->db->count($options5);
        
        //刷榜激活总数
        $options6['table']='log_game_download_detail';
        $options6['field']='distinct(idfa)';
        $options6['where']=['idfa'=>['IN',$sql8],'cdate'=>'?','channel_id'=>'?','type'=>'?'];
        $options6['param']=[$date,$date,4,3];
        $reportinnum=$this->db->count($options6);
        
        //创角总数
        $options7['table']='log_game_download_detail';
        $options7['field']='distinct(idfa)';
        $options7['where']=['channel_id'=>'?','type'=>'?','cdate'=>'?'];
        $options7['param']=[4,4,$date];
        $allnum=$this->db->count($options7);
        
        //刷榜创角总数
        $options9['table']='log_game_download_detail';
        $options9['field']='distinct(idfa)';
        $options9['where']=['idfa'=>['IN',$sql8],'cdate'=>'?','channel_id'=>'?','type'=>'?'];
        $options9['param']=[$date,$date,4,4];
        $reportnum=$this->db->count($options9);
        
        $data['download']['allin']=$allinnum;
        $data['download']['reportin']=$reportinnum;
        $data['download']['all']=$allnum;
        $data['download']['report']=$reportnum;
        return $this->returnResult(200,$data);
    }
    
    
}

