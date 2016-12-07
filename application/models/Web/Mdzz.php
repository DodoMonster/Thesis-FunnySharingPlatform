<?php
namespace Web;
class MdzzModel extends \Core\BaseModels {

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

    // 首页处理文章标题、时间
    public function returnArticle($data){
        if(!empty($data) && is_array($data)){
            foreach($data as $k=>$v){
                //$data[$k]['title'] = $this->cutStr($v['title'],30);
                $data[$k]['add_time'] = date('m/d',$v['add_time']);
            }
        }
        return $data;
    }


    public function getShortCategoryName($category){
        $result = array('7'=>'新闻','8'=>'活动','9'=>'公告','10'=>'新手','11'=>'高手','12'=>'特色');
        return $result[$category];
    }

    public function getCategoryName($category){
        $result = array('7'=>'媒体新闻','8'=>'活动','9'=>'公告','10'=>'新手攻略','11'=>'高手进阶','12'=>'特色玩法','13'=>'玩家风采','latest'=>'综合');
        return $result[$category];
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


    // 增加的
    // 首页
    public function home1($gameId,$url){
        $options['table'] = 'ty_web_slide';
        $options['where'] = array('is_delete'=>'?','game_id'=>'?','is_mobile'=>'?');
        $options['order'] = 'sort desc';
        $options['param'] = array('0',$gameId,'1');
        $slide = $this->db->select($options);          // 热门活动幻灯片
        if(!empty($slide)){
            foreach($slide as $k=>$v){
                $slide[$k]['slide_img'] = $url.$v['slide_img'];
            }
        }

        $options1['table'] = 'ty_web_article';
        $options1['field'] = 'article_id,title,add_time';
        $options1['where'] = array('is_delete'=>'?','game_id'=>'?','category_id'=>array('IN',array('7','8','9')));
        $options1['order'] = 'add_time desc';
        $options1['limit'] = '5';
        $options1['param'] = array('0',$gameId,'7','8','9');
        $lastArticle = $this->db->select($options1);        // 最新
        $lastArticle = $this->returnArticle1($lastArticle);
        //print_r($lastArticle);exit;

        $options2['table'] = 'ty_web_article';
        $options2['field'] = 'article_id,title,add_time';
        $options2['where'] = array('is_delete'=>'?','game_id'=>'?','category_id'=>'?');
        $options2['order'] = 'add_time desc';
        $options2['limit'] = '5';
        $options2['param'] = array('0',$gameId,'7');
        $newsArticle = $this->db->select($options2);        // 新闻
        $newsArticle = $this->returnArticle1($newsArticle);

        $options3['table'] = 'ty_web_article';
        $options3['field'] = 'article_id,title,add_time';
        $options3['where'] = array('is_delete'=>'?','game_id'=>'?','category_id'=>'?');
        $options3['order'] = 'add_time desc';
        $options3['limit'] = '5';
        $options3['param'] = array('0',$gameId,'8');
        $activeArticle = $this->db->select($options3);        // 活动
        $activeArticle = $this->returnArticle1($activeArticle);

        $options4['table'] = 'ty_web_article';
        $options4['field'] = 'article_id,title,add_time';
        $options4['where'] = array('is_delete'=>'?','game_id'=>'?','category_id'=>array('IN',array('10','11')));
        $options4['order'] = 'add_time desc';
        $options4['limit'] = '5';
        $options4['param'] = array('0',$gameId,'10','11');
        $guideArticle = $this->db->select($options4);        // 游戏攻略
        $guideArticle = $this->returnArticle1($guideArticle);
        //print_r($guideArticle);exit;

        $list = array(
            'slide'=>$slide,
            'lastArticle'=>$lastArticle,
            'newsArticle'=>$newsArticle,
            'activeArticle'=>$activeArticle,
            'guideArticle'=>$guideArticle,
        );
        return $list;
    }

    /*// 首页处理文章标题、时间
    public function returnArticle1($data,$length){
        if(!empty($data) && is_array($data)){
            foreach($data as $k=>$v){
                $data[$k]['title'] = $this->cutStr($v['title'],$length);
                $data[$k]['add_time'] = date('m/d',$v['add_time']);
            }
        }
        return $data;
    }
    // 首页处理文章标题、时间*/
    public function returnArticle1($data){
        if(!empty($data) && is_array($data)){
            foreach($data as $k=>$v){
                //$data[$k]['title'] = $this->cutStr($v['title'],$length);
                $data[$k]['add_time'] = date('m/d',$v['add_time']);
            }
        }
        return $data;
    }

    // 分类列表
    public function categoryList1($param,$gameId,$count,$page){
        $category = $param['category'];
        $options['table'] = 'ty_web_article';
        $options['field'] = 'article_id,title,add_time';
        $options['where'] = array('is_delete'=>'?','game_id'=>'?');
        $options['param'] = array('0',$gameId);
        if(!empty($category)){
            if($category=='guide'){
                $guideIdArr = array('10','11');
                $options['where'] = array_merge($options['where'],array('category_id'=>array('IN',$guideIdArr)));
                $options['param'] = array_merge($options['param'],$guideIdArr);
            }else if($category=='8'){
                $options['where'] = array_merge($options['where'],array('category_id'=>'?'));
                $options['param'] = array_merge($options['param'],array($category));
            }
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

    // 新增的2
    // 首页
    public function home($gameId,$url){
        $options['table'] = 'ty_web_slide';
        $options['where'] = array('is_delete'=>'?','game_id'=>'?','is_mobile'=>'?');
        $options['order'] = 'sort desc';
        $options['param'] = array('0',$gameId,'1');
        $slide = $this->db->select($options);          // 热门活动幻灯片
        if(!empty($slide)){
            foreach($slide as $k=>$v){
                $slide[$k]['slide_img'] = $url.$v['slide_img'];
            }
        }

        $options5['table'] = 'ty_web_article';
        $options5['field'] = 'article_id,title,add_time';
        $options5['where'] = array('category_id'=>'?','is_recommend'=>'?','game_id'=>'?','is_delete'=>'?');
        $options5['order'] = 'add_time desc';
        $options5['param'] = array('7','1',$gameId,'0');
        $newsRecommend = $this->db->find($options5);        // 新闻最后那个推荐
        if(empty($newsRecommend)){
            $options5['where'] = array('category_id'=>'?','game_id'=>'?','is_delete'=>'?');
            $options5['param'] = array('7',$gameId,'0');
            $newsRecommend = $this->db->find($options5);        // 新闻最新一条
        }
        //print_r($newsRecommend);exit;

        $options6['table'] = 'ty_web_article';
        $options6['field'] = 'article_id,title,add_time';
        $options6['where'] = array('category_id'=>'?','is_recommend'=>'?','game_id'=>'?','is_delete'=>'?');
        $options6['order'] = 'add_time desc';
        $options6['param'] = array('9','1',$gameId,'0');
        $noticeRecommend = $this->db->find($options6);        // 公告最后那个推荐
        if(empty($noticeRecommend)){
            $options6['where'] = array('category_id'=>'?','game_id'=>'?','is_delete'=>'?');
            $options6['param'] = array('9',$gameId,'0');
            $noticeRecommend = $this->db->find($options6);        // 公告最后一条
        }

        $options7['table'] = 'ty_web_article';
        $options7['field'] = 'article_id,title,add_time';
        $options7['where'] = array('category_id'=>'?','is_recommend'=>'?','game_id'=>'?','is_delete'=>'?');
        $options7['order'] = 'add_time desc';
        $options7['param'] = array('8','1',$gameId,'0');
        $actRecommend = $this->db->find($options7);        // 活动最后那个推荐
        if(empty($actRecommend)){
            $actRecommend['add_time'] = date('Y-m-d',$actRecommend['add_time']);
            $options7['where'] = array('category_id'=>'?','game_id'=>'?','is_delete'=>'?');
            $options7['param'] = array('8',$gameId,'0');
            $actRecommend = $this->db->find($options7);        // 活动最后一条
        }
        //print_r($actLastRecommend);exit;

        $options8['table'] = 'ty_web_article';
        $options8['field'] = 'article_id,title,add_time';
        $options8['where'] = array('category_id'=>array('IN',array('22','23','24','25')),'is_recommend'=>'?','game_id'=>'?','is_delete'=>'?');
        $options8['order'] = 'add_time desc';
        $options8['param'] = array('22','23','24','25','1',$gameId,'0');
        $guideRecommend = $this->db->find($options8);        // 攻略最后那个推荐
        if(empty($guideRecommend)){
            $guideRecommend['add_time'] = date('Y-m-d',$guideRecommend['add_time']);
            $options8['where'] = array('category_id'=>array('IN',array('22','23','24','25')),'game_id'=>'?','is_delete'=>'?');
            $options8['param'] = array('22','23','24','25',$gameId,'0');
            $guideRecommend = $this->db->find($options8);        // 攻略最后一条
        }
        //print_r($guideLastRecommend);exit;

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
        $newsArticle = $this->returnArticle2($newsArticle);

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
        $activeArticle = $this->db->select($options3);        // 活动
        $activeArticle = $this->returnArticle2($activeArticle);

        $options9['table'] = 'ty_web_article';
        $options9['field'] = 'article_id,title,add_time';
        $options9['where'] = array('is_delete'=>'?','game_id'=>'?','category_id'=>'?');
        $options9['order'] = 'add_time desc';
        $options9['limit'] = '8';
        $options9['param'] = array('0',$gameId,'9');
        if(!empty($noticeRecommend)){
            $options9['where'] = array_merge($options9['where'],array('article_id'=>array('NEQ','?')));
            $options9['param'] = array_merge($options9['param'],array($noticeRecommend['article_id']));
        }
        $noticeArticle = $this->db->select($options9);         // 公告
        $noticeArticle = $this->returnArticle2($noticeArticle);

        $options4['table'] = 'ty_web_article';
        $options4['field'] = 'article_id,title,add_time';
        $options4['where'] = array('is_delete'=>'?','game_id'=>'?','category_id'=>array('IN',array('22','23','24','25')));
        $options4['order'] = 'add_time desc';
        $options4['limit'] = '8';
        $options4['param'] = array('0',$gameId,'22','23','24','25');
        if(!empty($guideRecommend)){
            $options4['where'] = array_merge($options4['where'],array('article_id'=>array('NEQ','?')));
            $options4['param'] = array_merge($options4['param'],array($guideRecommend['article_id']));
        }
        $guideArticle = $this->db->select($options4);        // 攻略
        $guideArticle = $this->returnArticle2($guideArticle);
        //print_r($guideArticle);exit;

        $list = array(
            'newsRecommend'=>$newsRecommend,
            'noticeRecommend'=>$noticeRecommend,
            'actRecommend'=>$actRecommend,
            'guideRecommend'=>$guideRecommend,
            'slide'=>$slide,
            'newsArticle'=>$newsArticle,
            'activeArticle'=>$activeArticle,
            'noticeArticle'=>$noticeArticle,
            'guideArticle'=>$guideArticle,
        );
        return $list;
    }

    // 分类列表
    public function categoryList($param,$gameId,$count,$page){
        $category = $param['category'];

        $options1['table'] = 'ty_web_article';
        $options1['field'] = 'article_id,title,add_time';
        $options1['where'] = array('is_recommend'=>'?','game_id'=>'?','is_delete'=>'?');
        $options1['order'] = 'add_time desc';
        $options1['param'] = array('1',$gameId,'0');
        if(!empty($category)){
            if($category=='guide'){
                $guideIdArr = array('22','23','24','25');
                $options1['where'] = array_merge($options1['where'],array('category_id'=>array('IN',$guideIdArr)));
                $options1['param'] = array_merge($options1['param'],$guideIdArr);
            }else{
                $options1['where'] = array_merge($options1['where'],array('category_id'=>'?'));
                $options1['param'] = array_merge($options1['param'],array($category));
            }
        }
        $articleRecommend = $this->db->find($options1);        // 新闻最后那个推荐

        if(empty($articleRecommend)){
            $options2['table'] = 'ty_web_article';
            $options2['field'] = 'article_id,title,add_time';
            $options2['where'] = array('game_id'=>'?','is_delete'=>'?');
            $options2['order'] = 'add_time desc';
            $options2['param'] = array($gameId,'0');
            if(!empty($category)){
                if($category=='guide'){
                    $guideIdArr = array('22','23','24','25');
                    $options2['where'] = array_merge($options2['where'],array('category_id'=>array('IN',$guideIdArr)));
                    $options2['param'] = array_merge($options2['param'],$guideIdArr);
                }else{
                    $options2['where'] = array_merge($options2['where'],array('category_id'=>'?'));
                    $options2['param'] = array_merge($options2['param'],array($category));
                }
            }
            $articleRecommend = $this->db->find($options2);        // 新闻最后那个推荐
        }
        //print_r($articleRecommend);exit;

        $options['table'] = 'ty_web_article';
        $options['field'] = 'article_id,title,add_time';
        $options['where'] = array('is_delete'=>'?','game_id'=>'?');
        $options['param'] = array('0',$gameId);
        if(!empty($category)){
            if($category=='guide'){
                $guideIdArr = array('22','23','24','25');
                $options['where'] = array_merge($options['where'],array('category_id'=>array('IN',$guideIdArr)));
                $options['param'] = array_merge($options['param'],$guideIdArr);
            }else{
                $options['where'] = array_merge($options['where'],array('category_id'=>'?'));
                $options['param'] = array_merge($options['param'],array($category));
            }
        }
        if(!empty($articleRecommend)){
            $options['where'] = array_merge($options['where'],array('article_id'=>array('NEQ','?')));
            $options['param'] = array_merge($options['param'],array($articleRecommend['article_id']));
        }
        $options['limit']=($page-1)*$count.','.$count;
        $options['order'] = 'add_time desc';
        $totalNum=$this->db->count($options);
        $totalPage=ceil($totalNum/$count);
        $list = $this->db->select($options);
        $list = $this->returnArticle2($list);
        //print_r($list);exit;




        $data=array('totalPage'=>$totalPage,'count'=>count($list),'page'=>$page,'list'=>$list,'articleRecommend'=>$articleRecommend);
        return $this->returnResult(200,$data);
    }

    // 文章详情
    public function articleDetail($param,$url){
        $articleId = $param['article_id'];
        $options['table'] = 'ty_web_article';
        $options['where'] = array('article_id'=>'?');
        $options['param'] = array($articleId);
        $article = $this->db->find($options);
        if(!empty($article)){
            $article['add_time'] = date('Y-m-d',$article['add_time']);
            $this->views($articleId,$article['visit_times']);       // 浏览量
        }
        $article['content'] = preg_replace('/src=&quot;/','src=&quot;'.$url,$article['content']);
        $article['content'] = htmlspecialchars_decode($article['content']);             // 内容转码
        $list = array(
            'article'=>$article,
        );
        return $list;
    }

    // 首页处理文章标题、时间*/
    public function returnArticle2($data){
        if(!empty($data) && is_array($data)){
            foreach($data as $k=>$v){
                //$data[$k]['title'] = $this->cutStr($v['title'],$length);
                $data[$k]['add_time'] = date('Y-m-d',$v['add_time']);
            }
        }
        return $data;
    }


}