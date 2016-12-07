<?php
namespace Web;
class MtyModel extends \Core\BaseModels {

    // 文章列表数据
    public function articleData($param,$url){
        $options['table'] = 'ty_web_tianyu_article';
        $options['where'] = array('is_delete'=>'?');
        $options['param'] = array('0');
        $options['order'] = 'add_time desc';
        $options['limit'] = $param['last'].','.$param['amount'];
        $data = $this->db->select($options);
        if(!empty($data)){
            foreach($data as $k=>$v){
                $content = htmlspecialchars_decode($v['content']);
                $content = preg_replace("/<\/?[^>]+>/i",'',$content);   // 去掉html标签
                $data[$k]['content'] = $this->cutStr($content,100);
                $data[$k]['article_img'] = $url.$v['article_img'];
                $data[$k]['add_time'] = date('Y.m.d',$v['add_time']);
            }
            foreach($data as $k=>$v){
                $data[$k]['left'] = "<a href='/web/mty/articledetail?articleId={$v['article_id']}'><img src='{$v['article_img']}' width='179' height='106'/></a>";
                $data[$k]['right'] = "<a href='/web/mty/articledetail?articleId={$v['article_id']}'><p>{$v['title']}</p></a><span>{$v['add_time']}</span>";
                $data[$k]['bottom'] = "<p>{$v['content']}</p>";
            }
            return $this->returnResult(200,$data);
        }else {
            return $this->returnResult(201);
        }
    }

    // 首页文章推荐截取汉字
    public function cutStr($str,$length){
        $res = mb_substr($str,0,$length,'utf-8');
        $data = (strlen($res)==strlen($str)) ? $res:$res.'...';
        return $data;
    }

    // 文章详情页
    public function articleDetail($param,$url){
        $options['table'] = 'ty_web_tianyu_article';
        $options['where'] = array('article_id'=>'?');
        $options['param'] = array($param['article_id']);
        $data = $this->db->find($options);
        //print_r($data);exit;
        if(!empty($data)){
            $content = preg_replace('/src=&quot;/','src=&quot;'.$url,$data['content']);
            $data['content'] = htmlspecialchars_decode($content);             // 内容转码
            $data['article_img'] = $url.$data['article_img'];
            $data['add_time'] = date('Y.m.d',$data['add_time']);
            return $this->returnResult(200,$data);
        }else {
            return $this->returnResult(201);
        }
    }


}