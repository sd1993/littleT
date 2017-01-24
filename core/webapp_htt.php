<?php
#WA Hyper Text Template Class
final class webapp_htt extends SimpleXMLElement
{
#add
	function add( string $q, string $w = NULL ):webapp_htt
	{
		$q = &$this[0]->{ $q }[];
		$w === NULL || $q[0] = $w;
		return $q;
	}
	function add_after( string $q, string $w = NULL ):webapp_htt
	{
		return $this[0]->add_dom_after( new DOMElement( $q, $w ) );
	}
	function add_before( string $q, string $w = NULL ):webapp_htt
	{
		return $this[0]->add_dom_before( new DOMElement( $q, $w ) );
	}
	function add_cdata( string $q ):webapp_htt
	{
		$w = $this[0]->get_dom();
		$w->appendChild( $w->ownerDocument->createCDATASection( $q ) );
		return $this[0];
	}
	function add_class( string $q ):webapp_htt
	{
		$this[0]['class'] = join( ' ',
			array_unique(
				array_filter(
					explode( ' ', is_array( $q )
						? $this[0]->get_class() . ' ' . join( ' ', $q )
						: $this[0]->get_class() . ' ' . $q
					), 'strlen' ) ) );
		return $this[0];
	}
	function add_comment( string $q ):webapp_htt
	{
		$w = $this[0]->get_dom();
		$w->appendChild( $w->ownerDocument->createComment( $q ) );
		return $this[0];
	}
	function add_css( array $q ):webapp_htt
	{
		$this[0]->set_css( $q + $this[0]->get_css() );
		return $this[0];
	}
	function add_dom( DOMNode $q ):webapp_htt
	{
		return simplexml_import_dom( $this[0]->get_dom()->appendChild( $q ), __CLASS__ );
	}
	function add_dom_after( DOMNode $q ):webapp_htt
	{
		$w = $this[0]->get_dom();
		return simplexml_import_dom( $w->parentNode->insertBefore( $q, $w->nextSibling ), __CLASS__ );
	}
	function add_dom_before( DOMNode $q ):webapp_htt
	{
		$w = $this[0]->get_dom();
		return simplexml_import_dom( $w->parentNode->insertBefore( $q, $w ), __CLASS__ );
	}
	function add_dom_top( DOMNode $q ):webapp_htt
	{
		$w = $this[0]->get_dom();
		return simplexml_import_dom( $w->insertBefore( $q, $w->firstChild ), __CLASS__ );
	}
	function add_raw( string $q ):webapp_htt
	{
		$w = $this[0]->get_dom();
		$e = $w->ownerDocument->createDocumentFragment();
		$e->appendXML( $q );
		$w->appendChild( $e );
		return $this[0];
	}
	function add_top( string $q, string $w = NULL ):webapp_htt
	{
		return $this[0]->add_dom_top( new DOMElement( $q, $w ) );
	}
#del
	function del()
	{
		unset( $this[0] );
	}
	// function del_attr( $q )
	// {
	// 	unset( $this[0][ $q ] );
	// 	return $this[0];
	// }
	// function del_attrs()
	// {
	// 	foreach( $this[0]->get_attrs() as $q )
	// 	{
	// 		unset( $this[0][ $q ] );
	// 	};
	// 	return $this[0];
	// }
	// function del_class( $q = NULL )
	// {
	// 	if ( $q === NULL )
	// 	{
	// 		unset( $this[0]['class'] );
	// 		return $this[0];
	// 	};
	// 	if ( $q = array_filter(
	// 		array_diff(
	// 			explode( ' ', $this[0]->get_class() ),
	// 			explode( ' ', is_array( $q ) ? join( ' ', $q ) : $q )
	// 	), 'strlen' ) )
	// 	{
	// 		$this[0]['class'] = join( ' ', $q );
	// 	}
	// 	else
	// 	{
	// 		unset( $this[0]['class'] );
	// 	};
	// 	return $this[0];
	// }
	// function del_css( array $q )
	// {
	// 	$this[0]->set_css( array_combine( $q, array_fill( 0, count( $q ), NULL ) ) + $this[0]->get_css() );
	// 	return $this[0];
	// }
#get
	// function get()
	// {
	// 	return $this[0]->asXML();
	// }
	function get_attr( string $q )
	{
		return isset( $this[0][ $q ] ) ? (string)$this[0][ $q ] : NULL;
	}
	function get_attrs():array
	{
		$q = (array)$this[0]->attributes();
		return isset( $q['@attributes'] ) ? $q['@attributes'] : [];
	}
	// function get_class()
	// {
	// 	return $this[0]->get_attr( 'class' );
	// }
	// function get_css()
	// {
	// 	return preg_match_all( '/([a-z\-]+)\s*\:\s*([^;]+)/i', $this[0]->get_style(), $q ) ? array_combine( $q[1], $q[2] ) : [];
	// }
	function get_dom():DOMNode
	{
		return dom_import_simplexml( $this[0] );
	}
	// function get_each( $q, callable $w )
	// {
	// 	foreach ( $this[0]->xpath( $q ) as $e )
	// 	{
	// 		$w( $e );
	// 	};
	// 	return $this[0];
	// }
	function get_parent():webapp_htt
	{
		return $this[0]->xpath( '..' )[0];
	}
	function get_style()
	{
		return $this[0]->get_attr( 'style' );
	}
	function get_val():string
	{
		return (string)$this[0];
	}
#set
	function set( callable $q )
	{
		return $q( $this[0] );
	}
	function set_attr( string $q, string $w ):webapp_htt
	{
		$this[0][ $q ] = $w;
		return $this[0];
	}
	function set_attrs( array $q ):webapp_htt
	{
		foreach ( $q as $w => $e )
		{
			$this[0][ $w ] = $e;
		};
		return $this[0];
	}
	function set_id( &$q ):webapp_htt
	{
		static $w = 0;
		$this[0]['id'] = $q = 'wa_htt_id' . ++$w;
		return $this[0];
	}
	function set_class( string $q ):webapp_htt
	{
		$this[0]['class'] = $q;
		return $this[0];
	}
	function set_css( array $q ):webapp_htt
	{
		$w = [];
		foreach ( $q as $q => $e )
		{
			$e === NULL || $w[] = $q . ':' . $e;
		};
		$this[0]['style'] = join( ';', $w );
		return $this[0];
	}
	function set_style( string $q ):webapp_htt
	{
		$this[0]['style'] = $q;
		return $this[0];
	}
	function set_val( $q )
	{
		$this[0] = $q;
		return $this[0];
	}
#tag
	function tag_a( string $q, string $w = '#' ):webapp_htt
	{
		$e = &$this[0]->a[];
		$e['href'] = $w;
		$e[0] = $q;
		return $e;
	}
	function tag_button( string $q, string $w = 'button' ):webapp_htt
	{
		$e = &$this[0]->button[];
		$e['type'] = $w;
		$e[0] = $q;
		return $e;
	}
	function tag_form( callable $q = NULL )
	{
		$w = &$this[0]->form[];
		$w['autocomplete'] = 'off';
		$w['target'] = '_self';
		$w['method'] = 'post';
		return $q === NULL ? $w : $q( $w );
	}
	function tag_input( string $q = 'text' ):webapp_htt
	{
		$w = &$this[0]->input[];
		$w['type'] = $q;
		return $w;
	}
	function tag_script( string $q ):webapp_htt
	{
		$w = &$this[0]->script[];
		$w['type'] = 'text/javascript';
		return $w->add_cdata( $q );
	}
	function tag_select( array $q, string $w = NULL ):webapp_htt
	{
		$e = &$this[0]->select[];
		foreach ( $q as $q => $r )
		{
			if ( is_array( $r ) )
			{
				$t = &$e->optgroup[];
				$t['label'] = $q;
				foreach ( $r as $r => $q )
				{
					$y = &$t->option[];
					$y['value'] = $r;
					$w == $r && $y['selected'] = TRUE;
					$y[0] = $q;
				};
				continue;
			};
			$t = &$e->option[];
			$t['value'] = $q;
			$w == $q && $t['selected'] = TRUE;
			$t[0] = $r;
		};
		return $e;
	}
	function tag_style( string $q, string $w = 'all' ):webapp_htt
	{
		$e = &$this[0]->style[];
		$e['media'] = $w;
		$e[0] = $q;
		return $e;
	}
	function tag_table( callable $q = NULL )
	{
		$w = &$this[0]->table[];
		$w->tfoot = $w->tbody = $w->thead = $w->caption = NULL;
		//[ $w->thead, $w->tbody, $w->tfoot ];
		return $q === NULL ? $w : $q( $w );
	}
#ins
	function ins_dialog( string $q = NULL, string $w = NULL, string $e = NULL ):webapp_htt
	{
		$r = &$this[0]->div[];
		$r['class'] = 'wa_dialog' . ( $e ? ' ' . $e : '' );
		$r->div = $q;
		$r->div[] = $w;
		return $r;
	}

	function ins_label_input( string $q, bool $w = TRUE ):webapp_htt
	{
		$e = &$this[0]->label[];
		$e['class'] = 'wa_label_input over-bgcolor';
		$e->input['type'] = $w === TRUE ? 'checkbox' : 'radio';
		$e->span = $q;
		return $e;
	}
	function ins_progressbar():webapp_htt
	{
		$q = &$this[0]->div[];
		$q['class'] = 'wa_progressbar';
		$q->div->div['style'] = 'width:0%';
		return $q;
	}

	function ins_page( array $q, int $w = 9 ):webapp_htt
	{
		$w = wa::get_query_remove( $w ) . '(' . $w . ')';
		$e = &$this[0]->div[];
		$e['class'] = 'wa_page';
		$r = &$e->span;
		$r->tag_a( wa_page_prev, $w . ( $q['page_current'] - 1 ) );
		foreach ( $q['page_list'] as $t )
		{
			$r = &$e->span[];
			$r->tag_a( $t, $w . $t );
		};
		$r = &$e->span[];
		$r->tag_a( wa_page_next, $w . ( $q['page_current'] + 1 ) );
		$r = &$e->span[];
		$t = $r->tag_input();
		$t['value'] = $q['page_current'];
		$t['onkeyup'] = 'event.keyCode==13&&wa.query_act({9:this.value})';
		$t['maxlength'] = 6;
		$r = &$e->span[];
		$t = $r->tag_a( wa_goto, $w );
		$t['onclick'] = 'return $.go(this.href+this.parentElement.previousSibling.firstChild.value)';
		return $e;
	}

	function ins_filter( array $q, array $w = [], int $e = 7 ):webapp_htt
	{
		$r = $this[0]->tag_form();
		$r['class'] = 'wa_filter';
		$r['action'] = wa::get_query_remove( $e );
		$r['onsubmit'] = 'return wa.filter(this)';
		$r['data-index'] = $e;
		$t = &$r->dl;
		$y = &$t->dt;
		$y->tag_button( wa_filter_add_conditions )->set_attrs([
			'onclick' => 'wa.filter_add_conditions(this)',
			'class' => 'g'
		]);
		$y->tag_button( wa_filter_submit_screening, 'submit' )['class'] = 'b';
		$y = &$t->dd;
		$y['style'] = 'display:none';
		$y->tag_button( wa_remove )->set_attrs([
			'onclick' => 'this.parentNode.remove()',
			'class' => 'r'
		]);
		$y->tag_select( $q )['name'] = 'field[]';
		$y->tag_select([
			'eq' =>wa_filter_where_eq,
			'le' => wa_filter_where_le,
			'ge' => wa_filter_where_ge,
			'ne' => wa_filter_where_ne,
			'not_like' => wa_filter_where_not_like,
			'regexp' => wa_filter_where_regexp,
			'like' => wa_filter_where_like ])['name'] = 'query[]';
		$y->tag_input()->set_attrs( $w + [ 'name' => 'value[]', 'style' => 'width:300px' ] );
		if ( isset( $_GET[ $e ] ) )
		{
			$q = array_keys( $q );
			$w = $y->get_dom();
			$t = $t->get_dom();
			foreach ( explode( '/', $_GET[ $e ], WA_FILTER_MAX ) as $e )
			{
				$e = explode( '.', $e, 3 );
				if ( isset( $e[1] ) && preg_match( '/^(eq|le|ge|ne|not_like|regexp|like)$/', $e[1] ) && in_array( $e[0], $q, TRUE ) )
				{
					$y = simplexml_import_dom( $t->appendChild( $w->cloneNode( TRUE ) ) );
					$y->select[0]->xpath( 'option[@value="' . $e[0] . '"]' )[0]['selected'] = TRUE;
					$y->select[1]->xpath( 'option[@value="' . $e[1] . '"]' )[0]['selected'] = TRUE;
					isset( $e[2] ) && $y->input['value'] = urldecode( $e[2] );
					unset( $y['style'] );
				};
			};
		};
		return $r;
	}

	function ins_multiselect( string $q, $w, array $e = NULL ):webapp_htt
	{
		$r = $this[0]->tag_table();
		$r['class'] = 'wa_multiselect';
		$r->caption->select['name'] = $q . '[]';
		$q = &$r->tbody->tr;
		$q->td[]->select = $q->td[] = $q->td->select = NULL;
		$q->td[2]->select['multiple'] = $q->td->select['multiple'] = $r->caption->select['multiple'] = TRUE;
		$q->td[2]->select['style'] = $q->td->select['style'] = '178px';
		if ( is_array( $w ) )
		{
			$q = $r->tbody->tr->td->select;
			foreach ( $e === NULL ? $w : array_diff_key( $w, $e ) as $t => $y )
			{
				$w = &$q->option[];
				$w['value'] = $t;
				$w[0] = $y;
			};
		}
		else
		{
			$r->thead->tr->td['colspan'] = 3;
			$q = &$r->thead->tr->td->div;
			$q = $q->tag_input();
			$q['onfocus'] = 'wa.over_input(this,wa.multiselect_ajax,$.query("select",$.get_parent_table(this))[1])';
			$q['data-url'] = $w;
		};
		$q = $r->tbody->tr->td[2]->select;
		if ( $e !== NULL )
		{
			foreach ( $e as $t => $y )
			{
				$w = &$r->caption->select->option[];
				$e = &$q->option[];
				$w['selected'] = TRUE;
				$e['value'] = $w['value'] = $t;
				$w[0] = $e[0] = $y;
			};
		};

		$q = $r->tbody->tr->td[1];
		$w = 'wa.multiselect_%s.apply(wa,$.query("select",$.get_parent_table(this))%s)';

		$e = $q->tag_button( '>>' );
		$e['onclick'] = sprintf( $w, 'append', '.concat(true)' );

		$e = $q->tag_button( '>' );
		$e['onclick'] = sprintf( $w, 'append', NULL );

		$e = $q->tag_button( '<' );
		$e['class'] = 'r';
		$e['onclick'] = sprintf( $w, 'remove', NULL );

		$e = $q->tag_button( '<<' );
		$e['class'] = 'r';
		$e['onclick'] = sprintf( $w, 'remove', '.concat(true)' );

		return $r;
	}


	function ins_user_change_password( callable $q = NULL )
	{
		$w = $this[0]->tag_form();
		$w['class'] = 'wa_user_change_password';
		$w['action'] = '?/wa/user_change_password(1)' . urlencode( wa::$user['username'] );
		$w['onsubmit'] = 'return wa.user_change_password(this)';
		$w['data-error_old_password'] = wa_error_old_password;
		$w['data-error_confirm_password'] = wa_error_confirm_password;
		$e = $w->tag_table();
		$e->tbody->add( 'tr' )->add( 'td' )->tag_input( 'password' )->set_attrs([
			'name' => 'password_old',
			'onfocus' => 'wa.over_input(this,wa.user_change_password_input)',
			'placeholder' => wa_enter_old_password,
			'data-password_md5' => wa::$user['password'],
			'required' => 'required'
		]);
		$e->tbody->add( 'tr' )->add( 'td' )->tag_input( 'password' )->set_attrs([
			'name' => 'password_new',
			'onfocus' => 'wa.over_input(this,wa.user_change_password_test)',
			'placeholder' => wa_enter_new_password,
			'required' => 'required'
		]);
		$e->tbody->add( 'tr' )->add( 'td' )->tag_input( 'password' )->set_attrs([
			'name' => 'password_confirm',
			'onfocus' => 'wa.over_input(this,wa.user_change_password_input)',
			'placeholder' => wa_enter_confirm_password,
			'required' => 'required'
		]);
		$e->tfoot->add( 'tr' )->add( 'td' )->tag_button( wa_submit, 'submit' );
		return $q === NULL ? $w : $q( $w );
	}
}