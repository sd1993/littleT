<?php
#WA Core Drive Class
final class wa extends core
{
	const v = 1.7, x = 'Beta';
	static private $action = [];
	static public
	$referer = '?',
	$cookies = [],
	$headers = [
		'Cache-Control' => 'no-cache',
		'Content-Type' => 'text/plain; charset=utf-8',
		'Expires' => -1
	],
	$buffers,
	$user,#save sign in userdata
	$htt, #save template class
	$sql; #save database class

	static public function locale( callable $q = NULL ):string
	{
		$w = [
			//'en-us' => './data/locale/en-us.php',
			'zh-cn' => './data/locale/zh-cn.php'
		];
		isset( $_SERVER['HTTP_REFERER'] ) && self::$referer = $_SERVER['HTTP_REFERER'];
		isset( $_COOKIE['wa_locale'], $w[ $_COOKIE['wa_locale'] ] )
			? $e = $_COOKIE['wa_locale']
			: self::$cookies[] = [ 'wa_locale',
				$e = preg_match( '/[a-z]{2}\-[a-z]{2}/', strtolower( $_SERVER['HTTP_ACCEPT_LANGUAGE'] ), $e ) && isset( $w[ $e[0] ] ) ? $e[0] : 'zh-cn',
				$_SERVER['REQUEST_TIME'] + 30931200 ];
		require $w[ $e ];
		$q === NULL || $q( $e );
		return __CLASS__;
	}

	static public function htt( bool $q = TRUE, string $w = '<!DOCTYPE html><html><head><meta http-equiv="Content-Type" content="text/html;charset=utf-8"/><title/></head><body/></html>' ):webapp_htt
	{
		if ( self::$htt === NULL )
		{
			self::$htt = self::call( 'webapp_htt', $w, LIBXML_COMPACT, $w[0] !== '<' );
			self::$headers['Content-Type'] = 'text/html; charset=utf-8';
			self::$buffers = (object)[
				'format' => FALSE,
				'write' => &self::$htt->body[0],
				'css' => [ 'ps/wa.css' ],
				'js' => [ 'js/pq.js', 'js/pq.webkit.js', 'js/wa.js' ]
			];
			if ( $q === TRUE )
			{
				self::debug_time( self::$buffers->debug_time );
				$q = self::$buffers->nav = &self::$buffers->write->nav;
				$q['class'] = 'wa';
				$q = &$q->div;
				$q->tag_a( '', '?' . WA_HOME_QUERY );
				$q->div[]['style'] = 'float:left';
				$q->div[]['style'] = 'float:right';
				$w = &self::$buffers->write->div;
				$w['class'] = 'wa';
				$w->div = NULL;
				self::$buffers->write = &$w->div[];
				self::$buffers->end = &$w->div[];
			};
		};
		return self::$htt;
	}

	static public function sql():webapp_sql
	{
		if ( self::$sql === NULL )
		{
			self::$sql = self::call( 'webapp_sql', WA_DB_HOST, WA_DB_USER, WA_DB_PASSWORD, WA_DB_DATABASE );
			self::$sql->connect_error ? self::$errors[] = self::$sql->connect_error : self::$sql->stack_errors = &self::$errors;
		};
		return self::$sql;
	}

	static private function set_action_request_method( string $q, string &$w, callable &$e ):string
	{
		self::$action[ $w ][ $q ] = $e;
		return __CLASS__;
	}

	static public function set_action_get( string $q, callable $w ):string
	{
		return self::set_action_request_method( 'GET', $q, $w );
	}

	static public function set_action_post( string $q, callable $w ):string
	{
		return self::set_action_request_method( 'POST', $q, $w );
	}

	static public function set_bind( callable $q, ... $w ):callable
	{
		return function( ... $e ) use( &$q, &$w )
		{
			return call_user_func_array( $q, array_merge( $w, $e ) );
		};
	}

	// static public function set_callback( callable $q = NULL )
	// {
	// 	self::$callback = $q === NULL ? 'self::end_str_status' : $q;
	// }

	static public function set_cookie( ... $q )
	{
		$q[1] = [ $q[1] ];
		isset( $q[2] ) && $q[1][] = $q[2] | 0;
		$q[1] = self::encrypt( json_encode( $q[1], JSON_UNESCAPED_UNICODE ) );
		self::$cookies[] = $q;
	}

	static public function set_sendfile( string $q )
	{
		self::$buffers = self::$htt = self::$sql = NULL;
		self::$headers = [ 'X-Sendfile' => $q ];
	}

	static public function set_header_download( string $q = NULL )
	{
		self::$headers['Content-Type'] = 'application/force-download';
		self::$headers['Content-Disposition'] = 'attachment; filename=' . ( $q === NULL ? self::entime() : urlencode( $q ) );
	}

	static public function set_user_create( string $q, string $w, string $e = '', array $r = NULL ):bool
	{
		if ( preg_match( WA_USER_MATCH, $q ) )
		{
			$q = [ 'username' => $q, 'password' => md5( $w ), 'time' => time(), 'have' => $e ];
			if ( $r === NULL )////////////////////////////////////////////////////////////////
			{
				return self::$sql->q_insert( 'wa_user', $q ) === 1;
			};
			$r['username'] = $q['username'];
			return self::$sql->q_task(
				[ 'insert into wa_user set ?b', self::$sql->q_value( $q ) ],
				[ 'insert into ?a set ?b', $e, self::$sql->q_value( $r ) ] );
		};
		return FALSE;
	}

	static public function set_user_rename( string $q, string $w, string $e = NULL ):bool
	{
		if ( preg_match( WA_USER_MATCH, $w ) )
		{
			$r = self::$sql->q_join( [ 'update wa_user set username=?s where username=?s limit 1', $w, $q ] );
			return $e === NULL
				? self::$sql->q_send( $r ) === 1
				: self::$sql->q_task( $r, [ 'update ?a set username=?s where username=?s limit 1', $e, $w, $q ] );
		};
		return FALSE;
	}

	static public function set_user_delete( string $q, string $w = NULL ):bool
	{
		$e = self::$sql->q_join( [ 'delete from wa_user where username=?s limit 1', $q ] );
		return $w === NULL
			? self::$sql->q_send( $e ) === 1
			: self::$sql->q_task( $e, [ 'delete from ?a where username=?s limit 1', $w, $q ] );
	}

	static public function set_signin_user( string $q, string $w, string $e = NULL ):bool
	{
		$e = $e === NULL
			? 'select * from wa_user where username=?s limit 1'
			: 'select * from wa_user,' . $e . ' where ' . $e . '.username=wa_user.username and wa_user.username=?s limit 1';
		self::$user = self::sql()->q_query( $e, $q )->fetch_assoc();
		return self::$user && self::$user['password'] === $w;
	}

	static public function get_cookie( string $q )
	{
		return isset( $_COOKIE[ $q ] )
			&& ( $q = json_decode( self::decrypt( $_COOKIE[ $q ] ), TRUE ) )
			&& isset( $q[0] )
			&& ( isset( $q[1] ) ? $q[1] > $_SERVER['REQUEST_TIME'] : TRUE )
			? $q[0] : NULL;
	}

	static public function get_past_time( int $q ):string
	{
		$q = $_SERVER['REQUEST_TIME'] - $q;
		if ( $q < 2592000 ) //month
		{
			if ( $q < 3600 ) //hour
			{
				return $q < 60
					? wa_past_just
					: sprintf( wa_past_minute, $q / 60 );
			};
			return $q < 86400 //day
				? sprintf( wa_past_hour, $q / 3600 )
				: sprintf( wa_past_day, $q / 86400 );

		};
		return $q < 31556926 //year
			? sprintf( wa_past_month, $q / 2592000 )
			: sprintf( wa_past_year, $q / 31556926 );
	}

	static public function get_format_size( int $q ):string
	{
		if ( $q < 1073741824 ) //G
		{
			if ( $q > 1048576 ) //M
			{
				return sprintf( '%.2f MB', $q / 1048576 );
			};
			return $q < 1024 //B
				? $q . 'B'
				: sprintf( '%.2f KB', $q / 1024 );
		};
		return $q < 1099511627776 //T
			? sprintf( '%.2f GB', $q / 1073741824 )
			: sprintf( '%.2f TB', $q / 1099511627776 );
	}

	static public function get_signin_user( string $q = 'wa_user', callable $w = NULL ):bool
	{
		//$w === NULL && $w = [ __CLASS__, 'set_signin_user' ];
		return is_array( $q = self::get_cookie( $q ) ) && isset( $q[0], $q[1] ) && ( $w ?? __CLASS__ . '::set_signin_user' )( $q[0], $q[1] );
	}

	static public function get_signin_admin():bool
	{
		return self::get_signin_user( 'wa_admin', function( $q, $w )
		{
			return $q === WA_ADMIN_ID && $w === md5( WA_ADMIN_PW );
		});
	}

	static public function get_captcha_new():string
	{
		return self::encrypt( self::entime() . substr( str_shuffle( WA_CAPTCHA_STR ),
			mt_rand( 0, strlen( WA_CAPTCHA_STR ) - WA_CAPTCHA_LEN ), WA_CAPTCHA_LEN ) );
	}

	static public function get_captcha_key( string $q )
	{
		return self::detime( substr( $q = self::decrypt( $q ), 0, 9 ) ) + WA_CAPTCHA_KEEP > $_SERVER['REQUEST_TIME']
			&& strlen( $w = substr( $q, 9 ) ) === WA_CAPTCHA_LEN ? $w : NULL;
	}

	static public function get_filter( array $q, array $w = [], int $e = 7 ):string
	{
		if ( isset( $_GET[ $e ] ) )
		{
			$r = [
				'eq' => '=',
				'le' => '<=',
				'ge' => '>=',
				'ne' => '!=',
				'not_like' => ' not like ',
				'regexp' => ' regexp ',
				'like' => ' like '
			];
			foreach ( explode( '/', $_GET[ $e ], WA_FILTER_MAX ) as $e )
			{
				$e = explode( '.', $e, 3 );
				if ( isset( $e[1], $r[ $e[1] ] ) && in_array( $e[0], $q, TRUE ) )
				{
					if ( isset( $e[2] ) )
					{
						$e[1] = $r[ $e[1] ];
						$e[2] = self::$sql->escape( urldecode( $e[2] ) );
					}
					else
					{
						$e[1] = $e[1] === 'ne' ? ' is not' : ' is';
						$e[2] = ' null';
					};
					$w[] = self::$sql->quote( $e[0] ) . $e[1] . $e[2];
				};
			};
		};
		return isset( $w[0] ) ? 'where ' . join( ' and ', $w ) : '';
	}

	static public function get_form_post( array $q )
	{
		$w = [];
		foreach ( $q as $q => $e )
		{
			if ( $e['type'] === 'file' )
			{
				if ( isset( $_FILES[ $q ] ) && is_array( $_FILES[ $q ]['error'] ) )
				{
					$w[ $q ] = [];
					foreach ( $_FILES[ $q ]['tmp_name'] as $r => $t )
					{
						if ( $_FILES[ $q ]['error'][ $r ] === 0 && is_uploaded_file( $t ) )
						{
							$y = strrpos( $_FILES[ $q ]['name'][ $r ], '.' );
							$y = $y && preg_match( '/^\w{1,8}$/', substr( $_FILES[ $q ]['name'][ $r ], $y + 1 ), $u )
								? [ strtolower( $u[0] ), substr( $_FILES[ $q ]['name'][ $r ], 0, $y ) ]
								: ['unknown', $_FILES[ $q ]['name'][ $r ] ];
							$w[ $q ][] = [
								'link' => $t,
								'size' => $_FILES[ $q ]['size'][ $r ],
								'mime' => $_FILES[ $q ]['type'][ $r ],
								'type' => $y[0],
								'name' => $y[1]
							];
						};
					};
					$r = count( $w[ $q ] );
					if ( $r >= $e['test'][0] && $r <= $e['test'][1] )
					{
						continue;
					};
				};
				return NULL;
			};
			$e = $e['test'];
			$r = isset( $_POST[ $q ] ) ? $_POST[ $q ] : '';
			if ( is_string( $r ) )
			{
				$t = strlen( $r );
				if ( $t >= $e[0] && $t <= $e[1] && ( isset( $e[2] ) ? preg_match( $e[2], $r ) : TRUE ) )
				{
					$w[ $q ] = $r;
					continue;
				};
				return NULL;
			};
			if ( isset( $e[2] ) )
			{
				foreach ( $r as $t )
				{
					if ( preg_match( $e[2], $t ) === 0 )
					{
						return NULL;
					};
				};
			};
			$t = count( $r );
			if ( $t >= $e[0] && $t <= $e[1] )
			{
				$w[ $q ] = $r;
				continue;
			};
			return NULL;
		};
		return $w;
	}

	static public function get_form_post_bind( array $q, &$w )
	{
		return $w = self::get_form_post( $q );
	}

	static public function get_form_post_callback( array $q, callable $w, bool $e = FALSE )
	{
		if ( $q = self::get_form_post( $q ) )
		{
			return $e === TRUE ? self::$sql->q_task_callback( $w, $q ) : $w( $q, self::$sql );
		};
		return NULL;
	}

	static public function get_query_remove( int ... $q ):string
	{
		$w = isset( $_GET[0] ) ? $_GET[0] : '';
		foreach ( array_diff_key( array_intersect_key( $_GET, array_fill( 1, 9, NULL ) ), array_fill_keys( $q, NULL ) ) as $q => $e )
		{
			$w .= '(' . $q . ')' . $e;
		}
		return '?' . $w;
	}

	static public function get_stat_page( int $q, int $w, int $e = 9, int $r = 9 ):array
	{
		$q = [
			'stat_rows' => $q,
			'page_rows' => $w,
			'page_max' => ceil( $q / $w )
		];
		$q['page_current'] = isset( $_GET[ $e ] ) ? max( 1, min( $q['page_max'], $_GET[ $e ] ) ) : 1;
		$q['skip_rows'] = ( $q['page_current'] - 1 ) * $q['page_rows'];
		if ( $q['page_max'] > $r )
		{
			$w = $r * 0.5 | 0;
			$e = min( $q['page_max'], max( $q['page_current'], $w + 1 ) + $w ) - 1;
			$q['page_list'] = range( max( 1, $e - $w * 2 + 1 ), $e );
			$q['page_list'][0] = 1;
			$q['page_list'][] = $q['page_max'];
		}
		else
		{
			$q['page_list'] = range( 1, $q['page_max'] );
		};
		return $q;
	}

	static public function htt_title( string $q )
	{
		self::$htt->head[0]->title[0] = $q;
	}

	static public function htt_css( string $q )
	{
		$w = &self::$htt->head[0]->link[];
		$w['rel'] = 'stylesheet';
		$w['type'] = 'text/css';
		$w['media'] = 'all';
		$w['href'] = $q;
	}

	static public function htt_js( string $q )
	{
		$w = &self::$htt->head[0]->script[];
		$w['type'] = 'text/javascript';
		$w['src'] = $q;
	}

	static public function htt_nav( string $q, $w, int $e = 0 ):webapp_htt
	{
		$e = &self::$buffers->nav->div->div[ $e ]->a[];
		if ( is_array( $w ) )
		{
			self::htt_menu( $w, self::$buffers->nav )->set_id( $w );
			$e['href'] = '#';
			$e['class'] = 'arrow_down';
			$e['onclick'] = 'return false';
			$e['onmousedown'] = 'wa.menu_act(this)';
			$e['data-target'] = $w;
			$e['data-fixed'] = TRUE;
			$e['data-center'] = TRUE;
			$e['data-offset_y'] = -2;
		}
		else
		{
			$e['href'] = $w;
		};
		$e[0] = $q;
		return $e;
	}

	static public function htt_menu( array $q, webapp_htt $w = NULL ):webapp_htt
	{
		$w === NULL && $w = &self::$htt->body->div->div;
		$w = &$w->ul[];
		$w['class'] = 'wa_menu';
		foreach ( $q as $q )
		{
			$e = &$w->li[];
			if ( is_array( $q ) )
			{
				$r = &$e->a;
				$r['href'] = $q[1];
				isset( $q[2] ) ? $r->i['class'] = $q[2] : $r->i = NULL;
				$r->span = $q[0];
				continue;
			};
			$e['class'] = 'divider';
		};
		return $w;
	}

	static public function htt_nav_array( array $q )
	{
		foreach ( $q as $q )
		{
			self::htt_nav( $q[0], $q[1] );
		};
	}

	// static public function htt_menu_search()
	// {
	// 	self::$buffers->quick_search = self::$buffers->menu->div[0]->add_after( 'div' )->set_class( 'wa_quick_search' );
	// 	self::$buffers->quick_search->tag_input()->set_attrs([
	// 		'type' => 'text',
	// 		'placeholder' => 'Enter keywords'
	// 	]);
	// 	self::$buffers->quick_search->add( 'button', wa_search )->set_class( 'b' );
	// 	return self::$buffers->quick_search;
	// }

	// static public function htt_both_classic( callable $q = NULL )
	// {
	// 	$w = self::$buffers->write->ins_table_std()->frame->set_class( 'wa_both_classic' );
	// 	$e = $w->tbody->add( 'tr' );
	// 	self::$buffers->both_left = $e->add( 'td' );
	// 	self::$buffers->write = $e->add( 'td' );
	// 	$q === NULL || $q( $w, self::$buffers->both_left, self::$buffers->write );
	// }

	// static public function htt_both_link( $q, array $w )
	// {
	// 	//$e = self::$buffers->both_left->add( '' );
	// }



	static public function htt_form_post( array $q, array $w = NULL ):webapp_htt
	{
		$e = self::$buffers->write->tag_form();
		$e['class'] = 'wa_form_post';
		$r = [];
		$t = $e->tag_table();
		$t->thead->tr->td['colspan'] = 3;
		foreach ( $q as $q => $y )
		{
			$r[] = $q . ':[' . join( ',', $y['test'] ) . ']';
			$u = isset( $w[ $q ] ) ? $w[ $q ] : NULL;
			$i = &$t->tbody->tr[];
			$o = &$i->td;
			$o[0] = $y['name'];
			$o = &$i->td[];
			$o = &$i->td[];
			isset( $y['note'] ) && $o[0] = $y['note'];
			$i = &$i->td[1];
			switch ( $y['type'] )
			{
				case 'select':
					$i->tag_select( $y['value'], $u )['name'] = $q;
					break;
				case 'multiselect':
					$i->ins_multiselect( $q, $y['value'], is_array( $u ) ? $u : NULL );
					break;
				case 'file':
					$e['enctype'] = 'multipart/form-data';
					$i = $i->tag_input( 'file' );
					$i['name'] = $q . '[]';
					$i['multiple'] = TRUE;
					$i['onchange'] = 'wa.form_post_file(this)';
					break;
				case 'checkbox':
				case 'radio':
					$i = &$i->div;
					$i['class'] = 'set';
					$y['type'] = $y['type'] === 'checkbox' && $q .= '[]';
					foreach ( $y['value'] as $o => $p )
					{
						$p = $i->ins_label_input( $p, $y['type'] )->input[0];
						$p['name'] = $q;
						$p['value'] = $o;
						strpos( ',' . $u . ',', ',' . $o . ',' ) === FALSE || $p['checked'] = TRUE;
					};
					break;
				case 'textarea':
					$i = &$i->div;
					$i['class'] = 'fix';
					$i->textarea[0] = $u;
					$i->textarea['name'] = $q;
					break;
			 	default:
					$i = &$i->div;
					$i['class'] = 'fix';
			 		$i = $i->tag_input( $y['type'] );
			 		$i['name'] = $q;
			 		$i['value'] = $u;
			};
		};
		$e['onsubmit'] = 'return wa.form_post(this,{' . join( ',', $r ) . '})';
		$w = &$t->tfoot->tr->td;
		$w['colspan'] = 3;
		$w->ins_progressbar();
		$w->tag_button( wa_reset, 'reset' );
		$w->tag_button( wa_submit, 'submit' )['class'] = 'b';
		return $e;
	}

	static public function htt_data_table( string $q, array $w, callable $e, array $r = [] ):webapp_htt
	{
		$r += [
			'select_fields' => [],
			'order_fields' => [],
			'title_fields' => [],
			'merge_query' => '',
			'order_index' => 8,
			'page_index' => 9,
			'page_rows' => 21
		];
		foreach ( $w as $w => $t )
		{
			if ( is_string( $t ) )
			{
				$r['title_fields'][] = [ $t ];
				continue;
			};
			$r['select_fields'][] = self::$sql->quote( $w );
			if ( is_bool( $t ) )
			{
				$t && $r['order_fields'][] = $w;
				continue;
			};
			if ( $t[0] )
			{
				$r['title_fields'][] = [ $t[1], $w, 'asc' ];
				$r['order_fields'][] = $w;
				is_string( $t[0] )
					&& ( $t[0] === 'desc' || $t[0] === 'asc' )
					&& $r['order_by'] = [ $w, $t[0] ];
			}
			else
			{
				$r['title_fields'][] = [ $t[1] ];
			};
		};
		if ( isset( $_GET[ $r['order_index'] ] ) )
		{
			$w = explode( '.', $_GET[ $r['order_index'] ], 2 );
			isset( $w[1] )
				&& ( $w[1] === 'asc' || $w[1] === 'desc' )
				&& in_array( $w[0], $r['order_fields'], TRUE )
				&& $r['order_by'] = $w;
		};
		$w = self::$buffers->write->tag_table();
		$w['class'] = 'wa_data_table';
		$t = &$w->thead->tr;
		foreach ( $r['title_fields'] as $y )
		{
			$u = &$t->td[];
			$u[0] = $y[0] ;
			if ( isset( $y[1] ) )
			{
				if ( isset( $r['order_by'] ) && $r['order_by'][0] === $y[1] )
				{
					if ( $r['order_by'][1] === 'asc' )
					{
						$y[2] = 'desc';
						$u->addChild( 'span', '&#9650;' );
					}
					else
					{
						$y[2] = 'asc';
						$u->addChild( 'span', '&#9660;' );
					};
				};
				$u['class'] = 'orderable';
				$u['onclick'] = 'wa.query_act({' . $r['order_index'] . ':"' . $y[1] . '.' . $y[2] . '"})';
			};
		};
		isset( $r['stat_rows'] ) || $r['stat_rows'] = self::$sql->get_rows( $q, $r['merge_query'] );
		$t = self::get_stat_page( $r['stat_rows'], $r['page_rows'], $r['page_index'] );
		$q = [ 'select', join( ',', $r['select_fields'] ), 'from', self::$sql->quote( $q ) ];
		$r['merge_query'] && $q[] = $r['merge_query'];
		if ( isset( $r['order_by'] ) )
		{
			$q[] = 'order by';
			$q[] = self::$sql->quote( $r['order_by'][0] );
			$q[] = $r['order_by'][1];
		};
		$q[] = 'limit';
		$q[] = $t['skip_rows'] . ',' . $t['page_rows'];
		$y = $w->tbody;
		foreach ( self::$sql->q_query( join( ' ', $q ) ) as $q )
		{
			$e( $u = &$y->tr[], $q );
		};
		if ( $t['page_max'] > 1 )
		{
			$w->tfoot->tr->td['colspan'] = count( $r['title_fields'] );
			$w->tfoot->tr->td->ins_page( $t, $r['page_index'] );
		};
		return $w;
	}

	static public function end_action( callable $q = NULL, callable $w = NULL )
	{	
		isset( $_GET[1], $_SERVER['REQUEST_METHOD'],
			self::$action[ $_GET[1] ],
			self::$action[ $_GET[1] ][ $_SERVER['REQUEST_METHOD'] ] )
			? self::$action[ $_GET[1] ][ $_SERVER['REQUEST_METHOD'] ]()
			: $q && $q();
		$w && $w();
		exit;
	}

	static public function end_action_class( string $q )
	{
		$w = new $q;
		$e = new reflectionclass( $q );
		foreach ( $e->getMethods( ReflectionMethod::IS_PUBLIC ) as $r )
		{
			if ( $r->class === $q && preg_match( '/^(get|post)_(\w+)$/i', $r->name, $r ) )
			{
				$r[0] = [ &$w, $r[0] ];
				self::set_action_request_method( strtoupper( $r[1] ), $r[2], $r[0] );
			};
		};
		self::end_action( $e->hasMethod( 'index' ) && $e->getMethod( 'index' )->class === $q ? [ &$w, 'index' ] : NULL, function() use( &$w )
		{
			$w = NULL;
		});
	}

	static public function end_string( string $q )
	{
		self::$htt = NULL;
		self::$headers = [
			'Cache-Control' => 'no-cache',
			'Content-Type' => 'text/plain; charset=utf-8',
			'Expires' => -1
		];
		self::$buffers = &$q;
		exit;
	}

	static public function end_sendfile( string $q )
	{
		self::set_sendfile( $q );
		exit;
	}

	static public function end_download_file( string $q, string $w = NULL )
	{
		self::set_sendfile( $q );
		self::set_header_download( $w );
		exit;
	}

	static public function end_download_data( string $q, string $w = NULL )
	{
		self::$headers = self::$htt = NULL;
		self::set_header_download( $w );
		self::$buffers = &$q;
		exit;
	}

	static public function end_str_status( callable $q = NULL )
	{
		//$q === NULL || self::$buffers = $q();
		self::end_string( isset( self::$errors[0] ) ? join( "\n", self::$errors ) : self::entime() . json_encode( (array)self::$buffers + [
			1 => 'goto_referer',
			'http_referer' => self::$referer,
			'request_time' => $_SERVER['REQUEST_TIME'],
			'remote_addr' => $_SERVER['REMOTE_ADDR']
		], JSON_UNESCAPED_UNICODE ) );
	}

	static public function end_str_signin( bool $q = TRUE, callable $w = NULL )
	{
		$q === TRUE && ( ( isset( $_POST['captcha_encrypt'], $_POST['captcha_decrypt'] )
				&& self::get_captcha_key( $_POST['captcha_encrypt'] ) === $_POST['captcha_decrypt'] )
			|| self::end_string( wa_error_captcha ) );
		$w === NULL && $q = [ __CLASS__, 'set_signin_user' ];
		self::end_string( isset( $_POST['username'], $_POST['password'] ) && $w( $_POST['username'], $_POST['password'] ) ?
			self::entime() . self::encrypt( json_encode( [
				[ $_POST['username'], $_POST['password'] ],
				$_SERVER['REQUEST_TIME'] + WA_SIGNIN_KEEP
			], JSON_UNESCAPED_UNICODE ) ) : wa_signin_failure );
	}

	static public function end_htt_signin( bool $q = TRUE, callable $w = NULL )
	{
		$e = self::$buffers->write->tag_form();
		$e['class'] = 'wa_signin';
		$e['action'] = '?/wa/user_signin_reply';
		$e['onsubmit'] = 'return wa.signin(this)';
		$e['data-referer'] = self::$referer;
		$e['data-tagname'] = 'wa_signin';
		$e->div = NULL;
		$r = $e->tag_input();
		$r['name'] = 'username';
		$r['placeholder'] = wa_enter_username;
		$r['required'] = 'required';
		$r = $e->tag_input( 'password' );
		$r['name'] = 'password';
		$r['placeholder'] = wa_enter_password;
		$r['required'] = 'required';
		$r = $e->ins_label_input( wa_signin_save_local )->input;
		$r['name'] = 'keep';
		$r['value'] = WA_SIGNIN_KEEP;
		if ( $q === TRUE )
		{
			$q = self::get_captcha_new();
			$r = $e->tag_input( 'hidden' );
			$r['name'] = 'captcha_encrypt';
			$r['value'] = $q;
			$r = $e->tag_input();
			$r['name'] = 'captcha_decrypt';
			$r['placeholder'] = wa_enter_captcha;
			$r['required'] = 'required';
			$r = &$e->b;
			$r['style'] = 'background:white url("?/wa/captcha(1)' . $q . '")no-repeat scroll center center';
			$r['onclick'] = 'wa.signin_captcha_refresh(this)';
		};
		$e->i['data-wa_warn_client_time_diff'] = wa_warn_client_time_diff;
		$r = $e->tag_button( wa_sign_up );
		$r['onclick'] = '$.go("?/wa/user_signup_page")';
		$r = $e->tag_button( wa_sign_in, 'submit' );
		$r['class'] = 'b';
		$w === NULL || $w( $e );
		exit;
	}

	static public function end_htt_signin_admin( string $q )
	{
		self::end_htt_signin( TRUE, function( $w ) use( $q )
		{
			$w['action'] = '?/wa/admin_signin';
			$w['data-referer'] = $q;
			$w['data-tagname'] = 'wa_admin';
			$w->div = 'Web Application Admin Sign In';
			unset( $w->button[0] );
		});
	}

	static public function end_download_xmlexcel( array $q, string $w = 'webapp.xls' )
	{
		$e = '<?xml version="1.0" encoding="utf-8"?>'
			. '<Workbook'
			. ' xmlns="urn:schemas-microsoft-com:office:spreadsheet"'
			. ' xmlns:x="urn:schemas-microsoft-com:office:excel"'
			. ' xmlns:ss="urn:schemas-microsoft-com:office:spreadsheet"'
			. ' xmlns:html="http://www.w3.org/TR/REC-html40">'
			. '<Worksheet ss:Name="webapp"><Table>';
		foreach ( $q as $q )
		{
			$e .= '<Row>';
			foreach ( $q as $r )
			{
				$e .= '<Cell><Data ss:Type="String">' . $r . '</Data></Cell>';
			};
			$e .= '</Row>';
		};
		$e .= '</Table></Worksheet></Workbook>';
		self::end_download_data( $e, $w );
	}
}