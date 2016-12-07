<?php
class AdminuploadController extends \Core\BaseControllers {
    //protected $_resourceDomain = '';
    //protected $_cdnDomain = '';
    public function init() {
        parent::init();
        //$this->_resourceDomain = 'http://wanyouxi1.cn-gd.ufileos.com/';
        //$this->_cdnDomain = 'http://wanyouxi1.ufile.ucloud.com.cn/';
    }

    // froala editor 上传图片
    public function imageUploadAction(){
        $model=new Addons\Images\Images();
        $data=$model->uploadFileByUcloud('pic');
        $name = $data['data']['images'][0];
        $response = new StdClass;
        $response->link = REAL_RESOURCE_URL . $name;
        echo stripslashes(json_encode($response));
    }
    
    public function remoteImageUpload($file){
        //$file='http://cdn.wanyouxi.com/Uploads/1460375498_800300679.jpg';
        $model=new Addons\Images\Images();
        $data=$model->remoteFileUploadByUcloud($file);
        return $data;
    }

    // 官网数据库文章表数据转移
    public function addArticleDataAction(){
        $json = file_get_contents(RESOURCE_PATH.'/data/json/article_2.json');
        $decodeData = json_decode($json,true);
        $data = $decodeData['RECORDS'];
        //print_r($data);exit;
        if(!empty($data)){
            foreach($data as $k=>$v){
                $content = preg_replace('/src="\/Uploads/','src="http://cdn.wanyouxi.com/Uploads',$v['content']);   // 加域名
                $content= preg_replace('/title=("[^"]*")/i','',$content);      // 去title
                preg_match_all('/<img.*?src="(.*?)".*?>/is',$content,$res);
                //print_r($res[1]);exit;
                if(!empty($res[1])){        // 所有图片源地址
                    foreach($res[1] as $k1=>$v1){
                        $path = $this->remoteImageUpload($v1);
                        $pattern = '/src="http:\/\/cdn.wanyouxi.com\/Uploads\/ueditor\/image\/[0-9]{8}\/[0-9]{16}width[0-9]{0,5}px\.(jpg|jpeg|png)"/';
                        $content = preg_replace($pattern,'src="'.$path.'"',$content,1);   // 图片新路径代替源地址
                    }
                }
                $data[$k]['content'] = htmlspecialchars($content);
                $data[$k]['category_id'] = $this->getCategoryId($v['game_id'],$v['category']);      // category_id
                $data[$k]['game_id'] = $v['game_id']=='1'?'11111':($v['game_id']=='2'?'11112':($v['game_id']=='3'?'11114':''));     // game_id
            }
            //print_r($data);exit;
            $values = '';
            foreach($data as $k=>$v){
                $values .= "('".$v['article_id']."','".$v['title']."','".$v['content']."','".$v['add_time']."','".$v['visit_times']."','".$v['is_delete']."','".$v['game_id']."','".$v['is_recommend']."','".$v['category_id']."'),";
            }
            $values = substr($values,0,-1);
            $sql = 'INSERT INTO  `ty_web_article` (`article_id`,`title`,`content`,`add_time`,`visit_times`,`is_delete`,`game_id`,`is_recommend`,`category_id`) VALUES '.$values;
            $model = new \Admin\AdminIndexModel();
            $res = $model->opInsertData($sql);
            print_r($res);
        }
    }

    // 幻灯片表数据转移(slide_url未处理)
    public function addSlideDataAction(){
        $json = file_get_contents(RESOURCE_PATH.'/data/json/slide.json');
        $decodeData = json_decode($json,true);
        $data = $decodeData['RECORDS'];
        //print_r($data);exit;
        if(!empty($data)){
            foreach($data as $k=>$v){
                $slideImg = 'http://cdn.wanyouxi.com/'.$v['slide_img'];
                $data[$k]['slide_img'] = $this->remoteImageUpload($slideImg);
                $data[$k]['game_id'] = $v['game_id']=='1'?'11111':($v['game_id']=='2'?'11112':($v['game_id']=='3'?'11114':''));     // game_id
            }
            $values = '';
            foreach($data as $k=>$v){
                $values .= "('".$v['slide_id']."','".$v['slide_img']."','".$v['slide_url']."','".$v['add_time']."','".$v['sort']."','".$v['is_delete']."','".$v['game_id']."','".$v['is_mobile']."'),";
            }
            $values = substr($values,0,-1);
            //echo $values;exit;
            $sql = 'INSERT INTO  `ty_web_slide` (`slide_id`,`slide_img`,`slide_url`,`add_time`,`sort`,`is_delete`,`game_id`,`is_mobile`) VALUES '.$values;
            $model = new \Admin\AdminIndexModel();
            $res = $model->opInsertData($sql);
            print_r($res);
        }
    }

    // 礼包表，兑换码表，领取兑换码表数据转移
    public function addGiftDataAction(){
        $giftJson = file_get_contents(RESOURCE_PATH.'/data/json/gift.json');
        $decodeGiftData = json_decode($giftJson,true);
        $giftData = $decodeGiftData['RECORDS'];
        $codeJson = file_get_contents(RESOURCE_PATH.'/data/json/exchange_code.json');
        $decodeCodeData = json_decode($codeJson,true);
        $codeData = $decodeCodeData['RECORDS'];
        $receiveJson = file_get_contents(RESOURCE_PATH.'/data/json/receive_code.json');
        $decodeReceiveData = json_decode($receiveJson,true);
        $receiveData = $decodeReceiveData['RECORDS'];
        $model = new \Admin\AdminIndexModel();
        //print_r($giftData);exit;
        if(!empty($giftData)){
            foreach($giftData as $k=>$v){
                $giftImg = 'http://cdn.wanyouxi.com/'.$v['gift_img'];
                $giftData[$k]['gift_img'] = $this->remoteImageUpload($giftImg);
                $giftData[$k]['game_id'] = $v['game_id']=='1'?'11111':($v['game_id']=='2'?'11112':($v['game_id']=='3'?'11114':''));     // game_id
            }
            $giftValues = '';
            foreach($giftData as $k=>$v){
                $giftValues .= "('".$v['gift_id']."','".$v['gift_name']."','".$v['content']."','".$v['instruction']."','".$v['gift_img']."','".$v['gift_num']."','".$v['add_time']."','".$v['gift_limit']."','".$v['game_id']."','".$v['is_delete']."'),";
            }
            $giftValues = substr($giftValues,0,-1);
            $giftSql = 'INSERT INTO  `ty_web_gift` (`gift_id`,`gift_name`,`content`,`instruction`,`gift_img`,`gift_num`,`add_time`,`gift_limit`,`game_id`,`is_delete`) VALUES '.$giftValues;
            $giftRes = $model->opInsertData($giftSql);
        }
        if(!empty($codeData)){
            $codeValues = '';
            foreach($codeData as $k=>$v){
                $codeValues .= "('".$v['exchange_code_id']."','".$v['exchange_code']."','".$v['gift_id']."','".$v['is_receive']."','".$v['add_time']."'),";
            }
            $codeValues = substr($codeValues,0,-1);
            $codeSql = 'INSERT INTO  `ty_web_exchange_code` (`exchange_code_id`,`exchange_code`,`gift_id`,`is_receive`,`add_time`) VALUES '.$codeValues;
            $codeRes = $model->opInsertData($codeSql);
        }
        if(!empty($receiveData)){
            $receiveValues = '';
            foreach($receiveData as $k=>$v){
                $receiveValues .= "('".$v['receive_code_id']."','".$v['user_ip']."','".$v['exchange_code_id']."','".$v['receive_time']."','".$v['gift_id']."'),";
            }
            $receiveValues = substr($receiveValues,0,-1);
            $receiveSql = 'INSERT INTO  `ty_web_receive_code` (`receive_code_id`,`user_ip`,`exchange_code_id`,`receive_time`,`gift_id`) VALUES '.$receiveValues;
            $receiveRes = $model->opInsertData($receiveSql);
        }
        print_r(array($giftRes,$codeRes,$receiveRes));
    }

    // 素材表数据转移
    public function addMaterialDataAction(){
        $json = file_get_contents(RESOURCE_PATH.'/data/json/material_2.json');
        $decodeData = json_decode($json,true);
        $data = $decodeData['RECORDS'];
        //print_r($data);exit;
        if(!empty($data)){
            foreach($data as $k=>$v){
                $thumbImg = 'http://cdn.wanyouxi.com/'.$v['thumb_img'];
                $data[$k]['thumb_img'] = $this->remoteImageUpload($thumbImg);
                if($v['material_url']){
                    $materialUrl = 'http://cdn.wanyouxi.com/'.$v['material_url'];
                    $data[$k]['material_url'] = $this->remoteImageUpload($materialUrl);
                }
                $data[$k]['game_id'] = $v['game_id']=='1'?'11111':($v['game_id']=='2'?'11112':($v['game_id']=='3'?'11114':''));     // game_id
            }
            $values = '';
            foreach($data as $k=>$v){
                $values .= "('".$v['material_id']."','".$v['thumb_img']."','".$v['material_url']."','".$v['outside_url']."','".$v['category']."','".$v['add_time']."','".$v['is_delete']."','".$v['game_id']."'),";
            }
            $values = substr($values,0,-1);
            //echo $values;exit;
            $sql = 'INSERT INTO  `ty_web_material` (`material_id`,`thumb_img`,`material_url`,`outside_url`,`category`,`add_time`,`is_delete`,`game_id`) VALUES '.$values;
            $model = new \Admin\AdminIndexModel();
            $res = $model->opInsertData($sql);
            print_r($res);
        }
    }

    // 天豫官网文章表数据转移
    public function addTianyuArticleDataAction(){
        $json = file_get_contents(RESOURCE_PATH.'/data/json/tianyu_article.json');
        $decodeData = json_decode($json,true);
        $data = $decodeData['RECORDS'];
        //print_r($data);exit;
        if(!empty($data)){
            foreach($data as $k=>$v){
                $content = preg_replace('/src="\/Uploads/','src="http://cdn.wanyouxi.com/Uploads',$v['content']);   // 加域名
                $content= preg_replace('/title=("[^"]*")/i','',$content);      // 去title
                preg_match_all('/<img.*?src="(.*?)".*?>/is',$content,$res);
                //print_r($res[1]);exit;
                if(!empty($res[1])){        // 所有图片源地址
                    foreach($res[1] as $k1=>$v1){
                        $path = $this->remoteImageUpload($v1);
                        $pattern = '/src="http:\/\/cdn.wanyouxi.com\/Uploads\/ueditor\/image\/[0-9]{8}\/[0-9]{16}width[0-9]{0,5}px\.(jpg|jpeg|png)"/';
                        $content = preg_replace($pattern,'src="'.$path.'"',$content,1);   // 图片新路径代替源地址
                    }
                }
                $data[$k]['content'] = htmlspecialchars($content);
                $articleImg = 'http://cdn.wanyouxi.com/'.$v['article_img'];
                $data[$k]['article_img'] = $this->remoteImageUpload($articleImg);
            }
            //print_r($data);exit;
            $values = '';
            foreach($data as $k=>$v){
                $values .= "('".$v['article_id']."','".$v['title']."','".$v['content']."','".$v['article_img']."','".$v['is_recommend']."','".$v['add_time']."','".$v['is_delete']."'),";
            }
            $values = substr($values,0,-1);
            $sql = 'INSERT INTO  `ty_web_tianyu_article` (`article_id`,`title`,`content`,`article_img`,`is_recommend`,`add_time`,`is_delete`) VALUES '.$values;
            $model = new \Admin\AdminIndexModel();
            $res = $model->opInsertData($sql);
            print_r($res);
        }
    }

    // 获取文章分类id
    public function getCategoryId($gameId,$cName){
        $categoryId='';
        switch($gameId){
            case '1':
                $categoryId=$cName=='news'?'1':($cName=='activity'?'2':($cName=='jackaroo'?'3':($cName=='advanced'?'4':($cName=='feature'?'5':($cName=='elegant'?'6':'')))));
                break;
            case '2':
                $categoryId=$cName=='news'?'7':($cName=='activity'?'8':($cName=='notice'?'9':($cName=='jackaroo'?'10':($cName=='advanced'?'11':($cName=='feature'?'12':($cName=='elegant'?'13':''))))));
                break;
            case '3':
                $categoryId=$cName=='news'?'14':($cName=='activity'?'15':($cName=='notice'?'16':($cName=='jackaroo'?'17':($cName=='advanced'?'18':($cName=='feature'?'19':($cName=='elegant'?'20':''))))));
                break;
        }
        return $categoryId;
    }
}

