<?php
$post = [
	'username' => [
		'test' => [ 5, 5, '/^\d{5}$/' ],
		'name' => '工号',
		'type' => 'text'
	],
	'type' => [
		'test' => [ 1, 1, '/^(1|2)$/' ],
		'name' => '记得',
		'type' => 'select',
		'value' => [ '请选择', '身份证号码' ]
	],
	'value' => [
		'test' => [ 1, 32 ],
		'name' => '内容',
		'type' => 'text'
	]
];


wa::set_action_post( 'send', function() use( &$post )
{
	wa::$buffers = [ '无效数据', 'warn' ];
	if ( ( $q = wa::get_form_post( $post ) ) && ( $w = wa::sql()->get_only( 'rx_hr_staff', 'username', $q['username'] ) ) )
	{
		$q['type'] = $q['type'] == 2 ? 'phone' : 'idcard';
		if ( isset( $w[ $q['type'] ] ) && $w[ $q['type'] ] == $q['value'] )
		{
			wa::$sql->q_update( 'wa_user', [
				'password' => 'e10adc3949ba59abbe56e057f20f883e'
			], 'where username=?s limit 1', $w['username'] );
			wa::$buffers = [ '密码恢复为 123456', 'warn' ];
		};
	};
	wa::end_str_status();


})::end_action(function() use( &$post )
{
	wa::locale();
	wa::htt();
	wa::htt_title( '锐讯员工重置密码' );
	$q = wa::htt_form_post( $post );
	$q['action'] = '?/rx_erp/fn_password_reset(1)send';
	$q->table->caption['style'] = 'font-size:32px';
	$q->table->caption = '重置密码';
	$q->table->tbody->tr->td[1]->div->input['placeholder'] = '请输入自己的工号';
	$q->table->tfoot->tr->td->button[0] = '想起密码';
	$q->table->tfoot->tr->td->button[0]['type'] = 'button';
	$q->table->tfoot->tr->td->button[0]['onclick'] = '$.go("?/rx_erp")';
	$q->table->tfoot->tr->td->button[1] = '提交发送';
});