<?php
class AdminindexController extends \Core\BaseControllers {
    
    private $_pageUri='/admin/adminindex/';
    protected $_gameInfo=[];
    protected $_gameArray=[];
    protected $_adminInfo=[];
    protected $_menuIds=[];//菜单ids
    protected $_channelIds=[];//渠道ids
    protected $_productIds=[];//游戏ids
    protected $_columnIds=[];//单日报表列名
    protected $_allColumnIds=['newAccountNum'=>'新增用户','newDeviceNum'=>'新增设备','activeAccountNum'=>'活跃用户数','newAccountRoleNum'=>'新用户创角数','totalAccountRoleNum'=>'总创角数','newAccountPayNum'=>'新付费用户数','totalAccountPayNum'=>'总付费用户数','newAccountSumPayMoney'=>'新用户充值额','totalAccountPayMoney'=>'总充值额','newAccountPayRate'=>'新用户付费率','activePayRate'=>'活跃付费率','payARPU'=>'付费ARPU值','activeARPU'=>'活跃ARPU值','registerARPU'=>'注册ARPU值'];
    
    public function init() {
        parent::init();
        $this->getView()->assign('_aid', $this->_aid);
        $this->getView()->assign('title', '后台管理系统');
        $this->getView()->assign('keywords', '');
        $this->getView()->assign('description', '');
        $this->getView()->assign('navbarBrand', '后台管理系统');
        if($this->_aid<=0){
            $this->error404Action();
        }
        $this->_adminInfo['aid']= $this->_aid;
        $this->_adminInfo['aid_level']= $this->_aidLevel;
        $model2= new \Admin\AdminSystemModel();
        $sysRole=$model2->sysRole($this->_aidLevel);
        if(empty($sysRole)){
            $this->error404Action();
        }
        $this->_menuIds=$sysRole['menus'];
        $this->_productIds=$sysRole['products'];
        $this->_channelIds=$sysRole['channels'];
        $this->_columnIds=$sysRole['columns'];
        $this->_adminInfo['aid_role_name']= $sysRole['role_name'];
        //菜单查询
        $model1= new \Admin\AdminSystemModel();
        $tmp=$model1->sysMenu($this->_menuIds);
        //print_r($tmp);exit;
        $sysMenus=$tmp[0];
        $pageTypes=$tmp[1];
        //可访问页过滤
        if($this->_aid>0 && (!in_array($this->_action,array_keys($pageTypes)) && $this->_action!=strtolower('opSetGameSession'))){
            $this->error404Action();
        }
               
        // 游戏查询
        $defaultGame=[];
        $model = new \Admin\AdminIndexModel();     
        $gameList = $model->gameList($this->_productIds,$this->_count);
        foreach ($gameList['data']['list'] as $v){
            $gameArray[$v['game_id']] =$v;
            if($v['game_id']==11114){
                $defaultGame=$v;
            }
        }
        if(empty($defaultGame)){
            $defaultGame=$gameList['data']['list'][0];
        }
        // 设置默认游戏
        if(empty($_SESSION['game']['game_id'])){
            $_SESSION['game']['game_id'] = $defaultGame['game_id'];
            $_SESSION['game']['game_name'] = $defaultGame['game_name'];
            $_SESSION['game']['game_table_name'] = $defaultGame['game_table_name'];
            $_SESSION['game']['package_name'] = $defaultGame['package_name'];
        }elseif(!empty($this->_postData['game_id']) && !empty($this->_postData['game_name'])){
            $_SESSION['game']['game_id']=$this->_postData['game_id'];
            $_SESSION['game']['game_name']=$this->_postData['game_name'];
            $_SESSION['game']['game_table_name']=$gameArray[$this->_postData['game_id']]['game_table_name'];
            $_SESSION['game']['package_name'] = $gameArray[$this->_postData['game_id']]['package_name'];
        }
        $this->_gameArray=$gameArray;
        $this->_gameInfo=$_SESSION['game'];
        
        $this->getView()->assign('adminInfo',$this->_adminInfo);
        $this->getView()->assign('gameName',$_SESSION['game']['game_name']);
        $this->getView()->assign('gameList',$gameList['data']['list']);
        $this->getView()->assign('sysMenus', $sysMenus);
        $this->getView()->assign('pageTypes', $pageTypes);
        $this->getView()->assign('pageType', $this->_action);
        $this->getView()->assign('pageUri', $this->_pageUri);

    }
        
    //验证用户权限
    private function verifyAdminPrivilege($userLevel,$level){
        return $userLevel==$level?TRUE:FALSE;
    }

    //页面不存在
    public function error404Action(){
        print_r($_SESSION);
        Header("Location:/admin/adminoauth/login");
    }
    
    // 选择游戏，设置游戏id及游戏名SESSION
    public function opSetGameSessionAction(){
        $gameId = isset($this->_postData['game_id']) ? $this->_postData['game_id']:'';
        $gameName = isset($this->_postData['game_name']) ? $this->_postData['game_name']:'';
        $data=array('code'=>200,'message'=>'success','data'=>array('game_id'=>$gameId,'game_name'=>$gameName));
        return $this->returnValue($data);   
    }
    
    //首页
    public function homeAction(){               
        $this->display('admin-index');
    }
    
    // 年日报表
    public function reportYearAction(){
        $gameNames=array();
        foreach ($this->_gameArray as $k=>$v){
            $gameNames[]=$v['game_name'];
        }
        $model=new \Admin\AdminIndexModel();
        $datas = $model->allGamesNewUserNum($this->_gameArray);
        echo "<script> var TITLE='游戏新增用户数年表'; "
                . "var GAMES=".json_encode($gameNames)."; "
                . "var DATAS=".json_encode($datas[0])."; "
                . "var MAX=".$datas[1]."</script>";
        $payDatas = $model->allGameNewPayNum($this->_gameArray);
        echo "<script> var TITLE1='游戏充值额年表'; "
            . "var DATAS1=".json_encode($payDatas[0])."; "
            . "var MAX1=".$payDatas[1]."</script>";

        $channelDatas = $model->gameChannelUserNum($this->_gameInfo);
        echo "<script> var TITLE2='游戏渠道用户数量图'; "
            . "var DATAS2=".json_encode($channelDatas[0])."; "
            . "var CHANNELS=".json_encode($channelDatas[1])."</script>";
        $this->display('admin-index');
    }
    
    //下载统计分析
    public function analyzeDownloadAction(){
        $date = isset($this->_getData['date']) ? $this->_getData['date']:'';
        $thead = ['时间','首页访问数','android下载数','ios下载数','web下载数','下载总数'];
        $model = new \Admin\AdminAnalyzeModel();
        $data = $model->analyzeDownload($this->_gameInfo['game_id'],$date);
        
        $this->getView()->assign('download', $data['data']['web_download']);
        $this->getView()->assign('thead', $thead);
        $this->getView()->assign('results',$data['data']['download_list']);
        $this->display('admin-index');
    }
    
    //ios刷榜统计分析
    public function analyzeIosDownloadAction(){
        $date = isset($this->_getData['date']) ? $this->_getData['date']:'';
        $thead = ['时间','adwan上报数','上报同一时间段激活数','最终实际激活数','创建角色数'];
        $model = new \Admin\AdminAnalyzeModel();
        $data = $model->analyzeIosDownload($this->_gameInfo['game_id'],$date);
        $this->getView()->assign('download',$data['data']['download']);
        $this->getView()->assign('thead', $thead);
        $this->getView()->assign('results',$data['data']['oneday_list']);
        $this->display('admin-index');
    }

    // 游戏列表
    public function gameListAction(){
        $thead = ['游戏id','游戏名称','游戏表名','包名','支付回调地址','首页地址','bbs地址','备注','添加时间','客户id','客户秘钥','认证回调地址'];
        $this->getView()->assign('thead',$thead);

        $model = new \Admin\AdminIndexModel();
        $data = $model->gameList($this->_productIds,$this->_count,$this->_page);
        $this->getView()->assign('count', $data['data']['count']);
        $this->getView()->assign('page', $data['data']['page']);
        $this->getView()->assign('totalPage', $data['data']['totalPage']);
        $this->getView()->assign('results', $data['data']['list']);
        $this->getView()->assign('listurl', '');
        $this->display('admin-index');
    }
    
    // 用户列表
    public function userListAction(){
        $thead=['用户id','用户名','渠道','ip','设备号','注册时间','手机','权限','账号状态'];
        $this->getView()->assign('thead', $thead);

        $param['uid'] = isset($this->_getData['uid']) ? $this->_getData['uid']:'';
        $param['uname'] = isset($this->_getData['uname']) ? $this->_getData['uname']:'';
        $param['cellphone'] = isset($this->_getData['cellphone']) ? $this->_getData['cellphone']:'';
        $str = http_build_query($param);

        $model= new \Admin\AdminIndexModel();
        $data=$model->userList($param,$this->_count,$this->_page);
        $this->getView()->assign('params',$str);
        $this->getView()->assign('count', $data['data']['count']);
        $this->getView()->assign('page', $data['data']['page']);
        $this->getView()->assign('totalPage', $data['data']['totalPage']);
        $this->getView()->assign('results', $data['data']['list']);
        $this->getView()->assign('listurl', '');
        $this->display('admin-index');
    }

    // 游戏商户列表
    public function gameMerchantListAction(){
        $thead=['用户id','用户名','渠道','ip','设备号','注册时间','手机','权限','账号状态'];
        $this->getView()->assign('thead', $thead);

        $param['uid'] = isset($this->_getData['uid']) ? $this->_getData['uid']:'';
        $param['uname'] = isset($this->_getData['uname']) ? $this->_getData['uname']:'';
        $param['cellphone'] = isset($this->_getData['cellphone']) ? $this->_getData['cellphone']:'';
        $str = http_build_query($param);

        $model= new \Admin\AdminIndexModel();
        $data=$model->gameMerchantList($param,$this->_count,$this->_page);
        //print_r($data);exit;
        $this->getView()->assign('params',$str);
        $this->getView()->assign('count', $data['data']['count']);
        $this->getView()->assign('page', $data['data']['page']);
        $this->getView()->assign('totalPage', $data['data']['totalPage']);
        $this->getView()->assign('results', $data['data']['list']);
        $this->getView()->assign('listurl', '');
        $this->display('admin-index');
    }

    // 渠道列表
    public function channellistAction(){
        $thead=['渠道id','渠道名称','游戏id','游戏名称','游戏包名','负责人名称','渠道包名','下载地址','添加时间'];
        $this->getView()->assign('thead', $thead);

        $param['man'] = isset($this->_getData['man']) ? trim($this->_getData['man']):'';
        $param['game_id'] = isset($this->_getData['game_id']) ? $this->_getData['game_id']:'';
        $str =  http_build_query($param);

        $model = new \Admin\AdminIndexModel();
        $data = $model->channelList($this->_channelIds,$param,$this->_count,$this->_page);
        $game = $model->gameList($this->_productIds,$this->_count);//TODO优化使用$this->_gameArray
        $this->getView()->assign('params',$str);
        $this->getView()->assign('gameList',$game['data']['list']);
        $this->getView()->assign('count', $data['data']['count']);
        $this->getView()->assign('page', $data['data']['page']);
        $this->getView()->assign('totalPage', $data['data']['totalPage']);
        $this->getView()->assign('results', $data['data']['list']);
        $this->getView()->assign('listurl', '');
        $this->display('admin-index');
    }

    // 添加渠道
    public function channelAddAction(){
        $model = new \Admin\AdminIndexModel();
        $game = $model->gameList($this->_productIds,$this->_count);
        $this->getView()->assign('gameList',$game['data']['list']);
        $this->getView()->assign('listurl', '');
        $this->display('admin-index');
    }

    // 单日报表(搜索)
    public function dayReportListAction(){
        $thead=['日期','渠道'];
        if(!empty($this->_columnIds)){
            foreach ($this->_allColumnIds as $k=>$v){
                if(in_array($k,$this->_columnIds)){
                    $thead[]=$v;
                }
            }
        }else{
            $thead= array_merge($thead,array_values($this->_allColumnIds));
        }
        $this->getView()->assign('thead',$thead);

        $param['start'] = isset($this->_getData['start']) ? $this->_getData['start']:'';
        $param['end'] = isset($this->_getData['end']) ? $this->_getData['end']:'';
        $param['channel_id'] = isset($this->_getData['channel_id']) ? $this->_getData['channel_id']:'';
        $str =  http_build_query($param);

        $model = new \Admin\AdminIndexModel();
        $data = $model->dayReportList($this->_gameInfo,$this->_channelIds,$this->_columnIds,$param,$this->_count,$this->_page);        // 修改后
        $channel = $model->getChannel($this->_channelIds,'',$this->_gameInfo['game_id']);

        $this->getView()->assign('params',$str);
        $this->getView()->assign('channel',$channel['data']['list']);
        $this->getView()->assign('count', $data['data']['count']);
        $this->getView()->assign('page', $data['data']['page']);
        $this->getView()->assign('totalPage', $data['data']['totalPage']);
        $this->getView()->assign('results', $data['data']['list']);
        $this->getView()->assign('listurl', '');
        $this->display('admin-index');
    }

    // 游戏用户列表
    public function gameUserListAction(){
        $thead=['uid','用户名称','渠道','游戏uid','游戏角色名称','游戏服务器','激活时间','最后登录时间','付费数','总付费额'];
        $this->getView()->assign('thead', $thead);

        $param['start'] = isset($this->_getData['start']) ? $this->_getData['start']:'';
        $param['end'] = isset($this->_getData['end']) ? $this->_getData['end']:'';
        $param['uid'] = isset($this->_getData['uid']) ? trim($this->_getData['uid']):'';
        $param['uname'] = isset($this->_getData['uname']) ? trim($this->_getData['uname']):'';
        $param['channel_id'] = isset($this->_getData['channel_id']) ? $this->_getData['channel_id']:'';
        $str = http_build_query($param);

        $model= new \Admin\AdminIndexModel();
        $data=$model->gameUserList($this->_gameInfo,$this->_channelIds,$param,$this->_count,$this->_page);
        $channel = $model->getChannel($this->_channelIds,'',$this->_gameInfo['game_id']);

        $this->getView()->assign('params',$str);
        $this->getView()->assign('channel',$channel['data']['list']);
        $this->getView()->assign('count', $data['data']['count']);
        $this->getView()->assign('page', $data['data']['page']);
        $this->getView()->assign('totalPage', $data['data']['totalPage']);
        $this->getView()->assign('results', $data['data']['list']);
        $this->getView()->assign('listurl', '');
        $this->display('admin-index');
    }

    // 游戏登录列表
    public function gameLoginListAction(){
        $thead=['用户id','用户名称','渠道','游戏名称','ip','设备id','登录时间'];
        $this->getView()->assign('thead', $thead);

        $param['start'] = isset($this->_getData['start']) ? $this->_getData['start']:'';
        $param['end'] = isset($this->_getData['end']) ? $this->_getData['end']:'';
        $param['uid'] = isset($this->_getData['uid']) ? trim($this->_getData['uid']):'';
        $param['uname'] = isset($this->_getData['uname']) ? trim($this->_getData['uname']):'';
        $param['channel_id'] = isset($this->_getData['channel_id']) ? $this->_getData['channel_id']:'';
        $str = http_build_query($param);

        $model= new \Admin\AdminIndexModel();
        $data=$model->gameLoginList($this->_gameInfo,$this->_channelIds,$param,$this->_count,$this->_page);
        $channel = $model->getChannel($this->_channelIds,'',$this->_gameInfo['game_id']);

        $this->getView()->assign('params',$str);
        $this->getView()->assign('channel',$channel['data']['list']);
        $this->getView()->assign('count', $data['data']['count']);
        $this->getView()->assign('page', $data['data']['page']);
        $this->getView()->assign('totalPage', $data['data']['totalPage']);
        $this->getView()->assign('results', $data['data']['list']);
        $this->getView()->assign('listurl', '');
        $this->display('admin-index');
    }
    
    // 支付记录列表
    public function payListAction(){
        $thead=['uid','用户名称','交易号','支付类型','交易状态','价格','总费用','交易时间','游戏名称','游戏uid','游戏服务器','是否通知'];
        $this->getView()->assign('thead', $thead);

        $param['uid'] = isset($this->_getData['uid']) ? trim($this->_getData['uid']):'';
        $param['uname'] = isset($this->_getData['uname']) ? trim($this->_getData['uname']):'';
        $param['trade_no'] = isset($this->_getData['trade_no']) ? trim($this->_getData['trade_no']):'';
        $str = http_build_query($param);

        $model= new \Admin\AdminIndexModel();
        $data=$model->payList($this->_gameInfo,$param,$this->_count,$this->_page);

        $this->getView()->assign('params',$str);
        $this->getView()->assign('count', $data['data']['count']);
        $this->getView()->assign('page', $data['data']['page']);
        $this->getView()->assign('totalPage', $data['data']['totalPage']);
        $this->getView()->assign('results', $data['data']['list']);
        $this->getView()->assign('listurl', '');
        $this->display('admin-index');
    }

    // 阿里支付列表
    public function aliPayListAction(){
        $thead=['用户id','用户名称','游戏名称','游戏用户id','支付宝交易号','卖家支付宝账号','卖家支付宝账号','交易状态','交易付款时间','商品名称','交易金额'];
        $this->getView()->assign('thead', $thead);

        $param['uid'] = isset($this->_getData['uid']) ? trim($this->_getData['uid']):'';
        $param['uname'] = isset($this->_getData['uname']) ? trim($this->_getData['uname']):'';
        $param['trade_no'] = isset($this->_getData['trade_no']) ? trim($this->_getData['trade_no']):'';
        $str = http_build_query($param);

        $model= new \Admin\AdminIndexModel();
        $data=$model->aliPayList($this->_gameInfo,$param,$this->_count,$this->_page);

        $this->getView()->assign('payType','阿里支付');
        $this->getView()->assign('params',$str);
        $this->getView()->assign('count', $data['data']['count']);
        $this->getView()->assign('page', $data['data']['page']);
        $this->getView()->assign('totalPage', $data['data']['totalPage']);
        $this->getView()->assign('results', $data['data']['list']);
        $this->getView()->assign('listurl', '');
        $this->display('admin-index');
    }

    // 微信支付列表
    public function wechatPayListAction(){
        $thead=['用户id','用户名称','支付订单号','商户订单号','游戏名称','游戏用户id','业务结果','总金额','商家数据包','支付完成时间'];
        $this->getView()->assign('thead', $thead);

        $param['uid'] = isset($this->_getData['uid']) ? trim($this->_getData['uid']):'';
        $param['uname'] = isset($this->_getData['uname']) ? trim($this->_getData['uname']):'';
        $param['trade_no'] = isset($this->_getData['trade_no']) ? trim($this->_getData['trade_no']):'';
        $str = http_build_query($param);

        $model= new \Admin\AdminIndexModel();
        $data=$model->wechatPayList($this->_gameInfo,$param,$this->_count,$this->_page);

        $this->getView()->assign('payType','微信支付');
        $this->getView()->assign('params',$str);
        $this->getView()->assign('count', $data['data']['count']);
        $this->getView()->assign('page', $data['data']['page']);
        $this->getView()->assign('totalPage', $data['data']['totalPage']);
        $this->getView()->assign('results', $data['data']['list']);
        $this->getView()->assign('listurl', '');
        $this->display('admin-index');
    }

    // 易宝支付列表
    public function yeePayListAction(){
        $thead=['用户id','用户名称','交易号','支付结果','成功金额','卡序列号','游戏名称','游戏用户id','确认金额','实际金额','扩展信息','创建时间'];
        $this->getView()->assign('thead', $thead);

        $param['uid'] = isset($this->_getData['uid']) ? trim($this->_getData['uid']):'';
        $param['uname'] = isset($this->_getData['uname']) ? trim($this->_getData['uname']):'';
        $param['trade_no'] = isset($this->_getData['trade_no']) ? trim($this->_getData['trade_no']):'';
        $str = http_build_query($param);

        $model= new \Admin\AdminIndexModel();
        $data=$model->yeePayList($this->_gameInfo,$param,$this->_count,$this->_page);

        $this->getView()->assign('payType','易宝支付');
        $this->getView()->assign('params',$str);
        $this->getView()->assign('count', $data['data']['count']);
        $this->getView()->assign('page', $data['data']['page']);
        $this->getView()->assign('totalPage', $data['data']['totalPage']);
        $this->getView()->assign('results', $data['data']['list']);
        $this->getView()->assign('listurl', '');
        $this->display('admin-index');
    }

    // 玩家留存列表
    public function userLiveListAction(){
        $thead=['时间','渠道','当日新增设备','次日留存','3日留存','4日留存','5日留存','6日留存','7日留存','15日留存','30日留存'];
        $this->getView()->assign('thead', $thead);

        $param['start'] = isset($this->_getData['start']) ? $this->_getData['start']:'';
        $param['end'] = isset($this->_getData['end']) ? $this->_getData['end']:'';
        $param['channel_id'] = isset($this->_getData['channel_id']) ? $this->_getData['channel_id']:'';
        $str = http_build_query($param);

        $model = new \Admin\AdminIndexModel();
        $data = $model->userLiveList($this->_gameInfo,$this->_channelIds,$param,$this->_count,$this->_page);    // 修改后
        $channel = $model->getChannel($this->_channelIds,'',$this->_gameInfo['game_id']);

        $this->getView()->assign('params',$str);
        $this->getView()->assign('channel',$channel['data']['list']);
        $this->getView()->assign('count', $data['data']['count']);
        $this->getView()->assign('page', $data['data']['page']);
        $this->getView()->assign('totalPage', $data['data']['totalPage']);
        $this->getView()->assign('results', $data['data']['list']);
        $this->getView()->assign('listurl', '');
        $this->display('admin-index');
    }

    // LTV值列表
    public function ltvListAction(){
        $thead=['时间','渠道','新增设备','LTV1','LTV2','LTV3','LTV7','LTV15','LTV30','LTV45','LTV60','LTV90'];
        $this->getView()->assign('thead', $thead);

        $param['start'] = isset($this->_getData['start']) ? $this->_getData['start']:'';
        $param['end'] = isset($this->_getData['end']) ? $this->_getData['end']:'';
        $param['channel_id'] = isset($this->_getData['channel_id']) ? $this->_getData['channel_id']:'';
        $str = http_build_query($param);

        $model = new \Admin\AdminIndexModel();
        $data = $model->ltvList($this->_gameInfo,$this->_channelIds,$param,$this->_count,$this->_page);     // 修改后
        $channel = $model->getChannel($this->_channelIds,'',$this->_gameInfo['game_id']);

        $this->getView()->assign('params',$str);
        $this->getView()->assign('channel',$channel['data']['list']);
        $this->getView()->assign('count', $data['data']['count']);
        $this->getView()->assign('page', $data['data']['page']);
        $this->getView()->assign('totalPage', $data['data']['totalPage']);
        $this->getView()->assign('results', $data['data']['list']);
        $this->getView()->assign('listurl', '');
        $this->display('admin-index');
    }
    
    //管理员列表
    public function adminListAction(){
        $thead=['用户id','用户名','手机','角色id','角色名称','注册时间','账号状态'];
        $this->getView()->assign('thead', $thead);

        $model= new \Admin\AdminIndexModel();
        $data=$model->adminList($this->_count,$this->_page);
        $roleData=$model->adminRoleList(999,1);
        $this->getView()->assign('count', $data['data']['count']);
        $this->getView()->assign('page', $data['data']['page']);
        $this->getView()->assign('totalPage', $data['data']['totalPage']);
        $this->getView()->assign('results', $data['data']['list']);
        $this->getView()->assign('roleData', $roleData['data']['list']);
        $this->getView()->assign('listurl', '');
        $this->display('admin-index');
    }
    
    //管理员角色列表
    public function adminRoleListAction(){
        $thead=['角色id','角色名称'];
        $this->getView()->assign('thead', $thead);
        $param['uid'] = isset($this->_getData['uid']) ? $this->_getData['uid']:'';
        $str = http_build_query($param);

        $model= new \Admin\AdminIndexModel();
        $data=$model->adminRoleList($this->_count,$this->_page);
        //print_r($data);exit;
        $adModel= new \Admin\AdminSystemModel();
        $roleData=$adModel->getAdminRoleData();
        //print_r($this->_allColumnIds);exit;
        //print_r($roleData[0]);exit;
        $this->getView()->assign('params',$str);
        $this->getView()->assign('count', $data['data']['count']);
        $this->getView()->assign('page', $data['data']['page']);
        $this->getView()->assign('totalPage', $data['data']['totalPage']);
        $this->getView()->assign('results', $data['data']['list']);
        $this->getView()->assign('menuData', $roleData[0]);
        $this->getView()->assign('gameData', $roleData[1]);
        $this->getView()->assign('channelData', $roleData[2]);
        $this->getView()->assign('columnData',$this->_allColumnIds);
        $this->getView()->assign('listurl', '');
        $this->display('admin-index');
    }


    //文章列表
    public function articleListAction(){
        $thead=['id','标题','发布时间','类型','阅读次数','是否推荐','游戏'];
        $this->getView()->assign('thead', $thead);
        $param['title'] = isset($this->_getData['title']) ? trim($this->_getData['title']):'';
        $param['category_id'] = isset($this->_getData['category_id']) ? $this->_getData['category_id']:'';
        $str = http_build_query($param);
        //print_r($param);exit;

        $model= new \Admin\AdminIndexModel();
        $data=$model->articleList($this->_gameInfo,$param,CDN_URL,$this->_count,$this->_page);
        $category=$model->getArticleCategory($this->_gameInfo['game_id']);
        //print_r($data);exit;
        $this->getView()->assign('category',$category['data']['list']);
        $this->getView()->assign('params',$str);
        $this->getView()->assign('count', $data['data']['count']);
        $this->getView()->assign('page', $data['data']['page']);
        $this->getView()->assign('totalPage', $data['data']['totalPage']);
        $this->getView()->assign('results', $data['data']['list']);
        $this->getView()->assign('gameId', $this->_gameInfo['game_id']);
        $this->getView()->assign('listurl', '');
        $this->display('admin-index');
    }

    // 发布文章
    public function articleAddAction(){
        $model = new \Admin\AdminIndexModel();
        $category=$model->getArticleCategory($this->_gameInfo['game_id']);
        $this->getView()->assign('category',$category['data']['list']);
        $this->getView()->assign('gameId',$this->_gameInfo['game_id']);
        $this->getView()->assign('listurl', '');
        $this->display('admin-index');
    }  

    //幻灯片列表
    public function slideListAction(){
        $thead=['id','图片','幻灯片链接','添加时间','排序','所属游戏','是否手机端'];
        $this->getView()->assign('thead', $thead);

        $model= new \Admin\AdminIndexModel();
        $data=$model->slideList($this->_gameInfo,CDN_URL,$this->_count,$this->_page);
        //print_r($data['data']['list']);exit;
        $model = new \Admin\AdminIndexModel();
        $game = $model->gameList($this->_productIds,$this->_count);

        $this->getView()->assign('params','');
        $this->getView()->assign('count', $data['data']['count']);
        $this->getView()->assign('page', $data['data']['page']);
        $this->getView()->assign('totalPage', $data['data']['totalPage']);
        $this->getView()->assign('results', $data['data']['list']);
        $this->getView()->assign('gameId', $this->_gameInfo['game_id']);
        $this->getView()->assign('gameList',$game['data']['list']);
        $this->getView()->assign('listurl', '');
        $this->display('admin-index');
    }

    // 礼包列表
    public function giftListAction(){
        $thead=['id','名字','内容','说明','图片','数量','限制次数','游戏'];
        $this->getView()->assign('thead', $thead);

        $model= new \Admin\AdminIndexModel();
        $data=$model->giftList($this->_gameInfo,CDN_URL,$this->_count,$this->_page);
        //print_r($data);exit;

        $this->getView()->assign('params','');
        $this->getView()->assign('count', $data['data']['count']);
        $this->getView()->assign('page', $data['data']['page']);
        $this->getView()->assign('totalPage', $data['data']['totalPage']);
        $this->getView()->assign('results', $data['data']['list']);
        $this->getView()->assign('gameId', $this->_gameInfo['game_id']);
        //$this->getView()->assign('gameList',$game['data']['list']);
        $this->getView()->assign('listurl', '');
        $this->display('admin-index');
    }

    // 素材列表
    public function materialListAction(){
        $thead=['id','缩略图','大图','视频链接','添加时间','所属游戏','类型'];
        $this->getView()->assign('thead', $thead);

        $param['category'] = isset($this->_getData['category'])?$this->_getData['category']:'';
        $str = http_build_query($param);

        $model= new \Admin\AdminIndexModel();
        $data=$model->materialList($this->_gameInfo,CDN_URL,$param,$this->_count,$this->_page);
        //print_r($data);exit;

        $this->getView()->assign('params','');
        $this->getView()->assign('count', $data['data']['count']);
        $this->getView()->assign('page', $data['data']['page']);
        $this->getView()->assign('totalPage', $data['data']['totalPage']);
        $this->getView()->assign('results', $data['data']['list']);
        $this->getView()->assign('gameId', $this->_gameInfo['game_id']);
        //$this->getView()->assign('gameList',$game['data']['list']);
        $this->getView()->assign('listurl', '');
        $this->display('admin-index');
    }

    // 天豫文章列表
    public function tyArticleListAction(){
        $thead=['id','标题','图片','发布时间','是否推荐'];
        $this->getView()->assign('thead', $thead);

        $model= new \Admin\AdminIndexModel();
        $data=$model->tyArticleList(CDN_URL,$this->_count,$this->_page);
        //print_r($data);exit;

        $this->getView()->assign('params','');
        $this->getView()->assign('count', $data['data']['count']);
        $this->getView()->assign('page', $data['data']['page']);
        $this->getView()->assign('totalPage', $data['data']['totalPage']);
        $this->getView()->assign('results', $data['data']['list']);
        $this->getView()->assign('listurl', '');
        $this->display('admin-index');
    }

    // 游戏包列表
    public function packageListAction(){
        $thead=['游戏id','游戏名称','ios下载包','android下载包','微端下载包'];
        $this->getView()->assign('thead', $thead);

        $model= new \Admin\AdminIndexModel();
        $data=$model->packageList($this->_count,$this->_page);
        //print_r($data);exit;

        $this->getView()->assign('params','');
        $this->getView()->assign('gameInfo',$this->_gameInfo);
        $this->getView()->assign('count', $data['data']['count']);
        $this->getView()->assign('page', $data['data']['page']);
        $this->getView()->assign('totalPage', $data['data']['totalPage']);
        $this->getView()->assign('results', $data['data']['list']);
        $this->getView()->assign('listurl', '');
        $this->display('admin-index');
    }


    //官网敏感词列表
    public function sensitiveWordListAction(){
        $thead=['id','敏感词'];
        $this->getView()->assign('thead', $thead);

        $model= new \Admin\AdminIndexModel();
        $data=$model->sensitiveWordList($this->_count,$this->_page);
        //print_r($data);exit;
        $this->getView()->assign('params','');
        $this->getView()->assign('count', $data['data']['count']);
        $this->getView()->assign('page', $data['data']['page']);
        $this->getView()->assign('totalPage', $data['data']['totalPage']);
        $this->getView()->assign('results', $data['data']['list']);
        $this->getView()->assign('listurl', '');
        $this->display('admin-index');
    }

    // 落地页列表
    public function landingListAction(){
        $thead=['id','落地页id','名称','链接','自动下载','自动下载时间(秒)','全屏下载','下载链接','公司名称','模板','添加时间'];
        $this->getView()->assign('thead', $thead);
        $param['landing_id'] = isset($this->_getData['id']) ? trim($this->_getData['id']):'';
        $param['landing_name'] = isset($this->_getData['name']) ? $this->_getData['name']:'';
        //print_r($param);exit;
        $str = http_build_query($param);
        //print_r($param);exit;
        $defaultDownloadUrl = 'http://www.91sd.com/dzz/tip';

        $model= new \Admin\AdminIndexModel();
        $data=$model->landingList($this->_gameInfo,$param,$this->_count,$this->_page);
        //print_r($data);exit;
        $template = [];
        if(isset($data['data']['template'])){
            $template = $data['data']['template'];
        }
        //print_r($data['data']['list']);exit;
        $this->getView()->assign('params',$str);
        $this->getView()->assign('count', $data['data']['count']);
        $this->getView()->assign('page', $data['data']['page']);
        $this->getView()->assign('totalPage', $data['data']['totalPage']);
        $this->getView()->assign('results', $data['data']['list']);
        $this->getView()->assign('defaultDownloadUrl', $defaultDownloadUrl);
        $this->getView()->assign('gameId', $this->_gameInfo['game_id']);
        $this->getView()->assign('template', $template);
        $this->getView()->assign('listurl', '');
        $this->display('admin-index');
    }

    //sdk列表
    public function sdkAction(){

        $model= new \Admin\AdminIndexModel();
        $sdklist = $model->getSdkList();
        if($sdklist['code'] == 201)
            $sdklist['data'] = array();
        $tmp = $sdklist['data'];
        foreach($tmp as $key => $value) {
            if ($value['patch_version'] == "" || $value['patch_version'] == null)
                $tmp[$key]['patch_next_version'] = $value['sdk_version'] . '.1';
            else{
                $version = substr($value['patch_version'],strrpos($value['patch_version'],'.')+1)+1;
                $tmp[$key]['patch_next_version'] = $tmp[$key]['sdk_version'] .'.'. $version;
            }
        }
        if(isset($this->_getData['modalname']) && $this->_getData['url'])
        {
            if($this->_getData['modalname'] == 'sdk')
                $this->getView()->assign("sdkurl",$this->_getData['url']);
            if($this->_getData['modalname'] == 'patch')
                $this->getView()->assign("patchurl",$this->_getData['url']);
            if($this->_getData['modalname'] == 'update')
                $this->getView()->assign("updateurl",$this->_getData['url']);
        }
        $sdklist['data'] = $tmp; 
        $patchlist = $model->getPatchList();
        $this->getView()->assign("patchlist",$patchlist['data']);
        $this->getView()->assign("sdklist",$sdklist['data']);
        $this->display('admin-index');
    }

    // 模板列表
    public function templateListAction(){
        $thead=['模板名称'];
        $this->getView()->assign('thead', $thead);
        $param['template'] = isset($this->_getData['template']) ? trim($this->_getData['template']):'';
        $str = http_build_query($param);
        //print_r($param);exit;

        $model= new \Admin\AdminIndexModel();
        $data=$model->templateList($this->_gameInfo,$param,$this->_count,$this->_page);
        //print_r($data);exit;
        $this->getView()->assign('params',$str);
        $this->getView()->assign('count', $data['data']['count']);
        $this->getView()->assign('page', $data['data']['page']);
        $this->getView()->assign('totalPage', $data['data']['totalPage']);
        $this->getView()->assign('results', $data['data']['list']);
        $this->getView()->assign('listurl', '');
        $this->display('admin-index');
    }
}

