<?php
error_reporting(0);
$table='ysts';//表名称
if($_GET['key'] != 'rd_serverrdTEST'){
	
	exit;
}
	
	require './config.php';
	require './core/webapp_sql.php';
	$sql = new webapp_sql( WA_DB_HOST, WA_DB_USER, WA_DB_PASSWORD, WA_DB_DATABASE );

	$data =$sql->q_query( "SELECT * FROM `$table` WHERE is_get=0");

	if($data->num_rows > 0){
		//$sql->q_delete( $table, 'where is_get=?s',0);
		$body =$data->num_rows;
	}else{
		$body =0;
	}
	if($_GET['ajax'] == "ajax"){
		echo json_encode($body);exit;
	}
	
	
?>

