<?php

/**
 * Created by PhpStorm.
 * User: pc
 * Date: 2016/10/26
 * Time: 15:41
 */
class GameController extends \Core\BaseControllers
{

    public function init()
    {
        parent::init();
    }
    public function serversAction()
    {
        $gameId = isset($this->_getData['gameid']) ? (int)$this->_getData['gameid'] : 0;
        if(!$gameId){
            $msgDzz1 = $this->dzzServer(111141);
            $msgDzz2 = $msgDzz1;
            $msgDzz3 = $msgDzz1;
            $msgDzz1['game_name'] =  '大主宰(IOS)';
            $msgDzz2['game_name'] =  '大主宰(安卓)';
            $msgDzz3['game_name'] =  '大主宰(PC)';

            $msg['111141'] = $msgDzz1;
            $msg['111142'] = $msgDzz2;
            $msg['111143'] = $msgDzz3;
        }
        else{
            $msg[$gameId]  = $this->dzzServer($gameId);;
        }
        $return = array('code' => 1, "msg" => $msg);
        echo json_encode($return);
    }


    private function dzzServer($gameId){
        $lastId = substr($gameId,-1,1) ;
        switch  ($lastId){
            case 1:
                $return['game_name'] = '大主宰(IOS)';
                break;
            case 2:
                $return['game_name'] = '大主宰(安卓)';
                break;
            case 3:
                $return['game_name'] = '大主宰(PC)';
                break;
        }
        $model = new \Web\DzzModel();
        if ($_servers = $model->getServerList()) {
            foreach ($_servers as $v) {
                $return['data'][] =
                    array(
                        'server_id' => $v['serverid'],
                        'server_name' => $v['name'],
                        'open_time' => isset($v['opentime']) ? $v['opentime'] : '',
                    );
            }
        }
        return $return;
    }

    public function indexAction(){

    }

    /*
        请求补丁包接口
        需要通过post方式发送三个参数
        $version sdk的版本号
        $sdkType sdk的类型(android 或者 IOS)
        $debug 是否请求测试包(1,测试，0,非测试);
        请求示例:
            http://mytest.wanyouxi.com/api/game/patchload
        返回示例:
            {"code":200,"message":"success","data":{"sdk_version":"2.3.1","patch_version":"2.3.1.3","patch_url":"http:\/\/tfstatic.cn-bj.ufileos.com\/php_redis.dll"}}
    */
    public function patchloadAction() {
        if (empty($this->_postData['version']) || empty($this->_postData['sdkType']) ||
             ($this->_postData['debug']==null || $this->_postData['debug']==""))
        {
            $res = array('code'=> '4300','message' =>'Parameter Error');
        } else {
            $param['sdk_version'] = $this->_postData['version'];
            if(strtoupper($this->_postData['sdkType']) == 'ANDROID')
                $param['sdk_type'] = 1;
            else if(strtoupper($this->_postData['sdkType'])== 'IOS')
                $param['sdk_type'] = 2;
            else{
                $res = array('code'=> '4300','message' =>'Parameter Error');
                echo json_encode($res);
                die;
            }
            $param['debug'] = (intval($this->_postData['debug'])==1)? TRUE : FALSE;
            $model= new \Web\DzzModel();
            $data = $model->patchload($param);
            if($data['code'] == 200) {
                $res['code'] = 200;
                $res['message'] = 'success';
                $res['data'] = $data['data'];
            } else {
                $res['code'] = 201;
                $res['message'] = 'Not Have Any Data';
            }
        }
        echo json_encode($res);
    }




}
