<?php
namespace Web;
class QjzsModel extends \Core\BaseModels {

    //获取游戏数据
    public function getGameInfo(){
        $options['table'] = 'game';
        $options['where'] = array('game_name'=>'?');
        $options['param'] = array('奇迹战神');
        $game = $this->db->find($options);
        //print_r($game);exit;
        if(!empty($game)){
            return $this->returnResult(200,$game);
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
        foreach($slide as $k=>$v){
            $slide[$k]['slide_img'] = $url.$v['slide_img'];
        }
        //print_r($slide);exit;

        $options1['table'] = 'ty_web_article';
        $options1['field'] = 'article_id,title,add_time';
        $options1['where'] = array('is_delete'=>'?','game_id'=>'?');
        $options1['order'] = 'add_time desc';
        $options1['limit'] = '8';
        $options1['param'] = array('0',$gameId);
        $lastArticle = $this->db->select($options1);        // 最新
        $lastArticle = $this->timeFormat($lastArticle);
        //print_r($lastArticle);exit;

        $options2['table'] = 'ty_web_article';
        $options2['field'] = 'article_id,title,add_time';
        $options2['where'] = array('is_delete'=>'?','game_id'=>'?','category_id'=>'?');
        $options2['order'] = 'add_time desc';
        $options2['limit'] = '8';
        $options2['param'] = array('0',$gameId,'1');
        $newsArticle = $this->db->select($options2);        // 新闻
        $newsArticle = $this->timeFormat($newsArticle);
        //print_r($newsArticle);exit;

        $options3['table'] = 'ty_web_article';
        $options3['field'] = 'article_id,title,add_time';
        $options3['where'] = array('is_delete'=>'?','game_id'=>'?','category_id'=>'?');
        $options3['order'] = 'add_time desc';
        $options3['limit'] = '8';
        $options3['param'] = array('0',$gameId,'2');
        $actArticle = $this->db->select($options3);         // 活动
        $actArticle = $this->timeFormat($actArticle);
        //print_r($actArticle);exit;

        $options4['table'] = 'ty_web_material';
        $options4['field'] = 'material_id,thumb_img,material_url,outside_url';
        $options4['where'] = array('category'=>'?','is_delete'=>'?','game_id'=>'?');
        $options4['order'] = 'add_time';
        $options4['limit'] = '3';
        $options4['param'] = array('video','0',$gameId);
        $video = $this->db->select($options4);              // 视频
        foreach($video as $k=>$v){
            $video[$k]['thumb_img'] = $url.$v['thumb_img'];
        }

        $options5['table'] = 'ty_web_material';
        $options5['field'] = 'material_id,thumb_img,material_url,outside_url';
        $options5['where'] = array('category'=>'?','is_delete'=>'?','game_id'=>'?');
        $options5['order'] = 'add_time';
        $options5['param'] = array('origPicture','0',$gameId);
        $origPicture = $this->db->select($options5);        // 原图
        foreach($origPicture as $k=>$v){
            $origPicture[$k]['thumb_img'] = $url.$v['thumb_img'];
            $origPicture[$k]['material_url'] = $url.$v['material_url'];
        }

        $options6['table'] = 'ty_web_material';
        $options6['field'] = 'material_id,thumb_img,material_url,outside_url';
        $options6['where'] = array('category'=>'?','is_delete'=>'?','game_id'=>'?');
        $options6['order'] = 'add_time';
        $options6['param'] = array('screenShot','0',$gameId);
        $screenShot = $this->db->select($options6);        // 截图
        foreach($screenShot as $k=>$v){
            $screenShot[$k]['thumb_img'] = $url.$v['thumb_img'];
            $screenShot[$k]['material_url'] = $url.$v['material_url'];
        }

        $options7['table'] = 'ty_web_article';
        $options7['field'] = 'article_id,title,add_time';
        $options7['where'] = array('is_recommend'=>'?','game_id'=>'?','is_delete'=>'?');
        $options7['order'] = 'add_time desc';
        $options7['param'] = array('1',$gameId,'0');
        $lastRecommend = $this->db->find($options7);        // 最新推荐
        $lastRecommend = $this->timeFormat($lastRecommend);

        $options8['table'] = 'ty_web_article';
        $options8['field'] = 'article_id,title,add_time';
        $options8['where'] = array('category_id'=>'?','is_recommend'=>'?','game_id'=>'?','is_delete'=>'?');
        $options8['order'] = 'add_time desc';
        $options8['param'] = array('1','1',$gameId,'0');
        $newsLastRecommend = $this->db->find($options8);        // 新闻最后那个推荐
        $newsLastRecommend = $this->timeFormat($newsLastRecommend);

        if($lastRecommend!=$newsLastRecommend){
            $newsRecommend = $newsLastRecommend;
        }else {
            $options9['table'] = 'ty_web_article';
            $options9['field'] = 'article_id,title,add_time';
            $options9['where'] = array('category_id'=>'?','is_recommend'=>'?','game_id'=>'?','is_delete'=>'?');
            $options9['order'] = 'add_time desc';
            $options9['limit'] = '1,1';
            $options9['param'] = array('1','1',$gameId,'0');
            $res = $this->db->select($options9);
            $newsSecRecommend = $res ? $res[0]:array();         // 时间倒叙，新闻推荐倒数第二条
            $newsSecRecommend = $this->timeFormat($newsSecRecommend);
            $newsRecommend = $newsSecRecommend ? $newsSecRecommend:$newsLastRecommend;
        }

        $options10['table'] = 'ty_web_article';
        $options10['field'] = 'article_id,title,add_time';
        $options10['where'] = array('category_id'=>'?','is_recommend'=>'?','game_id'=>'?','is_delete'=>'?');
        $options10['order'] = 'add_time desc';
        $options10['param'] = array('2','1',$gameId,'0');
        $actLastRecommend = $this->db->find($options10);        // 活动最后那个推荐
        $actLastRecommend = $this->timeFormat($actLastRecommend);

        if($lastRecommend!=$actLastRecommend){
            $actRecommend = $actLastRecommend;
        }else {
            $options11['table'] = 'ty_web_article';
            $options11['field'] = 'article_id,title,add_time';
            $options11['where'] = array('category_id'=>'?','is_recommend'=>'?','game_id'=>'?','is_delete'=>'?');
            $options11['order'] = 'add_time desc';
            $options11['limit'] = '1,1';
            $options11['param'] = array('2','1',$gameId,'0');
            $result = $this->db->select($options11);
            $actSecRecommend = $result ? $result[0]:array();        // 时间倒叙，活动推荐倒数第二条
            $actSecRecommend = $this->timeFormat($actSecRecommend);
            $actRecommend = $actSecRecommend ? $actSecRecommend:$actLastRecommend;
        }

        if(!empty($lastRecommend)){
            $lastRecommend['shortTitle'] = $this->cutStr($lastRecommend['title'],24);
        }
        if(!empty($newsRecommend)){
            $newsRecommend['shortTitle'] = $this->cutStr($newsRecommend['title'],24);
        }
        if(!empty($actRecommend)){
            $actRecommend['shortTitle'] = $this->cutStr($actRecommend['title'],24);
        }
        //print_r($lastRecommend);exit;
        $list = array(
            'lastRecommend'=>$lastRecommend,
            'newsRecommend'=>$newsRecommend,
            'actRecommend'=>$actRecommend,
            'slide'=>$slide,
            'lastArticle'=>$lastArticle,
            'newsArticle'=>$newsArticle,
            'actArticle'=>$actArticle,
            'video'=>$video,
            'origPicture'=>$origPicture,
            'screenShot'=>$screenShot,
        );
        return $list;
    }

    // 文章列表
    public function articleList($param,$gameId,$count,$page){
        $category = $param['category'];
        $options['table'] = 'ty_web_article';
        $options['field'] = 'article_id,title,add_time';
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
        $list = $this->timeFormat($list);
        //print_r($list);exit;

        $categoryName = $this->getCategoryName($category);
        $data=array('totalPage'=>$totalPage,'count'=>count($list),'page'=>$page,'list'=>$list,'categoryName'=>$categoryName);
        return $this->returnResult(200,$data);
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
        $content = preg_replace('/<\/?[^>]+>/i','',$article['content']);
        $description =  mb_substr($content,0,100);              // 文章内容截取
        $categoryName = $this->getCategoryName($article['category_id']);
        $list = array(
            'article'=>$article,
            'description'=>$description,
            'categoryName'=>$categoryName,
        );
        return $list;
    }

    // 礼包列表
    public function giftList($param,$gameId,$url,$count,$page){
        $giftName = $param['gift_name'];
        $options['table'] = 'ty_web_gift';
        $options['where'] = array('is_delete'=>'?','game_id'=>'?');
        $options['param'] = array('0',$gameId);
        if(!empty($giftName)){
            $options['where'] = array_merge($options['where'],array('gift_name'=>array('LIKE','?')));
            $options['param'] = array_merge($options['param'],array("%$giftName%"));
        }
        $options['order'] = 'add_time desc';
        $options['limit'] = ($page-1)*$count.','.$count;
        $totalNum=$this->db->count($options);
        $totalPage=ceil($totalNum/$count);
        $list = $this->db->select($options);
        //print_r($list);exit;
        if(!empty($list)){
            foreach($list as $k=>$v){
                $list[$k]['gift_img'] = $url.$v['gift_img'];
            }
            $data=array('totalPage'=>$totalPage,'count'=>count($list),'page'=>$page,'list'=>$list);
            return $this->returnResult(200,$data);
        }else {
            return $this->returnResult(201);
        }
    }

    // 领取礼包兑换码操作
    public function opReceiveCode($param){
        $giftId = $param['gift_id'];
        $userIp = $param['user_ip'];
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
        $options1['where'] = array('user_ip'=>'?','gift_id'=>'?');
        $options1['param'] = array($userIp,$giftId);
        $receiveCodeCount = $this->db->count($options1);       // 该ip领过多少次该礼包
        //echo $receiveCodeCount;exit;
        if(bccomp($receiveCodeCount,$giftLimit)!=-1){           // 领次数不小于限制
            return $this->returnResult(201,"你已经领过{$giftLimit}次该礼包了！");
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

            $tmpData4 = array('user_ip'=>'?','exchange_code_id'=>'?','receive_time'=>'?','gift_id'=>'?');
            $options4['table'] = 'ty_web_receive_code';
            $options4['param'] = array($userIp,$exchangeCodeData['exchange_code_id'],time(),$giftId);
            $receiveRes = $this->db->add($tmpData4,$options4);                  // receive_code表添加领取数据
            //echo $exchangeCodeData['exchange_code_id'];exit;

            $tmpData5 = array('gift_num'=>'?');
            $options5['table'] = 'ty_web_gift';
            $options5['where'] = array('gift_id'=>'?');
            $options5['param'] = array(bcsub($giftNum,1),$giftId);
            $giftRes = $this->db->save($tmpData5,$options5);                    // gift表兑换码数量减1

            if($exchangeCodeRes!==FALSE && $receiveRes!==FALSE && $giftRes!==FALSE){
                $this->db->commit();
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

    // 首页文章时间格式转化
    public function timeFormat($data){
        if(!empty($data) && is_array($data)){
            foreach($data as $k=>$v){
                $dimension = is_array($v) ? 2:1;
            }
            if($dimension===1) {
                $data['add_time'] = date('m/d', $data['add_time']);
            }else{
                foreach($data as $k=>$v){
                    $data[$k]['add_time'] = date('m/d',$v['add_time']);
                }
            }
            return $data;
        }
    }

    // 获取分类名称
    public function getCategoryName($category='latest'){
        $result = array('1'=>'媒体新闻', '2'=>'活动', '3'=>'新手攻略', '4'=>'高手进阶', '5'=>'特色玩法', '6'=>'玩家风采', 'latest'=>'最新文章');
        if(isset($result[$category])){
            return $result[$category];
        }else{
            return '最新文章';
        }
    }


}