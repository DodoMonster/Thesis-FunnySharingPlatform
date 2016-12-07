<?php
namespace Web;
class DzzModel extends \Core\BaseModels {
    
    //角色信息秘钥43@FDGJ34fdERT
    //选美信息秘钥xm@B28AMyDTRGXrMM

    //获取游戏数据
    public function getGameInfo(){
        $options['table'] = 'game';
        $options['where'] = array('game_name'=>'?');
        $options['param'] = array('大主宰');
        $game = $this->db->find($options);
        //print_r($game);exit;
        if(!empty($game)){
            return $this->returnResult(200,$game);
        }else {
            return $this->returnResult(201);
        }
    }

    // 获取下载包数据
    public function getGamePackage($gameId){
        $options['table'] = 'ty_web_game_package';
        $options['where'] = array('game_id'=>'?');
        $options['param'] = array($gameId);
        $data = $this->db->find($options);
        //print_r($data);exit;
        if(!empty($data)){
            return $this->returnResult(200,$data);
        }else {
            return $this->returnResult(201);
        }
    }

    // 首页
    public function home($gameId,$url){
        $options['table'] = 'ty_web_slide';
        $options['where'] = array('is_delete'=>'?','game_id'=>'?','is_mobile'=>'?');
        $options['order'] = 'sort desc';
        $options['param'] = array('0',$gameId,'0');
        $slide = $this->db->select($options);          // 幻灯片
        if(!empty($slide)){
            foreach($slide as $k=>$v){
                $slide[$k]['slide_img'] = $url.$v['slide_img'];
            }
        }


        $options8['table'] = 'ty_web_article';
        $options8['field'] = 'article_id,title,add_time';
        $options8['where'] = array('is_recommend'=>'?','game_id'=>'?','is_delete'=>'?');
        $options8['order'] = 'add_time desc';
        $options8['param'] = array('1',$gameId,'0');
        $lastRecommend = $this->db->find($options8);        // 最新推荐
        if(!empty($lastRecommend)){
            $lastRecommend['add_time'] = date('m/d',$lastRecommend['add_time']);
        }
        //print_r($lastRecommend);exit;
        $options9['table'] = 'ty_web_article';
        $options9['field'] = 'article_id,title,add_time';
        $options9['where'] = array('category_id'=>'?','is_recommend'=>'?','game_id'=>'?','is_delete'=>'?');
        $options9['order'] = 'add_time desc';
        $options9['param'] = array('7','1',$gameId,'0');
        $newsLastRecommend = $this->db->find($options9);        // 新闻最后那个推荐
        if(!empty($newsLastRecommend)){
            $newsLastRecommend['add_time'] = date('m/d',$newsLastRecommend['add_time']);
        }

        if($lastRecommend!=$newsLastRecommend){
            $newsRecommend = $newsLastRecommend;
        }else {
            $options10['table'] = 'ty_web_article';
            $options10['field'] = 'article_id,title,add_time';
            $options10['where'] = array('category_id'=>'?','is_recommend'=>'?','game_id'=>'?','is_delete'=>'?');
            $options10['order'] = 'add_time desc';
            $options10['limit'] = '1,1';
            $options10['param'] = array('7','1',$gameId,'0');
            $res = $this->db->select($options10);
            $newsSecRecommend = $res ? $res[0]:array();         // 时间倒叙，新闻推荐倒数第二条
            if(!empty($newsSecRecommend)){
                $newsSecRecommend['add_time'] = date('m/d',$newsSecRecommend['add_time']);
            }
            $newsRecommend = $newsSecRecommend ? $newsSecRecommend:$newsLastRecommend;
        }

        $options11['table'] = 'ty_web_article';
        $options11['field'] = 'article_id,title,add_time';
        $options11['where'] = array('category_id'=>'?','is_recommend'=>'?','game_id'=>'?','is_delete'=>'?');
        $options11['order'] = 'add_time desc';
        $options11['param'] = array('8','1',$gameId,'0');
        $actLastRecommend = $this->db->find($options11);        // 活动最后那个推荐
        if(!empty($actLastRecommend)){
            $actLastRecommend['add_time'] = date('m/d',$actLastRecommend['add_time']);
        }

        if($lastRecommend!=$actLastRecommend){
            $actRecommend = $actLastRecommend;
        }else {
            $options12['table'] = 'ty_web_article';
            $options12['field'] = 'article_id,title,add_time';
            $options12['where'] = array('category_id'=>'?','is_recommend'=>'?','game_id'=>'?','is_delete'=>'?');
            $options12['order'] = 'add_time desc';
            $options12['limit'] = '1,1';
            $options12['param'] = array('8','1',$gameId,'0');
            $result = $this->db->select($options12);
            $actSecRecommend = $result ? $result[0]:array();        // 时间倒叙，活动推荐倒数第二条
            if(!empty($actSecRecommend)){
                $actSecRecommend['add_time'] = date('m/d',$actSecRecommend['add_time']);
            }
            $actRecommend = $actSecRecommend ? $actSecRecommend:$actLastRecommend;
        }

        $options13['table'] = 'ty_web_article';
        $options13['field'] = 'article_id,title,add_time';
        $options13['where'] = array('category_id'=>'?','is_recommend'=>'?','game_id'=>'?','is_delete'=>'?');
        $options13['order'] = 'add_time desc';
        $options13['param'] = array('9','1',$gameId,'0');
        $noticeLastRecommend = $this->db->find($options13);        // 公告最后那个推荐
        if(!empty($noticeLastRecommend)){
            $noticeLastRecommend['add_time'] = date('m/d',$noticeLastRecommend['add_time']);
        }

        if($lastRecommend!=$noticeLastRecommend){
            $noticeRecommend = $noticeLastRecommend;
        }else {
            $options14['table'] = 'ty_web_article';
            $options14['field'] = 'article_id,title,add_time';
            $options14['where'] = array('category_id'=>'?','is_recommend'=>'?','game_id'=>'?','is_delete'=>'?');
            $options14['order'] = 'add_time desc';
            $options14['limit'] = '1,1';
            $options14['param'] = array('9','1',$gameId,'0');
            $result = $this->db->select($options14);
            $noticeSecRecommend = $result ? $result[0]:array();        // 时间倒叙，公告推荐倒数第二条
            if(!empty($noticeSecRecommend)){
                $noticeSecRecommend['add_time'] = date('m/d',$noticeSecRecommend['add_time']);
            }
            $noticeRecommend = $noticeSecRecommend ? $noticeSecRecommend:$noticeLastRecommend;
        }

        $options1['table'] = 'ty_web_article';
        $options1['field'] = 'article_id,title,add_time';
        $options1['where'] = array('is_delete'=>'?','game_id'=>'?');
        $options1['order'] = 'add_time desc';
        $options1['limit'] = '8';
        $options1['param'] = array('0',$gameId);
        if(!empty($lastRecommend)){
            $options1['where'] = array_merge($options1['where'],array('article_id'=>array('NEQ','?')));
            $options1['param'] = array_merge($options1['param'],array($lastRecommend['article_id']));
        }
        $lastArticle = $this->db->select($options1);        // 最新
        $lastArticle = $this->returnArticle($lastArticle);

        $options2['table'] = 'ty_web_article';
        $options2['field'] = 'article_id,title,add_time';
        $options2['where'] = array('is_delete'=>'?','game_id'=>'?','category_id'=>'?');
        $options2['order'] = 'add_time desc';
        $options2['limit'] = '8';
        $options2['param'] = array('0',$gameId,'7');
        if(!empty($newsRecommend)){
            $options2['where'] = array_merge($options2['where'],array('article_id'=>array('NEQ','?')));
            $options2['param'] = array_merge($options2['param'],array($newsRecommend['article_id']));
        }
        $newsArticle = $this->db->select($options2);        // 新闻
        $newsArticle = $this->returnArticle($newsArticle);
        //print_r($newsArticle);exit;

        $options3['table'] = 'ty_web_article';
        $options3['field'] = 'article_id,title,add_time';
        $options3['where'] = array('is_delete'=>'?','game_id'=>'?','category_id'=>'?');
        $options3['order'] = 'add_time desc';
        $options3['limit'] = '8';
        $options3['param'] = array('0',$gameId,'8');
        if(!empty($actRecommend)){
            $options3['where'] = array_merge($options3['where'],array('article_id'=>array('NEQ','?')));
            $options3['param'] = array_merge($options3['param'],array($actRecommend['article_id']));
        }
        $actArticle = $this->db->select($options3);         // 活动
        $actArticle = $this->returnArticle($actArticle);

        $options4['table'] = 'ty_web_article';
        $options4['field'] = 'article_id,title,add_time';
        $options4['where'] = array('is_delete'=>'?','game_id'=>'?','category_id'=>'?');
        $options4['order'] = 'add_time desc';
        $options4['limit'] = '8';
        $options4['param'] = array('0',$gameId,'9');
        if(!empty($noticeRecommend)){
            $options4['where'] = array_merge($options4['where'],array('article_id'=>array('NEQ','?')));
            $options4['param'] = array_merge($options4['param'],array($noticeRecommend['article_id']));
        }
        $noticeArticle = $this->db->select($options4);         // 公告
        $noticeArticle = $this->returnArticle($noticeArticle);

        $list = array(
            'lastRecommend'=>$lastRecommend,
            'newsRecommend'=>$newsRecommend,
            'actRecommend'=>$actRecommend,
            'noticeRecommend'=>$noticeRecommend,
            'slide'=>$slide,
            'lastArticle'=>$lastArticle,
            'newsArticle'=>$newsArticle,
            'actArticle'=>$actArticle,
            'noticeArticle'=>$noticeArticle,
        );
        return $list;
    }

    // 首页处理文章标题、时间
    public function returnArticle($data){
        if(!empty($data) && is_array($data)){
            foreach($data as $k=>$v){
                //$data[$k]['title'] = $this->cutStr($v['title'],21);
                $data[$k]['add_time'] = date('m/d',$v['add_time']);
            }
        }
        return $data;
    }

    // 文章列表
    public function articleList($param,$gameId,$count,$page){
        $category = $param['category'];
        $options['table'] = 'ty_web_article';
        $options['field'] = 'article_id,title,add_time';
        $options['where'] = array('is_delete'=>'?','game_id'=>'?');
        $options['param'] = array('0',$gameId);
        if(!empty($category) && $category!='latest'){
            if($category=='guide'){
                $guideIdArr = array('10','11');
                $options['where'] = array_merge($options['where'],array('category_id'=>array('IN',$guideIdArr)));
                $options['param'] = array_merge($options['param'],$guideIdArr);
            }else if($category=='news'){
                $newsIdArr = array('7','8','9');
                $options['where'] = array_merge($options['where'],array('category_id'=>array('IN',$newsIdArr)));
                $options['param'] = array_merge($options['param'],$newsIdArr);
            }else {
                $options['where'] = array_merge($options['where'],array('category_id'=>'?'));
                $options['param'] = array_merge($options['param'],array($category));
            }
        }
        //$options['limit']=($page-1)*$count.','.$count;
        $options['order'] = 'add_time desc';
        $totalNum=$this->db->count($options);
        $totalPage=ceil($totalNum/$count);
        $list = $this->db->select($options);
        if(!empty($list)){
            foreach($list as $k=>$v){
                $list[$k]['add_time'] = date('m/d',$v['add_time']);
            }
        }
        //print_r($list);exit;
        $nav = $this->getNav($category);
        $data=array('totalPage'=>$totalPage,'count'=>count($list),'page'=>$page,'list'=>$list,'nav'=>$nav);
        return $this->returnResult(200,$data);
    }

    //
    public function getNav($category){
        $nav = array();
        $cat = array();
        if(in_array($category,array('7','8','9','news','latest'))){
            $nav = array('7'=>'新闻','8'=>'活动','9'=>'公告');
            $cat = array('最新资讯','News');
        }else if(in_array($category,array('10','11','guide'))){
            $nav = array('10'=>'新手指导','11'=>'高手进阶');
            $cat = array('游戏资料','Game Data');
        }
        return array('category'=>$cat,'nav'=>$nav);
    }

    // 文章详情
    public function articleDetail($param,$url){
        $articleId = $param['article_id'];
        $options['table'] = 'ty_web_article';
        $options['where'] = array('article_id'=>'?');
        $options['param'] = array($articleId);
        $article = $this->db->find($options);
        //print_r($article);exit;
        if(!empty($article)){
            $article['add_time'] = date('Y-m-d',$article['add_time']);
            $this->views($articleId,$article['visit_times']);       // 浏览量
        }
        $article['content'] = preg_replace('/src=&quot;/','src=&quot;'.$url,$article['content']);
        $article['content'] = htmlspecialchars_decode($article['content']);             // 内容转码
        $nav = $this->getNav($article['category_id']);
        $list = array(
            'article'=>$article,
            'nav'=>$nav,
        );
        return $list;
    }

    // 礼包列表
    public function giftList($param,$gameId,$url,$count,$page){
        $options1['table'] = 'ty_web_gift';
        $options1['where'] = array('is_delete'=>'?','game_id'=>'?','gift_id'=>'?');
        $options1['param'] = array('0',$gameId,'7');
        $gift = $this->db->find($options1);

        $giftName = $param['gift_name'];
        $options['table'] = 'ty_web_gift';
        $options['where'] = array('is_delete'=>'?','game_id'=>'?');
        $options['param'] = array('0',$gameId);
        if(!empty($giftName)){
            $options['where'] = array_merge($options['where'],array('gift_name'=>array('LIKE','?')));
            $options['param'] = array_merge($options['param'],array("%$giftName%"));
        }
        if(!empty($gift)){
            $gift['gift_img'] = $url.$gift['gift_img'];
            $options['where'] = array_merge($options['where'],array('gift_id'=>array('NEQ','?')));
            $options['param'] = array_merge($options['param'],array($gift['gift_id']));
        }
        $options['order'] = 'add_time desc';
        //$options['limit'] = ($page-1)*$count.','.$count;
        $totalNum=$this->db->count($options);
        $totalPage=ceil($totalNum/$count);
        $list = $this->db->select($options);



        if(!empty($list)){
            foreach($list as $k=>$v){
                $list[$k]['gift_img'] = $url.$v['gift_img'];
            }
            //print_r($list);exit;
            $data=array('totalPage'=>$totalPage,'count'=>count($list),'page'=>$page,'list'=>$list,'wechat_gift'=>$gift);
            return $this->returnResult(200,$data);
        }else {
            return $this->returnResult(201);
        }
    }

    // 领取礼包兑换码操作
    public function opReceiveCode($param){
        //setcookie("dzz_lblq", "", time()-3600);exit;      // 清除该cookie
        /*if(isset($_COOKIE['dzz_lblq'])){
            return $this->returnResult(201,"你已经领过该礼包了！");
        }*/
        $giftId = $param['gift_id'];
        $userIp = $param['user_ip'];
        $sessionId = $param['session_id'];
        $httpUserAgent = $param['http_user_agent'];
        //echo $giftId;exit;
        $options['table'] = 'ty_web_gift';
        $options['where'] = array('gift_id'=>'?');
        $options['param'] = array($giftId);
        $gift = $this->db->find($options);
        //print_r($gift);exit;
        if(!empty($gift)){
            $giftLimit = $gift['gift_limit'];           // 每个ip能领取个数
            $giftNum = $gift['gift_num'];
        }
        $options1['table'] = 'ty_web_receive_code';
        /*$options1['where'] = array('user_ip'=>'?','user_http_agent'=>'?','gift_id'=>'?');
        $options1['param'] = array($userIp,$httpUserAgent,$giftId);*/
        $options1['where'] = array('session_id'=>'?','gift_id'=>'?');
        $options1['param'] = array($sessionId,$giftId);
        $receiveCodeCount = $this->db->count($options1);       // 该ip领过多少次该礼包
        //echo $receiveCodeCount;exit;
        if(bccomp($receiveCodeCount,5)!=-1){           // 领次数大于等于5
            return $this->returnResult(201,"你已经领过该礼包了！");
        }
        $options2['table'] = 'ty_web_exchange_code';
        $options2['where'] = array('gift_id'=>'?','is_receive'=>'?');
        $options2['param'] = array($giftId,'0');
        $exchangeCodeData = $this->db->find($options2);
        //print_r($exchangeCodeData);exit;
        if(!empty($exchangeCodeData)){
            $this->db->startTrans();
            $tmpData3 = array('is_receive'=>'?');
            $options3['table'] = 'ty_web_exchange_code';
            $options3['where'] = array('exchange_code_id'=>'?');
            $options3['param'] = array('1',$exchangeCodeData['exchange_code_id']);
            $exchangeCodeRes = $this->db->save($tmpData3,$options3);              // exchange_code表修改兑换码已领取

            $tmpData4 = array('user_ip'=>'?','exchange_code_id'=>'?','receive_time'=>'?','gift_id'=>'?','user_http_agent'=>'?','session_id'=>'?');
            $options4['table'] = 'ty_web_receive_code';
            $options4['param'] = array($userIp,$exchangeCodeData['exchange_code_id'],time(),$giftId,$httpUserAgent,$sessionId);
            $receiveRes = $this->db->add($tmpData4,$options4);                  // receive_code表添加领取数据
            //echo $exchangeCodeData['exchange_code_id'];exit;

            $tmpData5 = array('gift_num'=>'?');
            $options5['table'] = 'ty_web_gift';
            $options5['where'] = array('gift_id'=>'?');
            $options5['param'] = array(bcsub($giftNum,1),$giftId);
            $giftRes = $this->db->save($tmpData5,$options5);                    // gift表兑换码数量减1

            if($exchangeCodeRes!==FALSE && $receiveRes!==FALSE && $giftRes!==FALSE){
                $this->db->commit();
                //setcookie('dzz_lblq', session_id(),time()+3600*24*365,'/');     // 领取礼包成功设置cookie
                return $this->returnResult(200,$exchangeCodeData);
            }else{
                $this->db->rollback();
                $this->returnResult(4000);
            }
        }else {
            return $this->returnResult(202,'礼包已被领完了');
        }
    }

    // 添加统计阅读量
    public function views($articleId,$views){
        $times = $views+1;
        $tmpData = array('visit_times'=>'?');
        $options['table'] = 'ty_web_article';
        $options['where'] = array('article_id'=>'?');
        $options['param'] = array($times,$articleId);
        $aid = $this->db->save($tmpData,$options);
        if($aid!=FALSE){
            return $this->returnResult(200);
        }else {
            return $this->returnResult(4000);
        }
    }

    // 首页文章推荐截取汉字
    public function cutStr($str,$length){
        $res = mb_substr($str,0,$length,'utf-8');
        $data = (strlen($res)==strlen($str)) ? $res:$res.'...';
        return $data;
    }
    
    //获得短链接信息
    public function getPromoteInfo($mkkey){
        if(empty($mkkey)){
            return $this->returnResult(4300);
        }
        $options['table']='game_user_dzz_link';
        $options['where']=['mkkey'=>'?'];
        $options['param']=[$mkkey];
        $data=$this->db->find($options);
        if($data){
            return $this->returnResult(200,$data);
        }else{
            return $this->returnResult(201);
        }
    }

    // 记录log
    public function log($data){
        $tmpData = array('content'=>'?','time'=>'?');
        $options['table'] = 'ty_web_log';
        $content = json_encode($data);
        $time = date('Y-m-d H:i:s',time());
        $options['param'] = array($content,$time);
        $logId = $this->db->add($tmpData,$options);
        if($logId){
            return $logId;
        }else {
            return false;
        }
    }
    
    //获取severList
    public function getServerList(){
        $serverUrl="http://fnclientversion.yuelangnet.com:8002/?action=getupdate&passport=3cb6558ed8b99ad5bd74f5ef29ec4b51&product=dzz&path=public/serverlist/server.json";
        $data=file_get_contents($serverUrl);
        $serverList=  json_decode($data,true);
        $servers=[];
        if(!empty($serverList)){
            foreach ($serverList['serverlist'] as $k=>$v){
                if(!empty($v['pfshow'])){
			$tmp=array_intersect($v['pfshow'],['cy_ad','cy_ios','ty_ad','ty_ios']);
                	if(!empty($tmp)){
                    		$servers[]=$v;
                	}
		}
	   }
        }
        return $servers;
    }

    //获取角色信息
    public function getRoleInfo($post){
        /*$serverUrl="http://fnclientversion.yuelangnet.com:8002/?action=getupdate&passport=3cb6558ed8b99ad5bd74f5ef29ec4b51&product=dzz&path=public/serverlist/server.json";
        $data=file_get_contents($serverUrl);
        $serverList=  json_decode($data,true);
        $serverId=$serverList['serverlist'][0]['serverid'];*/
        $serverId=$post['server_id'];
        $timestamp=time();
        $params='';
        foreach ($post as $k=>$v){
            $params.='&'.$k.'='. urlencode($v);
        }  
        $string=$post['user_id'].$serverId.$post['os'].$timestamp.$post['game_id'].$post['agent_id'].$post['gameflag'].'43@FDGJ34fdERT';
        $signature=md5($string);
        $params.='&server_id='.$serverId.'&timestamp='.urlencode($timestamp).'&signature='.urlencode($signature);
        $url='http://datacenter.yuelangnet.com:7006/?action=fnquery'.$params;
        $data1=\Addons\Grab\GrabUtil::single_grab_getdata($url);
        $roleInfo=json_decode($data1,true);
        return $this->returnResult(200,$roleInfo);
    }
    
    //返回用户id
    public function getUserIdViaUname($uname,$gameId){
        $model=new \Users\UserInfoModel();
        return $model->getUserIdViaUname($uname,$gameId);
    }
    
    //预约新服（图形验证码）
    public function bookingNewSever($account,$get){
        $msgCode=$this->nosql->get('captcha_'.$account);
        if($msgCode==strtolower($get['captcha_code'])){
            $this->nosql->delete('captcha_'.$account);
            $url="http://pubserver.yuelangnet.com:8011/commitphone/?gameflag=dzz&phone=".$get['phone']."&server=".$get['server']."&ip=".$get['ip'];
            $tmp=\Addons\Grab\GrabUtil::single_grab_getdata($url);
            $result=json_decode($tmp,true);
            return $this->returnResult(200,$result);  
        }else{
            return $this->returnResult(400,'Captcha Error');
        }
    }
    
    //预约新服（短信验证码）
    public function bookingNewSeverfm($account,$get){
        $msgCode=$this->nosql->get($account."_fm");
        if($msgCode==strtolower($get['msg_code'])){
            if($get['verify_code']=='1'){       // 单纯检测短信验证码是否正确
                return $this->returnResult(200);
            }
            $this->nosql->delete($account."_fm");
            $url="http://pubserver.yuelangnet.com:8011/commitphone/?gameflag=dzz&phone=".$get['phone']."&server=".$get['server']."&ip=".$get['ip']."&platformid=".$get['platformid']."&yunyingid=".$get['yunyingid'];
            $tmp=\Addons\Grab\GrabUtil::single_grab_getdata($url);
            $result=json_decode($tmp,true);
            return $this->returnResult(200,$result);  
        }else{
            return $this->returnResult(400,'Msgcode Error');
        }
    }

    //预约新服
    public function getServerInfo($get){
        if(empty($get['server_id'])){
            $this->returnResult(4300);
        }
        $options['table']='game_server_reserved';
        $options['where']=['server_id'=>'?','game_id'=>'?'];
        $options['param']=[$get['server_id'],$get['game_id']];
        $data=$this->db->find($options);
        if($data){
            $tmpData1=['server_name'=>'?','status'=>'?','start_time'=>'?'];
            $options1['table']='game_server_reserved';
            $options1['where']=['server_id'=>'?','game_id'=>'?'];
            $options1['param']=[$get['server_name'],$get['status'],$get['start_time'],$get['server_id'],$get['game_id']];
            $stauts1=$this->db->save($tmpData1,$options1);
            if($stauts1!==FALSE){
                return $this->returnResult(200);
            }else{
                return $this->returnResult(400,'Failed');
            }
        }else{
            $tmpData1=['server_name'=>'?','status'=>'?','start_time'=>'?','server_id'=>'?','game_id'=>'?'];
            $options1['table']='game_server_reserved';
            $options1['param']=[$get['server_name'],$get['status'],$get['start_time'],$get['server_id'],$get['game_id']];
            $stauts1=$this->db->add($tmpData1,$options1);
            if($stauts1!==FALSE){
                return $this->returnResult(200);
            }else{
                return $this->returnResult(400,'Failed');
            }
        }
    }
    
    //预约新服信息
    public function getNewSeverInfo($gameId){
        $options['table']='game_server_reserved';
        $options['where']=['game_id'=>'?','status'=>'?'];
        $options['param']=[$gameId,1];
        $data=$this->db->select($options);
        if($data){
            return $this->returnResult(200,$data);
        }else{
            return $this->returnResult(201);
        }
    }
    
    //选美查询
    public function beautySearch($get){
        $options['table']='game_user_dzz_xm';
        //$options['where']=['bid'=>'?','serverid'=>'?'];
        //$options['param']=[$get['bid'],$get['serverid']];
        $options['where']=['bid'=>'?'];
        $options['param']=[$get['bid']];
        $beauty=$this->db->find($options);
        if($beauty){
            $key=$key='dzzxm_bid'.$get['bid'];
            $voteInfo=$this->nosql->get($key);
            $beauty['vote']=$voteInfo;
            return $this->returnResult(200,$beauty);
        }else{
            return $this->returnResult(201);
        }
    }
    
    //选美排名
    //{“code”:1,”msg”:[{“bid”:11,”NO”:1,”vote”:[200,300,400,500,200]},{“bid”:12,”NO”:2,”vote”: [200,300,400,500,100] },{“bid”:13,”NO”:3,”vote”: [200,300,400,500,50]}]}
    public function beautyRank($get,$count,$page){
        /*$this->nosql->zAdd("dzzxm_test",1600,'11_1_200_300_400_500_200');
        $this->nosql->zAdd("dzzxm_test",1500,'21_2_200_300_400_500_100');
        //$this->nosql->expire("dzzxm_test",30*60);
        $start=($page-1)*$count;
        $end=$start+$count;
        $list=$this->nosql->zRange('dzzxm_test',$start,$end,true);
        print_r($list);
        $list1=$this->nosql->zRevRange('dzzxm_test',$start,$end,true);
        print_r($list1);
        exit;*/

        if($get['allserver']==1){
            $rankKey="dzzxm_all";
        }else{
            $rankKey="dzzxm_".$get['serverid'];
            //$rankKey="dzzxm_1051003"; // 调试用
        }
        $start=($page-1)*$count;
        $end=$start+$count-1;
        $list=[];
        $totalPage=0;
        $list= $this->nosql->zRevRange($rankKey,$start,$end,true);
        //print_r($list);exit;
        if(empty($list)){
            $sign=md5($get['action'].$get['serverid'].$get['allserver'].'xm@B28AMyDTRGXrMM');
            $url="http://pubserver.yuelangnet.com:8010/?action=".$get['action']."&serverid=".$get['serverid']."&allserver=".$get['allserver']."&sign=$sign";
            $tmp=\Addons\Grab\GrabUtil::single_grab_getdata($url);
            $result=json_decode($tmp,true);
            file_put_contents(SITE_PATH."/data/xmlogs.log","\r\n".$tmp.date('Y-m-d H:i:s',time())."\r\n",FILE_APPEND);   // 日志
            $this->nosql->delete($rankKey);
            $this->nosql->delete($rankKey.'totalpage');
            if(!empty($result) && isset($result['code']) && $result['code']==1){
                $list=$result['msg'];
                $totalPage=ceil(count($list)/$count);
                foreach ($list as $k=>$v){
                    $tmp='';
                    $score=array_sum($v['vote']);
                    $tmp=$v['bid'].'_'.$v['NO'].'_'.implode('_', $v['vote']);
                    $this->nosql->zAdd($rankKey,$score,$tmp);
                    $key='dzzxm_bid'.$v['bid'];
                    $this->nosql->set($key,$tmp,1800);
                }
                $this->nosql->expire($rankKey,1800);
            }
            $list=$this->nosql->zRevRange($rankKey,$start,$end,true);
            $this->nosql->set($rankKey.'totalpage',$totalPage,1800);
        }
        if(empty($totalPage)){
            $totalPage=$this->nosql->get($rankKey.'totalpage');
        }
        $list1=[];
        foreach ($list as $kt=>$vt){
            //11_1_200_300_400_500_200
            $tmp=explode('_', $kt);
            if(!empty($tmp) && isset($tmp[0]) && isset($tmp[1])){
                $arr['bid']=$tmp[0];
                $arr['NO']=$tmp[1];
                unset($tmp[0]);
                unset($tmp[1]);
                $arr['vote']=$tmp;
                $arr['vote'] = array_values($arr['vote']);  //
                $arr['score']=$vt;
                $list1[]=$arr;
            }
            //unset($list[$kt]);
        }
        $data=['totalPage'=>$totalPage,'page'=>$page,'count'=>$count,'list'=>$list1];
        $log = json_encode($data).date('Y-m-d H:i:s',time());
        file_put_contents(SITE_PATH."/data/xmlogs.log",$log."\r\n",FILE_APPEND);   // 日志
        if(!empty($list1)){            
            return $this->returnResult(200,$data);
        }else{
            return $this->returnResult(201,$data);
        }
    }

    //选美排名
    //{“code”:1,”msg”:[{“bid”:11,”NO”:1,”vote”:[200,300,400,500,200]},{“bid”:12,”NO”:2,”vote”: [200,300,400,500,100] },{“bid”:13,”NO”:3,”vote”: [200,300,400,500,50]}]}
    public function beautyRank1($get,$count,$page){
        /*$this->nosql->zAdd("dzzxm_test",1600,'11_1_200_300_400_500_200');
        $this->nosql->zAdd("dzzxm_test",1500,'21_2_200_300_400_500_100');
        //$this->nosql->expire("dzzxm_test",30*60);
        $start=($page-1)*$count;
        $end=$start+$count;
        $list=$this->nosql->zRange('dzzxm_test',$start,$end,true);
        print_r($list);
        $list1=$this->nosql->zRevRange('dzzxm_test',$start,$end,true);
        print_r($list1);
        exit;*/

        if($get['allserver']==1){
            $rankKey="dzzxmwap_all";
        }else{
            $rankKey="dzzxmwap_".$get['serverid'];
            //$rankKey="dzzxm_1051003"; // 调试用
        }
        $start=($page-1)*$count;
        $end=$start+$count-1;
        $list=[];
        $totalPage=0;
        $list= $this->nosql->zRevRange($rankKey,$start,$end,true);
        //print_r($list);exit;
        if(empty($list)){
            $sign=md5($get['action'].$get['serverid'].$get['allserver'].'xm@B28AMyDTRGXrMM');
            $url="http://pubserver.yuelangnet.com:8010/?action=".$get['action']."&serverid=".$get['serverid']."&allserver=".$get['allserver']."&sign=$sign";
            $tmp=\Addons\Grab\GrabUtil::single_grab_getdata($url);
            $result=json_decode($tmp,true);
            $this->nosql->delete($rankKey);
            $this->nosql->delete($rankKey.'totalpage');
            if(!empty($result) && isset($result['code']) && $result['code']==1){
                $list=$result['msg'];
                $totalPage=ceil(count($list)/$count);
                foreach ($list as $k=>$v){
                    $tmp='';
                    $score=array_sum($v['vote']);
                    $tmp=$v['bid'].'_'.$v['NO'].'_'.implode('_', $v['vote']);
                    $this->nosql->zAdd($rankKey,$score,$tmp);
                    //$key='dzzxm_bid'.$v['bid'];
                    $key='dzzxmwap_bid'.$v['bid'];  // 11.10 添加
                    $this->nosql->set($key,$tmp,1800);
                }
                $this->nosql->expire($rankKey,1800);
            }
            $list=$this->nosql->zRevRange($rankKey,$start,$end,true);
            $this->nosql->set($rankKey.'totalpage',$totalPage,1800);
        }
        if(empty($totalPage)){
            $totalPage=$this->nosql->get($rankKey.'totalpage');
        }
        $list1=[];
        foreach ($list as $kt=>$vt){
            //11_1_200_300_400_500_200
            $tmp=explode('_', $kt);
            if(!empty($tmp) && isset($tmp[0]) && isset($tmp[1])){
                $arr['bid']=$tmp[0];
                $arr['NO']=$tmp[1];
                unset($tmp[0]);
                unset($tmp[1]);
                $arr['vote']=$tmp;
                $arr['vote'] = array_values($arr['vote']);  //
                $arr['score']=$vt;
                $list1[]=$arr;
            }
            //unset($list[$kt]);
        }
        $data=['totalPage'=>$totalPage,'page'=>$page,'count'=>$count,'list'=>$list1];
        if(!empty($list1)){
            return $this->returnResult(200,$data);
        }else{
            return $this->returnResult(201,$data);
        }
    }
    
    //选美信息(编辑，新增)
    public function editBeautyInfo($get){
        $signstr='';
        ksort($get);
        //验证签名
        foreach ($get as $kt=>$vt){
            if($kt!='sign'){
                $signstr.=$vt;
            }
        }
        $sign=md5($signstr.'xm@B28AMyDTRGXrMM');
        if($sign!=$get['sign']){
            return $this->returnResult(201);
        }
        unset($get['sign']);    
        $options['table']='game_user_dzz_xm';
        $options['where']=['bid'=>'?'];
        $options['param']=[$get['bid']];
        $beauty=$this->db->find($options);
        if(!empty($beauty)){//修改
            if(isset($get['data'])){
                $tmp=json_decode($get['data'],true);
                $get=array_merge($get,$tmp);
                unset($get['data']);
            }
            foreach ($get as $k=>$v){
                if($v!==''){
                    $tmpData1[$k]='?';
                    $options1['param'][]=$v;
                }
            }
            $options1['table']='game_user_dzz_xm';
            $options1['where']=['bid'=>'?'];
            $options1['param'][]=$get['bid'];
            $status1=$this->db->save($tmpData1,$options1);
            if($status1!==FALSE){
                return $this->returnResult(200);
            }else{
                return $this->returnResult(400);
            }         
        }else{//新增
            foreach ($get as $k=>$v){
               if($v!==''){
                   $tmpData1[$k]='?';
                   $options1['param'][]=$v;
               }
            }
            $options1['table']='game_user_dzz_xm';
            $status1=$this->db->add($tmpData1,$options1);
            if($status1!==FALSE){
                return $this->returnResult(200);
            }else{
                return $this->returnResult(400);
            }
        }
    }
    
    //选美照片url
    public function beautyImgUrl($get){
        //$url='http://103.244.80.219:8010/?action=webgetpic&bid='.$get['bid'].'&pic=1';
        //return $url;
        $url1='http://103.244.80.219:8010/?action=webgetpic&bid='.$get['bid'].'&pic=1';
        $url2='http://103.244.80.219:8010/?action=webgetpic&bid='.$get['bid'].'&pic=2';
        $url3='http://103.244.80.219:8010/?action=webgetpic&bid='.$get['bid'].'&pic=3';
        return [$url1,$url2,$url3];
    }
    
    //选美音频url
    public function beautyAudioUrl($get){
        $url='http://103.244.80.219:8010/?action=webgetmp3&bid='.$get['bid'];
        return $url;
    }
    
    //选美投票
    public function beautyVote($get){
        $sign=md5($get['action'].$get['bid'].$get['ip'].$get['type'].'xm@B28AMyDTRGXrMM');
        $url="http://pubserver.yuelangnet.com:8010/?action=".$get['action']."&bid=".$get['bid']."&ip=".$get['ip']."&type=".$get['type']."&sign=$sign";
        $tmp=\Addons\Grab\GrabUtil::single_grab_getdata($url);
        $result=json_decode($tmp,true);
        if(isset($result['code']) && $result['code']==1){
            return $this->returnResult(200);
        }else{
            return $this->returnResult(400);
        }
    }

    // 根据bid获取选美信息
    public function beautyInfo($data){
        //print_r($data);exit;
        foreach($data['list'] as $k=>$v){
            $options['table'] = 'game_user_dzz_xm';
            $options['field'] = 'id,bid,serverid,nickname,level,race,group,declaration';
            $options['where'] = array('bid'=>'?','state'=>'?');
            $options['param'] = array($v['bid'],'1');
            $res = $this->db->find($options);
            $data['list'][$k]['info'] = $res;
        }
        return $this->returnResult(200,$data);
    }
    
    
    

    // 新增
    // 游戏资料列表
    public function gameDataList($param){
        $cat = $param['category'];
        $ids = array();
        if($cat=='richang'){
            $ids = array(172,173,174,175,178,179,180,181,182,183,184);
        }elseif($cat=='xitong'){
            $ids = array(146,158,159,160,161,162,163,164,165,166,168,169);
        }elseif($cat=='faq'){
            $ids = array(142);
        }else{
            $ids = array(157,171,185,192);      // zhiye
        }
        $options['table'] = 'ty_web_article';
        $options['field'] = 'article_id,title,add_time';
        $options['where'] = array('article_id'=>array('IN',$ids));
        $options['param'] = $ids;
        $data=$this->db->select($options);
        if($data){
            foreach($data as $k=>$v){
                $data[$k]['add_time'] = date('m/d',$v['add_time']);
            }
            return $this->returnResult(200,$data);
        }else{
            return $this->returnResult(201);
        }
    }

    // 文章详情
    public function articleDetailNew($param,$url){
        $articleId = $param['article_id'];
        $options['table'] = 'ty_web_article';
        $options['where'] = array('article_id'=>'?');
        $options['param'] = array($articleId);
        $article = $this->db->find($options);
        //print_r($article);exit;
        if(!empty($article)){
            $article['add_time'] = date('Y-m-d',$article['add_time']);
            $this->views($articleId,$article['visit_times']);       // 浏览量
        }
        $article['content'] = preg_replace('/src=&quot;/','src=&quot;'.$url,$article['content']);
        $article['content'] = htmlspecialchars_decode($article['content']);             // 内容转码
        $category = $article['category_id'];
        $nav = $this->getNav($article['category_id']);
        $list = array(
            'article'=>$article,
            'category'=>$category,
            'nav'=>$nav,
        );
        return $list;
    }

    // 新增2
    // 文章列表
    public function articleList2($param,$gameId,$count,$page){
        $category = $param['category'];
        $options['table'] = 'ty_web_article';
        $options['field'] = 'article_id,title,add_time,category_id';
        $options['where'] = array('is_delete'=>'?','game_id'=>'?');
        $options['param'] = array('0',$gameId);
        if(!empty($category) && $category!='latest'){
            $options['where'] = array_merge($options['where'],array('category_id'=>'?'));
            $options['param'] = array_merge($options['param'],array($category));
        }
        $options['limit']=($page-1)*$count.','.$count;
        $options['order'] = 'add_time desc';
        $totalNum=$this->db->count($options);
        $totalPage=ceil($totalNum/$count);
        $list = $this->db->select($options);
        $list = $this->returnArticle1($list);
        //print_r($list);exit;
        $data=array('totalPage'=>$totalPage,'count'=>count($list),'page'=>$page,'list'=>$list);
        return $this->returnResult(200,$data);
    }

    // 游戏资料列表
    public function gameDataList2($param,$count,$page){
        $cat = $param['category'];
        $ids = array();
        if($cat=='richang'){
            $ids = array(172,173,174,175,178,179,180,181,182,183,184);
        }elseif($cat=='xitong'){
            $ids = array(146,158,159,160,161,162,163,164,165,166,168,169);
        }elseif($cat=='faq'){
            $ids = array(142);
        }else{
            $ids = array(157,171,185,192);      // zhiye
        }
        $options['table'] = 'ty_web_article';
        $options['field'] = 'article_id,title,add_time';
        $options['where'] = array('article_id'=>array('IN',$ids));
        $options['param'] = $ids;
        $options['limit']=($page-1)*$count.','.$count;
        $options['order'] = 'add_time desc';
        $totalNum=$this->db->count($options);
        $totalPage=ceil($totalNum/$count);
        $data=$this->db->select($options);
        if($data){
            foreach($data as $k=>$v){
                $data[$k]['add_time'] = date('Y-m-d',$v['add_time']);
            }
            $data=array('totalPage'=>$totalPage,'count'=>count($data),'page'=>$page,'list'=>$data);
            return $this->returnResult(200,$data);
        }else{
            return $this->returnResult(201);
        }
    }

    // 游戏资料列表
    public function gameDataList3($param,$count,$page){
        $cat = $param['category'];
        $ids = array();
        if($cat=='jieshao'){
            //$ids = array(146,158,159,160,161,162,163,164,165,166,168,169);
            //$options['where'] = array('article_id'=>array('IN',$ids),'is_delete'=>'?');
            $options['where'] = array('category_id'=>'?','is_delete'=>'?');
            $options['param'] = array('23','0');
        }elseif($cat=='wanfa'){
            //$ids = array(172,173,174,175,178,179,180,181,182,183,184);
            //$options['where'] = array('article_id'=>array('IN',$ids),'is_delete'=>'?');
            $options['where'] = array('category_id'=>'?','is_delete'=>'?');
            $options['param'] = array('24','0');
        }elseif($cat=='gonglv'){
            //$arr = array(10,11);
            //$options['where'] = array('category_id'=>array('IN',$arr),'is_delete'=>'?');
            $options['where'] = array('category_id'=>'?','is_delete'=>'?');
            $options['param'] = array('25','0');
        }else{      // zhiying
            //$ids = array(142,144,157,171,185,192);
            //$options['where'] = array('article_id'=>array('IN',$ids),'is_delete'=>'?');
            $options['where'] = array('category_id'=>'?','is_delete'=>'?');
            $options['param'] = array('22','0');
        }
        $options['table'] = 'ty_web_article';
        $options['field'] = 'article_id,title,add_time';
        $options['limit']=($page-1)*$count.','.$count;
        $options['order'] = 'add_time desc';
        $totalNum=$this->db->count($options);
        $totalPage=ceil($totalNum/$count);
        $data=$this->db->select($options);
        if($data){
            foreach($data as $k=>$v){
                $data[$k]['add_time'] = date('Y-m-d',$v['add_time']);
            }
            $data=array('totalPage'=>$totalPage,'count'=>count($data),'page'=>$page,'list'=>$data);
            return $this->returnResult(200,$data);
        }else{
            return $this->returnResult(201);
        }
    }

    // 首页处理文章标题、时间
    public function returnArticle1($data){
        if(!empty($data) && is_array($data)){
            foreach($data as $k=>$v){
                $cid = $v['category_id'];
                //$data[$k]['title'] = $this->cutStr($v['title'],21);
                $data[$k]['add_time'] = date('Y-m-d',$v['add_time']);
                $data[$k]['category'] = $cid==7?'新闻':($cid==8?'活动':($cid==21?'连载':'公告'));
            }
        }
        return $data;
    }


    // 落地页配置
    public function landingConf($param){
        $options['table'] = 'landing';
        $options['field'] = 'id,landing_id,conf';
        $options['where'] = array('landing_id'=>'?','is_delete'=>'?');
        $options['param'] = array($param['landing_id'],'0');
        $data = $this->db->find($options);
        //print_r($data);exit;
        if($data){
            $data['conf'] = json_decode($data['conf'],true);
            return $this->returnResult(200,$data);
        }else{
            return $this->returnResult(201);
        }
    }

    //请求补丁包
    public function patchload($param) {
        $options['table'] = 'sdk';
        if($param['debug'] === FALSE){ 
            $options['where'] = array('sdk_version'=>'?','sdk_type'=>'?','debug'=>'?');
            $options['param'] = array($param['sdk_version'],$param['sdk_type'],0);
        } else {
            $options['where'] = array('sdk_version'=>'?','sdk_type'=>'?','debug'=>'?');
            $options['param'] = array($param['sdk_version'],$param['sdk_type'],1);
        }
        $options['field'] = 'sdk_version,patch_version,patch_url,debug';
        $data = $this->db->find($options);
        if(!empty($data) && $data['patch_version']!=null){
            $data['debug'] = intval($data['debug']);
            return $this->returnResult(200,$data);
        }
        else
            return $this->returnResult(201);
    }

    // 获取游戏列表
    public function getGameList(){
        $options['table'] = 'game';
        $options['field'] = 'game_id,game_name,pay_rate';
        $options['where'] = array('game_id'=>'?');
        $options['param'] = array('11114');
        $data = $this->db->select($options);
        //print_r($data);exit;
        if(!empty($data)){
            return $this->returnResult(200,$data);
        }else {
            return $this->returnResult(201);
        }
    }

    //预约新服信息
    public function getNewServerInfo($gameId){
        $options['table']='game_server_reserved';
        $options['where']=['game_id'=>'?','status'=>'?'];
        $options['param']=[$gameId,1];
        $data=$this->db->find($options);
        //print_r($data);exit;
        if($data){
            $data['booking_end_time'] = (string)($data['start_time']-3600);
            return $this->returnResult(200,$data);
        }else{
            return $this->returnResult(201);
        }
    }

    //预约新服（短信验证码）
    public function verifyMsgCode($account,$get){
        $msgCode=$this->nosql->get($account."_fm");
        if($msgCode==strtolower($get['msg_code'])){
            return $this->returnResult(200);
        }else{
            return $this->returnResult(400,'Msgcode Error');
        }
    }

}
