<?php
namespace Admin;
class AdminIndexModel extends \Core\BaseModels {
    
    //新建游戏表
    private function createGameTable($tableName){
        $sql="CREATE TABLE `$tableName` (
            `id`  int(11) NOT NULL AUTO_INCREMENT ,
            `uid`  bigint(20) NOT NULL ,
            `channel_id`  int(11) NOT NULL ,
            `device_id`  varchar(100) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL ,
            `game_id`  int(11) NOT NULL ,
            `game_uid`  varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL ,
            `game_uname`  varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL ,
            `game_server`  varchar(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci NOT NULL ,
            `addtime`  int(11) NOT NULL ,
            `adddate`  date NOT NULL ,
            `state`  tinyint(4) NOT NULL ,
            PRIMARY KEY (`id`),
            UNIQUE INDEX `uid_gameuid` (`uid`, `game_uid`) USING BTREE ,
            INDEX `addtime` (`addtime`, `channel_id`) USING BTREE ,
            INDEX `channel_id` (`channel_id`) USING BTREE ,
            INDEX `device_id` (`device_id`) USING BTREE ,
            INDEX `adddate` (`adddate`, `channel_id`) USING BTREE 
            )
        ENGINE=InnoDB
        DEFAULT CHARACTER SET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        AUTO_INCREMENT=1
        ROW_FORMAT=COMPACT;";
        return $sql;    
    }
    
    //用户列表
    public function userList($param,$count,$page){
        $options['table']='user as A';
        $options['join']=array('user_cellphone as B on A.uid=B.uid');
        $options['field']='A.uid,A.uname,A.channel_id,A.ip,A.device_id,A.addtime,B.cellphone,A.privilege,A.state';
        $arr=array(0);
        $options['where']=array('A.privilege'=>array('IN',$arr));
        $options['param']=$arr;
        if($param['uid']){
            $options['where'] = array_merge($options['where'],array('A.uid'=>'?'));
            $options['param'] = array_merge($options['param'],array($param['uid']));
        }
        if($param['uname']){
            $options['where'] = array_merge($options['where'],array('A.uname'=>array('LIKE','?')));
            $options['param'] = array_merge($options['param'],array("%{$param['uname']}%"));
        }
        if($param['cellphone']){
            $options['where'] = array_merge($options['where'],array('B.cellphone'=>'?'));
            $options['param'] = array_merge($options['param'],array($param['cellphone']));
        }
        $totalNum=$this->db->count($options);
        $totalPage=ceil($totalNum/$count);
        $options['limit']=($page-1)*$count.','.$count;
        $options['order']='A.uid DESC';
        $list=$this->db->select($options);
        //print_r($list);exit;
        if(!empty($list)){
            foreach($list as $k=>$v){
                $list[$k]['addtime'] = date('Y-m-d H:i:s',$v['addtime']);
                $list[$k]['privilege'] = $v['privilege']==0?'普通':($v['privilege']==2?'游戏商户':($v['privilege']>=6?'管理员':'其他'));
                $list[$k]['state'] = $v['state']==0?'正常':'禁封';
            }
            $data=array('totalPage'=>$totalPage,'count'=>count($list),'page'=>$page,'list'=>$list);
            return $this->returnResult(200,$data);
        }else{
            return $this->returnResult(201);
        }
    }

    //游戏商户列表
    public function gameMerchantList($param,$count,$page){
        $options['table']='user as A';
        $options['join']=array('user_cellphone as B on A.uid=B.uid');
        $options['field']='A.uid,A.uname,A.channel_id,A.ip,A.device_id,A.addtime,B.cellphone,A.privilege,A.state';
        $arr=array(2);
        $options['where']=array('A.privilege'=>array('IN',$arr));
        $options['param']=$arr;
        if($param['uid']){
            $options['where'] = array_merge($options['where'],array('A.uid'=>'?'));
            $options['param'] = array_merge($options['param'],array($param['uid']));
        }
        if($param['uname']){
            $options['where'] = array_merge($options['where'],array('A.uname'=>array('LIKE','?')));
            $options['param'] = array_merge($options['param'],array("%{$param['uname']}%"));
        }
        if($param['cellphone']){
            $options['where'] = array_merge($options['where'],array('B.cellphone'=>'?'));
            $options['param'] = array_merge($options['param'],array($param['cellphone']));
        }
        $totalNum=$this->db->count($options);
        $totalPage=ceil($totalNum/$count);
        $options['limit']=($page-1)*$count.','.$count;
        $options['order']='A.uid DESC';
        $list=$this->db->select($options);
        //print_r($list);exit;
        if(!empty($list)){
            foreach($list as $k=>$v){
                $list[$k]['addtime'] = date('Y-m-d H:i:s',$v['addtime']);
                $list[$k]['privilege'] = $v['privilege']==0?'普通':($v['privilege']==2?'游戏商户':($v['privilege']>=6?'管理员':'其他'));
                $list[$k]['state'] = $v['state']==0?'正常':'禁封';
            }
            $data=array('totalPage'=>$totalPage,'count'=>count($list),'page'=>$page,'list'=>$list);
            return $this->returnResult(200,$data);
        }else{
            return $this->returnResult(201);
        }
    }
    
    //管理员列表
    public function adminList($count,$page){
        $options['table']='user as A';
        $options['join']=array('user_cellphone as B on A.uid=B.uid','sys_user_role as C on A.privilege=C.role_id');
        $options['field']='A.uid,A.uname,B.cellphone,C.role_id,C.role_name,A.addtime,A.state,C.menus,C.products,C.channels,C.columns';
        $options['where']=array('A.privilege'=>array('EGT','?'),'C.role_id'=>array('NEQ','?'));
        $options['param']=array(6,'');
        $totalNum=$this->db->count($options);
        $totalPage=ceil($totalNum/$count);
        $options['limit']=($page-1)*$count.','.$count;
        $list=$this->db->select($options);
        if(!empty($list)){
            foreach($list as $k=>$v){
                $list[$k]['addtime'] = date('Y-m-d H:i:s',$v['addtime']);
                $list[$k]['state'] = $v['state']==0?'正常':'禁封';
            }
            $data=array('totalPage'=>$totalPage,'count'=>count($list),'page'=>$page,'list'=>$list);
            return $this->returnResult(200,$data);
        }else{
            return $this->returnResult(201);
        }
    }
    
     //管理员角色列表
    public function adminRoleList($count,$page){
        $options['table']='sys_user_role';
        $totalNum=$this->db->count($options);
        $totalPage=ceil($totalNum/$count);
        $options['limit']=($page-1)*$count.','.$count;
        $list=$this->db->select($options);
        if(!empty($list)){
            $data=array('totalPage'=>$totalPage,'count'=>count($list),'page'=>$page,'list'=>$list);
            return $this->returnResult(200,$data);
        }else{
            return $this->returnResult(201);
        }
    }

    // 用户编辑操作
    public function opUserEdit($param){
        if(isset($param['state'])){
            $tmpData = array('state'=>'?');
            $options['table'] = 'user';
            $options['where'] = array('uid'=>'?');
            $options['param'] = array($param['state'],$param['uid']);
        }
        $uid = $this->db->save($tmpData,$options);
        if($uid!=FALSE){
            return $this->returnResult(200);
        }else {
            return $this->returnResult(4000);
        }
    }

    // 普通用户重置密码操作
    public function opUserResetPwd($param){
        $priArr = array(0);
        $options['table'] = 'user';
        $options['where'] = array('uid'=>'?');
        $options['param'] = array($param['uid']);
        $data = $this->db->find($options);
        if(empty($data)){
            return $this->returnResult(201);
        }else if(!in_array($data['privilege'],$priArr)){
            return $this->returnResult(202);
        }else if($data['password']==md5($param['pwd'])){
            return $this->returnResult(200);
        }
        $tmpData = array('password'=>'?');
        $options1['table'] = 'user';
        $options1['where'] = array('uid'=>'?');
        $options1['param'] = array(md5($param['pwd']),$param['uid']);
        $uid = $this->db->save($tmpData,$options1);
        if($uid!=FALSE){
            return $this->returnResult(200);
        }else {
            return $this->returnResult(4000);
        }
    }

    // 管理员修改密码操作
    public function opAdminResetPwd($param){
        $options['table'] = 'user';
        $options['where'] = array('uid'=>'?','password'=>'?');
        $options['param'] = array($param['uid'],md5($param['old_pwd']));
        $data = $this->db->find($options);
        if(empty($data)){
            return $this->returnResult(201);
        }
        $tmpData = array('password'=>'?');
        $options1['table'] = 'user';
        $options1['where'] = array('uid'=>'?');
        $options1['param'] = array(md5($param['new_pwd']),$param['uid']);
        $uid = $this->db->save($tmpData,$options1);
        if($uid!=FALSE){
            return $this->returnResult(200);
        }else {
            return $this->returnResult(4000);
        }
    }

    // 游戏列表
    public function gameList($productIds,$count,$page=1){
        $options['table'] = 'game as A';
        $options['field'] = 'A.game_id,A.game_name,A.game_table_name,A.package_name,A.pay_redirect_uri,A.home_uri,A.bbs_uri,A.remark,A.addtime,B.*';
        $options['join'] = 'oauth2_clients as B on A.client_id=B.client_id';
        if(!empty($productIds)){
            $options['where'] = array('A.game_id'=>array('IN',$productIds));
            $options['param'] = $productIds;
        }
        $totalNum = $this->db->count($options);
        $totalPage = ceil($totalNum/$count);
        $options['limit'] = ($page-1)*$count.','.$count;
        $list = $this->db->select($options);
        //print_r($list);exit;
        if(!empty($list)){
            foreach($list as $k=>$v){
                $list[$k]['addtime'] = date('Y-m-d',$v['addtime']);
            }
            $data=array('totalPage'=>$totalPage,'count'=>count($list),'page'=>$page,'list'=>$list);
            return $this->returnResult(200,$data);
        }else{
            return $this->returnResult(201);
        }
    }

    // 游戏创建操作
    public function opGameAdd($param){
        $param['table_name']='game_user_'.$param['table_name'];
        $options['table'] = 'game';
        $options['where']= array('_logic'=>'or','game_name'=>'?','game_table_name'=>'?');
        $options['param']= array($param['game_name'],$param['table_name']);
        $game = $this->db->find($options);
        if(!empty($game)){
            return $this->returnResult(201);
        }
        $uname=time()+mt_rand(10000000,99999999)+1000000000;
        $password=md5(rand(10000001,99999999));
        $clientSecret=md5(base64_encode(pack('N5',mt_rand(),mt_rand(), mt_rand(), mt_rand(), uniqid())));
        $tmpData1=array('uname'=>'?','password'=>'?','privilege'=>'?','addtime'=>'?');
        $options1=array('table'=>'user','param'=>array($uname,$password,2,time()));
        $this->db->startTrans();
        $clientId=$this->db->add($tmpData1,$options1);
        $tmpData2=array('client_id'=>'?','client_secret'=>'?','redirect_uri'=>'?');
        $options2=array('table'=>'oauth2_clients','param'=>array($clientId,$clientSecret,'http://test.wanyouxi.com'));
        $status2=$this->db->add($tmpData2,$options2);     
        $tmpData3 = array('game_name'=>'?','game_table_name'=>'?','package_name'=>'?','client_id'=>'?','pay_redirect_uri'=>'?','home_uri'=>'?','bbs_uri'=>'?','remark'=>'?','addtime'=>'?');
        $options3['table'] = 'game';
        $options3['param'] = array($param['game_name'],$param['table_name'],$param['package_name'],$clientId,$param['redirect_uri'],$param['home_uri'],$param['bbs_uri'],$param['remark'],time());
        $status3 = $this->db->add($tmpData3,$options3);
        $status4 = $this->db->create($this->createGameTable($param['table_name']));
        if($clientId!==FALSE && $status2!==FALSE && $status3!==FALSE && $status4!==FALSE){
            $this->db->commit();
            return $this->returnResult(200);
        }else {
            $this->db->rollback();
            return $this->returnResult(4000);
        }        
    }

    // 游戏修改操作
    public function opGameEdit($param){
        $options['table'] = 'game';
        $options['where'] = array('_logic'=>'and','game_id'=>array('neq','?'),'game_name'=>'?');
        $options['param'] = array($param['game_id'],$param['game_name']);
        $game = $this->db->find($options);
        if(!empty($game)){
            return $this->returnResult(201);
        }else {
            $tmpData1 = array('game_name'=>'?','game_table_name'=>'?','package_name'=>'?','pay_redirect_uri'=>'?','home_uri'=>'?','bbs_uri'=>'?','remark'=>'?');
            $options1['table'] = 'game';
            $options1['where'] = array('game_id'=>'?');
            $options1['param'] = array($param['game_name'],$param['table_name'],$param['package_name'],$param['redirect_uri'],$param['home_uri'],$param['bbs_uri'],$param['remark'],$param['game_id']);
            $gid = $this->db->save($tmpData1,$options1);
            if($gid!=FALSE){
                return $this->returnResult(200);
            }else {
                return $this->returnResult(4000);
            }
        }
    }

    // 渠道列表
    public function channelList($channelIds,$param,$count,$page){
        $options['table'] = 'user_channel as A';
        $options['where'] = array();
        $options['param'] = array();
        if(!empty($channelIds)){
            $options['where'] = array('A.channel_id'=>array('IN',$channelIds));
            $options['param'] = $channelIds;
        }
        if(!empty($param['man'])){
            $options['where'] = array_merge($options['where'],array('A.responsible_man'=>'?'));
            $options['param'] = array_merge($options['param'],array($param['man']));
        }
        if(!empty($param['game_id'])){
            $options['where'] = array_merge($options['where'],array('A.game_id'=>'?'));
            $options['param'] = array_merge($options['param'],array($param['game_id']));
        }
        $totalNum = $this->db->count($options);
        $totalPage = ceil($totalNum/$count);
        $options['join'] = ['game as B on B.game_id=A.game_id'];
        $options['field'] = 'A.channel_id,A.channel_name,A.game_id,B.game_name,B.package_name,A.responsible_man,A.channel_package_name,A.download_url,A.addtime';
        $options['order'] = 'channel_id DESC';
        $options['limit'] = ($page-1)*$count.','.$count;
        $list = $this->db->select($options);
        if(!empty($list)){
            foreach($list as $k=>$v){
                $list[$k]['addtime'] = date('Y-m-d',$v['addtime']);
            }
            $data=array('totalPage'=>$totalPage,'count'=>count($list),'page'=>$page,'list'=>$list);
            return $this->returnResult(200,$data);
        }else{
            return $this->returnResult(201);
        }
    }

    // 渠道添加操作
    public function opChannelAdd($param){
        if(empty($param['game_id'])){
            return $this->returnResult(203);
        }
        $options['table'] = 'user_channel';
        $options['where'] = array('game_id'=>'?','channel_name'=>'?');
        $options['param'] = array($param['game_id'],$param['channel_name']);
        $channel = $this->db->find($options);
        //print_r($channel);exit;
        if(!empty($channel)){
            return $this->returnResult(201);
        }
        $options1['table'] = 'game';
        $options1['field'] = 'game_id,package_name';
        $options1['where'] = array('game_id'=>'?');
        $options1['param'] = array($param['game_id']);
        $detail = $this->db->find($options1);
        $tmpData2 = array('channel_name'=>'?','package_name'=>'?','game_id'=>'?','responsible_man'=>'?','channel_package_name'=>'?','download_url'=>'?','addtime'=>'?');
        $options2['table'] = 'user_channel';
        $options2['param'] = array($param['channel_name'],$detail['package_name'],$detail['game_id'],$param['responsible_man'],$param['channel_package_name'],$param['download_url'],time());
        $status = $this->db->add($tmpData2,$options2);
        if($status!==FALSE){
            return $this->returnResult(200);
        }else {
            return $this->returnResult(4000);
        }
    }

    // 渠道查询
    public function getChannel($channelIds,$channelId='',$gameId=''){
        $options['table']='user_channel';
        $options['order']='channel_id';
        $options['where'] = array();
        $options['param'] = array();
        if(!empty($channelIds)){
            $options['where'] = array('channel_id'=>array('IN',$channelIds));
            $options['param'] = $channelIds;
        }
        if(!empty($channelId)){
            $options['where'] = array_merge($options['where'],array('channel_id'=>'?'));
            $options['param'] = array_merge($options['param'],array($channelId));
        }else if(!empty($gameId)){
            $options['where'] = array_merge($options['where'],array('game_id'=>'?'));
            $options['param'] = array_merge($options['param'],array($gameId));
        }
        $list=$this->db->select($options);
        if(!empty($list)){
            foreach($list as $k=>$v){
                $list[$k]['addtime'] = date('Y-m-d',$v['addtime']);
            }
            $data = array('list'=>$list);
            return $this->returnResult(200,$data);
        }else {
            return $this->returnResult(201);
        }
    }

    // 渠道编辑操作
    public function opChannelEdit($param){
        $options1['table'] = 'user_channel';
        // 检测，同一款游戏下渠道名称、渠道包名和下载地址不相同
        $options1['where'] = "channel_id!='{$param['channel_id']}' and game_id='{$param['game_id']}' and (channel_name='{$param['channel_name']}' or channel_package_name='{$param['channel_package_name']}' or download_url='{$param['download_url']}')";
        $channel = $this->db->find($options1);
        //print_r($channel);exit;
        if(!empty($channel)){
            return $this->returnResult(201);
        }else {
            $options2['table'] = 'game';
            $options2['field'] = 'game_id,package_name';
            $options2['where'] = array('game_id'=>'?');
            $options2['param'] = array($param['game_id']);
            $detail = $this->db->find($options2);

            $tmpData3 = array('channel_name'=>'?','package_name'=>'?','game_id'=>'?','responsible_man'=>'?','channel_package_name'=>'?','download_url'=>'?');
            $options3['table'] = 'user_channel';
            $options3['where'] = array('channel_id'=>'?');
            $options3['param'] = array($param['channel_name'],$detail['package_name'],$detail['game_id'],$param['responsible_man'],$param['channel_package_name'],$param['download_url'],$param['channel_id']);
            $gid = $this->db->save($tmpData3,$options3);
            if($gid!=FALSE){
                return $this->returnResult(200);
            }else {
                return $this->returnResult(4000);
            }
        }
    }

    // 返回改游戏下有权限的渠道id
    public function returnRoleGameChannelIds($gameId,$channelIds){
        $channel = $this->getChannel('','',$gameId);
        $channels = $channel['data']['list'];
        $cids = array();
        if(!empty($channels)){
            if(!empty($channelIds)){
                foreach($channels as $k=>$v){
                    if(in_array($v['channel_id'],$channelIds)){
                        $cids[] = $v['channel_id'];             // 该游戏下有权限的渠道id
                    }
                }
            }else {
                foreach($channels as $k=>$v){
                    $cids[] = $v['channel_id'];
                }
            }
        }
        return $cids;
    }
    
    protected function getdayReportListRedis($redisKey){
        $result=$this->nosql->get($redisKey);
        return json_decode($result,true);
    }
    
    protected function setdayReportListRedis($redisKey,$result){
        $result=json_encode($result);
        $this->nosql->set($redisKey,$result,300);
    }

    // 当日报表，修改后
    public function dayReportList($gameInfo,$channelIds,$columnIds,$param,$count,$page){
        if(empty($gameInfo['game_id'])){
            return $this->returnResult(201);
        }
        $time = $param['end'] ? strtotime($param['end'].' 00:00:00'):strtotime(date('Y-m-d',time()));           // 结束日期0时0分0秒时间戳，值为空，日期默认当天（08-25有数据）
        $startTime = $param['start'] ? strtotime($param['start'].' 00:00:00'):strtotime(date('Y-m-d',time())."-19day");       // 开始日期0时0分0秒时间戳，值为空，日期默认20天
        $channelId = $param['channel_id'];
        $game=$gameInfo;
        $report = [];
        
        $cids = $this->returnRoleGameChannelIds($game['game_id'],$channelIds);
        if(!empty($cids)){
            $nowChannelId=implode('_', $cids);
            if($channelId==''){
                $redisKey=$game['game_id'].'_'.$startTime.'_'.$time.'_'.$nowChannelId.'_'.$page;
            }else{
                $redisKey=$game['game_id'].'_'.$startTime.'_'.$time.'_'.$channelId.'_'.$page;
            }
            $result=$this->getdayReportListRedis($redisKey);
            if(!empty($result)){
                return $this->returnResult(200,$result);
            }
            $channelName = '所有';
            if(!empty($channelId)){
                $channel = $this->getChannel('',$channelId);
                $channelName = $channel['data']['list'][0]['channel_name'];
            }          
                  
            while($time>=$startTime){
                $date = date('Y-m-d',$time);
                $data=array('newAccountNum'=>0,'newDeviceNum'=>0,'activeAccountNum'=>0,'newAccountRoleNum'=>0,'totalAccountRoleNum'=>0,'newAccountPayNum'=>0,'totalAccountPayNum'=>0,'newAccountSumPayMoney'=>0,'totalAccountPayMoney'=>0,'newAccountPayRate'=>0,'activePayRate'=>0,'payARPU'=>0,'activeARPU'=>0,'registerARPU'=>0,);
                $options1 = array();
                $options1['table'] = $game['game_table_name'];
                $options1['field'] ='uid';
                $options1['where'] = array('adddate'=>'?');
                $options1['param'] = array($date);
                if($channelId){
                    $options1['where'] = array_merge($options1['where'],array('channel_id'=>'?'));
                    $options1['param'] = array_merge($options1['param'],array($channelId));
                }else{                   // 没有进行渠道搜索
                    $options1['where'] = array_merge($options1['where'],array('channel_id'=>array('IN',$cids)));
                    $options1['param'] = array_merge($options1['param'],$cids);
                }
                //print_r($cids);exit;
                $sql1 = $this->db->buildSelect($options1);      // 新用户id查询
                $data['newAccountNum'] = $this->db->count($options1);   // 新增用户数

                if($data['newAccountNum']!=0){                    //当天存在新增用户数
                    $options1['field'] = 'distinct(device_id)';
                    $data['newDeviceNum'] = $this->db->count($options1);        // 新增设备数

                    $options3['table'] = 'log_user_pay';
                    $options3['field'] ='sum(total_fee) as sum';
                    $options3['where'] = array('game_id'=>'?','cdate'=>'?','uid'=>array('IN',$sql1));
                    $options3['param'] = array_merge(array($game['game_id'],$date),$options1['param']);
                    $newAccountPay = $this->db->select($options3);
                    //print_r($newAccountPay);exit;
                    $data['newAccountSumPayMoney'] = $newAccountPay[0]['sum']?$newAccountPay[0]['sum']:0;       // 新用户充值额

                    $options3['field'] = 'distinct(uid)';
                    $data['newAccountPayNum'] = $this->db->count($options3);    // 新用户付费数

                    if($data['newDeviceNum']){
                        $registerARPU = bcdiv($data['newAccountSumPayMoney'],$data['newDeviceNum'],4);      // 注册ARPU值
                        if($registerARPU!=='0.0000'){
                            //$data['registerARPU'] = ($registerARPU*100).'%';
                            $data['registerARPU'] = $registerARPU;
                        }
                    }
                    //echo $data['registerARPU'];exit;
                    if($data['newAccountNum']){
                        $newAccountPayRate = bcdiv($data['newAccountPayNum'],$data['newAccountNum'],4);       // 新用户付费率
                        if($newAccountPayRate!=='0.0000'){
                            $data['newAccountPayRate'] = ($newAccountPayRate*100).'%';
                        }
                    }
                    //新用户创角数
                    $options5['table'] = $game['game_table_name'].'_role';
                    $options5['field'] = 'id';
                    $options5['where'] = ['uid'=>['IN',$sql1]];
                    $options5['param'] = $options1['param'];
                    $newAccountRoleNum=$this->db->count($options5);
                    $data['newAccountRoleNum']=$newAccountRoleNum;
                }

                $options7['table'] = $game['game_table_name'];
                $options7['field'] = 'uid';
                if($channelId){
                    $options7['where'] = array('channel_id'=>'?');
                    $options7['param'] = array($channelId);
                }else{
                    $options7['where'] = array('channel_id'=>array('IN',$cids));
                    $options7['param'] = $cids;
                }
                $sql7 = $this->db->buildSelect($options7);      // 渠道对应的用户总数
                
                //总用户创角色
                $options6['table'] = $game['game_table_name'].'_role';
                $options6['field'] = 'id';
                $options6['where'] = ['uid'=>['IN',$sql7],'cdate'=>'?'];
                $options6['param'] = array_merge($options7['param'],[$date]);
                $totalAccountRoleNum=$this->db->count($options6);
                $data['totalAccountRoleNum']=$totalAccountRoleNum;

                $options2['table'] = 'log_user_login';
                $options2['field'] = 'distinct(uid)';
                $options2['where'] = array('game_id'=>'?','login_date'=>'?','uid'=>array('IN',$sql7));
                $options2['param'] = array_merge(array($game['game_id'],$date),$options7['param']);
                $data['activeAccountNum'] = $this->db->count($options2);    // 活跃用户数

                $options4 = array();
                $options4['table'] = 'log_user_pay';
                $options4['field'] = 'sum(total_fee) as sum';
                $options4['where'] = array('game_id'=>'?','cdate'=>'?','uid'=>array('IN',$sql7));
                $options4['param'] = array_merge(array($game['game_id'],$date),$options7['param']);
                $totalAccountPay = $this->db->select($options4);
                $data['totalAccountPayMoney'] = $totalAccountPay[0]['sum']?$totalAccountPay[0]['sum']:0;// 总支付额

                $options4['field'] = 'distinct(uid)';
                $data['totalAccountPayNum'] = $this->db->count($options4);// 总付费用户数

                if($data['activeAccountNum']){
                    $activePayRate = bcdiv($data['totalAccountPayNum'],$data['activeAccountNum'],4);// 活跃付费率
                    if($activePayRate!=='0.0000'){
                        $data['activePayRate'] = ($activePayRate*100).'%';
                    }
                }
                if($data['totalAccountPayNum']){
                    $payARPU = bcdiv($data['totalAccountPayMoney'],$data['totalAccountPayNum'],4);// 付费ARPU值
                    if($payARPU!=='0.0000'){
                        //$data['payARPU'] = ($payARPU*100).'%';
                        $data['payARPU'] = $payARPU;
                    }
                }
                if($data['activeAccountNum']){
                    $activeARPU = bcdiv($data['totalAccountPayMoney'],$data['activeAccountNum'],4);// 活跃ARPU值
                    if($activeARPU!=='0.0000'){
                        //$data['activeARPU'] = ($activeARPU*100).'%';
                        $data['activeARPU'] = $activeARPU;
                    }
                }
                foreach ($data as $col=>$val){
                    if(!empty($columnIds) && !in_array($col,$columnIds)){
                        unset($data[$col]);
                    }
                }
                $data=array_merge(array('date'=>$date,'channel'=>$channelName),$data);
                $report[] = $data;
                $time -= 86400;
            }
        }
        //print_r($report);exit;
        $totalNum = count($report);
        $totalPage = ceil($totalNum/$count);
        $list = array_slice($report,($page-1)*$count,$count);
        $result=['totalPage'=>$totalPage,'count'=>count($list),'page'=>$page,'list'=>$list];
        $this->setdayReportListRedis($redisKey, $result);
        return $this->returnResult(200,$result);
    }

    // 游戏用户列表
    public function gameUserList($gameInfo,$channelIds,$param,$count,$page){
        $gameId = $gameInfo['game_id'];
        $startTime = $param['start'] ? strtotime($param['start'].' 00:00:00'):'';       // 开始日期0时0分0秒时间戳
        $endTime = $param['end'] ? strtotime($param['end'].' 23:59:59'):'';             // 结束日期23时59分59秒时间戳
        $uid = $param['uid'];
        $uname = $param['uname'];
        $channelId = $param['channel_id'];
        $cids = $this->returnRoleGameChannelIds($gameId,$channelIds);
        if(!empty($cids)){
            $options['table'] = "{$gameInfo['game_table_name']} as A";
            $options['join']=array('user as B on A.uid=B.uid','user_channel as C on A.channel_id=C.channel_id');
            $options['field']='A.uid,B.uname,C.channel_name,A.game_uid,A.game_uname,A.game_server,A.addtime,B.last_logintime';
            $options['limit']=($page-1)*$count.','.$count;
            $options['where'] = array();
            $options['param'] = array();
            if(!empty($uid)){
                $options['where'] = array_merge($options['where'],array('A.uid'=>'?'));
                $options['param'] = array_merge($options['param'],array($uid));
            }
            if(!empty($channelId)){     // 进行了渠道搜索
                $options['where'] = array_merge($options['where'],array('A.channel_id'=>'?'));
                $options['param'] = array_merge($options['param'],array($channelId));
            }else {
                $options['where'] = array_merge($options['where'],array('A.channel_id'=>array('IN',$cids)));
                $options['param'] = array_merge($options['param'],$cids);
            }
            if(!empty($startTime) && empty($endTime)){
                $options['where'] = array_merge($options['where'],array('A.addtime'=>array('GT','?')));
                $options['param'] = array_merge($options['param'],array($startTime));
            }
            if(!empty($endTime) && empty($startTime)){
                $options['where'] = array_merge($options['where'],array('A.addtime'=>array('LT','?')));
                $options['param'] = array_merge($options['param'],array($endTime));
            }
            if(!empty($startTime) && !empty($endTime)){
                $options['where'] = array_merge($options['where'],array('A.addtime'=>array('BETWEEN',array('?','?'))));
                $options['param'] = array_merge($options['param'],array($startTime,$endTime));
            }
            if(!empty($uname)){
                $options['where'] = array_merge($options['where'],array('B.uname'=>array('LIKE','?')));
                $options['param'] = array_merge($options['param'],array("%$uname%"));
            }
            $totalNum = $this->db->count($options);
            $options['order'] = 'A.addtime DESC';
            $totalPage = ceil($totalNum/$count);
            $users = $this->db->select($options);

            if(!empty($users)){
                foreach($users as $k=>$v){
                    $options1['table'] = 'log_user_pay';
                    $options1['where'] = array('uid'=>'?','game_id'=>'?');
                    $options1['param'] = array($v['uid'],$gameId);
                    $payData = $this->db->select($options1);
                    $users[$k]['pay_times'] = count($payData);        // 付费数
                    //echo $users[$k]['pay_times'];exit;
                    $users[$k]['pay_total_amount'] = 0;             // 付费额
                    if(!empty($payData)){
                        foreach($payData as $ki=>$vi){
                            $users[$k]['pay_total_amount'] += $vi['total_fee'];
                        }
                    }
                    $users[$k]['addtime'] = date('Y-m-d H:i:s',$users[$k]['addtime']);
                    $users[$k]['last_logintime'] = date('Y-m-d H:i:s',$users[$k]['last_logintime']);
                }
                $data=array('totalPage'=>$totalPage,'count'=>count($users),'page'=>$page,'list'=>$users);
                return $this->returnResult(200,$data);
            }else {
                return $this->returnResult(201);
            }
        }else {
            return $this->returnResult(201);
        }
    }

    // 游戏登录列表
    public function gameLoginList($gameInfo,$channelIds,$param,$count,$page){
        $startTime = $param['start'] ? strtotime($param['start'].' 00:00:00'):'';       // 开始日期0时0分0秒时间戳
        $endTime = $param['end'] ? strtotime($param['end'].' 23:59:59'):'';             // 结束日期23时59分59秒时间戳
        $uid = $param['uid'];
        $uname = $param['uname'];
        $channelId = $param['channel_id'];
        $cids = $this->returnRoleGameChannelIds($gameInfo['game_id'],$channelIds);
        if(!empty($cids)){
            $options['table'] = "log_user_login as A";
            $options['join']=array("{$gameInfo['game_table_name']} as B on A.uid=B.uid",'game as C on A.game_id=C.game_id','user_channel as D on A.channel_id=D.channel_id');
            $options['field']='A.uid,A.uname,D.channel_name,C.game_name,A.ip,A.device_id,A.login_time';
            $options['limit']=($page-1)*$count.','.$count;
            $options['order']='login_time DESC';
            $options['where'] = array();
            $options['param'] = array();
            if(!empty($uid)){
                $options['where'] = array_merge($options['where'],array('A.uid'=>'?'));
                $options['param'] = array_merge($options['param'],array($uid));
            }
            if(!empty($channelId)){         // 进行了渠道搜索
                $options['where'] = array_merge($options['where'],array('A.channel_id'=>'?'));
                $options['param'] = array_merge($options['param'],array($channelId));
            }else {
                $options['where'] = array_merge($options['where'],array('A.channel_id'=>array('IN',$cids)));
                $options['param'] = array_merge($options['param'],$cids);
            }
            if(!empty($startTime) && empty($endTime)){
                $options['where'] = array_merge($options['where'],array('A.login_time'=>array('GT','?')));
                $options['param'] = array_merge($options['param'],array($startTime));
            }
            if(!empty($endTime) && empty($startTime)){
                $options['where'] = array_merge($options['where'],array('A.login_time'=>array('LT','?')));
                $options['param'] = array_merge($options['param'],array($endTime));
            }
            if(!empty($startTime) && !empty($endTime)){
                $options['where'] = array_merge($options['where'],array('A.login_time'=>array('BETWEEN',array('?','?'))));
                $options['param'] = array_merge($options['param'],array($startTime,$endTime));
            }
            if(!empty($uname)){
                $options['where'] = array_merge($options['where'],array('A.uname'=>array('LIKE','?')));
                $options['param'] = array_merge($options['param'],array("%$uname%"));
            }
            $totalNum=$this->db->count($options);
            $totalPage=ceil($totalNum/$count);
            $res = $this->db->select($options);
            //print_r($res);exit;
            if(!empty($res)){
                foreach($res as $k=>$v){
                    $res[$k]['login_time'] = date('Y-m-d H:i:s',$v['login_time']);
                }
                $data=array('totalPage'=>$totalPage,'count'=>count($res),'page'=>$page,'list'=>$res);
                return $this->returnResult(200,$data);
            }else {
                return $this->returnResult(201);
            }
        }else {
            $this->returnResult(201);
        }
    }
    
    //补单操作
    public function opNotifyGameServer($game,$param){
        if(empty($param['trade_no'])){
            return $this->returnResult(4000);
        }
        $options['table']='game as A';
        $options['join']='oauth2_clients as B on A.client_id=B.client_id';
        $options['field']='A.pay_redirect_uri,B.client_secret';
        $options['where']=array('game_id'=>'?');
        $options['param']=array($game['game_id']);
        $gameInfo=$this->db->find($options);
        if(empty($gameInfo)){
            return $this->returnResult(4000);
        }
        $game['pay_redirect_uri']=$gameInfo['pay_redirect_uri'];
        $game['client_secret']=$gameInfo['client_secret'];
        $options1['table']='log_user_pay';
        $options1['where']=array('trade_no'=>'?');
        $options1['param']=array($param['trade_no']);
        $post=$this->db->find($options1);
        if(empty($post)){
            return $this->returnResult(4000);
        }
        switch ($post['pay_type']){
            case 1:
                $payType='ALIPAY';
                break;
            case 2:
                $payType='WECHATPAY';
                break;
            case 3:
                $payType='YEEPAY';
                break;
            case 4:
                $payType='APPLEPAY';
                break;
            case 5:
                $payType='YEEPAYBANK';
                break;
            default :
                $payType='ALIPAY';
                break;
        }
        //统一发送通知参数           
        $notify=array('uid'=>$post['uid'],'trade_no'=>$post['trade_no'],'pay_type'=>$payType,'out_trade_no'=>$post['out_trade_no'],'trade_status'=>$post['trade_status'],'buyer_id'=>$post['buyer_id'],'price'=>$post['price'],'total_fee'=>$post['total_fee'],'trade_time'=>$post['trade_time'],'game_id'=>$post['game_id'],'game_uid'=>$post['game_uid'],'game_server'=>$post['game_server'],'extra_param'=>$post['extra_param'],'ctime'=>$post['ctime']);
        $notify['client_secret']=$game['client_secret'];
        $model=new \Addons\Pay\PayUtil();
        $sysSign=$model->generateSign($notify);
        unset($notify['client_secret']);
        $notify['sys_sign']=$sysSign;
        $model3=new \Log\LogModel();
        //记录已发送消息
        $model3->addLogNotify($game['pay_redirect_uri'],$notify['trade_no'],$notify);
        $result=\Addons\Grab\GrabUtil::single_grab_json_postdata($game['pay_redirect_uri'],$notify);
        if(strtoupper($result)=='SUCCESS'){
            return $this->returnResult(200);
        }else{
            return $this->returnResult(201);
        }
    }

    // 用户充值列表
    public function payList($gameInfo,$param,$count,$page){
        $uid = $param['uid'];
        $uname = $param['uname'];
        $tradeNo = $param['trade_no'];
        $gameId = $gameInfo['game_id'];

        $options['table'] = 'log_user_pay as A';
        $options['join'] = array('user as B on A.uid=B.uid','game as C on A.game_id=C.game_id');
        $options['field'] = 'A.uid,B.uname,A.trade_no,A.pay_type,A.trade_status,A.price,A.total_fee,A.trade_time,C.game_name,A.game_uid,A.game_server,A.is_notify,A.ctime';
        $options['order'] = 'A.ctime desc';
        $options['limit']=($page-1)*$count.','.$count;

        $options['where'] = array('A.game_id'=>'?');
        $options['param'] = array($gameId);
        if($uid){
            $options['where'] = array_merge($options['where'],array('A.uid'=>'?'));
            $options['param'] = array_merge($options['param'],array($uid));
        }
        if($uname){
            $options['where'] = array_merge($options['where'],array('B.uname'=>array('LIKE','?')));
            $options['param'] = array_merge($options['param'],array("%$uname%"));
        }
        if($tradeNo){
            $options['where'] = array_merge($options['where'],array('A.trade_no'=>'?'));
            $options['param'] = array_merge($options['param'],array($tradeNo));
        }
        $totalNum=$this->db->count($options);
        $totalPage=ceil($totalNum/$count);
        $data = $this->db->select($options);

        if(!empty($data)){
            foreach($data as $k=>$v){
                switch ($v['pay_type']){
                    case 1:
                        $payType='阿里支付';
                        break;
                    case 2:
                        $payType='微信支付';
                        break;
                    case 3:
                        $payType='易宝支付';
                        break;
                    case 4:
                        $payType='苹果支付';
                        break;
                    case 5:
                        $payType='银联支付';
                        break;
                    default :
                        $payType='阿里支付';
                        break;
                }
                $data[$k]['pay_type'] = $payType;                    
                $data[$k]['trade_time'] = date('Y-m-d H:i:s',$data[$k]['ctime']);
                $data[$k]['is_notify'] = $v['is_notify']==1 ? '是':'否';
                unset($data[$k]['ctime']);
            }
            $data=array('totalPage'=>$totalPage,'count'=>count($data),'page'=>$page,'list'=>$data);
            return $this->returnResult(200,$data);
        }else {
            return $this->returnResult(201);
        }
    }

    //阿里充值列表
    public function aliPayList($gameInfo,$param,$count,$page){
        $uid = $param['uid'];
        $uname = $param['uname'];
        $tradeNo = $param['trade_no'];

        $gameId = $gameInfo['game_id'];
        $options['table'] = 'log_user_pay_ali as A';
        $options['join'] = array('log_user_pay as B on A.trade_no=B.trade_no','user as C on B.uid=C.uid','game as D on B.game_id=D.game_id ');
        $options['field'] = 'C.uid,C.uname,D.game_name,B.game_uid,A.trade_no,A.seller_email,A.buyer_email,A.trade_status,A.gmt_payment,A.subject,A.total_fee';

        $options['where'] = array('B.game_id'=>'?');
        $options['param'] = array($gameId);
        if($uid){
            $options['where'] = array_merge($options['where'],array('C.uid'=>'?'));
            $options['param'] = array_merge($options['param'],array($uid));
        }
        if($uname){
            $options['where'] = array_merge($options['where'],array('C.uname'=>array('LIKE','?')));
            $options['param'] = array_merge($options['param'],array("%$uname%"));
        }
        if($tradeNo){
            $options['where'] = array_merge($options['where'],array('A.trade_no'=>'?'));
            $options['param'] = array_merge($options['param'],array($tradeNo));
        }
        $options['order'] = 'A.gmt_create desc';
        $options['limit']=($page-1)*$count.','.$count;
        $totalNum=$this->db->count($options);
        $totalPage=ceil($totalNum/$count);
        $data = $this->db->select($options);
        //print_r($data);exit;
        if(!empty($data)){
            $data=array('totalPage'=>$totalPage,'count'=>count($data),'page'=>$page,'list'=>$data);
            return $this->returnResult(200,$data);
        }else {
            return $this->returnResult(201);
        }
    }

    // 微信充值列表
    public function wechatPayList($gameInfo,$param,$count,$page){
        $uid = $param['uid'];
        $uname = $param['uname'];
        $tradeNo = $param['trade_no'];

        $gameId = $gameInfo['game_id'];
        $options['table'] = 'log_user_pay_wechat as A';
        $options['join'] = array('log_user_pay as B on A.transaction_id=B.trade_no','user as C on B.uid=C.uid','game as D on B.game_id=D.game_id ');
        $options['field'] = 'C.uid,C.uname,A.transaction_id,A.out_trade_no,D.game_name,B.game_uid,A.result_code,A.total_fee,A.attach,A.time_end';

        $options['where'] = array('B.game_id'=>'?');
        $options['param'] = array($gameId);
        if($uid){
            $options['where'] = array_merge($options['where'],array('C.uid'=>'?'));
            $options['param'] = array_merge($options['param'],array($uid));
        }
        if($uname){
            $options['where'] = array_merge($options['where'],array('C.uname'=>array('LIKE','?')));
            $options['param'] = array_merge($options['param'],array("%$uname%"));
        }
        if($tradeNo){
            $options['where'] = array_merge($options['where'],array('A.transaction_id'=>'?'));
            $options['param'] = array_merge($options['param'],array($tradeNo));
        }
        $options['order'] = 'A.time_end desc';
        $options['limit']=($page-1)*$count.','.$count;

        $totalNum=$this->db->count($options);
        $totalPage=ceil($totalNum/$count);
        $data = $this->db->select($options);
        //print_r($data);exit;
        if(!empty($data)){
            foreach($data as $k=>$v){
                $data[$k]['total_fee'] = $v['total_fee']/100;
            }
            $data=array('totalPage'=>$totalPage,'count'=>count($data),'page'=>$page,'list'=>$data);
            return $this->returnResult(200,$data);
        }else {
            return $this->returnResult(201);
        }
    }

    //易宝充值列表
    public function yeePayList($gameInfo,$param,$count,$page){
        $uid = $param['uid'];
        $uname = $param['uname'];
        $tradeNo = $param['trade_no'];
        $gameId = $gameInfo['game_id'];

        $options['table'] = 'log_user_pay_yee as A';
        $options['join'] = array('log_user_pay as B on A.trx_id=B.trade_no','user as C on B.uid=C.uid','game as D on B.game_id=D.game_id ');
        $options['field'] = 'C.uid,C.uname,A.trx_id,A.code,A.amt,A.card_no,D.game_name,B.game_uid,A.confirm_amount,A.real_amount,A.mp,A.ctime';

        $options['where'] = array('B.game_id'=>'?');
        $options['param'] = array($gameId);
        if($uid){
            $options['where'] = array_merge($options['where'],array('C.uid'=>'?'));
            $options['param'] = array_merge($options['param'],array($uid));
        }
        if($uname){
            $options['where'] = array_merge($options['where'],array('C.uname'=>array('LIKE','?')));
            $options['param'] = array_merge($options['param'],array("%$uname%"));
        }
        if($tradeNo){
            $options['where'] = array_merge($options['where'],array('A.trx_id'=>'?'));
            $options['param'] = array_merge($options['param'],array($tradeNo));
        }
        $options['order'] = 'A.ctime desc';
        $options['limit']=($page-1)*$count.','.$count;

        $totalNum=$this->db->count($options);
        $totalPage=ceil($totalNum/$count);
        $data = $this->db->select($options);
        //print_r($data);exit;
        if(!empty($data)){
            foreach($data as $k=>$v){
                $data[$k]['ctime'] = date('Y-m-d H:i:s',$v['ctime']);
            }
            $data=array('totalPage'=>$totalPage,'count'=>count($data),'page'=>$page,'list'=>$data);
            return $this->returnResult(200,$data);
        }else {
            return $this->returnResult(201);
        }
    }

    //用户充值详情页
    public function payDetail($table,$tradeNo){
        $options['table'] = "$table as A";
        if($table=='log_user_pay_ali'){
            $options['join'] = array('log_user_pay as B on A.trade_no=B.trade_no','user as C on B.uid=C.uid','game as D on B.game_id=D.game_id ');
            $options['where'] = array('A.trade_no'=>'?');
        }else if($table=='log_user_pay_wechat'){
            $options['join'] = array('log_user_pay as B on A.transaction_id=B.trade_no','user as C on B.uid=C.uid','game as D on B.game_id=D.game_id ');
            $options['where'] = array('transaction_id'=>'?');
        }else if($table=='log_user_pay_yee'){
            $options['join'] = array('log_user_pay as B on A.trx_id=B.trade_no','user as C on B.uid=C.uid','game as D on B.game_id=D.game_id ');
            $options['where'] = array('trx_id'=>'?');
        }
        $options['field'] = 'C.uid,C.uname,D.game_name,B.game_uid,A.*';
        $options['param'] = array($tradeNo);
        $data = $this->db->find($options);
        //print_r($data);exit;
        if(!empty($data)){
            if($table=='log_user_pay_wechat'){
                $data['total_fee'] = $data['total_fee']/100;
                $data['cash_fee'] = $data['cash_fee']/100;
            }
            return $this->returnResult(200,$data);
        }else {
            return $this->returnResult(201);
        }
    }

    // 用户留存列表
    public function userLiveList($gameInfo,$channelIds,$param,$count,$page){
        $time = $param['end'] ? strtotime($param['end'].' 00:00:00'):strtotime(date('Y-m-d',time()));      // 结束日期0时0分0秒时间戳，值为空，日期默认当天（08-15有数据）
        $startTime = $param['start'] ? strtotime($param['start'].' 00:00:00'):strtotime(date('Y-m-d',time())."-19day");       // 开始日期0时0分0秒时间戳，值为空，日期默认2016-04-21
        $channelId = $param['channel_id'];
        $list = array();
        $live = array();
        $cids = $this->returnRoleGameChannelIds($gameInfo['game_id'],$channelIds);      // $cids，这个游戏下并且该用户有权限访问的渠道
        //print_r($cids);exit;
        if(!empty($cids)){
            while($time>=$startTime){
                $date = date('Y-m-d',$time);
                $end =  strtotime(date('Y-m-d',$time)."+1day")-1;
                if(!empty($channelId)){
                    $options['table'] = 'user_channel';
                    $options['where'] = array('channel_id'=>'?');
                    $options['param'] = array($channelId);
                    $channel = $this->db->find($options);
                }
                $data = array('date'=>date('Y-m-d',$time), 'channel'=>isset($channel['channel_name']) ? $channel['channel_name']:'所有', 'newDeviceNum'=>0, 'twoLive'=>0, 'threeLive'=>0, 'fourLive'=>0, 'fiveLive'=>0, 'sixLive'=>0, 'sevenLive'=>0, 'fifteenLive'=>0, 'thirtyLive'=>0,);

                $options1['table'] = $gameInfo['game_table_name'];
                $options1['where'] = array('adddate'=>'?');
                if(!empty($channelId)){             // 进行了渠道搜索
                    $options1['where'] = array_merge($options1['where'],array('channel_id'=>'?'));
                    $options1['param'] = array($date,$channelId);
                }else{
                    $options1['where'] = array_merge($options1['where'],array('channel_id'=>array('IN',$cids)));
                    $options1['param'] = array_merge(array($date),$cids);
                }
                $options1['field'] = 'distinct(device_id)';
                $sql1 = $this->db->buildSelect($options1);      // 新增设备id
                $newDeviceNum = $this->db->count($options1);
                $data['newDeviceNum'] = $newDeviceNum;        // 当日新增设备数
                if($newDeviceNum!=0){
                    $data['twoLive'] = $this->returnLive($time,1,$channelId,$cids,$sql1,$options1['param'],$newDeviceNum);
                    $data['threeLive'] = $this->returnLive($time,2,$channelId,$cids,$sql1,$options1['param'],$newDeviceNum);
                    $data['fourLive'] = $this->returnLive($time,3,$channelId,$cids,$sql1,$options1['param'],$newDeviceNum);
                    $data['fiveLive'] = $this->returnLive($time,4,$channelId,$cids,$sql1,$options1['param'],$newDeviceNum);
                    $data['sixLive'] = $this->returnLive($time,5,$channelId,$cids,$sql1,$options1['param'],$newDeviceNum);
                    $data['sevenLive'] = $this->returnLive($time,6,$channelId,$cids,$sql1,$options1['param'],$newDeviceNum);
                    $data['fifteenLive'] = $this->returnLive($time,14,$channelId,$cids,$sql1,$options1['param'],$newDeviceNum);
                    $data['thirtyLive'] = $this->returnLive($time,29,$channelId,$cids,$sql1,$options1['param'],$newDeviceNum);

                }
                $live[] = $data;
                $time -= 86400;
            }
            //print_r($live);exit;
            $totalNum = count($live);
            $totalPage = ceil($totalNum/$count);
            $list = array_slice($live,($page-1)*$count,$count);
            $data=array('totalPage'=>$totalPage,'count'=>count($list),'page'=>$page,'list'=>$list);
            if(!empty($data)){
                return $this->returnResult(200,$data);
            }else {
                return $this->returnResult(201);
            }
        }else {
            return $this->returnResult(201);
        }
    }

    // 返回留存率
    public function returnLive($time,$addDay,$channelId,$cids,$sql,$param,$newDeviceNum){
        $date = date('Y-m-d',$time+$addDay*86400);

        $options['table'] = 'log_user_login';
        $options['where'] = array('login_date'=>'?','device_id'=>array('IN',$sql));
        $options['param'] = array_merge(array($date),$param);
        if(!empty($channelId)){                         // 进行了渠道搜索
            $options['where'] = array_merge($options['where'],array('channel_id'=>'?'));
            $options['param'] = array_merge($options['param'],array($channelId));
        }else {
            $options['where'] = array_merge($options['where'],array('channel_id'=>array('IN',$cids)));
            $options['param'] = array_merge($options['param'],$cids);
        }
        $options['field'] = 'distinct(device_id)';
        $num = $this->db->count($options);
        if($num){
            return (bcdiv($num,$newDeviceNum,4)*100).'%';
        }else {
            return 0;
        }
    }

    // LTV值列表（修改后）
    public function ltvList($gameInfo,$channelIds,$param,$count,$page){
        $gameId = $gameInfo['game_id'];
        $time = $param['end'] ? strtotime($param['end'].' 00:00:00'):strtotime(date('Y-m-d',time()));      // 结束日期0时0分0秒时间戳，值为空，日期默认当天
        $startTime = $param['start'] ? strtotime($param['start'].' 00:00:00'):strtotime(date('Y-m-d',time())."-19day");       // 开始日期0时0分0秒时间戳，值为空，日期默认2016-04-21
        $channelId = $param['channel_id'];
        $list = array();
        $ltv = array();
        $cids = $this->returnRoleGameChannelIds($gameId,$channelIds);
        if(!empty($cids)){
            while($time>=$startTime){
                $date = date('Y-m-d',$time);
                $end =  strtotime(date('Y-m-d',$time)."+1day")-1;
                if(!empty($channelId)){
                    $options1['table'] = 'user_channel';
                    $options1['where'] = array('channel_id'=>'?');
                    $options1['param'] = array($channelId);
                    $channel = $this->db->find($options1);
                }
                $data = array('date'=>date('Y-m-d',$time), 'channel'=>isset($channel['channel_name']) ? $channel['channel_name']:'所有', 'newDeviceNum'=>0, 'LTV1'=>0, 'LTV2'=>0, 'LTV3'=>0, 'LTV7'=>0, 'LTV15'=>0, 'LTV30'=>0, 'LTV45'=>0, 'LTV60'=>0, 'LTV90'=>0,);

                $options2['table'] = $gameInfo['game_table_name'];
                $options2['field'] = 'uid';
                $options2['where'] = array('adddate'=>'?');
                if(!empty($channelId)){         // 进行了渠道搜索
                    $options2['where'] = array_merge($options2['where'],array('channel_id'=>'?'));
                    $options2['param'] = array($date,$channelId);
                }else {
                    $options2['where'] = array_merge($options2['where'],array('channel_id'=>array('IN',$cids)));
                    $options2['param'] = array_merge(array($date),$cids);
                }
                $sql2 = $this->db->buildSelect($options2);
                $newAccount = $this->db->count($options2);         // 当日新增用户数
                //var_dump($newAccount);exit;
                if(!empty($newAccount)){
                    $options2['field'] = 'distinct(device_id)';
                    $newDeviceNum = $this->db->count($options2);
                    $data['newDeviceNum'] = $newDeviceNum;        // 当日新增设备数

                    $data['LTV1'] = $this->returnLtv($gameId,$time,0,$sql2,$options2['param'],$newDeviceNum);
                    $data['LTV2'] = $this->returnLtv($gameId,$time,1,$sql2,$options2['param'],$newDeviceNum);
                    $data['LTV3'] = $this->returnLtv($gameId,$time,2,$sql2,$options2['param'],$newDeviceNum);
                    $data['LTV7'] = $this->returnLtv($gameId,$time,6,$sql2,$options2['param'],$newDeviceNum);
                    $data['LTV15'] = $this->returnLtv($gameId,$time,14,$sql2,$options2['param'],$newDeviceNum);
                    $data['LTV30'] = $this->returnLtv($gameId,$time,29,$sql2,$options2['param'],$newDeviceNum);
                    $data['LTV45'] = $this->returnLtv($gameId,$time,44,$sql2,$options2['param'],$newDeviceNum);
                    $data['LTV60'] = $this->returnLtv($gameId,$time,59,$sql2,$options2['param'],$newDeviceNum);
                    $data['LTV90'] = $this->returnLtv($gameId,$time,89,$sql2,$options2['param'],$newDeviceNum);
                }
                $ltv[] = $data;
                $time -= 86400;
            }
            $totalNum = count($ltv);
            $totalPage = ceil($totalNum/$count);
            $list = array_slice($ltv,($page-1)*$count,$count);
            $data=array('totalPage'=>$totalPage,'count'=>count($list),'page'=>$page,'list'=>$list);
            if(!empty($data)){
                return $this->returnResult(200,$data);
            }else {
                return $this->returnResult(201);
            }
        }else {
            $this->returnResult(201);
        }
    }

    // 返回LTV值
    public function returnLtv($gameId,$time,$addDay,$sql,$param,$newDeviceNum){
        $endTime = $time+$addDay*86400;
        $end = strtotime(date('Y-m-d',$endTime)."+1day")-1;

        $options['table'] = 'log_user_pay';
        $options['where'] = array('game_id'=>'?','trade_time'=>array('BETWEEN',array('?','?')),'uid'=>array('IN',$sql));
        $options['param'] = array_merge(array($gameId,$time,$end),$param);
        $options['field'] = "sum(total_fee) as 'total'";
        $data = $this->db->select($options);
        $sumMoney = $data[0]['total'];         // 新用户充值额
        if($sumMoney){
            //return (bcdiv($sumMoney,$newDeviceNum,4)*100).'%';
            return bcdiv($sumMoney,$newDeviceNum,2);
        }else {
            return 0;
        }
    }
    
    //一年中所有游戏的用户增加数
    public function allGamesNewUserNum($gameArray){
        $datas=array();
        $max=0;
        foreach ($gameArray as $kt=>$vt){
            $begin=mktime(0,0,0,date('m'),1,date('Y'));
            $end=mktime(23,59,59,date('m'),date('t'),date('Y'));
            $year=array(0,0,0,0,0,0,0,0,0,0,0,0);
            $num=date('n',time());//循环求解次数
            $options['table']=$vt['game_table_name'];
            for($i=$num-1;$i>=0;$i--){
                $options['where']=array('addtime'=>array('BETWEEN',array('?','?')));
                $options['param']=array($begin,$end);
                $count=$this->db->count($options);
                if($count>$max){
                    $max=$count;
                }
                $year[$i]=$count;
                $begin=mktime(0,0,0,date('m')-($num-$i),1,date('Y'));
                $end=mktime(23,59,59,date('m')-($num-$i),date('t'),date('Y'));
            }
            $datas[]=$year;
        }
        return array($datas,$max);
    }

    // 一年中所有游戏的新增充值额//TODOsum
    public function allGameNewPayNum($gameArray){
        $datas=array();
        $max = 0;
        foreach ($gameArray as $kt=>$vt){
            $begin=mktime(0,0,0,date('m'),1,date('Y'));
            $end=mktime(23,59,59,date('m'),date('t'),date('Y'));
            $year=array(0,0,0,0,0,0,0,0,0,0,0,0);
            $num=date('n',time());//循环求解次数
            $options['table']='log_user_pay';
            $options['field']='total_fee';
            for($i=$num-1;$i>=0;$i--){
                $options['where']=array('trade_time'=>array('BETWEEN',array('?','?')),'game_id'=>'?');
                $options['param']=array($begin,$end,$kt);
                $data = $this->db->select($options);
                $totalFee = 0;
                if(!empty($data)){
                    foreach($data as $k=>$v){
                        $totalFee += $v['total_fee'];
                    }
                }
                if($totalFee>$max){
                    $max=$totalFee;
                }
                $year[$i]=$totalFee;
                $begin=mktime(0,0,0,date('m')-($num-$i),1,date('Y'));
                $end=mktime(23,59,59,date('m')-($num-$i),date('t'),date('Y'));
            }
            $datas[]=$year;
        }
        return array($datas,$max);
    }

    // 游戏渠道用户数量
    public function gameChannelUserNum($gameInfo){
        $options['table'] = 'user_channel';
        $options['where'] = array('game_id'=>'?');
        $options['param'] = array($gameInfo['game_id']);
        $channelData = $this->db->select($options);
        $datas = array();
        $channels = array();
        if(!empty($channelData)){
            foreach($channelData as $k=>$v){
                $options1['table'] = $gameInfo['game_table_name'];
                $options1['where'] = array('channel_id'=>'?');
                $options1['param'] = array($v['channel_id']);
                $userData = $this->db->select($options1);
                $datas[] = count($userData);
                $channels[] = $v['channel_name'];
            }
        }
        return array($datas,$channels);
    }

    // 文章列表
    public function articleList($gameInfo,$param,$url,$count,$page){
        $title = $param['title'];
        $categoryId = $param['category_id'];
        $gameId = $gameInfo['game_id'];
        $options['table'] = 'ty_web_article as A';
        $options['join'] = array('ty_web_article_category as B on A.category_id=B.category_id');
        $options['field'] = 'A.article_id,A.title,A.content,A.add_time,A.category_id,B.category_name,A.visit_times,A.is_recommend,A.visit_times';
        $options['where'] = array('B.game_id'=>'?','A.is_delete'=>'?');
        $options['param'] = array($gameId,'0');
        if(!empty($title)){
            $options['where'] = array_merge($options['where'],array('A.title'=>array('LIKE','?')));
            $options['param'] = array_merge($options['param'],array("%$title%"));
        }
        if(!empty($categoryId)){
            $options['where'] = array_merge($options['where'],array('B.category_id'=>'?'));
            $options['param'] = array_merge($options['param'],array($categoryId));
        }
        $options['order'] = 'A.add_time desc';
        $options['limit'] = ($page-1)*$count.','.$count;
        $totalNum=$this->db->count($options);
        $totalPage=ceil($totalNum/$count);
        $data = $this->db->select($options);
        //print_r($data);exit;
        if(!empty($data)){
            foreach($data as $k=>$v){
                $data[$k]['add_time'] = date('Y-m-d H:i:s',$v['add_time']);
                $data[$k]['game_name'] = $gameInfo['game_name'];
                //var_dump($v['content']);exit;
                $data[$k]['content'] = preg_replace('/src=&quot;/','src=&quot;'.$url,$v['content']);
            }
            $data=array('totalPage'=>$totalPage,'count'=>count($data),'page'=>$page,'list'=>$data);
            return $this->returnResult(200,$data);
        }else {
            return $this->returnResult(201);
        }
    }

    // 文章分类查询
    public function getArticleCategory($gameId){
        $options['table'] = 'ty_web_article_category';
        $options['where'] = array('game_id'=>'?','is_delete'=>'?');
        $options['param'] = array($gameId,'0');
        $data = $this->db->select($options);
        if(!empty($data)){
            $data = array('list'=>$data);
            return $this->returnResult(200,$data);
        }else {
            return $this->returnResult(201);
        }
    }

    // 发布文章操作
    public function opAddArticle($param){
        $addTime = $param['add_time']?strtotime($param['add_time']):time();
        $tmpData = array('title'=>'?','content'=>'?','add_time'=>'?','category_id'=>'?','game_id'=>'?','is_recommend'=>'?');
        $options['table'] = 'ty_web_article';
        $options['param'] = array($param['title'],$param['content'],$addTime,$param['category_id'],$param['game_id'],$param['is_recommend']);
        $articleId = $this->db->add($tmpData,$options);
        if($articleId!=FALSE){
            return $this->returnResult(200);
        }else {
            return $this->returnResult(4000);
        }
    }

    // 编辑文章操作
    public function opEditArticle($param){
        $addTime = $param['add_time']?strtotime($param['add_time']):time();
        $tmpData = array('title'=>'?','category_id'=>'?','add_time'=>'?','is_recommend'=>'?','content'=>'?','visit_times'=>'?');
        $options['table'] = 'ty_web_article';
        $options['where'] = array('article_id'=>'?');
        $options['param'] = array($param['title'],$param['category_id'],$addTime,$param['is_recommend'],$param['content'],$param['visit_times'],$param['article_id']);
        $articleId = $this->db->save($tmpData,$options);
        if($articleId!=FALSE){
            return $this->returnResult(200);
        }else {
            return $this->returnResult(4000);
        }
    }

    // 删除文章操作
    public function opDeleteArticle($param){
        $tmpData = array('is_delete'=>'?');
        $options['table'] = 'ty_web_article';
        $options['where'] = array('article_id'=>'?');
        $options['param'] = array('1',$param['article_id']);
        $articleId = $this->db->save($tmpData,$options);
        if($articleId!==FALSE){
            return $this->returnResult(200);
        }else {
            return $this->returnResult(4000);
        }

    }

    // 幻灯片列表
    public function slideList($gameInfo,$resourceDomain,$count,$page){
        $options['table'] = 'ty_web_slide';
        $options['field'] = 'slide_id,slide_img,slide_url,add_time,sort,game_id,is_mobile';
        $options['where'] = array('game_id'=>'?','is_delete'=>'?');
        $options['order'] = 'sort desc';
        $options['param'] = array($gameInfo['game_id'],'0');
        $options['limit'] = ($page-1)*$count.','.$count;
        $totalNum=$this->db->count($options);
        $totalPage=ceil($totalNum/$count);
        $data = $this->db->select($options);
        //print_r($data);exit;
        if(!empty($data)){
            foreach($data as $k=>$v){
                $data[$k]['add_time'] = date('Y-m-d H:i:s',$v['add_time']);
                $data[$k]['game_name'] = $gameInfo['game_name'];
                $data[$k]['is_mobile_text'] = $v['is_mobile']=='1'?'是':'否';
                $data[$k]['slide_img'] = $resourceDomain.$v['slide_img'];
            }
            //print_r($data);exit;
            $data=array('totalPage'=>$totalPage,'count'=>count($data),'page'=>$page,'list'=>$data);
            return $this->returnResult(200,$data);
        }else {
            return $this->returnResult(201);
        }
    }

    // 添加幻灯片操作
    public function opAddSlide($param){
        $tmpData = array('slide_img'=>'?','slide_url'=>'?','sort'=>'?','is_mobile'=>'?','game_id'=>'?','add_time'=>'?','is_delete'=>'?');
        $options['table'] = 'ty_web_slide';
        $options['param'] = array($param['img'],$param['url'],$param['sort'],$param['is_mobile'],$param['game_id'],time(),'0');
        $slideId = $this->db->add($tmpData,$options);
        if($slideId!=FALSE){
            return $this->returnResult(200);
        }else {
            return $this->returnResult(4000);
        }
    }

    // 编辑幻灯片操作
    public function opEditSlide($param){
        $tmpData = array('slide_url'=>'?','sort'=>'?','is_mobile'=>'?','game_id'=>'?');
        $options['table'] = 'ty_web_slide';
        $options['where'] = array('slide_id'=>'?');
        $options['param'] = array($param['slide_url'],$param['sort'],$param['is_mobile'],$param['game_id']);
        if(!empty($param['slide_img'])){
            $tmpData = array_merge($tmpData,array('slide_img'=>'?'));
            $options['param'] = array_merge($options['param'],array($param['slide_img'],$param['slide_id']));
        }else {
            $options['param'] = array_merge($options['param'],array($param['slide_id']));
        }
        $slideId = $this->db->save($tmpData,$options);
        if($slideId!=FALSE){
            return $this->returnResult(200);
        }else {
            return $this->returnResult(4000);
        }
    }

    // 删除幻灯片操作
    public function opDeleteSlide($param){
        $tmpData = array('is_delete'=>'?');
        $options['table'] = 'ty_web_slide';
        $options['where'] = array('slide_id'=>'?');
        $options['param'] = array('1',$param['slide_id']);
        $giftId = $this->db->save($tmpData,$options);
        if($giftId!==FALSE){
            return $this->returnResult(200);
        }else {
            return $this->returnResult(4000);
        }
    }

    // 礼包列表
    public function giftList($gameInfo,$url,$count,$page){
        $options['table'] = 'ty_web_gift';
        $options['field'] = 'gift_id,gift_name,content,instruction,gift_img,gift_num,gift_limit,game_id';
        $options['where'] = array('game_id'=>'?','is_delete'=>'?');
        $options['order'] = 'add_time';
        $options['param'] = array($gameInfo['game_id'],'0');
        $options['limit'] = ($page-1)*$count.','.$count;
        $totalNum=$this->db->count($options);
        $totalPage=ceil($totalNum/$count);
        $data = $this->db->select($options);
        //print_r($data);exit;
        if(!empty($data)){
            foreach($data as $k=>$v){
                $data[$k]['gift_img'] = $url.$v['gift_img'];
                $data[$k]['game_name'] = $gameInfo['game_name'];
            }
            //print_r($data);exit;
            $data=array('totalPage'=>$totalPage,'count'=>count($data),'page'=>$page,'list'=>$data);
            return $this->returnResult(200,$data);
        }else {
            return $this->returnResult(201);
        }
    }

    // 添加礼包操作
    public function opAddGift($param){
        $tmpData = array('gift_name'=>'?','content'=>'?','instruction'=>'?','gift_img'=>'?','add_time'=>'?','is_delete'=>'?','gift_limit'=>'?','game_id'=>'?');
        $options['table'] = 'ty_web_gift';
        $options['param'] = array($param['gift_name'],$param['content'],$param['instruction'],$param['gift_img'],time(),'0',$param['gift_limit'],$param['game_id']);
        $giftId = $this->db->add($tmpData,$options);
        if($giftId!==FALSE){
            return $this->returnResult(200);
        }else {
            return $this->returnResult(4000);
        }
    }

    // 编辑礼包操作
    public function opEditGift($param){
        $tmpData = array('gift_name'=>'?','content'=>'?','instruction'=>'?','gift_limit'=>'?');
        $options['table'] = 'ty_web_gift';
        $options['where'] = array('gift_id'=>'?');
        $options['param'] = array($param['gift_name'],$param['content'],$param['instruction'],$param['gift_limit']);
        if(!empty($param['gift_img'])){
            $tmpData = array_merge($tmpData,array('gift_img'=>'?'));
            $options['param'] = array_merge($options['param'],array($param['gift_img'],$param['gift_id']));
        }else {
            $options['param'] = array_merge($options['param'],array($param['gift_id']));
        }
        $giftId = $this->db->save($tmpData,$options);
        if($giftId!=FALSE){
            return $this->returnResult(200);
        }else {
            return $this->returnResult(4000);
        }
    }

    // 删除礼包操作
    public function opDeleteGift($param){
        $tmpData = array('is_delete'=>'?');
        $options['table'] = 'ty_web_gift';
        $options['where'] = array('gift_id'=>'?');
        $options['param'] = array('1',$param['gift_id']);
        $giftId = $this->db->save($tmpData,$options);
        if($giftId!==FALSE){
            return $this->returnResult(200);
        }else {
            return $this->returnResult(4000);
        }
    }

    // 添加兑换码操作
    public function opAddExchangeCode($param){
        $data = $this->returnAddCodeData($param['tmp_name'],$param['gift_id']);
        //print_r($data);exit;
        if(!$data){
            return $this->returnResult(201);
        }
        $this->db->startTrans();
        $sql = "insert into `ty_web_exchange_code` (`exchange_code`,`gift_id`,`add_time`) values $data[str]";
        //echo $sql;exit;
        $status = $this->db->create($sql);

        $options['table'] = 'ty_web_gift';
        $options['where'] = array('gift_id'=>'?');
        $options['param'] = array($param['gift_id']);
        $gift = $this->db->find($options);
        //print_r($gift);exit;

        $num = bcadd($gift['gift_num'],$data['num']);
        $tmpData1 = array('gift_num'=>'?');
        $options1['table'] = 'ty_web_gift';
        $options1['where'] = array('gift_id'=>'?');
        $options1['param'] = array($num,$param['gift_id']);
        $status1 = $this->db->save($tmpData1,$options1);
        if($status!==FALSE && $status1!==FALSE){
            $this->db->commit();
            return $this->returnResult(200);
        }else {
            $this->db->rollback();
            return $this->returnResult(4000);
        }
    }

    // 解析csv、txt，返回需要插入的字符串数据
    public function returnAddCodeData($filename,$giftId) {
        $handle = fopen($filename,'r');
        $out = array ();
        $n = 0;
        while ($data = fgetcsv($handle, 10000)) {
            $count = count($data);
            for ($i = 0; $i < $count; $i++) {
                $out[$n][$i] = $data[$i];
            }
            $n++;
        }
        if(empty($out)){
            return false;
        }
        $addTime = time();
        $values = '';
        $num = 0;
        foreach($out as $v){
            foreach($v as $vi){
                $num += 1;
                $values .= "('$vi','$giftId','$addTime'),";
            }
        }
        fclose($handle);
        $values = substr($values,0,-1); //去掉最后一个逗号
        return array('str'=>$values,'num'=>$num);
    }

    // 素材列表
    public function materialList($gameInfo,$url,$param,$count,$page){
        $options['table'] = 'ty_web_material';
        $options['field'] = 'material_id,thumb_img,material_url,outside_url,category,add_time,game_id';
        $options['where'] = array('game_id'=>'?','is_delete'=>'?');
        $options['order'] = 'add_time desc';
        $options['param'] = array($gameInfo['game_id'],'0');
        $options['limit'] = ($page-1)*$count.','.$count;
        //echo $param['category'];exit;
        if(!empty($param['category'])){
            $options['where'] = array_merge($options['where'],array('category'=>'?'));
            $options['param'] = array_merge($options['param'],array($param['category']));
        }
        $totalNum=$this->db->count($options);
        $totalPage=ceil($totalNum/$count);
        $data = $this->db->select($options);
        //print_r($data);exit;
        if(!empty($data)){
            foreach($data as $k=>$v){
                $data[$k]['add_time'] = date('Y-m-d H:i:s',$v['add_time']);
                $data[$k]['game_name'] = $gameInfo['game_name'];
                $data[$k]['thumb_img'] = $url.$v['thumb_img'];
                if($v['category']!='video'){
                    $data[$k]['material_url'] = $url.$v['material_url'];
                }
                $data[$k]['category_name'] = $v['category']=='origPicture'?'原图':($v['category']=='screenShot'?'截图':($v['category']=='video'?'视频':''));
            }
            //print_r($data);exit;
            $data=array('totalPage'=>$totalPage,'count'=>count($data),'page'=>$page,'list'=>$data);
            return $this->returnResult(200,$data);
        }else {
            return $this->returnResult(201);
        }
    }

    // 添加素材
    public function opAddMaterial($param){
        $tmpData = array('thumb_img'=>'?','material_url'=>'?','category'=>'?','add_time'=>'?','game_id'=>'?','outside_url'=>'?');
        $options['table'] = 'ty_web_material';
        $options['param'] = array($param['thumb_img'],$param['material_url'],$param['category'],time(),$param['game_id'],$param['outside_url']);
        $materialId = $this->db->add($tmpData,$options);
        if($materialId!==FALSE){
            return $this->returnResult(200);
        }else {
            return $this->returnResult(4000);
        }
    }

    // 删除素材操作
    public function opDeleteMaterial($param){
        $tmpData = array('is_delete'=>'?');
        $options['table'] = 'ty_web_material';
        $options['where'] = array('material_id'=>'?');
        $options['param'] = array('1',$param['material_id']);
        $materialId = $this->db->save($tmpData,$options);
        if($materialId!==FALSE){
            return $this->returnResult(200);
        }else {
            return $this->returnResult(4000);
        }
    }

    // 天豫文章列表
    public function tyArticleList($url,$count,$page){
        $options['table'] = 'ty_web_tianyu_article';
        $options['field'] = array('article_id','title','article_img','is_recommend','add_time','content');
        $options['where'] = array('is_delete'=>'?');
        $options['param'] = array('0');
        $options['order'] = 'article_id desc';
        $options['limit'] = ($page-1)*$count.','.$count;
        $totalNum = $this->db->count($options);
        $totalPage = ceil($totalNum/$count);
        $data = $this->db->select($options);
        if(!empty($data)){
            foreach($data as $k=>$v){
                $data[$k]['add_time'] = date('Y-m-d H:i:s',$v['add_time']);
                $data[$k]['article_img'] = $url.$v['article_img'];
                $data[$k]['recommend'] = $v['is_recommend']=='1'?'是':'否';
                $data[$k]['content'] = preg_replace('/src=&quot;/','src=&quot;'.$url,$v['content']);
            }
            //print_r($data);exit;
            $data=array('totalPage'=>$totalPage,'count'=>count($data),'page'=>$page,'list'=>$data);
            return $this->returnResult(200,$data);
        }else {
            return $this->returnResult(201);
        }
    }

    // 发布天豫官网文章操作
    public function opAddTyArticle($param){
        $addTime = $param['add_time']?strtotime($param['add_time']):time();
        $tmpData = array('title'=>'?','content'=>'?','article_img'=>'?','is_recommend'=>'?','add_time'=>'?','is_delete'=>'?');
        $options['table'] = 'ty_web_tianyu_article';
        $options['param'] = array($param['title'],$param['content'],$param['article_img'],$param['is_recommend'],$addTime,'0');
        $articleId = $this->db->add($tmpData,$options);
        if($articleId!==FALSE){
            return $this->returnResult(200);
        }else {
            return $this->returnResult(4000);
        }
    }

    // 编辑天豫官网文章操作
    public function opEditTyArticle($param){
        $addTime = $param['add_time']?strtotime($param['add_time']):time();
        $tmpData = array('title'=>'?','content'=>'?','is_recommend'=>'?','add_time'=>'?');
        $options['table'] = 'ty_web_tianyu_article';
        $options['where'] = array('article_id'=>'?');
        $options['param'] = array($param['title'],$param['content'],$param['is_recommend'],$addTime);
        if(!empty($param['article_img'])){
            $tmpData = array_merge($tmpData,array('article_img'=>'?'));
            $options['param'] = array_merge($options['param'],array($param['article_img'],$param['article_id']));
        }else {
            $options['param'] = array_merge($options['param'],array($param['article_id']));
        }
        $articleId = $this->db->save($tmpData,$options);
        if($articleId!==FALSE){
            return $this->returnResult(200);
        }else {
            return $this->returnResult(4000);
        }
    }

    // 删除天豫官网文章操作
    public function opDeleteTyArticle($param){
        $tmpData = array('is_delete'=>'?');
        $options['table'] = 'ty_web_tianyu_article';
        $options['where'] = array('article_id'=>'?');
        $options['param'] = array('1',$param['article_id']);
        $articleId = $this->db->save($tmpData,$options);
        if($articleId!==FALSE){
            return $this->returnResult(200);
        }else {
            return $this->returnResult(4000);
        }
    }

    // 官网数据库数据转移
    public function opInsertData($sql){
        $res = $this->db->create($sql);
        if($res!==FALSE){
            return $this->returnResult(200);
        }else {
            return $this->returnResult(4000);
        }
    }

    // 官网游戏包列表
    public function packageList($count,$page){
        $options['table']= 'ty_web_game_package as A';
        $options['join'] = array('game as B on A.game_id=B.game_id');
        $options['field'] = 'A.game_id,B.game_name,A.ios_package,A.android_package,A.weiduan_package';
        $options['limit'] = ($page-1)*$count.','.$count;
        $totalNum = $this->db->count($options);
        $totalPage = ceil($totalNum/$count);
        $data = $this->db->select($options);
        //print_r($data);exit;
        if(!empty($data)){
            $data=array('totalPage'=>$totalPage,'count'=>count($data),'page'=>$page,'list'=>$data);
            return $this->returnResult(200,$data);
        }else {
            return $this->returnResult(201);
        }
    }

    // 官网游戏包编辑操作
    public function opeditpackage($param){
        $tmpData = array('ios_package'=>'?','android_package'=>'?','weiduan_package'=>'?');
        $options['table'] = 'ty_web_game_package';
        $options['where'] = array('game_id'=>'?');
        $options['param'] = array($param['ios_package'],$param['android_package'],$param['weiduan_package'],$param['game_id']);
        $gameId = $this->db->save($tmpData,$options);
        if($gameId!==FALSE){
            return $this->returnResult(200);
        }else {
            return $this->returnResult(4000);
        }
    }

    // 官网敏感词列表
    public function sensitiveWordList($count,$page){
        $options['table'] = 'ty_web_sensitive_word';
        $options['order'] = 'id';
        $options['limit'] = ($page-1)*$count.','.$count;
        $totalNum = $this->db->count($options);
        $totalPage = ceil($totalNum/$count);
        $data = $this->db->select($options);
        if(!empty($data)){
            $data=array('totalPage'=>$totalPage,'count'=>count($data),'page'=>$page,'list'=>$data);
            return $this->returnResult(200,$data);
        }else {
            return $this->returnResult(201);
        }
    }

    // 插入小说文章数据
    public function opAddNoveData($sql){
        $data = $this->db->create($sql);
        if($data){
            return $this->returnResult(200);
        }else {
            return $this->returnResult(4000);
        }
    }

    // 落地页列表
    public function landingList($gameInfo,$param,$count,$page){
        $options['table'] = 'landing';
        $options['field'] = 'id,landing_id,landing_name,landing_url,template,conf,add_time';
        $options['where'] = array('game_id'=>'?','is_delete'=>'?');
        $options['param'] = array($gameInfo['game_id'],'0');
        if($param['landing_id']){
            $options['where'] = array_merge($options['where'],array('landing_id'=>array('LIKE','?')));
            $options['param'] = array_merge($options['param'],array("%{$param['landing_id']}%"));
        }
        if($param['landing_name']){
            $options['where'] = array_merge($options['where'],array('landing_name'=>array('LIKE','?')));
            $options['param'] = array_merge($options['param'],array("%{$param['landing_name']}%"));
        }
        $options['order'] = 'add_time desc';
        $options['limit'] = ($page-1)*$count.','.$count;
        $totalNum=$this->db->count($options);
        $totalPage=ceil($totalNum/$count);
        $data = $this->db->select($options);
        //print_r($data);exit;
        if(!empty($data)){
            foreach($data as $k=>$v){
                $conf = json_decode($v['conf'],true);
                //print_r($conf);exit;
                foreach($conf as $k1=>$v1){
                    if($k1=='autoDownload'){
                        $conf[$k1] = $v1=='1'?'是':'否';
                    }
                    if($k1=='screenDownload'){
                        $conf[$k1] = $v1=='1'?'是':'否';
                    }
                    $data[$k][$k1] = $conf[$k1];
                }
                $addTime = date('Y-m-d',$v['add_time']);
                $template = $v['template'];
                unset($data[$k]['conf']);
                unset($data[$k]['add_time']);
                unset($data[$k]['template']);
                $data[$k]['template'] = $template;
                $data[$k]['add_time'] = $addTime;
            }
            $options1['table'] = 'landing';
            $options1['field'] = 'distinct(template)';
            $options1['where'] = array('is_delete'=>'?');
            $options1['param'] = array('0');
            $template = $this->db->select($options1);
            //print_r($template);exit;

            $data=array('totalPage'=>$totalPage,'count'=>count($data),'page'=>$page,'list'=>$data,'template'=>$template);
            return $this->returnResult(200,$data);
        }else {
            return $this->returnResult(201);
        }
    }

    // 添加落地页
    public function opAddLanding($param){
        $tmpData = array('landing_id'=>'?','landing_name'=>'?','landing_url'=>'?','template'=>'?','conf'=>'?','game_id'=>'?','add_time'=>'?');
        $options['table'] = 'landing';
        $options['param'] = array($param['landing_id'],$param['landing_name'],$param['landing_url'],$param['template'],$param['conf'],$param['game_id'],$param['add_time']);
        $id = $this->db->add($tmpData,$options);
        if($id!=FALSE){
            return $this->returnResult(200);
        }else {
            return $this->returnResult(4000);
        }
    }

    // 编辑落地页
    public function opEditLanding($param){
        $tmpData = array('landing_name'=>'?','template'=>'?','conf'=>'?');
        $options['table'] = 'landing';
        $options['where'] = array('id'=>'?');
        $options['param'] = array($param['landing_name'],$param['template'],$param['conf'],$param['id']);
        $id = $this->db->save($tmpData,$options);
        if($id!=FALSE){
            return $this->returnResult(200);
        }else {
            return $this->returnResult(4000);
        }
    }

    // 删除文章操作
    public function opDeleteLanding($param){
        $tmpData = array('is_delete'=>'?');
        $options['table'] = 'landing';
        $options['where'] = array('id'=>'?');
        $options['param'] = array('1',$param['id']);
        $id = $this->db->save($tmpData,$options);
        if($id!==FALSE){
            return $this->returnResult(200);
        }else {
            return $this->returnResult(4000);
        }

    }

    // 强行删除用户的手机绑定
    public function deleteBindCellphone($oauthData){
        $options['table'] = 'user_cellphone';
        $options['where'] = array('cellphone'=>'?');
        $options['param'] = array($oauthData['account']);
        $result = $this->db->find($options);
        if(empty($result)){
            return $this->returnResult(4201);
        }

        $options1['table'] = 'user';
        $options1['where'] = array('uid'=>'?');
        $options1['param'] = array($result['uid']);
        $result1 = $this->db->find($options1);
        if(empty($result1)){
            return $this->returnResult(4302);
        }

        if($result['cellphone']==$result1['uname']){
            return $this->returnResult(4202);
        }

        $options2['table'] = 'member_phone_bind';
        $options2['where'] = array('phone'=>'?');
        $options2['param'] = array($oauthData['account']);
        $result2 = $this->db->find($options2);

        if(!empty($result2)){       // member_phone_bind表有数据，删
            $this->db->startTrans();
            $status = $this->db->delete($options);
            $status2 = $this->db->delete($options2);

            if($status2!==FALSE && $status!==FALSE){
                $this->db->commit();
                return $this->returnResult(200,$oauthData);
            }else {
                $this->db->rollback();
                return $this->returnResult(4000);
            }
        }else {
            $status = $this->db->delete($options);
            if($status!==FALSE){
                return $this->returnResult(200,$oauthData);
            }else {
                return $this->returnResult(4000);
            }
        }
    }

    /**
     * 查询sdk列表
     */
    public function getSdkList($field=array()){
        $options['table'] = 'sdk';
        if(empty($field)) {
            $data = $this->db->select($options);
        } else {
            $options['field'] = implode(",",$field);
            $data = $this->db->select($options);
        }
        if (!empty($data)) {
            return $this->returnResult(200,$data);
        } else {
            return $this->returnResult(201);
        }
    }

    //根据ID返回sdk_type,sdk_version,sdk_url
    public function getSdkInfoById($id) {
        $options['table'] = 'sdk';
        $options['where'] = array('id'=>'?');
        $options['param'] = array($id);
        $options['field'] = "id,sdk_type,sdk_version,sdk_url";
        $data = $this->db->find($options);
        if(!empty($data))
            return $this->returnResult(200,$data);
        else
            return $this->returnResult(201);
    }

    //获取sdk的版本号
    public function getSdkVersion() {
        $options['table'] = 'sdk';
        $options['field'] = "sdk_type,sdk_version";
        $data = $this->db->select($options);
        if(!empty($data)) {
            return $this->returnResult(200,$data);
        } else {
            return $this->returnResult(201);
        }
    }

    /**
     * 添加sdk
     */
    public function addSdk($param)
    {
        $options['table'] = 'sdk';
        if(!isset($param['debug']) || $param['debug']==null){
            $param['debug'] = 0;
        }
        $tmpData = array('sdk_version' => '?', 'sdk_url' => '?', 'sdk_add_time' => '?', 'sdk_type' => '?','debug'=>'?');
        $add_time = date('Y-m-d H:i:s');
        $options['param'] = array($param['sdk_version'], $param['sdk_url'], $add_time, $param['sdk_type'],$param['debug']);
        $id = $this->db->add($tmpData, $options);
        if ($id !== FALSE) {
            return $this->returnResult(200);
        } else {
            return $this->returnResult(400);
        }
    }

    //添加patch
    public function addPatch($param)
    {
        $options['table'] = 'sdk';
        $options['where'] = array('sdk_type'=>'?','sdk_version'=>'?','debug'=>'?');
        $options['param'] = array($param['sdk_type'],$param['sdk_version'],$param['sdk_debug']);
        $options['field'] = 'id';
        $data = $this->db->find($options);
        $sdk_id = $data['id'];
        $update_time = date('Y-m-d H:i:s');
        $tmpData = array('patch_version'=>'?','patch_update_time'=>'?','patch_url'=>'?');
        $options['where'] = array("id"=>'?');
        $options['param'] = array($param['patch_version'],$update_time,$param['patch_url'],$sdk_id);
        unset($options['field']);
        $id = $this->db->save($tmpData,$options);
        unset($options);
        if($id !== FALSE) {
            if(!isset($param['debug']) || $param['debug']==null){
                $param['debug'] = 0;
            }
            $options['table'] = 'sdk_patch';
            $tmpData = array('sdk_id'=>'?','patch_version'=>'?','patch_addtime'=>'?','patch_url'=>'?','debug'=>'?');
            $options['param'] = array($sdk_id,$param['patch_version'],$update_time,$param['patch_url'],$param['debug']);
            $id = $this->db->add($tmpData,$options);
            if ($id !== FALSE) {
                return $this->returnResult(200);
            } else {
                return $this->returnResult(400);
            }
        } else {
            return $this->returnResult(400);
        }
    }



    //根据id更新sdk信息
    public function updatesdk($param) {
        $options['table'] = 'sdk';
        $tmpData = array('sdk_version'=>'?','sdk_url'=>'?','sdk_type'=>'?');
        $options['where'] = array('id' => '?');
        $options['param'] = array($param['sdk_version'],$param['sdk_url'],$param['sdk_type'],$param['id']);
        $id = $this->db->save($tmpData,$options);
        if($id!==FALSE) {
            return $this->returnResult(200);
        } else {
            return $this->returnResult(400);
        }
    }

    //按sdk_id分类返回patch列表
    public function getPatchList() {
        $options['table'] = 'sdk_patch';
        $options['field'] = 'sdk_id,patch_version,patch_url';
        $data = $this->db->select($options);
        if(!empty($data)) {
            foreach($data as $key => $val) {
                $k = 'sdk'.$val['sdk_id'];
                if(!isset($data[$k])) 
                    $data[$k] = array();
                $data[$k][] = $val;
                unset($data[$key]);
            }
            return $this->returnResult(200,$data);
        } else {
            return $this->returnResult(201);
        }
    }

    /**
     * 根据sdk_version,sdk_type,debug判断是否已经存在一个相同的sdk记录
     * @param string $version 设备版本号
     * @param string $type 设备类型（android或者ios）
     * @param int $debug  是否为测试版
     * @param string $url sdk的 url
     * @return bool (存在 true，否则 false)
     */
    public function is_exists($version,$type,$debug=-1,$url=null){
        $version = strtolower($version);
        $options['table'] = 'sdk';
        if ($debug == -1 && $url != null){  //更新使用使用，如果存在该版本号，则不能修改
            $options['where'] = array("sdk_version"=>'?',"sdk_type"=>'?');
            $options['param'] = array($version,$type);
            $options['field'] = 'sdk_url';
            $data = $this->db->find($options);
            if(!empty($data) && ($data['sdk_url'] == $url)) return TRUE;
            return FALSE;
        } else if($debug == 0 || $debug == 1) {
            $options['where'] = array("sdk_version"=>'?',"sdk_type"=>'?',"debug"=>'?');
            $options['param'] = array($version,$type,$debug);
            $options['field'] = 'id';
            $data = $this->db->find($options);
            return (!empty($data))?TRUE:FALSE;
        } else {
            return TRUE;
        }
    }

    // 模板列表
    public function templateList($gameInfo,$param,$count,$page){
        $options['table'] = 'landing';
        $options['field'] = 'distinct(template)';
        $options['where'] = array('is_delete'=>'?','game_id'=>'?');
        $options['param'] = array('0',$gameInfo['game_id']);
        if(!empty($param['template'])){
            $options['where'] = array_merge($options['where'],array('template'=>array('LIKE','?')));
            $options['param'] = array_merge($options['param'],array("%{$param['template']}%"));
        }
        $options['order'] = 'add_time desc';
        $options['limit'] = ($page-1)*$count.','.$count;
        $totalNum=$this->db->count($options);
        $totalPage=ceil($totalNum/$count);
        $data = $this->db->select($options);
        //print_r($data);exit;
        if(!empty($data)){
            $data=['totalPage'=>$totalPage,'count'=>count($data),'page'=>$page,'list'=>$data];
            return $this->returnResult(200,$data);
        }else {
            return $this->returnResult(201);
        }
    }

}

