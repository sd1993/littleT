<?php
wa::$buffers = [ '无效数据!', 'warn' ];
wa::set_action_post( 'td_staff_update', function()
{
	wa::$buffers = rx_staff_update( '技术部' );






})::set_action_get( 'td_task_wait', function()
{
	isset( $_GET[2] ) && wa::$sql->q_update( 'rx_sd_task', [
		'start' => time(),
		'name' => wa::$user['username']
	], 'where start is null and only=?s limit 1', $_GET[2] ) && wa::$buffers = [
		0 => '/td_task_wait',
		1 => 'rx_ws_callback',
		'send' => 'reload ',
		'come' => '?/rx_erp(1)td_task_my'
	];

})::set_action_post( 'td_task_my', function()
{
	isset( $_GET[2], $_POST['value'] ) && wa::$sql->q_sent( 'update rx_sd_task set note=concat(note,?s) where name=?s and over is null and only=?s limit 1',
		"\n\n" . date( 'Y-m-d H:i:s' ) . "\n" . $_POST['value'], wa::$user['username'], $_GET[2] ) && wa::$buffers = [
		0 => '/sd_task_wait',
		1 => 'rx_ws_callback',
		'send' => 'reload ',
		'come' => '?/rx_erp(1)td_task_my'
	];

})::set_action_get( 'td_task_my', function()
{
	isset( $_GET[2] ) && wa::$sql->q_sent( 'update rx_sd_task set over=?s where name=?s and over is null and only=?s limit 1',
		time(), wa::$user['username'], $_GET[2] ) && wa::$buffers = [
		0 => '/sd_task_wait',
		1 => 'rx_ws_callback',
		'send' => 'reload ',
		'come' => '?/rx_erp(1)td_task_my'
	];


})::end_action(function()
{
	wa::$buffers = [ '未定义功能!', 'warn' ];


}, 'wa::end_str_status' );