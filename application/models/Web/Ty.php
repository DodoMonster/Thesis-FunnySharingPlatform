<?php
namespace Web;
class TyModel extends \Core\BaseModels {

    // 首页
    public function home($url){
        $options['table'] = 'game';
        $options['where'] = array('game_name'=>'?');
        $options['param'] = array('奇迹战神');
        $qjzs = $this->db->find($options);
        $options1['table'] = 'game';
        $options1['where'] = array('game_name'=>'?');
        $options1['param'] = array('大主宰');
        $dzz = $this->db->find($options1);
        $options2['table'] = 'game';
        $options2['where'] = array('game_name'=>'?');
        $options2['param'] = array('暗黑奇迹');
        $ahqj = $this->db->find($options2);

        $options4['table'] = 'ty_web_tianyu_article';
        $options4['where'] = array('is_delete'=>'?');
        $options4['order'] = 'add_time desc';
        $options4['limit'] = '5';
        $options4['param'] = array('0');
        $article = $this->db->select($options4);
        //print_r($article);exit;
        if(!empty($article)){
            foreach($article as $k=>$v){
                $content = htmlspecialchars_decode($v['content']);
                $content = preg_replace("/<\/?[^>]+>/i",'',$content);
                $article[$k]['content'] = $this->cutStr($content,60);
                $article[$k]['article_img'] = $url.$v['article_img'];
                $article[$k]['add_time'] = date('Y.m.d',$v['add_time']);
            }
        }
        $isMobile = $this->isMobile();

        $list = array(
            'qjzs'=>$qjzs,
            'dzz'=>$dzz,
            'ahqj'=>$ahqj,
            'article'=>$article,
            'isMobile'=>$isMobile,
        );
        return $list;
    }

    // 添加点赞次数操作
    public function opAddLikeTimes($param){
        $gameId = $param['game_id'];
        if($gameId){
            $options['table'] = 'game';
            $options['where'] = array('game_id'=>'?');
            $options['param'] = array($gameId);
            $game = $this->db->find($options);
            //print_r($game);exit;
            if(!empty($game)){
                $num = $game['like_times']+1;
                $tmpData = array('like_times'=>'?');
                $options1['table'] = 'game';
                $options1['where'] = array('game_id'=>'?');
                $options1['param'] = array($num,$gameId);
                $gid = $this->db->save($tmpData,$options1);
                if($gid!=FALSE) {
                    return $this->returnResult(200,$num);
                }
            }
        }
        return $this->returnResult(4000);
    }

    // 文章列表
    public function articleList($param,$url,$count,$page){
        $recommend = $param['recommend'];
        $options['table'] = 'ty_web_tianyu_article';
        $options['where'] = array('is_delete'=>'?');
        $options['param'] = array('0');
        if(!empty($recommend)){
            $options['where'] = array_merge($options['where'],array('is_recommend'=>'?'));
            $options['param'] = array_merge($options['param'],array('1'));
        }
        $options['order'] = 'add_time desc';
        $options['limit'] = ($page-1)*$count.','.$count;
        $totalNum=$this->db->count($options);
        $totalPage=ceil($totalNum/$count);
        $article = $this->db->select($options);
        //print_r($data);exit;
        if(!empty($article)){
            foreach($article as $k=>$v){
                $content = htmlspecialchars_decode($v['content']);
                $content = preg_replace("/<\/?[^>]+>/i",'',$content);
                $article[$k]['content'] = $this->cutStr($content,120);
                $article[$k]['article_img'] = $url.$v['article_img'];
                $article[$k]['add_time'] = date('Y.m.d',$v['add_time']);
            }
            $data=array('totalPage'=>$totalPage,'count'=>count($article),'page'=>$page,'list'=>$article);
            return $this->returnResult(200,$data);
        }else{
            return $this->returnResult(201);
        }
    }

    // 文章详情页
    public function articleDetail($param,$url){
        $articleId = $param['article_id'];
        $options['table'] = 'ty_web_tianyu_article';
        $options['where'] = array('article_id'=>'?');
        $options['param'] = array($articleId);
        $article = $this->db->find($options);
        //print_r($article);exit;
        if(!empty($article)){
            $article['content'] = preg_replace('/src=&quot;/','src=&quot;'.$url,$article['content']);
            $article['content'] = htmlspecialchars_decode($article['content']);             // 内容转码
            $article['add_time'] = date('Y.m.d',$article['add_time']);
        }

        $options1['table'] = 'ty_web_tianyu_article';
        $options1['field'] = 'article_id,title';
        $options1['where'] = array('is_delete'=>'?','is_recommend'=>'?');
        $options1['param'] = array('0','1');
        $options1['order'] = 'add_time desc';
        $options1['limit'] = '2';
        $recommendArticle = $this->db->select($options1);
        //print_r($recommendArticle);exit;
        if(!empty($recommendArticle)){
            foreach($recommendArticle as $k=>$v){
                $recommendArticle[$k]['title'] = $this->cutStr($v['title'],12);
            }
        }
        $list = array('article'=>$article,'recommend_article'=>$recommendArticle);
        return $list;
    }

    // 了解天豫
    public function about(){
        $isMobile = $this->isMobile();

        $list = array('isMobile'=>$isMobile,);
        return $list;
    }

    // 首页文章推荐截取汉字
    public function cutStr($str,$length){
        $res = mb_substr($str,0,$length,'utf-8');
        $data = (strlen($res)==strlen($str)) ? $res:$res.'...';
        return $data;
    }

    // 检测是否手机访问，是
    function isMobile(){
        $ua = strtolower($_SERVER['HTTP_USER_AGENT']);
        $uachar = "/(blackberry|configuration\/cldc|hp |hp-|htc |htc_|htc-|iemobile|kindle|midp|mmp|motorola|mobile|nokia|opera mini|opera |Googlebot-Mobile|YahooSeeker\/M1A1-R2D2|android|iphone|ipod|mobi|palm|palmos|pocket|portalmmm|ppc;|smartphone|sonyericsson|sqh|spv|symbian|treo|up.browser|up.link|vodafone|windows ce|xda |xda_)/i";
        if($ua == '' || preg_match($uachar, $ua)){      // 手机访问
            return 1;
        }else {
            return 0;
        }
    }


}