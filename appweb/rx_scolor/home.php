<?php
wa::locale();
wa::htt();
wa::htt_title( '业务员下设备色调图' );
//header( 'Content-Type: text/html; charset=utf-8' );
//$sql = new webapp_sql( WA_DB_HOST, WA_DB_USER, WA_DB_PASSWORD, WA_DB_DATABASE );
wa::$buffers->style = <<<STYLE
div.f{
	width:80%;margin:42px auto;border:1px solid #b9b9b9;
}

div.f>div{
	width:5%;display:inline-block;line-height:42px;
}

STYLE;
$q = &wa::$buffers->write->div;
$q['class'] = 'f';




foreach ( wa::sql()->q_query( 'select count(*)as`tt`,pw_userid from rx_rd_device group by pw_userid order by tt desc' ) as $w )
{
	$e = &$q->div[];
	$e['style'] = 'background:#' . sprintf( '%06X', wa::hsl_color( ( 1 - $w['tt'] / 400 ) * 0.5, 1, 0.5 ) );
	$e[0] = $w['pw_userid'];
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