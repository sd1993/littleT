<?php
if ( isset( $_GET[2] ) )
{
	wa::$buffers = [ '投票失败!', 'warn' ];
	wa::$sql->q_insert( 'rx_user_vote', [
		'username' => wa::$user['username'],
		'time' => time(),
		'vote' => $_GET[2]
	] ) && wa::$buffers = [ '投票成功!', 'warn_reload' ];
	wa::$errors = [];
	wa::end_str_status();
	exit;
}




wa::htt_title( '投票筛选生日礼品' );
$vote_data = [
	1 => [ '金士顿3.0接口16G', '1.png' ],
	2 => [ '大肚牛奶杯 容量301ml-400ml', '2.png' ],
	3 => [ '无线鼠标，距离10M', '3.png' ],
	4 => [ '水晶圆底座30分钟', '4.png' ],
	5 => [ '单曲有灯座 精致9.5cm', '5.png' ],
	6 => [ '抱枕被40*40（展开120cm*150cm）', '6.png' ],
	7 => [ '迪士尼玻璃碗三件套', '7.png' ],
	8 => [ '玻璃碗+保温袋3件套', '8.png' ],
	9 => [ '饭盒/保鲜盒6件套', '9.png' ],
	10 => [ '台灯音响', '10.png' ],
	11 => [ '记忆棉U型枕套装', '11.png' ],
	12 => [ '抱枕被40*40（展开110cm*140cm）', '12.png' ],
	13 => [ '茶具套装', '13.png' ],
	14 => [ '格尼龙 双肩包', '14.png' ],
	15 => [ '竹纤维毛巾2件套', '15.png' ]

];
wa::$buffers->script = <<<'SCRIPT'
function warn_reload( q )
{
	alert( q );
	location.reload();
}
SCRIPT;
wa::$buffers->style = <<<'STYLE'
table.user_vote
{
	margin:21px auto;
}
table.user_vote>caption
{
	font-size:32px;
}
table.user_vote div
{
	width:400px;
	height:400px;
	margin:21px;
	border-radius:8px;
	box-shadow:0 0 8px rgba( 0, 0, 0, 0.8 );
}
table.user_vote div>i
{
	margin-top:370px;
	display:inline-block;
	line-height:30px;
	padding:0 8px;
	background:rgba( 0, 0, 0, 0.1 );
	font-size:18px;
	border-radius:0 8px;
}

STYLE;


$vote_user = [];
foreach ( wa::$sql->q_query( 'select vote,count(username)as`stat` from rx_user_vote group by vote' ) as $q )
{
	$vote_user[ $q['vote'] ] = $q['stat'];
}
wa::$buffers->write->tag_table(function( $q ) use( &$vote_data, &$vote_user )
{
	$q['class'] = 'user_vote';
	$q->caption = '投票筛选生日礼品';
	$w = &$q->thead->tr->td;
	$w['colspan'] = 3;
	$w = $w->pre;
	$w['style'] = 'margin:32px auto;width:600px';
	$w[0] = '为弘扬公司企业文化，进一步增强员工的归属感和向心力，公司规划为过生日的同事发放生日礼包。现我们已筛选部分礼品，请各位同事根据自己的兴趣喜好参与投票，我们将选取候选礼品中票数最高的作为生日礼包，感谢大家的支持与配合！
投票截止时间：2015年10月29日17:30分';
	$w = 0;
	$e = &$q->tbody->tr[];
	foreach ( $vote_data as $r => $t )
	{
		if ( $w++ % 3 )
		{
		}
		else
		{
			$e = &$q->tbody->tr[];

		};
		
		$y = &$e->td[]->div[];
		$y['style'] = 'background:transparent url("./appweb/rx_erp/canvas/vote/' . $t[1] . '") no-repeat scroll center center';
		$y->i[0] = $t[0] . ' (' . ( isset( $vote_user[ $r ] ) ? $vote_user[ $r ] : 0 ) . ')票 ';
		$y = $y->i->tag_a( '投票', '?/rx_erp(1)user_vote(2)' . $r );
		$y['data-confirm'] = '注意：每个工号只可以投票一次，请慎重选择。';
		$y['onclick'] = 'return wa.ajax_query(this.href,this.dataset)';


	}


});






