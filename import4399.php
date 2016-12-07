<?php
/**
 * Created by PhpStorm.
 * User: pc
 * Date: 2016/10/14
 * Time: 17:59
 */
$argv = !empty($_SERVER['argv'][1]) ? $_SERVER['argv'][1] : '';

// 参数
parse_str($argv,$array);


$type = $array['type']; // 类型
$start = isset($array['start']) ? $array['start'] : flag($type); // 开始


// 最大值
if ($type == 'device') {
    $sqls = getDeviceSql($start);
} elseif ($type = 'game') {
    $sqls = getGameSql($start);
} elseif ($type = 'server') {
    $sqls = getServerSql($start);
}
else{
    exit('type错误');
}

if(!empty($sqls)){
    // 写SQL语句
    foreach ($sqls as $v) {
        file_put_contents("/tmp/{$type}_sql.sql", $v."\n", FILE_APPEND);
    }
}
else{
    echo "sql error";
}

flag($type,$start+5000);


function flag($type, $value = '')
{
    $file = "/tmp/{$type}.flag";
    if (!$value) {
        if (!file_exists($file)) {
            return 0;
        }
        return file_get_contents($file);
    } else {
        return file_put_contents($file, $value);
    }
}

function dbConnect()
{
    $dsn = 'mysql:dbname=user_center;host=10.19.34.191';
    $username = 'dbadmin';
    $password = 'Cheffinty413';
    return new PDO($dsn, $username, $password, array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"));
}

/**
 * 导设备基础表，根据start后面的num行，导出10个表的sql语句
 * @param $start 起始点
 * @param $num 源数据条数
 * @param int $new 只有在start=0的时候，$new=1 清空目标表，重新导入
 * @return array 10个分表sql
 */
function getDeviceSql($start, $num = 5000, $new = 0)
{
    $end = $start + $num;
    echo "device {$start} to {$end} ";
    $sqls = array();
    for ($i = 0; $i < 128; $i++) {
        $j = sprintf('%03x',$i);
        $sqls[$j] = "CREATE TABLE IF NOT EXISTS  `user_{$j}` (
                              `did` char(50) NOT NULL DEFAULT '0' COMMENT '设备ID',
                              `cid` int(11) NOT NULL DEFAULT '0' COMMENT '渠道id',
                              `game` bigint(20) NOT NULL DEFAULT '0' COMMENT '游戏',
                              `recdate` date NOT NULL COMMENT '日期',
                              `rectime` bigint(20) NOT NULL DEFAULT '0' COMMENT '事件时间戳',
                              `oid` bigint(20) NOT NULL DEFAULT '0',
                              `aid` bigint(20) NOT NULL DEFAULT '0' COMMENT '广告位ID',
                              `adtype` int(11) NOT NULL DEFAULT '0' COMMENT '广告类型',
                              `regtype` smallint(6) NOT NULL DEFAULT '0' COMMENT '注册类型',
                              `osid` smallint(6) NOT NULL DEFAULT '2' COMMENT '1 iOS 2 Android',
                              `os` char(20) NOT NULL COMMENT '操作系统',
                              `osver` char(45) NOT NULL COMMENT '操作系统版本',
                              `appver` char(30) NOT NULL,
                              `sdkver` char(30) NOT NULL,
                              `dev` char(45) NOT NULL,
                              `devtype` char(45) NOT NULL,
                              `screen` char(15) NOT NULL,
                              `mno` char(30) NOT NULL COMMENT '移动网络运营商',
                              `nm` char(20) NOT NULL COMMENT '联网方式：3G，WIFI',
                              `ip` bigint(20) NOT NULL DEFAULT '0' COMMENT '整型ip地址',
                              `lastdate` date NOT NULL COMMENT '最后登录日期',
                              `lasttime` bigint(20) NOT NULL DEFAULT '0' COMMENT '最后登录时间戳',
                              `ut` bigint(20) NOT NULL DEFAULT '0',
                              `rt` bigint(20) NOT NULL DEFAULT '0'
                            ) ENGINE=InnoDB DEFAULT CHARSET=utf8;\n";
        $sqls[$j] .= $new && $start == 0 ? "TRUNCATE  TABLE `user_{$j}` ;\n" : '';
        $sqls[$j] .= "INSERT IGNORE INTO `user_{$j}` VALUES";
    }
    $sql = "SELECT	
                A.id as id,
                A.idfa,
                A.imei,
                B.channel_id,
                B.game_id,
                B.adddate,
                B.addtime,
                0 AS oid,
                0 AS aid,
                0 AS adtype,
                0 AS regtype,
                0 AS osid,
                'os' AS os,
                '' as osver,
                '' as appver,
                '' as sdkver,
                '' as dev,
                '' as devtype,
                '' as screen,
                '' as mno,
                '' as nm,
                C.ip,
                D.login_date,
                D.login_time,
                0 as ut,
                0 as rt
                FROM user_device AS A
                LEFT JOIN game_user_dzz AS B ON A.device_id = B.device_id
                LEFT JOIN `user` as C ON B.uid=C.uid
                LEFT JOIN log_user_login as D ON B.uid=D.uid
                WHERE A.id >= ? AND A.id < ? AND ((A.`idfa` !='' and B.channel_id = 4 ) OR( A.`imei` !='' AND B.channel_id = 3))";
    $dbh = dbConnect();
    $sth=$dbh->prepare($sql);
    $sth->bindParam(1, $start, PDO::PARAM_INT);
    $sth->bindParam(2, $end, PDO::PARAM_INT);
    $sth->execute();
    while($row=$sth->fetch(PDO::FETCH_ASSOC)) {
        // 去重
        if (isset($_tmp[$row['id']])) {
            continue;
        } else {
            $_tmp[$row['id']] = 1;
        }
        unset($row['id']);
        // 3为安卓
        if ($row['channel_id'] == 3) {
            $row['osid'] = 2;
            $row['os'] = 'android';
            unset($row['idfa']);
            $did = $row['imei'];
        } // 4为IOS
        else {
            $row['osid'] = 1;
            $row['os'] = 'ios';
            unset($row['imei']);
            $did = $row['idfa'];
        } // 默认删除imei

        $j = sprintf('%03x',intval(sprintf('%u', crc32($did)))%128 );
        $k=0;
        //  顺序拼接SQL
        foreach ($row as  $v) {
            if ($k == 0) {
                $sqls[$j] .= " ('{$v}'";
            } else {
                $sqls[$j] .= " ,'{$v}'";
            }
            $k++;
        }

        $sqls[$j] .= "),\n";
    }

    foreach($sqls as $j => $v){
        $sqls[$j] = substr($v, 0, -2) . ";\n\n";
    }
    echo " ok\n";
    return $sqls;
}


/**
 * 游戏基础，根据start后面的num行，导出10个表的sql语句
 * @param $start 起始点
 * @param $num 源数据条数
 * @param int $new 只有在start=0的时候，$new=1 清空目标表，重新导入
 * @return array 10个分表sql
 */
function getGameSql($start = 0, $num = 5000, $new = 0)
{

    $start += 1000000000;
    $end = $start + $num;
    echo "game {$start} to {$num} ";
    $_tmp = array();
    $sqls = array();
    for ($i = 0; $i < 10; $i++) {
        $sqls[$i] = "CREATE TABLE IF NOT EXISTS `user_{$i}` (
  `uid` bigint(20) NOT NULL DEFAULT '0' COMMENT '用户唯一标识',
  `game` bigint(20) NOT NULL DEFAULT '0' COMMENT '游戏',
  `recdate` date NOT NULL COMMENT '日期',
  `rectime` bigint(20) NOT NULL DEFAULT '0' COMMENT '事件时间戳',
  `did` char(50) NOT NULL DEFAULT '0' COMMENT '设备ID',
  `cid` int(11) NOT NULL DEFAULT '0' COMMENT '渠道id',
  `oid` bigint(20) NOT NULL DEFAULT '0' COMMENT '创意ID',
  `aid` bigint(20) NOT NULL DEFAULT '0' COMMENT '广告位ID',
  `adtype` smallint(6) NOT NULL DEFAULT '0' COMMENT '广告类型',
  `regtype` smallint(6) NOT NULL DEFAULT '0' COMMENT '注册类型',
  `osid` smallint(6) NOT NULL DEFAULT '2' COMMENT '1 iOS 2 Android',
  `os` char(20) NOT NULL COMMENT '操作系统',
  `osver` char(45) NOT NULL COMMENT '操作系统版本',
  `appver` char(30) NOT NULL,
  `sdkver` char(30) NOT NULL,
  `dev` char(45) NOT NULL,
  `devtype` char(45) NOT NULL,
  `screen` char(15) NOT NULL,
  `mno` char(30) NOT NULL COMMENT '移动网络运营商',
  `nm` char(20) NOT NULL COMMENT '联网方式：3G，WIFI',
  `ip` bigint(20) NOT NULL DEFAULT '0' COMMENT '整型ip地址',
  `lastdate` date NOT NULL COMMENT '最后登录日期',
  `lasttime` bigint(20) NOT NULL COMMENT '最后登录时间戳',
  `firstpay` date NOT NULL COMMENT '首次充值日期',
  `lastpay` date NOT NULL COMMENT '最后一次充值',
  `ut` bigint(20) NOT NULL DEFAULT '0',
  `rt` bigint(20) NOT NULL DEFAULT '0'
) ENGINE=InnoDB DEFAULT CHARSET=utf8\n";
        $sqls[$i] .= $new && $start == 0 ? "TRUNCATE  TABLE `user_{$i}` ;\n" : '';
        $sqls[$i] = "INSERT IGNORE INTO `user_{$i}` VALUES(";
    }
    $sql = "
SELECT A.uid,B.game_id,0 as server,B.adddate,B.addtime,A.imei,A.idfa,A.,B.channel_id,
  0 AS oid,0 AS aid,0 AS adtype,0 AS regtype,0 AS osid,'os' AS os,E.user_agent,
  '' as appver,'' as sdkver,E.user_agent,'' as devtype,'' as screen,'' as mno,'' as nm,
  A.ip,D.login_date,D.login_time,C.cdate,C.cdate,'' as role,0 as level,0 as ut,0 as rt
FROM user AS A
LEFT JOIN game_user_dzz AS B ON A.uid = B.uid
LEFT JOIN log_user_login AS D ON A.uid=D.uid
LEFT JOIN log_user_pay AS C on A.uid=C.uid
LEFT JOIN user_device AS E on A.device_id=E.device_id 
WHERE A.uid >= {$start}, and A.uid < {$end} AND (A.`idfa` !='' OR A.`imei` !='')";

    echo $sql;
    exit();
    $conn = dbConnect();
    foreach ($conn->query($sql) as $row) {
        // 去重
        if (isset($_tmp[$row['uid']])) {
            continue;
        } else {
            $_tmp[$row['uid']] = 1;
        }
        $i = $row['uid'] % 10;
        // 3为安卓
        if ($row['channel_id'] == 3) {
            $row['os'] = 'android';
            $row['osid'] = 2;
            unset($row['idfa']);
        } // 4为IOS
        else if ($row['channel_id'] == 4) {
            $row['os'] = 'ios';
            $row['osid'] = 1;
            unset($row['imei']);
        } else {
            unset($row['imei']);
        }
        //  顺序拼接SQL
        foreach ($row as $k => $v) {
            if ($k == 1) {
                $sqls[$i] .= " ('{$v}'";
            } else {
                $sqls[$i] .= " ，'{$v}'";
            }
        }
        $sqls[$i] .= "),";
    }
    for ($i = 0; $i < 10; $i++) {
        $sqls[$i] = substr($sqls[$i], 0, -1) . ";\n";
    }
    echo " ok\n";
    return $sqls;
}


/**
 * 游戏基础，根据start后面的num行，导出10个表的sql语句
 * @param $start 起始点
 * @param $num 源数据条数
 * @param int $new 只有在start=0的时候，$new=1 清空目标表，重新导入
 * @return array 10个分表sql
 */
function getServerSql($start = 0, $num = 5000, $new = 0)
{

    $start += 1000000000;
    $end = $start + $num;
    echo "server {$start} to {$end} ";


    $_tmp = array();
    $sqls = array();
    for ($i = 0; $i < 10; $i++) {
        $sqls[$i] = "CREATE TABLE `user_{$i}` (
                      `uid` bigint(20) NOT NULL DEFAULT '0' COMMENT '用户唯一标识',
                      `game` bigint(20) NOT NULL DEFAULT '0' COMMENT '游戏',
                      `server` bigint(20) NOT NULL DEFAULT '0',
                      `recdate` date NOT NULL COMMENT '日期',
                      `rectime` bigint(20) NOT NULL DEFAULT '0' COMMENT '事件时间戳',
                      `did` char(50) NOT NULL DEFAULT '0' COMMENT '设备ID',
                      `cid` int(11) NOT NULL DEFAULT '0' COMMENT '渠道id',
                      `oid` bigint(20) NOT NULL DEFAULT '0' COMMENT '创意ID',
                      `aid` bigint(20) NOT NULL DEFAULT '0' COMMENT '广告位ID',
                      `adtype` smallint(6) NOT NULL DEFAULT '0' COMMENT '广告类型',
                      `regtype` smallint(6) NOT NULL DEFAULT '0' COMMENT '注册类型',
                      `osid` smallint(6) NOT NULL DEFAULT '2' COMMENT '2 android 1 iOS',
                      `os` char(20) NOT NULL COMMENT '操作系统',
                      `osver` char(45) NOT NULL COMMENT '操作系统版本',
                      `appver` char(30) NOT NULL,
                      `sdkver` char(30) NOT NULL,
                      `dev` char(45) NOT NULL,
                      `devtype` char(45) NOT NULL,
                      `screen` char(15) NOT NULL,
                      `mno` char(30) NOT NULL COMMENT '移动网络运营商',
                      `nm` char(20) NOT NULL COMMENT '联网方式：3G，WIFI',
                      `ip` bigint(20) NOT NULL DEFAULT '0' COMMENT '整型ip地址',
                      `lastdate` date NOT NULL COMMENT '最后登录日期',
                      `lasttime` bigint(20) NOT NULL COMMENT '最后登录时间戳',
                      `firstpay` date NOT NULL COMMENT '首充日期',
                      `lastpay` date NOT NULL COMMENT '最新充值日期',
                      `role` char(45) NOT NULL COMMENT '角色',
                      `level` int(11) NOT NULL DEFAULT '0' COMMENT '游戏服等级',
                      `ut` bigint(20) NOT NULL DEFAULT '0',
                      `rt` bigint(20) NOT NULL DEFAULT '0'
                    ) ENGINE=InnoDB DEFAULT CHARSET=utf8\n";
        $sqls[$i] .= $new && $start == 0 ? "TRUNCATE  TABLE `user_{$i}` ;\n" : '';
        $sqls[$i] = "INSERT INTO `user_{$i}` VALUES(";
    }


    $sql = "SELECT A.uid,B.game_id,0 as server,B.adddate,B.addtime,A.imei,A.idfa,A.,B.channel_id,
              0 AS oid,0 AS aid,0 AS adtype,0 AS regtype,0 AS osid,'os' AS os,E.user_agent,
              '' as appver,'' as sdkver,E.user_agent,'' as devtype,'' as screen,'' as mno,'' as nm,
              A.ip,D.login_date,D.login_time,C.cdate,C.cdate,'' as role,0 as level,0 as ut,0 as rt
            FROM user AS A
            LEFT JOIN game_user_dzz AS B ON A.uid = B.uid
            LEFT JOIN log_user_login AS D ON A.uid=D.uid
            LEFT JOIN log_user_pay AS C on A.uid=C.uid
            LEFT JOIN user_device AS E on A.device_id=E.device_id 
            WHERE A.uid >= {$start}, and A.uid < {$end} AND (A.`idfa` !='' OR A.`imei` !='')";
    $conn = dbConnect();
    foreach ($conn->query($sql) as $row) {
        // 去重
        if (isset($_tmp[$row['uid']])) {
            continue;
        } else {
            $_tmp[$row['uid']] = 1;
        }
        $i = $row['uid'] % 10;
        // 3为安卓
        if ($row['channel_id'] == 3) {
            $row['os'] = 'android';
            $row['osid'] = 2;
            unset($row['idfa']);
        } // 4为IOS
        else if ($row['channel_id'] == 4) {
            $row['os'] = 'ios';
            $row['osid'] = 1;
            unset($row['imei']);
        } else {
            unset($row['imei']);
        }
        //  顺序拼接SQL
        foreach ($row as $k => $v) {
            if ($k == 1) {
                $sqls[$i] .= " ('{$v}'";
            } else {
                $sqls[$i] .= " ，'{$v}'";
            }
        }
        $sqls[$i] .= "),";
    }
    for ($i = 0; $i < 10; $i++) {
        $sqls[$i] = substr($sqls[$i], 0, -1) . ";\n";
    }
    echo " ok\n";
    return $sqls;
}
