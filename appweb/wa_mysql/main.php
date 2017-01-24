<?php
const WA_MYSQL_MAXLEN = 42;
$wa_mysql_field_types = [
	'TINYINT',
	'SMALLINT',
	'MEDIUMINT',
	'INT',
	'BIGINT',
	'FLOAT',
	'DOUBLE',
	'DECIMAL',
	'DATE',
	'DATETIME',
	'TIMESTAMP',
	'TIME',
	'YEAR',
	'CHAR',
	'VARCHAR',
	'TINYBLOB',
	'TINYTEXT',
	'BLOB',
	'TEXT',
	'MEDIUMBLOB',
	'MEDIUMTEXT',
	'LONGBLOB',
	'LONGTEXT',
	'ENUM',
	'SET'
];
$wa_mysql_field_editor = [
	'Field' => [
		'test' => [ 1, 32 ],
		'name' => 'Field',
		'type' => 'text',
		'note' => 'Field name'
	],
	'Type' => [
		'test' => [ 3, 10, '/^[A-Z]{3,10}$/' ],
		'name' => 'Type',
		'type' => 'select'
	],
	'Length' => [
		'test' => [ 0, 1024 ],
		'name' => 'Length',
		'type' => 'text',
		'note' => 'Max length'
	],
	'Attribute' => [
		'test' => [ 0, 17, '/^[A-Z ]{0,17}$/' ],
		'name' => 'Attribute',
		'type' => 'select',
		'value' => [ '' => '', 'BINARY' => 'BINARY', 'UNSIGNED' => 'UNSIGNED', 'UNSIGNED ZEROFILL' => 'UNSIGNED ZEROFILL' ]
	],
	'Collation' => [
		'test' => [ 0, 32, '/^\w{0,32}$/' ],
		'name' => 'Collation',
		'type' => 'select',
		'value' => [ '' => '' ]
	],
	'Null' => [
		'test' => [ 2, 3, '/^(NO|YES)$/' ],
		'name' => 'Null',
		'type' => 'select',
		'value' => [ 'NO' => 'NO', 'YES' => 'YES' ]
	],
	'Default' => [
		'test' => [ 0, 1024 ],
		'name' => 'Default',
		'type' => 'text',
		'note' => 'Default value'
	],
	'Extra' => [
		'test' => [ 0, 14, '/^$|^AUTO_INCREMENT$/' ],
		'name' => 'Extra',
		'type' => 'select',
		'value' => [ '' => '', 'AUTO_INCREMENT' => 'AUTO_INCREMENT' ]
	],
	'Comment' => [
		'test' => [ 0, 255 ],
		'name' => 'Comment',
		'type' => 'text',
		'note' => 'Field comment'
	],
	'After' => [
		'test' => [ 0, 32 ],
		'name' => 'After',
		'type' => 'select',
		'value' => [ 'Default', 'At Top of Table' ]
	]
];
$wa_mysql_function_list = [
	'ASCII',
	'CHAR',
	'SOUNDEX',
	'ENCRYPT',
	'LCASE',
	'UCASE',
	'NOW',
	'PASSWORD',
	'ENCODE',
	'DECODE',
	'MD5',
	'RAND',
	'LAST_INSERT_ID',
	'COUNT',
	'AVG',
	'SUM',
	'CURDATE',
	'CURTIME',
	'FROM_DAYS',
	'FROM_UNIXTIME',
	'PERIOD_ADD',
	'PERIOD_DIFF',
	'TO_DAYS',
	'UNIX_TIMESTAMP',
	'USER',
	'WEEKDAY'
];
function wa_mysql_connect( $q )
{
	if ( isset( $q[0], $q[1], $q[2] ) )
	{
		wa::$sql = wa::call( 'webapp_sql', $q[0], $q[1], $q[2] );
		if ( wa::$sql->connect_error )
		{
			wa::$errors[] = wa::$sql->connect_error;
			return FALSE;
		};
		wa::$sql->stack_errors = &wa::$errors;
		return TRUE;
	};
	return FALSE;
}
function wa_mysql_select_collation( $q, $w, $e, $r )
{
	$q['onchange'] = $w;
	preg_match( $e, $r, $w );
	$q->option = '';
	foreach ( wa::$user->collation as $e => $r )
	{
		$t = &$q->optgroup[];
		$t['label'] = $e;
		foreach ( $r as $y )
		{
			$r = &$t->option[];
			$r['value'] = $r[0] = $y[0];
			if ( isset( $w[1] ) )
			{
				isset( $w[2] )
					? $y[0] == $w[2] && $r['selected'] = TRUE
					: $e == $w[1] && $y[1] && $r['selected'] = TRUE;
			};
		};
	};
}
function wa_mysql_field_select( $q, $w, $e )
{
	foreach ( wa::$sql->q_query( 'show full columns from ?a.?a', $q, $w ) as $q )
	{
		if ( $q['Field'] == $e )
		{
			$w = strpos( $q['Type'], '(' );
			if ( $w === FALSE )
			{
				$q['Attribute'] = '';
				$q['Length'] = '';
			}
			else
			{
				$e = strrpos( $q['Type'], ')' );
				$q['Attribute'] = strtoupper( substr( $q['Type'], $e + 2 ) );
				$q['Length'] = substr( $q['Type'], $w + 1, $e - $w - 1 );
				$q['Type'] = substr( $q['Type'], 0, $w );
			};
			$q['Type'] = strtoupper( $q['Type'] );
			return $q;
		};
	};
	return NULL;
}
function wa_mysql_field_submit( array $q )
{
	$w = wa::$sql->quote( $q['Field'] ) . ' ' . $q['Type'];
	$q['Length'] == ''			|| $w .= '(' . $q['Length'] . ')';
	$q['Attribute'] == ''		|| $w .= ' ' . $q['Attribute'];
	$q['Collation'] == ''		|| $w .= ' COLLATE ' . $q['Collation'];
	$q['Null'] == 'NO'			&& $w .= ' NOT';
	$w .= ' NULL';
	$q['Default'] == ''			|| $w .= ' DEFAULT ' . wa::$sql->escape( $q['Default'] );
	$q['Extra'] == ''			|| $w .= ' ' . $q['Extra'];
	$q['Comment'] == ''			|| $w .= ' COMMENT ' . wa::$sql->escape( $q['Comment'] );
	if ( isset( $q['After'] ) && $q['After'] != '0' )
	{
		$w .= $q['After'] == '1' ? ' FIRST' : ' AFTER ' . wa::$sql->quote( $q['After'] );
	};
	return $w;
}
