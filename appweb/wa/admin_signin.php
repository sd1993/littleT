<?php
wa::locale();
wa::end_str_signin( TRUE, function( $q, $w )
{
	if ( $e = @wa::call( 'webapp_htt', WA_APPWEB_DIR . '/wa/admin_signin.xml', LIBXML_COMPACT, TRUE ) )
	{
		$r = [
			'sec_try' => $e['sec_try'] | 0,
			'max_try' => $e['max_try'] | 0,
			'deny_sec' => $e['deny_sec'] | 0,
			'now_time' => $_SERVER['REQUEST_TIME'],
			'ip' => $_SERVER['REMOTE_ADDR'],
			'name' => $_SERVER['HTTP_HOST'],
			'authorized' => 0,
			'last_time' => 0,
			'last_deny' => 0,
			'hosts' => []
		];
		foreach ( $e->xpath( 'log[@ip="' . $r['ip'] . '"]' ) as $t )
		{
			$r['last_deny'] += $t['last_deny'];
			$y = $t['last_time'] | 0;
			$y > $r['last_time'] && $r['last_time'] = $y;
			foreach ( $t as $y )
			{
				$r['hosts'][ (string)$y['name'] ] = [ $y['time'] | 0, $y['authorized'] | 0 ];
			};
			$t->del();
		};
		$r['now_time'] - $r['last_time'] > $r['deny_sec'] && $r['last_deny'] = 0;
		if ( $r['max_try'] > $r['last_deny'] )
		{
			$t = $e->add_top( 'log' );
			$t['ip'] = $r['ip'];
			$t['last_time'] = $r['now_time'];
			if ( $q === WA_ADMIN_ID && $w === md5( WA_ADMIN_PW ) )
			{
				$r['authorized'] = 1;
			}
			else
			{
				$r['now_time'] - $r['last_time'] > $r['sec_try'] || ++$r['last_deny'];
			};
			$t['last_deny'] = $r['last_deny'];
			$y = &$t->host;
			$y['name'] = $r['name'];
			$y['time'] = $r['now_time'];
			$y['authorized'] = $r['authorized'];
			unset( $r['hosts'][ $r['name'] ] );
			foreach ( $r['hosts'] as $q => $w )
			{
				$y = &$t->host[];
				$y['name'] = $q;
				$y['time'] = $w[0];
				$y['authorized'] = $w[1];
			};
			return $e->asXML( WA_APPWEB_DIR . '/wa/admin_signin.xml' ) && $r['authorized'];
		};
	};
});