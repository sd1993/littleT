<?php
class shmop
{
	private $chmod = 0644, $shmid, $shmop;

	function __construct( int $q = NULL )
	{
		$this->shmid = $q ?? mt_rand() & 65535;
	}

	function __destruct()
	{
		$this->shmop && $this->close();
	}

	// function chmod( int $q = NULL )
	// {
	// }

	// function shmid( int $q = NULL )
	// {
	// }

	function open()
	{
		return $this->shmop ? $this->shmop : $this->shmop = @shmop_open( $this->shmid, 'w', 0, 0 );
	}

	function size():int
	{
		return shmop_size( $this->shmop );
	}

	function delete():shmop
	{
		shmop_delete( $this->shmop );
		return $this;
	}

	function close():shmop
	{
		$this->shmop = shmop_close( $this->shmop );
		return $this;
	}

	function read():string
	{
		// $q = shmop_read( $this->open(), 0, $this->size() );
		// $w = strpos( $q, "\0" );
		// return $w === FALSE ? $q : substr( $q, 0, $w );
		return shmop_read( $this->open(), 0, $this->size() );
	}

	function read_json( bool $q = TRUE ):array
	{
		return json_decode( $this->read(), $q );
	}

	function write( string $q ):shmop
	{
		$this->open() && $this->delete()->close();
		//$w = shmop_open( $this->shmid, 'c', $this->chmod, strlen( $q .= "\0" ) );
		$w = shmop_open( $this->shmid, 'c', $this->chmod, strlen( $q ) );
		shmop_write( $w, $q, 0 );
		shmop_close( $w );
		return $this;
	}

	function write_json( array $q ):shmop
	{
		return $this->write( json_encode( $q, JSON_UNESCAPED_UNICODE ) );
	}

	function callback( callable $q )
	{
		return $q( $this );
	}

	// function json_replace( $q, $w )
	// {
	// 	$e = $this->read_json();
	// 	$e[ $q ] = $w;
	// 	return $this->write_json( $e );
	// }

	// function json_delete( $q )
	// {
	// 	$e = $this->read_json();
	// 	unset( $e[ $q ] );
	// 	return $this->write_json( $e );
	// }
}