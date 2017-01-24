<?php
wa::$buffers = [ '无效数据!', 'warn' ];
switch ( isset( $_GET[1] ) ? $_GET[1] : NULL )
{
	case 'hr_staff_insert':
		$q = wa::get_form_post( $hr_form_staff_header + $hr_form_staff_update );
		//if ( $q && wa::set_user_create( $q['username'], substr( $q['idcard'], -6 ), 'rx_hr_staff', $q ) )
		foreach ( $rx_groups as $w => $e )
		{
			if ( $e['name'] == $q['group'] )
			{
				$q['rx_group'] = $w;
				break;
			};
		};
		if ( $q && wa::set_user_create( $q['username'], '123456', 'rx_hr_staff', $q ) )
		{
			wa::$buffers = [ '?/rx_erp(1)hr_staff_list(7)username.eq.' . urlencode( $q['username'] ), 'goto' ];
		};
		break;
	case 'hr_staff_update':
		if ( isset( $_GET[2] ) && ( $q = wa::get_form_post( $hr_form_staff_update ) ) )
		{
			foreach ( $rx_groups as $w => $e )
			{
				if ( $e['name'] == $q['group'] )
				{
					$q['rx_group'] = $w;
					break;
				};
			};
			$w = urldecode( $_GET[2] );
			wa::$sql->q_update( 'rx_hr_staff', $q, 'where username=?s limit 1', $w );
			wa::$buffers = [ '?/rx_erp(1)hr_staff_list(7)username.eq.' . $w, 'goto' ];
		};
		break;
	case 'hr_staff_delete':
		isset( $_GET[2] )
		&& wa::set_user_delete( urldecode( $_GET[2] ), 'rx_hr_staff' )
		&& wa::$buffers = [ '?/rx_erp(1)hr_staff_list', 'goto_referer' ];
		break;
	case 'hr_staff_rename':
		isset( $_GET[2], $_POST['value'] )
		&& preg_match( '/^\d{5}$/', $_POST['value'] )
		&& wa::set_user_rename( urldecode( $_GET[2] ), $_POST['value'], 'rx_hr_staff' )
		&& wa::$buffers = [ '?/rx_erp(1)hr_staff_list(7)username.eq.' . $_POST['value'], 'goto' ];
		break;
	case 'hr_staff_output':
		$q = [ [ '编号', '姓名', '身份证号码', '性别', '部门', '入职日期', '工龄(年,月,天)', '转正日期', '职务', '学历', '电话', '现住址' ] ];
		$w = wa::get_filter( [
			'username',
			'group',
			'idcard',
			'name',
			'gender',
			'office_date',
			'office_type',
			'office_imid',
			'formal',
			'socials',
			'deal',
			'post',
			'edu',
			'phone',
			'marry' ], [ '`resign`=0' ], 2 );
		foreach ( wa::$sql->q_query( 'select username,name,idcard,gender,`group`,office_date,formal,post,edu,phone,address1 from rx_hr_staff ?b', $w ) as $w )
		{
			$e = hr_calc_work_age( $w['office_date'], isset( $w['resign'] ) ? strtotime( $w['resign'] ) : $_SERVER['REQUEST_TIME'] );
			$q[] = [
				$w['username'],
				$w['name'],
				$w['idcard'],
				$w['gender'],
				$w['group'],
				$w['office_date'],
				$e[1] . ',' . substr( '0' . $e[2], -2 ) . ',' . substr( '0' . $e[3], -2 ) . ',' . substr( '000' . $e[4], -4 ),
				$w['formal'],
				$w['post'],
				$w['edu'],
				$w['phone'],
				$w['address1']
			];
			// = array_values( array_map( 'trim', $w ) );
		};
		wa::end_download_xmlexcel( $q, '筛选出的员工.xls' );
		break;
	case 'rd_staff_updated':
		wa::$buffers = rx_staff_update( '人力资源部' );
 	default:
 		wa::$buffers = [ '未定义功能!', 'warn' ];
};
wa::end_str_status();
exit;
?>