<?php
namespace Admin;
class AdminSystemModel extends \Core\BaseModels {
    
    //系统角色//[]表示全部
    public function sysRole($roleId){
        $options['table']='sys_user_role';
        $options['where']=array('role_id'=>'?');
        $options['param']=array($roleId);
        $role=$this->db->find($options);
        if(!empty($role)){
            if(empty($role['menus'])||empty($role['products'])||empty($role['channels'])||empty($role['columns'])){
                $this->returnResult(4000,'CANT BE EMPTY');
            }
            if(!empty($role['menus']) &&$role['menus'] != 'all'){
                $role['menus']=  explode(',', $role['menus']);
            }elseif($role['menus'] == 'all'){
                $role['menus']=[];
            }
            if(!empty($role['products']) &&$role['products'] != 'all'){
                $role['products']=  explode(',', $role['products']);
            }elseif($role['products'] == 'all'){
                $role['products']=[];
            }
            if(!empty($role['channels']) &&$role['channels'] != 'all'){
                $role['channels']=  explode(',', $role['channels']);
            }elseif($role['channels'] == 'all'){
                $role['channels']=[];
            }
            if(!empty($role['columns']) &&$role['columns'] != 'all'){
                $role['columns']=  explode(',', $role['columns']);
            }elseif($role['columns'] == 'all'){
                $role['columns']=[];
            }
        }
        return $role;
    }
    
    //菜单列表
    public function sysMenu($menuIds=array()){
        $options['table']='sys_menu';
        if(!empty($menuIds)){            
            //必须加上pid
            foreach ($menuIds as $k=>$v){
                $menuIds[]=floor($v/100)*100;
            }
            //没有子节点就直接去掉


            $menuIds= array_values(array_unique($menuIds));
            $options['where']=array('menu_id'=>array('IN',$menuIds));
            $options['param']=$menuIds;            
        }
        $list=$this->db->select($options);
        if(!empty($list)){
            $menus=array();
            $pageTypes=array();
            foreach ($list as $k=>$v){
                if($v['menu_pid']==0){
                    $menus[$v['menu_id']]['menu_title']=$v['menu_name'];
                }else{
                    $menus[$v['menu_pid']]['menu_ids'][]=$v;
                    $menus[$v['menu_pid']]['menu_types'][]=$v['menu_type'];
                    $pageTypes[$v['menu_type']]=$v['menu_page'];
                }               
            }
        }
        return array($menus,$pageTypes);
    }

    // 获取管理员权限数据
    public function getAdminRoleData(){
        $options['table'] = 'sys_menu';
        $data = $this->db->select($options);
        //print_r($data);exit;
        if(!empty($data)){
            $menus = array();
            foreach($data as $k=>$v){
                if($v['menu_pid']=='0'){
                    $menus[$v['menu_id']]['menu_title'] = $v['menu_name'];
                }else {
                    $menus[$v['menu_pid']]['menu_ids'][] = $v;
                }
            }
        }
        $options1['table'] = 'game';
        $games = $this->db->select($options1);
        $options2['table'] = 'user_channel as A';
        $options2['join'] = array('game as B on A.game_id=B.game_id');
        $options2['field'] = 'A.channel_id,A.channel_name,A.game_id,B.game_name';
        $channelData = $this->db->select($options2);
        //print_r($channelData);exit;
        if(!empty($channelData)){
            $channels = array();
            foreach($channelData as $k=>$v){
                $channels[$v['game_id']]['game_name'] = $v['game_name'];
                $channels[$v['game_id']]['channel_ids'][] = $v;
            }
        }
        return array($menus,$games,$channels);
    }
    
}
