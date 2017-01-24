<?php
require 'main.php';
wa::locale();
wa::htt();
wa::htt_title( 'WA MySQL' );
wa::get_signin_admin() || wa::end_htt_signin_admin( '?/wa_mysql' );
wa::$buffers->css[] = 'appweb/wa_mysql/style.css';
wa::$buffers->js[] = 'appweb/wa_mysql/script.js';
wa::htt_nav( wa_sign_out, 'javascript:$.setcookie_remove("wa_admin"),$.go("?/wa_mysql");', 1 );
wa::htt_nav( 'WA Admin', '?/wa_admin' );
wa::htt_nav( 'Connects', '?/wa_mysql' );
if ( isset( $_GET[1] ) )
{
	$wa_mysql = wa::get_cookie( 'wa_mysql' );
	if ( wa_mysql_connect( $wa_mysql ) )
	{
		goto wa_mysql;
	};
};
if ( isset( $_POST['hostaddr'], $_POST['username'], $_POST['password'] ) )
{
	wa::set_cookie( 'wa_mysql', [
		$_POST['hostaddr'],
		$_POST['username'],
		$_POST['password']
	] );
	wa::$headers = [ 'location' => '?/wa_mysql(1)' ];
	exit;
};
wa::$buffers->write->tag_form(function( $q ) use( &$wa_mysql )
{
	$q['class'] = 'wa_mysql_connect';
	$q->div = 'Connect to MySQL Server';
	$q->tag_input()->set_attrs([
		'name' => 'hostaddr',
		'value' => $wa_mysql[0] ? $wa_mysql[0] : WA_DB_HOST,
		'placeholder' => 'Enter MySQL Host address[:port]',
	]);
	$q->tag_input()->set_attrs([
		'name' => 'username',
		'value' => $wa_mysql[1] ? $wa_mysql[1] : WA_DB_USER,
		'placeholder' => 'Enter MySQL username',
	]);
	$q->tag_input()->set_attrs([
		'name' => 'password',
		'value' => $wa_mysql[2] ? $wa_mysql[2] : WA_DB_PASSWORD,
		'placeholder' => 'Enter MySQL user password',
	]);
	$q->span = wa::$errors ? join( "\n", wa::$errors ) : NULL;
	$q->tag_button( 'Start Connect MySQL', 'submit' )['class'] = 'b';
});
goto end_wa_mysql;

wa_mysql:
wa::$user = (object)NULL;
wa::$sql->q_query( 'show collation' )->each(function( $q )
{
	if ( $q['Compiled'] == 'Yes' )
	{
		isset( wa::$user->collation[ $q['Charset'] ] ) || wa::$user->collation[ $q['Charset'] ] = [];
		$w = &wa::$user->collation[ $q['Charset'] ];
		$w[] = [ $q['Collation'], $q['Default'] == 'Yes' ];
	};
});
if ( isset( $_COOKIE['wa_mysql_charset'] ) )
{
	wa::$sql->set_charset( $_COOKIE['wa_mysql_charset'] );
	wa::htt_nav( 'UTF8', 'javascript:$.setcookie_remove("wa_mysql_charset"),$.go(location.search);', 1 );
}
else
{
	wa::htt_nav( 'Latin1', 'javascript:$.setcookie("wa_mysql_charset","utf8"),$.go(location.search);', 1 );
};
wa::htt_nav( 'Database', '?/wa_mysql(1)' );
wa::htt_nav( 'Users', '?/wa_mysql(1)users' );
wa::htt_nav( 'Show Status', '?/wa_mysql(1)show_status' );
wa::htt_nav( 'Show Variables', '?/wa_mysql(1)show_variables' );
wa::htt_nav( 'Show Processlist', '?/wa_mysql(1)show_processlist' );
wa::$buffers->write->tag_table(function( $q )
{
	$q->thead->tr->td = wa::$sql->stat;
	$q->thead->tr->td['colspan'] = 2;
	$q->thead->tr->td['style'] = 'padding:12px 0 0 12px';
	$w = &$q->tbody->tr->td;
	$w['class'] = 'wa_mysql_list';
	foreach ( wa::$sql->q_query( 'show databases' ) as $e )
	{
		$e = [ $e['Database'], urlencode( $e['Database'] ) ];
		$r = &$w->nav[];
		$r->tag_a( $e[0], '?/wa_mysql(1)table_status(2)' . $e[1] );
		if ( isset( $_GET[2] ) && $_GET[2] == $e[1] )
		{
			wa::$user->databasename = $e;
			wa::$sql->select_db( wa::$user->location = $e[0] );
			$t = &$r->nav;
			wa::$user->table_status = wa::$sql->q_query( 'show table status' );
			foreach ( wa::$user->table_status as $y )
			{
				$u = urlencode( $y['Name'] );
				$t->tag_a( $y['Name'], '?/wa_mysql(1)table_fields(2)' . $e[1] . '(3)' . $u )->font = '(' . $y['Rows'] . ')';
				if ( isset( $_GET[3] ) && $_GET[3] == $u )
				{
					wa::$user->tablename = [ $y['Name'], $u ];
					wa::$user->location .= '.' . $y['Name'];
				};
			};
		};
	};
	wa::$buffers->write = &$q->tbody->tr->td[];
	wa::$buffers->write['class'] = 'wa_mysql_info';
});
if ( isset( wa::$user->tablename ) )
{
	unset( wa::$user->table_status );
	switch ( $_GET[1] )
	{
		case 'field_editor':	goto field_editor;
		case 'data_table':		goto data_table;
		case 'data_editor':		goto data_editor;
		default:				goto table_fields;
	};
};
if ( isset( wa::$user->databasename ) )
{
	goto table_status;
};
wa::$buffers->write->tag_table(function( $q )
{
	$q['class'] = 'wa_data_table';
	$q->thead->tr->td = wa::$sql->q_query( 'select current_user()' )->fetch_row()[0];
	$w = &$q->thead->tr[]->td;
	$w['class'] = 'wa_mysql_action';
	$w->span = 'Create database';
	$w->tag_input()->set_attrs([
		'onkeydown' => 'event.keyCode==13&&wa_mysql_ajax(this,"database_create",this.value)',
		'placeholder' => 'Enter new database name',
		'style' => 'width:200px'
	]);
 	$w->tag_button( 'Submit' )['onclick'] = 'wa_mysql_ajax(this,"database_create",this.previousSibling.value)';
 	$w->tag_button( 'Sync SQL Command' )['onclick'] = 'wa_mysql_sql(this)';
	$q->thead->tr[]->td = 'Sync Structured Query Language Command';
	$q->tbody['class'] = 'wa_mysql_sql';
	$w = &$q->tbody->tr[]->td->textarea;
	$w['style'] = 'width:800px;height:320px';
	$w['placeholder'] = 'Enter SQL command, Press the top button to synchronize';
});
goto end_wa_mysql;

table_status:
wa::$buffers->write->tag_table(function( $q )
{
	$q['class'] = 'wa_data_table';
	$w = [
		'Engine',
		'Version',
		'Row_format',
		'Rows',
		'Avg_row_length',
		'Data_length',
		//'Max_data_length',
		'Index_length',
		'Data_free',
		'Auto_increment',
		'Create_time',
		'Update_time',
		'Check_time',
		'Collation',
		'Checksum',
		'Create_options'
	];
	$e = count( $w ) + 3;
	$q->thead->tr->td['colspan'] = $e;
	$q->thead->tr->td = wa::$user->location;
	$r = &$q->thead->tr[]->td;
	$r['colspan'] = $e;
	$r['class'] = 'wa_mysql_action';
	$r->span = 'Create table';
	$r->tag_input()->set_attrs([
		'onkeydown' => 'event.keyCode==13&&wa_mysql_ajax(this,"table_create",this.value)',
		'placeholder' => 'Enter new table name',
		'style' => 'width:200px'
	]);
	$r->tag_button( 'Submit' )['onclick'] = 'wa_mysql_ajax(this,"table_create",this.previousSibling.value)';
	wa_mysql_select_collation(
		$r->select,
		'wa_mysql_ajax(this,"database_collate",this.value)',
		'/CHARACTER SET (\w+) ?(?:COLLATE (\w+))?/',
		$t = wa::$sql->q_query( 'show create database ?a', wa::$user->databasename[0] )->fetch_row()[1]
	);
	$r->tag_button( 'Delete all table' )->set_attrs([
		'data-confirm' => 'Cannot undo',
		'onclick' => 'wa_mysql_ajax(this,"database_empty")',
		'class' => 'r'
	]);
	$r->tag_button( 'Delete this database' )->set_attrs([
		'data-confirm' => 'Cannot undo',
		'onclick' => 'wa_mysql_ajax(this,"database_drop")',
		'class' => 'r'
	]);
	$q->tfoot->tr->td['colspan'] = $e;
	$q->tfoot->tr->td['style'] = 'padding:8px';
	$q->tfoot->tr->td = $t;
	$q->tbody['class'] = 'wa_mysql_table_action';
	$r = &$q->thead->tr[];
	$r->td = 'Del';
	$r->td[] = 'Comment';
	$r->td[] = 'Name';
	foreach ( $w as $t )
	{
		$r->td[] = $t;
	};
	foreach ( wa::$user->table_status as $e )
	{
		$r = &$q->tbody->tr[];
		$r['data-tablename'] = $e['Name'];
		$t = &$r->td;
		$t['data-confirm'] = 'Cannot undo';
		$t['onclick'] = 'wa_mysql_ajax(this,"table_drop",this.parentNode.dataset.tablename)';
		$t = &$r->td[];
		$t['data-prompt'] = 'Comment';
		$t['data-value'] = $e['Comment'];
		$t['onclick'] = 'wa_mysql_ajax(this,"table_comment",this.parentNode.dataset.tablename)';
		$t[0] = $e['Comment'];
		$t = &$r->td[];
		$t->tag_a( $e['Name'], '?/wa_mysql(1)(2)'. wa::$user->databasename[1] .'(3)' . urlencode( $e['Name'] ) );
		foreach ( $w as $t )
		{
			$r->td[] = $e[ $t ];
		};
	};
});
goto end_wa_mysql;

table_fields:
wa::$buffers->write->tag_table(function( $q )
{
	wa::$user->table_create = wa::$sql->q_query( 'show create table ?a', wa::$user->tablename[0] )->fetch_row()[1];
	$q['class'] = 'wa_data_table';
	$w = [
		'Type',
		'Collation',
		'Null',
		'Key',
		'Extra',
		'Privileges'
	];
	$e = count( $w ) + 9;
	$q->thead->tr->td = wa::$user->location;
	$q->thead->tr->td['colspan'] = $e;
	$r = &$q->thead->tr[]->td;
	$r['class'] = 'wa_mysql_action';
	$r['colspan'] = $e;
	$r->span = 'Rename this table';
	$r->tag_input()->set_attrs([
		'onkeydown' => 'event.keyCode==13&&wa_mysql_ajax(this,"table_rename",this.value)',
		'placeholder' => 'Table rename',
		'style' => 'width:180px'
	]);
	$r->tag_button( 'Submit' )['onclick'] = 'wa_mysql_ajax(this,"table_rename",this.previousSibling.value)';
	$r->add( 'select' )->set(function( $q )
	{
		$q['onchange'] = 'wa_mysql_ajax(this,"table_engine",this.value)';
		$w = strrpos( wa::$user->table_create, 'ENGINE=' ) + 7;
		$e = strpos( wa::$user->table_create, ' ', $w );
		$w = $e > $w ? substr( wa::$user->table_create, $w, $e - $w ) : substr( wa::$user->table_create, $w );
		foreach ( wa::$sql->q_query( 'show engines' ) as $e )
		{
			if ( $e['Support'] == 'YES' || $e['Support'] == 'DEFAULT' )
			{
				$r = &$q->option[];
				$r['value'] = $e['Engine'];
				$e['Engine'] == $w && $r['selected'] = TRUE;
				$r[0] = $e['Engine'];
			};
		};
	});
	wa_mysql_select_collation(
		$r->add( 'select' ),
		'wa_mysql_ajax(this,"table_collate",this.value)',
		'/CHARSET=(\w+) ?(?:COLLATE=(\w+))?/',
		wa::$user->table_create
	);
	$r = &$q->thead->tr[]->td;
	$r['colspan'] = $e;
	$r['class'] = 'wa_mysql_action';
	$r->tag_button( 'Invert selection' )['onclick'] = 'wa_mysql_checked_invert(this.parentNode.parentNode.parentNode.nextSibling)';
	$r->tag_button( 'View data table' )->set_attrs([
		'onclick' => 'wa_mysql_view_data(this.parentNode.parentNode.parentNode.nextSibling)',
		'class' => 'g'
	]);
	$r->tag_button( 'Insert data' )->set_attrs([
		'onclick' => 'wa.query_act({1:"data_editor"})',
		'class' => 'b'
	]);
	$r->tag_button( 'Fields insert' )->set_attrs([
		'onclick' => 'wa.query_act({1:"field_editor"})',
		'class' => 'b'
	]);
	$r->tag_button( 'Truncate this table' )->set_attrs([
		'data-confirm' => 'Cannot undo',
		'onclick' => 'wa_mysql_ajax(this,"table_truncate")',
		'class' => 'r'
	]);
	$r->tag_button( 'Drop this table' )->set_attrs([
		'data-confirm' => 'Cannot undo',
		'onclick' => 'wa_mysql_ajax(this,"table_drop")',
		'class' => 'r'
	]);
	$e = &$q->thead->tr[];
	$r = &$e->td;
	$r['colspan'] = 6;
	$r[0] = 'Select / Functions';
	$e->td[] = 'Comment';
	$e->td[] = 'Default';
	$e->td[] = 'Field';
	foreach ( $w as $r)
	{
		$e->td[] = $r;
	};
	$q->tbody['class'] = 'wa_mysql_field_editor';
	foreach ( wa::$sql->q_query( 'show full fields from ?a', wa::$user->tablename[0] ) as $e )
	{
		$r = &$q->tbody->tr[];
		$r['data-fieldname'] = $e['Field'];
		$t = &$r->td;
		$t->tag_input( 'checkbox' );
		$t = &$r->td[];
		$t['data-confirm'] = 'Cannot undo';
		$t['onclick'] = 'wa_mysql_ajax(this,"field_drop",this.parentNode.dataset.fieldname)';
		$t['title'] = 'Drop';
		$t = &$r->td[];
		$t['onclick'] = 'wa.query_act({1:"field_editor",4:this.parentNode.dataset.fieldname})';
		$t['title'] = 'Edit';
		$t = &$r->td[];
		$t['onclick'] = 'wa_mysql_ajax(this,"field_index",this.parentNode.dataset.fieldname)';
		$t['title'] = 'Index';
		$t = &$r->td[];
		$t['onclick'] = 'wa_mysql_ajax(this,"field_unique",this.parentNode.dataset.fieldname)';
		$t['title'] = 'Unique';
		$t = &$r->td[];
		$t['onclick'] = 'wa_mysql_ajax(this,"field_primary",this.parentNode.dataset.fieldname)';
		$t['title'] = 'Primary';
		$t = &$r->td[];
		$t['data-prompt'] = 'Comment';
		$t['data-value'] = $t[0] = $e['Comment'];
		$t['onclick'] = 'wa_mysql_ajax(this,"field_comment",this.parentNode.dataset.fieldname)';
		$t = &$r->td[];
		$t['data-prompt'] = 'Default';
		$t['data-value'] = $t[0] = $e['Default'];
		$t['onclick'] = 'wa_mysql_ajax(this,"field_default",this.parentNode.dataset.fieldname)';
		$t = &$r->td[];
		$t['data-prompt'] = 'Field';
		$t['data-value'] = $t[0] = $e['Field'];
		$t['onclick'] = 'wa_mysql_ajax(this,"field_rename",this.parentNode.dataset.fieldname)';
		foreach ( $w as $t )
		{
			$r->td[] = $e[ $t ];
		};
	};
});
wa::$buffers->write->tag_table(function( $q )
{
	$q['class'] = 'wa_data_table';
	$w = [
		'Table',
		'Non_unique',
		'Key_name',
		'Seq_in_index',
		'Column_name',
		'Collation',
		'Cardinality',
		'Sub_part',
		'Packed',
		'Null',
		'Index_type',
		'Comment',
		'Index_comment'
	];
	$e = &$q->thead->tr;
	$e->td = 'Del';
	foreach ( $w as $r )
	{
		$e->td[] = $r;
	};
	$q->tbody['class'] = 'wa_mysql_index_editor';
	foreach ( wa::$sql->q_query( 'show index from ?a', wa::$user->tablename[0] ) as $r )
	{
		$e = &$q->tbody->tr[];
		$e['data-keyname'] = $r['Key_name'];
		$t = &$e->td;
		$t['data-confirm'] = 'Cannot undo';
		$t['onclick'] = 'wa_mysql_ajax(this,"index_drpo",this.parentNode.dataset.keyname)';

		foreach ( $w as $t )
		{
			$e->td[] = $r[ $t ];
		};
	};
});
wa::$buffers->write->tag_table(function( $q )
{
	$q['class'] = 'wa_data_table';
	$q->thead->tr->td = 'Create table code';
	$q->tbody['class'] = 'wa_mysql_sql';
	$q->tbody->tr->td->pre = wa::$user->table_create;
});
goto end_wa_mysql;

field_editor:
if ( isset( $_GET[4] ) )
{
	$wa_mysql_field = wa_mysql_field_select( wa::$user->databasename[0], wa::$user->tablename[0], $_GET[4] );
	if ( $wa_mysql_field === NULL )
	{
		goto end_wa_mysql;
	};
}
else
{
	$wa_mysql_field = [ 'Type' => 'INT', 'Length' => '10', 'Attribute' => 'UNSIGNED' ];
};
$wa_mysql_field_editor['Type']['value'] = array_combine( $wa_mysql_field_types, $wa_mysql_field_types );
wa::htt_form_post( $wa_mysql_field_editor, $wa_mysql_field )->set(function( $q ) use( &$wa_mysql_field )
{
	$w = isset( $wa_mysql_field['Field'] )
		? [ 'field_update(4)' . urlencode( $wa_mysql_field['Field'] ), $wa_mysql_field['Field'] ]
		: [ 'field_insert', '' ];
	$q['action'] = '?/wa_mysql/ajax(1)' . $w[0] . '(2)' . wa::$user->databasename[1] . '(3)' . wa::$user->tablename[1];
	$q['style'] = 'padding:0';
	$q->table['class'] = 'wa_data_table wa_mysql_editor';
	$q->table['style'] = 'margin:12px 0';
	$q->table->thead->tr->td = wa::$user->location . '[' . $w[1] . ']';
	$w = &$q->table->thead->tr[]->td;
	$w['colspan'] = 3;
	$w['class'] = 'wa_mysql_action';
	$w->tag_button( 'Return this table' )['onclick'] = 'wa.query_act({1:"table_fields",4:null})';
	$w->span = 'If the field type is "enum" or "set", please in length enter this format, such as: "a", "b", "c" ...';
	$w = &$q->table->thead->tr[];
	$w->td = 'Name';
	$w->td[] = 'Value';
	$w->td[] = 'Explain';
	$q->table->tbody->tr->td[0]['style'] = 'width:100px';
	$q->table->tbody->tr->td[1]['style'] = 'width:420px';
	$q->table->tbody->tr->td[2]['style'] = 'width:210px';
	$w = $q->table->tbody->tr[4]->td[1]->select;
	foreach ( wa::$user->collation as $e => $r )
	{
		$t = &$w->optgroup[];
		$t['label'] = $e;
		foreach ( $r as $y )
		{
			$r = &$t->option[];
			$r['value'] = $r[0] = $y[0];
			isset( $wa_mysql_field['Collation'] )
				&& $wa_mysql_field['Collation'] == $y[0]
				&& $r['selected'] = TRUE;
		};
	};
	$w = &$q->table->tbody->tr[9]->td[1]->select;
	foreach ( wa::$sql->q_query( 'desc ?a', wa::$user->tablename[0] ) as $e )
	{
		$r = &$w->option[];
		$r['value'] = $r[0] = $e['Field'];
	};
});
goto end_wa_mysql;

data_table:
wa::$user->table_fields = [];
foreach ( wa::$sql->q_query( 'desc ?a', wa::$user->tablename[0] ) as $q )
{
	wa::$user->table_fields[] = $q['Field'];
	if ( $q['Key'] == 'PRI' || $q['Key'] == 'UNI' )
	{
		if ( !isset( wa::$user->field_only ) || wa::$user->field_only[0] != 'PRI' )
		{
			wa::$user->field_only = [ $q['Key'], $q['Field'] ];
		};
	};
};
if ( isset( $_GET[6] ) && $_GET[6] != '' )
{
	$q = array_intersect( array_map( 'urldecode', explode( '/', $_GET[6], count( wa::$user->table_fields ) ) ), wa::$user->table_fields );
	isset( $q[0] ) || $q = wa::$user->table_fields;
}
else
{
	$q = wa::$user->table_fields;
};
if ( isset( wa::$user->field_only ) )
{
	$w = [ 0 => 'Function' ];
	$e = wa::$user->field_only[1];
	in_array( $e, $q ) || $w[ $e ] = FALSE;
	$e = function( $w, $r ) use( $q, $e )
	{
		$w['data-value'] = $r[ $e ];
		$e = &$w->td;
		$e['onclick'] = 'wa_mysql_data_drop(this)';
		$e = &$w->td[];
		$e['onclick'] = 'wa_mysql_data_editor(this)';
		foreach ( $q as $e )
		{
			if ( $r[ $e ] === NULL )
			{
				$e = &$w->td[];
				$e['class'] = 'null';
				$e[0] = 'NULL';
			}
			else
			{
				if ( strlen( $r[ $e ] ) > WA_MYSQL_MAXLEN )
				{
					$t = &$w->td[];
					$t['class'] = 'long';
					$t[0] = wa::str_omit( $r[ $e ], WA_MYSQL_MAXLEN );
				}
				else
				{
					$w->td[] = $r[ $e ];
				};
			};
		};
	};
}
else
{
	$w = [];
	$e = function( $w, $e ) use( $q )
	{
		foreach ( $q as $r )
		{
			if ( $e[ $r ] === NULL )
			{
				$r = &$w->td[];
				$r['class'] = 'null';
				$r[0] = 'NULL';
			}
			else
			{
				if ( strlen( $e[ $r ] ) > WA_MYSQL_MAXLEN )
				{
					$t = &$w->td[];
					$t['class'] = 'long';
					$t[0] = wa::str_omit( $e[ $r ], WA_MYSQL_MAXLEN );
				}
				else
				{
					$w->td[] = $e[ $r ];
				};
			};
		};
	};
};
foreach ( $q as $r )
{
	$w[ $r ] = [ TRUE, $r ];
};
$q = count( $q );
$wa_mysql_merge_query = wa::get_filter( wa::$user->table_fields );
$wa_mysql_stat_rows = wa::$sql->get_rows( wa::$user->tablename[0], $wa_mysql_merge_query );
wa::htt_data_table( wa::$user->tablename[0], $w, $e, [
	'merge_query' => $wa_mysql_merge_query,
	'stat_rows' => $wa_mysql_stat_rows,
	'page_rows' => 21
] )->set(function( $w ) use( $q, $wa_mysql_stat_rows )
{
	if ( isset( wa::$user->field_only ) )
	{
		$q += 2;
		$w->thead->tr->td['colspan'] = 2;
		$w->tbody['class'] = 'wa_mysql_data_table';
		$w->tbody['data-field'] = wa::$user->field_only[1];
		isset( $w->tfoot->tr ) && $w->tfoot->tr->td['colspan'] = $q;
		wa::$buffers->script = '$(wa_mysql_data_edits)';
	};
	$e = $w->thead->add_top( 'tr' )->add( 'td' );
	$e['colspan'] = $q;
	$e = $e->ins_filter( array_combine( wa::$user->table_fields, wa::$user->table_fields ) )->dl->dt;
	$e->tag_button( 'Return this table' )['onclick'] = 'wa.query_act({1:"table_fields",4:null})';
	$e->tag_button( 'Insert data' )->set_attrs([
		'onclick' => 'wa.query_act({1:"data_editor"})',
		'class' => 'b'
	]);
	$e->tag_button( 'Delete all data' )->set_attrs([
		'data-confirm' => 'Cannot undo',
		'onclick' => 'wa_mysql_ajax(this,"data_drop")',
		'class' => 'r'
	]);
	$e->span = 'Filter the following conditions were found ' . $wa_mysql_stat_rows . ' record .';
	$e = $w->thead->add_top( 'tr' )->td;
	$e['colspan'] = $q;
	$e[0] = wa::$user->location;
});
goto end_wa_mysql;

data_editor:
call_user_func(function( $q ) use( &$wa_mysql_data_editor, &$wa_mysql_data_values )
{
	foreach ( wa::$sql->q_query( 'show full fields from ?a', $q ) as $q )
	{
		$w = strpos( $q['Type'], '(' );
		if ( $w !== FALSE )
		{
			$q['Length'] = substr( $q['Type'], $w + 1, strrpos( $q['Type'], ')' ) - $w - 1 );
			$q['Type'] = substr( $q['Type'], 0, $w );
		};
		$w = $q['Field'];
		$wa_mysql_data_editor[ $w ] = [
			'test' => [ 0, 0 ],
			'name' => $w,
			'note' => $q['Comment']
		];
		$q['Default'] === NULL || $wa_mysql_data_values[ $w ] = $q['Default'];
		if ( $q['Type'] == 'enum' || $q['Type'] == 'set' )
		{
			if ( $q['Type'] == 'set' )
			{
				$wa_mysql_data_editor[ $w ]['type'] = 'checkbox';
				$wa_mysql_data_editor[ $w ]['value'] = [];
			}
			else
			{
				$wa_mysql_data_editor[ $w ]['type'] = 'select';
				$wa_mysql_data_editor[ $w ]['value'] = [ '' => '' ];
			};
			preg_match_all( '/\'([^\']+)\'/', $q['Length'], $r );
			foreach ( $r[1] as $r )
			{
				$wa_mysql_data_editor[ $w ]['value'][ $r ] = $r;
			};
			continue;
		};
		if ( strpos( 'tinyblob|tinytext|blob|text|mediumblob|mediumtext|longblob|longtext', $q['Type'] ) )
		{
			$wa_mysql_data_editor[ $w ]['type'] = 'textarea';
			continue;
		};
		$wa_mysql_data_editor[ $w ]['type'] = 'text';
	};
}, wa::$user->tablename[0] );
if ( isset( $_GET[4], $_GET[5] ) )
{
	$wa_mysql_data = [ urldecode( $_GET[4] ), urldecode( $_GET[5] ) ];
	$wa_mysql_data_values = wa::$sql->get_only( wa::$user->tablename[0], $wa_mysql_data[0], $wa_mysql_data[1] );
	if ( $wa_mysql_data_values == NULL )
	{
		goto end_wa_mysql;
	};
}
else
{
	$wa_mysql_data = FALSE;
};
wa::htt_form_post( $wa_mysql_data_editor, $wa_mysql_data_values )->set(function( $q ) use(
	&$wa_mysql_function_list,
	&$wa_mysql_data_editor,
	&$wa_mysql_data_values,
	$wa_mysql_data )
{
	$q['style'] = 'padding:0';
	$q['onsubmit'] = 'return wa.form_post(this,{})';
	$q->table['class'] = 'wa_data_table wa_mysql_editor';
	$q->table['style'] = 'margin:12px 0';
	$w = &$q->table->thead->tr->td;
	$w['colspan'] = 4;
	$w[0] = wa::$user->location;
	$w = &$q->table->thead->tr[]->td;
	$w['colspan'] = 4;
	$w['class'] = 'wa_mysql_action';
	$w->tag_button( 'Return this table' )['onclick'] = 'wa.query_act({1:"table_fields",4:null})';
	$w->tag_button( 'Goto data table' )['onclick'] = 'wa.query_act({1:"data_table",4:null,5:null})';
	if ( $wa_mysql_data )
	{
		$w->span = wa::$sql->q_join([ 'Update data where ?a=?s', $wa_mysql_data[0], $wa_mysql_data[1] ]);
		$w = '(4)' . $_GET[4] . '(5)' . $_GET[5];
	}
	else
	{
		$w->span = 'Insert data';
		$w = '';
	};
	$q['action'] = '?/wa_mysql/ajax(1)data_editor(2)' . wa::$user->databasename[1] . '(3)' . wa::$user->tablename[1] . $w;
	$w = &$q->table->thead->tr[];
	$w->td = 'Field';
	$w->td[] = 'Value';
	$w->td[] = 'Function';
	$w->td[] = 'Comment';
	$q->table->tbody->tr->td[0]['style'] = 'width:100px';
	$q->table->tbody->tr->td[1]['style'] = 'width:420px';
	$q->table->tfoot->tr->td['colspan'] = 4;
	$w = '<option></option>';
	foreach ( $wa_mysql_function_list as $e )
	{
		$w .= '<option value="' . $e . '">' . $e . '</option>';
	};
	$e = 0;
	foreach ( $wa_mysql_data_editor as $r => $t )
	{
		$t = $q->table->tbody->tr[ $e++ ]->td[1]->add_after( 'td' );
		$t = &$t->select;
		$t['name'] = 'function_' . $r;
		$t->add_raw( $w );
	};
});
goto end_wa_mysql;

show_users:
goto end_wa_mysql;

show_status:
goto end_wa_mysql;

show_variables:
goto end_wa_mysql;

show_processlist:
goto end_wa_mysql;
/*
wa_mysql_editor_data:
wa::$buffers->write->tag_table(function( $q ) use( &$wa_mysql_query_act, &$wa_mysql_tablename )
{
	$q->set_class( 'wa_mysql_table_std' )->add_before( 'form' )->set_attrs([
		'action' => '?/wa_mysql/ajaxquery(1)editor_data' . $wa_mysql_query_act . ( isset( $_GET[4] ) ? '(4)' . $_GET[4] : '' ) . ( isset( $_GET[5] ) ? '(5)' . $_GET[5] : '' ),
		'method' => 'post',
		'target' => '_self',
		'onsubmit' => 'return wa.mysql_ajaxform(this)',
		'autocomplete' => 'off',
		'data-pageto' => '?/wa_mysql(1)select' . $wa_mysql_query_act
	])->add_dom( $q->get_dom() );
	$w = isset( $_GET[4], $_GET[5] )
		? wa::$sql->get_only( $wa_mysql_tablename, urldecode( $_GET[4] ), urldecode( $_GET[5] ) )
		: NULL;
	$q->thead->add( 'tr' )->set_class( 'wa_mysql_title' )->add( 'td', urldecode( $_GET[2] ) . '.' . $wa_mysql_tablename )->set_attr( 'colspan', 5 );
	$e = $q->thead->add( 'tr' )->add( 'td' )->set_attr( 'colspan', 5 );
	$e->add( 'span', 'Editor data' );
	$e = $q->thead->add( 'tr' )->set_class( 'wa_mysql_title' );
	$e->add( 'td', 'Field' );
	$e->add( 'td', 'Type' );
	$e->add( 'td', 'Function' );
	$e->add( 'td', 'Value' );
	$e->add( 'td', 'Comment' );
	foreach ( wa::$sql->q_query( 'show full fields from ?a', $wa_mysql_tablename ) as $r )
	{
		$t = $q->tbody->add( 'tr' );
		$t->add( 'td', $r['Field'] );
		$t->add( 'td', $r['Type'] );
		$t->add( 'td' )->add( 'select' )->set(function( $q ) use( &$r )
		{
			$q->set_attrs([
				'name' => $r['Field'] . '[]',
				'style' => 'width:130px'
			]);
			foreach ( [ '',
				'ascii',
				'char',
				'soundex',
				'encrypt',
				'lcase',
				'ucase',
				'now',
				'password',
				'encode',
				'decode',
				'md5',
				'rand',
				'last_insert_id',
				'count',
				'avg',
				'sum',
				'curdate',
				'curtime',
				'from_days',
				'from_unixtime',
				'period_add',
				'period_diff',
				'to_days',
				'unix_timestamp',
				'user',
				'weekday',
				'__null__' ] as $e )
			{
				$q->add( 'option', $e )->set_attr( 'value', $e );
			};
		});
		$t->add( 'td' )->tag_input()->set_attrs([
			'name' => $r['Field'] . '[]',
			'value' => $w[ $r['Field'] ],
			'style' => 'width:400px'
		]);
		$t->add( 'td', $r['Comment'] );
	};
	$e = $q->tfoot->add( 'tr' )->set_class( 'wa_mysql_act_tr a_bffe a_tar' )->add( 'td' )->set_attr( 'colspan', 5 );
	$e->tag_button( 'Submit', 'submit' )->set_class( 'wa_btn_b' );
});
goto end_wa_mysql;

wa_mysql_show_status:
wa::$buffers->write->tag_table(function( $q )
{
	$q['class'] = 'wa_mysql_table_std';
	$w = $q->thead->add( 'tr' )->set_attr( 'class', 'wa_mysql_title' );
	$w->add( 'td', 'Variable_name' );
	$w->add( 'td', 'Value' );
	$w->add( 'td', 'Variable_name' );
	$w->add( 'td', 'Value' );
	$w->add( 'td', 'Variable_name' );
	$w->add( 'td', 'Value' );
	$w = -1;
	$e = $q->tbody->add( 'tr' );
	foreach ( wa::$sql->q_query( 'show status' ) as $r )
	{
		++$w % 3 || $e = $q->tbody->add( 'tr' );
		$e->add( 'td', $r['Variable_name'] );
		$e->add( 'td', $r['Value'] );
	};
});
goto end_wa_mysql;

wa_mysql_show_variables:
wa::$buffers->write->tag_table(function( $q )
{
	$q->set_class( 'wa_mysql_table_std' );
	$w = $q->thead->add( 'tr' )->set_attr( 'class', 'wa_mysql_title' );
	$w->add( 'td', 'Variable_name' );
	$w->add( 'td', 'Value' );
	$w->add( 'td', 'Variable_name' );
	$w->add( 'td', 'Value' );
	$w = 0;
	foreach ( wa::$sql->q_query( 'show variables' ) as $e )
	{
		++$w % 2 && $r = $q->tbody->add( 'tr' );
		$r->add( 'td', htmlentities( $e['Variable_name'], ENT_XML1 ) );
		if ( strpos( $e['Value'], ',' ) === FALSE )
		{
			$r->add( 'td', htmlentities( $e['Value'], ENT_XML1 ) );
		}
		else
		{
			$r->add( 'td' )->add( 'pre', htmlentities( strtr( $e['Value'], ',', "\n" ), ENT_XML1 ) );
		};
	};
});
goto end_wa_mysql;

wa_mysql_show_processlist:
wa::$buffers->write->tag_table(function( $q )
{
	$q->set_class( 'wa_mysql_table_std' );
	$w = $q->thead->add( 'tr' )->set_attr( 'class', 'wa_mysql_title' );
	$e = [ 'Id', 'User', 'Host', 'db', 'Command', 'Time', 'State', 'Info' ];
	foreach ( $e as $r )
	{
		$w->add( 'td', $r );
	};
	foreach ( wa::$sql->q_query( 'show full processlist' ) as $r )
	{
		$w = $q->tbody->add( 'tr' );
		foreach ( $e as $t )
		{
			$w->add( 'td', $r[ $t ] );
		};
	};
});
goto end_wa_mysql;

wa_mysql_connect:
wa::$buffers->write->tag_table(function( $q ) use( &$wa_mysql )
{
	$q->set_class( 'wa_mysql_connect' )->add_before( 'form' )->set_attrs([
		'action' => '?/wa_mysql/ajaxquery(1)mysql_connect',
		'method' => 'post',
		'target' => '_self',
		'onsubmit' => 'return wa.mysql_ajaxform(this)',
		'autocomplete' => 'off',
		'data-pageto' => '?/wa_mysql(1)db'
	])->add_dom( $q->get_dom() );
	$q->thead->add( 'tr' )->add( 'td', 'Welcome use Simple MySQL' )->set_attr( 'colspan', 2 );
	$w = $q->tbody->add( 'tr' );
	$w->add( 'td', 'Host address' )->set_style( 'text-align:right' );
	$w->add( 'td' )->tag_input()->set_attrs([
		'name' => 'hostaddr',
		'value' => $wa_mysql['hostaddr'] ? $wa_mysql['hostaddr'] : WA_DB_HOST,
		'style' => 'width:210px',
		'placeholder' => 'Enter MySQL Host address',
	]);
	$w = $q->tbody->add( 'tr' );
	$w->add( 'td', 'Username' )->set_style( 'text-align:right' );
	$w->add( 'td' )->tag_input()->set_attrs([
		'name' => 'username',
		'value' => $wa_mysql['username'] ? $wa_mysql['username'] : WA_DB_USER,
		'style' => 'width:210px',
		'placeholder' => 'Enter MySQL username',
	]);
	$w = $q->tbody->add( 'tr' );
	$w->add( 'td', 'Password' )->set_style( 'text-align:right' );
	$w->add( 'td' )->tag_input()->set_attrs([
		'name' => 'password',
		'value' => $wa_mysql['password'] ? $wa_mysql['password'] : WA_DB_PASSWORD,
		'style' => 'width:210px',
		'placeholder' => 'Enter MySQL user password',
	]);
	$q->tfoot->add( 'tr' )->add( 'td' )->set_attrs([
		'style' => 'text-align:center',
		'colspan' => 2
	])->tag_button( 'Start Connect MySQL', 'submit' )->set_attr( 'class', 'wa_btn_g' );
});
goto end_wa_mysql;


?>
*/

end_wa_mysql:
exit;