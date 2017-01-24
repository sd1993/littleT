<?php
#WA MySQL Class
final class webapp_sql_result extends mysqli_result
{
	function __construct( webapp_sql $q )
	{
		parent::__construct( $q );
	}

	// function __destruct()
	// {
	// 	$this->free();
	// }

	function all( int $q = MYSQLI_ASSOC )
	{
		return $this->fetch_all( $q );
	}

	function each( callable $q ):webapp_sql_result
	{
		foreach( $this as $w => $e )
		{
			$q( $e, $w );
		};
		return $this;
	}

	function fetch_bind( &$q, int $w = MYSQLI_ASSOC ):array
	{
		return $q = $this->fetch_array( $w ) ?? [];
	}

	function fetch_callback( callable $q, int $w = MYSQLI_ASSOC )
	{
		$w = $this->fetch_array( $w );
		return $w ? $q( $w ) : NULL;
	}

	// function fetch_field_array( $q )
	// {

	// }
}
final class webapp_sql extends mysqli
{
	function __construct( string $q = '127.0.0.1', string $w = 'root', string $e = '', string $r = 'mysql' )
	{
		@parent::__construct( $q, $w, $e, $r );
		// if ( $this->connect_error )
		// {
		// 	echo $this->connect_error;
		// 	exit;
		// };
	}

	function __destruct()
	{
		@$this->close();
	}

	function escape( string $q ):string
	{
		return '"' . $this->real_escape_string( $q ) . '"';
	}

	function quote( string $q ):string
	{
		return '`' . addcslashes( $q, '`' ) . '`';
	}

	function q_join( array $q ):string
	{
		for ( $w = 0, $e = 0; isset( $q[ ++$e ] ); $w += strlen( $r ) )
		{
			if ( preg_match( '/\?[abdis]/', $q[0], $r, PREG_OFFSET_CAPTURE, $w ) )
			{
				$w = $r[0][1];
				switch ( $r[0][0] )
				{
					case '?a':
						$r = $this->quote( $q[ $e ] );
						break;
					case '?b':
						$r = $q[ $e ];
						break;
					case '?d':
						$r = floatval( $q[ $e ] );
						break;
					case '?i':
						$r = $q[ $e ] | 0;
						break;
					default:
						$r = $this->escape( $q[ $e ] );
				};
				$q[0] = substr( $q[0], 0, $w ) . $r . substr( $q[0], $w + 2 );
				continue;
			};
			throw new exception;
			break;
		};
		return $q[0];
	}

	function q_send( string $q ):int
	{
		$this->real_query( $q );
		if ( $this->error )
		{
			$this->stack_errors[] = $this->error;
			return 0;
		};
		return $this->affected_rows;
	}

	function q_sent( string ... $q ):int
	{
		return $this->q_send( $this->q_join( $q ) );
	}

	function q_query( string ... $q )
	{
		return $this->q_send( $this->q_join( $q ) ) ? new webapp_sql_result( $this ) : NULL;
	}

	function q_value( array $q ):string
	{
		$w = [];
		foreach ( $q as $q => $e )
		{
			$w[] = $this->quote( $q ) . '=' . ( $e === NULL ? 'null' : $this->escape( $e ) );
		};
		return join( ',', $w );
	}

	function q_insert( string $q, array $w ):int
	{
		return $this->q_sent( 'insert into ?a set ?b', $q, $this->q_value( $w ) );
	}

	function q_update( string $q, array $w, string ... $e ):int
	{
		return $this->q_sent( 'update ?a set ?b?b', $q, $this->q_value( $w ), isset( $e[0] ) ? ' ' . $this->q_join( $e ) : '' );
	}

	function q_delete( string $q, string ... $w ):int
	{
		return $this->q_sent( 'delete from ?a?b', $q, isset( $w[0] ) ? ' ' . $this->q_join( $w ) : '' );
	}

	function q_task( ... $q ):bool
	{
		if ( $this->autocommit( FALSE ) )
		{
			foreach ( $q as $q )
			{
				if ( $this->q_send( is_array( $q ) ? $this->q_join( $q ) : $q ) )
				{
					continue;
				};
				$this->rollback();
				$this->autocommit( TRUE );
				return FALSE;
			};
			$this->commit();
			$this->autocommit( TRUE );
			return TRUE;
		};
		return FALSE;
	}

	function q_task_callback( callable $q, ... $w ):bool
	{
		if ( $this->autocommit( FALSE ) )
		{
			if ( isset( $w[0] ) )
			{
				$w[] = $this;
				$q = call_user_func_array( $q, $w );
			}
			else
			{
				$q = $q( $this );
			};

			$q ? $this->commit() : $this->rollback();
			$this->autocommit( TRUE );
			return $q;
		};
		return FALSE;
	}

	function get_only( string $q, string $w, string $e )
	{
		return $this->q_query( 'select * from ?a where ?a=?s limit 1', $q, $w, $e )->fetch_assoc();
	}

	function get_only_bind( $q, $w, $e, &$r )
	{
		return $r = $this->get_only( $q, $w, $e );
	}

	function get_rows( string $q, string ... $w ):int
	{
		// return $this->q_query( 'select sql_no_cache count(*) from(select 1 from ?a?b)q', $q,
		// 	isset( $w[0] ) ? ' ' . $this->q_join( $w ) : '' )->fetch_row()[0];
		if ( isset( $w[0] ) && $w[0] !== '' )
		{
			$this->multi_query( 'select sql_calc_found_rows 0 from ' . $this->quote( $q ) . ' ' . $this->q_join( $w ) . ' limit 0;select found_rows()' );
			$this->use_result()->free();
			$this->next_result();
			return $this->use_result()->fetch_row()[0];
		};
		return $this->q_query( 'select sql_no_cache count(*) from ?a', $q )->fetch_row()[0];
	}
}