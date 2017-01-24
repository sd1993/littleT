<?php
wa::locale();
wa::htt();
wa::htt_title( '机房历史维护色调图，上限3000单' );
//header( 'Content-Type: text/html; charset=utf-8' );
//$sql = new webapp_sql( WA_DB_HOST, WA_DB_USER, WA_DB_PASSWORD, WA_DB_DATABASE );
wa::$buffers->style = <<<STYLE
div.f{
	width:80%;margin:42px auto;border:1px solid #b9b9b9;
}

div.f>div{
	width:10%;display:inline-block;line-height:42px;
}

STYLE;
$q = &wa::$buffers->write->div;
$q['class'] = 'f';

$users = [];
foreach ( wa::sql()->q_query( 'select username,name from rx_hr_staff where `group`="德胜机房运维中心" and resign=0' ) as $w )
{
	$users[ $w['username'] ] = $w['name'];
}


foreach ( wa::$sql->q_query( 'select count(*)as`tt`,d_userid from rx_ermsd_task_record group by d_userid' ) as $w )
{
	$e = &$q->div[];
	$e['style'] = 'background:#' . sprintf( '%06X', wa::hsl_color( ( 1 - $w['tt'] / 3000 ) * 0.5, 1, 0.5 ) );
	$e[0] = ( isset( $users[ $w['d_userid'] ] ) ? $users[ $w['d_userid'] ] : $w['d_userid'] ) . '(' . $w['tt'] . ')';
	//echo ($w['tt'] /250), "\n";
	//echo '<div>' . $q['pw_userid'] . '</div>';
}

// $aa = 0;
// while ( ++$aa < 100 )
// {
// 	$e = &$q->div[];
// 	$e['style'] = 'background:#' . sprintf( '%06X', wa::hsl_color( (1 - $aa * 0.01) * 0.5, 1, 0.5 ) );
// 	$e[0] = $aa;
// 	//echo join( ',', $q ), "\n";
// 	//echo '<div>' . $q['pw_userid'] . '</div>';
// }