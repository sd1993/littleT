<?php
require 'main.php';
wa::locale();
wa::get_signin_admin() || wa::end_string( wa_unauthorized );
wa_mysql_connect( wa::get_cookie( 'wa_mysql' ) ) || wa::end_string( join( "\n", wa::$errors ) );
array_walk( $_GET, function( &$q )
{
	$q = urldecode( $q );
});
isset( $_GET[2] ) && wa::$sql->select_db( $_GET[2] );
switch ( isset( $_GET[1] ) ? $_GET[1] : NULL )
{
#sql
	case 'sql':
		wa::$sql->q_send( $_POST['value'] );
		break;
#database
	case 'database_create':
		wa::$sql->q_sent( 'create database ?a', $_GET[4] );
		break;
	case 'database_collate':
		wa::$sql->q_sent( 'alter database ?a collate ?s', $_GET[2], $_GET[4] );
		break;
	case 'database_empty':
		foreach ( wa::$sql->q_query( 'show tables' )->fetch_all() as $q )
		{
			wa::$sql->q_sent( 'drop table ?a', $q[0] );
		};
	 	break;
	case 'database_drop':
		wa::$sql->q_sent( 'drop database ?a', $_GET[2] );
		break;
#table
	case 'table_create':
		wa::$sql->q_sent( 'create table ?a(only char(8) not null, primary key(only) using hash) engine=memory', $_GET[4] );
		break;
	case 'table_comment':
		wa::$sql->q_sent( 'alter table ?a comment ?s', $_GET[4], $_POST['value'] );
		break;
	case 'table_rename':
		wa::$sql->q_sent( 'alter table ?a rename ?a', $_GET[3], $_GET[4] );
		break;
	case 'table_engine':
		wa::$sql->q_sent( 'alter table ?a engine=?s', $_GET[3], $_GET[4] );
		break;
	case 'table_collate':
		wa::$sql->q_sent( 'alter table ?a collate ?s', $_GET[3], $_GET[4] );
		break;
	case 'table_truncate':
		wa::$sql->q_sent( 'truncate table ?a', $_GET[3] );
		break;
	case 'table_drop':
		wa::$sql->q_sent( 'drop table ?a', isset( $_GET[3] ) ? $_GET[3] : $_GET[4] );
		break;
#field
	case 'field_insert':
	case 'field_update':
		wa::get_form_post_callback( $wa_mysql_field_editor, function( $q )
		{
			wa::$buffers = [ '?/wa_mysql(1)table_fields(2)' . $_GET[2] . '(3)' . $_GET[3], 'goto' ];
			$w = wa_mysql_field_submit( $q );
			if ( $_GET[1] == 'field_update' )
			{
				wa::$sql->q_sent( 'alter table ?a.?a change ?a ?b', $_GET[2], $_GET[3], $_GET[4], $w );
			}
			else
			{
				wa::$sql->q_sent( 'alter table ?a.?a add ?b', $_GET[2], $_GET[3], $w );
			};
		});
		break;
	case 'field_index':
	case 'field_unique':
	case 'field_primary':
		wa::$sql->q_sent( 'alter table ?a add ?b(?a)', $_GET[3], $_GET[1] == 'field_primary' ? 'primary key' : substr( $_GET[1], 6 ), $_GET[4] );
		break;
	case 'field_comment':
	case 'field_default':
	case 'field_rename':
		if ( $q = wa_mysql_field_select( $_GET[2], $_GET[3], $_GET[4] ) )
		{
			$q[ [
				'field_comment' => 'Comment',
				'field_default' => 'Default',
				'field_rename' => 'Field'
			][ $_GET[1] ] ] = $_POST['value'];
			$q = wa_mysql_field_submit( $q );
			wa::$sql->q_sent( 'alter table ?a.?a change ?a ?b', $_GET[2], $_GET[3], $_GET[4], $q );
		};
		break;
	case 'field_drop':
		wa::$sql->q_sent( 'alter table ?a drop ?a', $_GET[3], $_GET[4] );
		break;
#index
	case 'index_drpo':
		wa::$sql->q_sent( 'alter table ?a drop index ?a', $_GET[3], $_GET[4] );
		break;
#data
	case 'data_editor':
		$q = [];
		foreach ( wa::$sql->q_query( 'desc ?a', $_GET[3] ) as $w )
		{
			$e = isset( $_POST[ $r = $w['Field'] ] ) ? $_POST[ $r ] : '';
			is_array( $e ) && $e = join( ',', $e );
			if ( isset( $_POST[ 'function_' . $r ] ) && in_array( $_POST[ 'function_' . $r ], $wa_mysql_function_list ) )
			{
				preg_match( '/^$|^-?\d+(\.\d+)?$/', $e ) || $e = wa::$sql->escape( $e );
				$e = $_POST[ 'function_' . $r ] . '(' . $e . ')';
			}
			else
			{
				$e = $w['Null'] === 'YES' && $e === '' ? 'null' : wa::$sql->escape( $e );
			};
			$q[] = wa::$sql->quote( $r ) . '=' . $e;
		};
		$q = join( ',', $q );
		isset( $_GET[4], $_GET[5] )
			? wa::$sql->q_sent( 'update ?a set ?b where ?a=?s limit 1', $_GET[3], $q, $_GET[4], $_GET[5] )
			: wa::$sql->q_sent( 'insert into ?a set ?b', $_GET[3], $q );
		wa::$buffers = [ '?/wa_mysql(1)data_table(2)' . $_GET[2] . '(3)' . $_GET[3], 'goto' ];
		break;
	case 'data_json':
		wa::$buffers = json_encode( wa::$sql->get_only( $_GET[3], $_GET[4], $_GET[5] ), JSON_UNESCAPED_UNICODE );
		exit;
	case 'data_edit':
		if ( isset( $_POST['value'][0] ) )
		{
			$q = wa::$sql->escape( $_POST['value'] );
		}
		else
		{
			$q = wa_mysql_field_select( $_GET[2], $_GET[3], $_GET[4] );
			$q = $q['Null'] === 'NO' ? '""' : 'null';
		};
		wa::$sql->q_sent( 'update ?a set ?a=?b where ?a=?s', $_GET[3], $_GET[4], $q, $_GET[5], $_GET[6] );
		break;
	case 'data_drop':
		isset( $_GET[4], $_GET[5] )
			? wa::$sql->q_delete( $_GET[3], 'where ?a=?s limit 1', $_GET[4], $_GET[5] )
			: wa::$sql->q_delete( $_GET[3] );
		break;
#else
	default:
	 	wa::$buffers = [ 'Unknown command', 'warn' ];
};
wa::end_str_status();