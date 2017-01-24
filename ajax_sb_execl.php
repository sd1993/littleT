<?php

if($_GET['key']!='wshsj2016!!!'){
	exit();
}

header("Content-Type: application/vnd.ms-excel;charset=UTF-8");
header("Content-Disposition:attachment;filename=zyb.xls");

require './config.php';
require './core/webapp_sql.php';
//header( 'Content-Type: text/html; charset=utf-8' );
$sql = new webapp_sql( WA_DB_HOST, WA_DB_USER, WA_DB_PASSWORD, WA_DB_DATABASE );
$q = [];

$w_rs = $sql->q_query( 'select a.*,b.name AS pw_editid_name 
from rx_rd_device a 
LEFT JOIN rx_hr_staff b ON b.username = a.pw_editid
where a.status!="机柜设备" ' );

$rs_arr = array();

foreach ( $w_rs as $w )
{

$e = json_decode( $w['sb_info'], TRUE );
is_array( $e ) || $e = [];


unset($e["密码"],$e["账号"]);


$e_tmp = "";


if('机柜'!=$w["sb_type"]){
	$e += [
		'主板' => '',
		'CPU' => '',
		'内存' => '',
		'硬盘' => '',
		'电源' => '',
		'风扇' => '',
		'机箱' => ''
	];

	foreach($e as $k=>$v){
		$e_tmp.=$k.":".$v."\r\n";
	}
}else{//机柜
	foreach($e as $k=>$v){
		if($v)
		$e_tmp.=$k.":".implode(";", $v)."\r\n";
	}
}

$rs_arr_tmp = array();
$rs_arr_tmp[] = date('Y-m-d H:i:s',$w['time']);
$rs_arr_tmp[] = $w['only'];
$rs_arr_tmp[] = $w["sb_type"];
$rs_arr_tmp[] = $w["hold"];
$rs_arr_tmp[] = $w["room"];
$rs_arr_tmp[] = $w["note"];
$rs_arr_tmp[] = $w["bandwidth"];
$rs_arr_tmp[] = $w["defense"];
$rs_arr_tmp[] = $w["status"];
$rs_arr_tmp[] = date('Y-m-d H:i:s',$w["pw_time"]);
$rs_arr_tmp[] = $w["pw_editid_name"];//更新员工ID

$rs_arr_tmp[] = str_replace(' ',"\r\n",$w['ip_main']);
$rs_arr_tmp[] = str_replace(' ',"\r\n",$w['ip_vice']);
$rs_arr_tmp[] = $e_tmp;

$rs_arr_tmp[] = 
  '业务员工: ' . $w["pw_userid"] . "\r\n" .
  '开通时间: ' . date('Y-m-d H:i:s', $w["pw_starts"]) . "\r\n" .
  '到期时间: ' . date('Y-m-d H:i:s', $w["pw_expire"]) . "\r\n" .
  '订单编号: ' . $w["pw_of"] . "\r\n" .
  '关联信息: ' . $w["pw_be"] . "\r\n" .
  '联系名称: ' . $w["pw_name"];


$rs_arr_tmp[] = strtr( $w['pw_imid'], ' ', "\r\n" );

//$rs_arr_tmp[] = addslashes($w["pw_note"]);
//$rs_arr_tmp[] = $w["pw_note"];
//$rs_arr_tmp[] = htmlentities($w["pw_note"]);
$rs_arr_tmp[] = htmlspecialchars($w["pw_note"]);

$rs_arr[] = $rs_arr_tmp;
};

//echo json_encode( $q, JSON_UNESCAPED_UNICODE );

//输出内容如下： 
$head_arr =  array('录入时间','设备编号','设备类型','产权','所在机房','设备备注','带宽','防御','状态','更新时间','更新员工','主IP','副IP','设备信息','状态','联系QQ:','业务备注');
foreach ($head_arr as $key => $value) {
	echo   iconv("utf-8", "gbk", $value)."\t"; 
}
echo   "\n"; 

foreach($rs_arr as $k =>$v_arr){
	foreach($v_arr as $v_k =>$v){
		echo   "\"".iconv("utf-8", "gbk", $v)."\""."\t"; 
	}

    echo   "\n"; 
}

