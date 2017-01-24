<?php
$hr_form_staff_header = [
	'username' => [
		'test' => [ 5, 5, '/^\d{5}$/' ],
		'name' => '员工唯一编号',
		'type' => 'text',
		'note' => '唯一的5位数字哦,不许重复'
	]
];
$hr_form_staff_update = [
	'group' => [
		'test' => [ 3, 32, '/^(' . join( '|', $rx_groups_name ) . ')$/' ],
		'name' => '部门',
		'type' => 'select',
		'value' => [ '' => '请选择' ] + array_combine( $rx_groups_name, $rx_groups_name )
	],
	'idcard' => [
		'test' => [ 15, 18, '/^(\d{15}|\d{18}|\d{17}X)$/' ],
		'name' => '身份证号码',
		'type' => 'text',
		'note' => '身份证唯一编号'
	],
	'name' => [
		'test' => [ 6, 16 ],
		'name' => '姓名',
		'type' => 'text',
		'note' => '身份证上的真实姓名'
	],
	'gender' => [
		'test' => [ 3, 3, '/^(男|女)$/' ],
		'name' => '性别',
		'type' => 'select',
		'value' => [ '' => '请选择', '男' => '男', '女' => '女' ]
	],
	'office_date' => [
		'test' => [ 8, 8, '/^\d{8}$/' ],
		'name' => '入职时间',
		'type' => 'text',
		'note' => '格式 YYYYMMDD'
	],
	'office_type' => [
		'test' => [ 6, 6 ],
		'name' => '入职类型',
		'type' => 'select',
		'value' => [ '试用' => '试用', '正式' => '正式', '兼职' => '兼职' ]
	],
	'office_imid' => [
		'test' => [ 0, 32, '/^$|^\d{5,12}$/' ],
		'name' => '办公即时通讯',
		'type' => 'text',
		'note' => '办公软件显示的即时联系方式 如 QQ号码'
	],
	'formal' => [
		'test' => [ 1, 8, '/^0$|^\d{8}$/' ],
		'name' => '转正日期',
		'type' => 'text',
		'note' => '格式 YYYYMMDD'
	],
	'socials' => [
		'test' => [ 6, 12, '/^(未签|已签|已签不买|已买)$/' ],
		'name' => '社保',
		'type' => 'select',
		'value' => [ '未签' => '未签', '已签' => '已签', '已签不买' => '已签不买', '已买' => '已买' ]
	],
	'deal' => [
		'test' => [ 6, 6, '/^(未签|已签)$/' ],
		'name' => '合同',
		'type' => 'select',
		'value' => [ '未签' => '未签', '已签' => '已签' ]
	],
	'post' => [
		'test' => [ 0, 32 ],
		'name' => '职务',
		'type' => 'text',
		'note' => '入职担当的职务'
	],
	'edu' => [
		'test' => [ 6, 6, '/^(小学|初中|中专|高中|大专|大学|本科|硕士|博士)$/' ],
		'name' => '学历',
		'type' => 'select',
		'value' => [
			'' => '请选择',
			'小学' => '小学',
			'初中' => '初中',
			'中专' => '中专',
			'高中' => '高中',
			'大专' => '大专',
			'大学' => '大学',
			'本科' => '本科',
			'硕士' => '硕士',
			'博士' => '博士'
		]
	],
	'prof' => [
		'test' => [ 0, 32 ],
		'name' => '专业',
		'type' => 'text',
		'note' => '有种高大上的感觉'
	],
	'phone' => [
		'test' => [ 0, 23 ],
		'name' => '联系电话',
		'type' => 'text',
		'note' => '约吗?'
	],
	'marry' => [
		'test' => [ 6, 6, '/^(未婚|已婚|离异)$/' ],
		'name' => '婚姻状况',
		'type' => 'select',
		'value' => [ '未婚' => '未婚', '已婚' => '已婚', '离异' => '离异' ]
	],
	'address0' => [
		'test' => [ 0, 128 ],
		'name' => '地址',
		'type' => 'textarea',
		'note' => '对应身份证上的地址'
	],
	'address1' => [
		'test' => [ 0, 128 ],
		'name' => '现住址',
		'type' => 'textarea',
		'note' => '现在的住址'
	],
	'note' => [
		'test' => [ 0, 1024 ],
		'name' => '备注',
		'type' => 'textarea',
		'note' => '记录调换岗位什么的'
	],
	'resign' => [
		'test' => [ 1, 8, '/^0$|^\d{8}$/' ],
		'name' => '辞职日期',
		'type' => 'text',
		'note' => '格式 YYYYMMDD'
	]
];
function hr_calc_work_age( $q, $w )
{
	$q = [ $w - strtotime( $q ) ];
	if ( $q[0] > 31556926 )
	{
		$q[] = $q[0] / 31556926 | 0;
		$w = $q[0] % 31556926;
	}
	else
	{
		$q[] = 0;
		$w = $q[0];
	};
	$q[] = 0;
	$q[] = 0;
	for ( $e = 1, $r = date( 'y' ); $e < 12; $e++ )
	{
		$y = date( 't', strtotime( $r . '-' . $e . '-1' ) ) * 86400;
		if ( $w - $y > 0 )
		{
			++$q[2];
			$w -= $y;
			continue;
		};
		$q[3] = $w / 86400 | 0;
		break;
	};
	$q[] = $q[0] / 86400 | 0;
	return $q;
}
function hr_calc_const_week( $q )
{
	$q = substr( $q, 10, 4 );
	switch ( TRUE )
	{
		case $q >= '0120' && $q <= '0218': return '水瓶';
		case $q >= '0219' && $q <= '0320': return '双鱼';
		case $q >= '0321' && $q <= '0419': return '白羊';
		case $q >= '0420' && $q <= '0520': return '金牛';
		case $q >= '0521' && $q <= '0621': return '双子';
		case $q >= '0622' && $q <= '0722': return '巨蟹';
		case $q >= '0723' && $q <= '0822': return '狮子';
		case $q >= '0823' && $q <= '0922': return '处女';
		case $q >= '0923' && $q <= '1023': return '天秤';
		case $q >= '1024' && $q <= '1122': return '天蝎';
		case $q >= '1123' && $q <= '1221': return '射手';
		default: return '摩羯';
	};
}
?>