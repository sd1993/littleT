<?php
wa::sql();
wa::htt();
$start_time = mktime( 0, 0, 0, 1, 1, 2016 );
$end_time = mktime( 0, 0, 0, 1, 1, 2017 );
$task_type = [
	'a' => 360,
	'b' => 900,
	'c' => 1800,
	'd' => 3600,
	'e' => 1200,
	'k' => 0
];

$staff = [];
foreach ( wa::$sql->q_query( 'select username,name from rx_hr_staff' ) as $item )
{
	$staff[ $item['username'] ] = $item['name'];
};


wa::$htt->body->div->div[2]['style'] = wa::$htt->body->div->div['style'] = wa::$htt->body->nav['style'] = 'display:none';



$table = wa::$buffers->write->tag_table();

$table['class'] = 'wa_grid_table';
$table['style'] = 'margin:21px auto';
$table->caption['style'] = 'padding:16px;font-size:32px';
$table->caption = '锐讯 德胜机房运维中心 2016 年度统计';


$table->thead->tr->td = '部门/统计条件描述';
$table->thead->tr->td[] = '总计任务数';
$table->thead->tr->td[] = '总响应时间差/秒';
$table->thead->tr->td[] = '总完成时间差/秒';
$table->thead->tr->td[] = '平均响应时间差/秒';
$table->thead->tr->td[] = '平均完成时间差/秒';


$pack_kf = wa::$sql->q_query( 'select count(*)as total,sum(start-time)as diff0,sum(done-start)as diff1 from rx_public_pack where time>?i and time<?i and c_group="客服部"', $start_time, $end_time )->fetch_assoc();
$table_tr = &$table->tbody->tr;
$table_tr->td = '客服部发送任务到机房';
$table_tr->td[] = $pack_kf['total'];
$table_tr->td[] = $pack_kf['diff0'];
$table_tr->td[] = $pack_kf['diff1'];
$table_tr->td[] = sprintf( '%.2f', $pack_kf['diff0'] / $pack_kf['total'] );
$table_tr->td[] = sprintf( '%.2f', $pack_kf['diff1'] / $pack_kf['total'] );

$pack_zy = wa::$sql->q_query( 'select count(*)as total,sum(start-time)as diff0,sum(done-start)as diff1 from rx_public_pack where time>?i and time<?i and c_group="资源部" and `describe`!="回收设备"', $start_time, $end_time )->fetch_assoc();
$table_tr = &$table->tbody->tr[];
$table_tr->td = '资源部发送非回收设备任务';
$table_tr->td[] = $pack_zy['total'];
$table_tr->td[] = $pack_zy['diff0'];
$table_tr->td[] = $pack_zy['diff1'];
$table_tr->td[] = sprintf( '%.2f', $pack_zy['diff0'] / $pack_zy['total'] );
$table_tr->td[] = sprintf( '%.2f', $pack_zy['diff1'] / $pack_zy['total'] );


$pack_zy_hs = wa::$sql->q_query( 'select count(*)as total,sum(start-time)as diff0,sum(done-start)as diff1 from rx_public_pack where time>?i and time<?i and c_group="资源部" and `describe`="回收设备"', $start_time, $end_time )->fetch_assoc();
$table_tr = &$table->tbody->tr[];
$table_tr->td = '资源部发送回收设备任务';
$table_tr->td[] = $pack_zy_hs['total'];
$table_tr->td[] = $pack_zy_hs['diff0'];
$table_tr->td[] = $pack_zy_hs['diff1'];
$table_tr->td[] = sprintf( '%.2f', $pack_zy_hs['diff0'] / $pack_zy_hs['total'] );
$table_tr->td[] = sprintf( '%.2f', $pack_zy_hs['diff1'] / $pack_zy_hs['total'] );


$table = wa::$buffers->write->tag_table();
$table['class'] = 'wa_grid_table';
$table['style'] = 'margin:21px auto';


$sql = wa::$sql->q_query( 'select count(*)as total,sum(case type when "a" then 360 when "b" then 900 when "c" then 1800 when "d" then 3600 when "e" then 1200 when "k" then 0 end - over)as diff0,sum(done)as diff1,sum(over)as diff2,from_unixtime(time,"%Y-%m")as`m` from rx_ermsd_task_record where time>?i and time<?i group by m order by m asc', $start_time, $end_time );
$table_tr0 = &$table->tbody->tr;
$table_tr1 = &$table->tbody->tr[];
$table_tr2 = &$table->tbody->tr[];
$table_tr3 = &$table->tbody->tr[];
$table_tr4 = &$table->tbody->tr[];
$table_tr5 = &$table->tbody->tr[];
$table_tr6 = &$table->tbody->tr[];
$table_tr7 = &$table->tbody->tr[];

$table_tr0->td = '统计条件描述';
$table_tr1->td = '处理任务总数量';
$table_tr2->td = '平均每天处理任务数量';
$table_tr3->td = '任务完成时间差总量/秒';
$table_tr4->td = '任务验收时间差总量/秒';
$table_tr5->td = '任务完成时间差平均/秒';
$table_tr6->td = '任务验收时间差平均/秒';
$table_tr7->td = '累计节省时间差平均/秒';
$m = 0;
foreach ( $sql as $item )
{
	$days = date( 't', strtotime( $item['m'] . '-1' ) );
	$table_tr0->td[] = (++$m) . '月(' . $days . ')';
	$table_tr1->td[] = $item['total'];
	$table_tr2->td[] = sprintf( '%.2f', $item['total'] / $days );
	$table_tr3->td[] = $item['diff0'];
	$table_tr4->td[] = $item['diff1'];
	$table_tr5->td[] = sprintf( '%.2f', $item['diff0'] / $item['total'] );
	$table_tr6->td[] = sprintf( '%.2f', $item['diff1'] / $item['total'] );
	$table_tr7->td[] = $item['diff2'];
}

$table->tbody->tr[]->td['colspan'] = 13;
$sql = wa::$sql->q_query( 'select count(*)as total,sum(case type when "a" then 360 when "b" then 900 when "c" then 1800 when "d" then 3600 when "e" then 1200 when "k" then 0 end - over)as diff0,sum(done)as diff1,sum(over)as diff2,from_unixtime(time,"%Y-%m")as`m` from rx_ermsd_task_record where time>?i and time<?i and team="火箭队" group by m order by m asc', $start_time, $end_time );

$table_tr1 = &$table->tbody->tr[];
$table_tr2 = &$table->tbody->tr[];
$table_tr3 = &$table->tbody->tr[];
$table_tr4 = &$table->tbody->tr[];
$table_tr5 = &$table->tbody->tr[];
$table_tr6 = &$table->tbody->tr[];
$table_tr7 = &$table->tbody->tr[];
$table_tr1->td = '火箭队 处理任务总数量';
$table_tr2->td = '火箭队 平均每天处理任务数量';
$table_tr3->td = '火箭队 任务完成时间差总量/秒';
$table_tr4->td = '火箭队 任务验收时间差总量/秒';
$table_tr5->td = '火箭队 任务完成时间差平均/秒';
$table_tr6->td = '火箭队 任务验收时间差平均/秒';
$table_tr7->td = '火箭队 累计节省时间差平均/秒';
$m = 0;
foreach ( $sql as $item )
{

	$table_tr1->td[] = $item['total'];
	$table_tr2->td[] = sprintf( '%.2f', $item['total'] / $days );
	$table_tr3->td[] = $item['diff0'];
	$table_tr4->td[] = $item['diff1'];
	$table_tr5->td[] = sprintf( '%.2f', $item['diff0'] / $item['total'] );
	$table_tr6->td[] = sprintf( '%.2f', $item['diff1'] / $item['total'] );
	$table_tr7->td[] = $item['diff2'];
}

$table->tbody->tr[]->td['colspan'] = 13;
$sql = wa::$sql->q_query( 'select count(*)as total,sum(case type when "a" then 360 when "b" then 900 when "c" then 1800 when "d" then 3600 when "e" then 1200 when "k" then 0 end - over)as diff0,sum(done)as diff1,sum(over)as diff2,from_unixtime(time,"%Y-%m")as`m` from rx_ermsd_task_record where time>?i and time<?i and team="光纤队" group by m order by m asc', $start_time, $end_time );
$table_tr1 = &$table->tbody->tr[];
$table_tr2 = &$table->tbody->tr[];
$table_tr3 = &$table->tbody->tr[];
$table_tr4 = &$table->tbody->tr[];
$table_tr5 = &$table->tbody->tr[];
$table_tr6 = &$table->tbody->tr[];
$table_tr7 = &$table->tbody->tr[];
$table_tr1->td = '光纤队 处理任务总数量';
$table_tr2->td = '光纤队 平均每天处理任务数量';
$table_tr3->td = '光纤队 任务完成时间差总量/秒';
$table_tr4->td = '光纤队 任务验收时间差总量/秒';
$table_tr5->td = '光纤队 任务完成时间差平均/秒';
$table_tr6->td = '光纤队 任务验收时间差平均/秒';
$table_tr7->td = '光纤队 累计节省时间差平均/秒';
$m = 0;
foreach ( $sql as $item )
{
	$table_tr1->td[] = $item['total'];
	$table_tr2->td[] = sprintf( '%.2f', $item['total'] / $days );
	$table_tr3->td[] = $item['diff0'];
	$table_tr4->td[] = $item['diff1'];
	$table_tr5->td[] = sprintf( '%.2f', $item['diff0'] / $item['total'] );
	$table_tr6->td[] = sprintf( '%.2f', $item['diff1'] / $item['total'] );
	$table_tr7->td[] = $item['diff2'];
}



$table = wa::$buffers->write->tag_table();
$table['class'] = 'wa_grid_table';
$table['style'] = 'margin:21px auto';



$sql = wa::$sql->q_query( join(',',['select d_userid,count(*)as total',
	'from_unixtime(time,"%m")as`m`',
	'count(case from_unixtime(time,"%m") when "01" then 1 end)as m1',
	'count(case from_unixtime(time,"%m") when "02" then 1 end)as m2',
	'count(case from_unixtime(time,"%m") when "03" then 1 end)as m3',
	'count(case from_unixtime(time,"%m") when "04" then 1 end)as m4',
	'count(case from_unixtime(time,"%m") when "05" then 1 end)as m5',
	'count(case from_unixtime(time,"%m") when "06" then 1 end)as m6',
	'count(case from_unixtime(time,"%m") when "07" then 1 end)as m7',
	'count(case from_unixtime(time,"%m") when "08" then 1 end)as m8',
	'count(case from_unixtime(time,"%m") when "09" then 1 end)as m9',
	'count(case from_unixtime(time,"%m") when "10" then 1 end)as m10',
	'count(case from_unixtime(time,"%m") when "11" then 1 end)as m11',
	'count(case from_unixtime(time,"%m") when "12" then 1 end)as m12',
	'sum(case type when "a" then 360 when "b" then 900 when "c" then 1800 when "d" then 3600 when "e" then 1200 when "k" then 0 end - over)as diff0',
	'sum(over)as diff1',
	'sum(score)as score',
	'count(case type when "a" then 1 end)as type_a',
	'count(case type when "b" then 1 end)as type_b',
	'count(case type when "c" then 1 end)as type_c',
	'count(case type when "d" then 1 end)as type_d',
	'count(case type when "e" then 1 end)as type_e',
	'count(case type when "e" then 1 end)as type_k from rx_ermsd_task_record where time>?i and time<?i group by d_userid order by total desc']), $start_time, $end_time );
$table->thead->tr->td = '员工工号';

$table->thead->tr->td[] = '1月';
$table->thead->tr->td[] = '2月';
$table->thead->tr->td[] = '3月';
$table->thead->tr->td[] = '4月';
$table->thead->tr->td[] = '5月';
$table->thead->tr->td[] = '6月';
$table->thead->tr->td[] = '7月';
$table->thead->tr->td[] = '8月';
$table->thead->tr->td[] = '9月';
$table->thead->tr->td[] = '10月';
$table->thead->tr->td[] = '11月';
$table->thead->tr->td[] = '12月';
$table->thead->tr->td[] = '总用时/秒';
$table->thead->tr->td[] = '总节省/秒';
$table->thead->tr->td[] = '总完成得分';
$table->thead->tr->td[] = 'A类';
$table->thead->tr->td[] = 'B类';
$table->thead->tr->td[] = 'C类';
$table->thead->tr->td[] = 'D类';
$table->thead->tr->td[] = 'E类';
$table->thead->tr->td[] = 'K类';
$table->thead->tr->td[] = '平均每个用时/秒';
$table->thead->tr->td[] = '平均每个节省/秒';
$table->thead->tr->td[] = '总计任务数';
foreach ( $sql as $item )
{
	$table_tr = &$table->tbody->tr[];
	$table_tr->td[] = isset( $staff[ $item['d_userid'] ] ) ? $staff[ $item['d_userid'] ] : $item['d_userid'];
	$table_tr->td[] = $item['m1'];
	$table_tr->td[] = $item['m2'];
	$table_tr->td[] = $item['m3'];
	$table_tr->td[] = $item['m4'];
	$table_tr->td[] = $item['m5'];
	$table_tr->td[] = $item['m6'];
	$table_tr->td[] = $item['m7'];
	$table_tr->td[] = $item['m8'];
	$table_tr->td[] = $item['m9'];
	$table_tr->td[] = $item['m10'];
	$table_tr->td[] = $item['m11'];
	$table_tr->td[] = $item['m12'];
	
	$table_tr->td[] = $item['diff0'];
	$table_tr->td[] = $item['diff1'];
	$table_tr->td[] = $item['score'];
	$table_tr->td[] = $item['type_a'];
	$table_tr->td[] = $item['type_b'];
	$table_tr->td[] = $item['type_c'];
	$table_tr->td[] = $item['type_d'];
	$table_tr->td[] = $item['type_e'];
	$table_tr->td[] = $item['type_k'];

	$table_tr->td[] = sprintf( '%.2f', $item['diff0'] / $item['total'] );
	$table_tr->td[] = sprintf( '%.2f', $item['diff1'] / $item['total'] );
	$table_tr->td[] = $item['total'];

}

//print_r(wa::$errors);