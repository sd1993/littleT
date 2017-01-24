<?php
abstract class core
{
	const a = 1.1, code = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwx';
	static private $key = '801462ABCDEFGHIJKLMNOPQRSTUVWXYZ', $lib;
	static public $errors = [];
	final function __construct(){exit;}

	static public function init( callable $q = NULL )
	{
		self::$lib === NULL ? self::$lib = __DIR__ . '/' : exit;
		if ( $q !== NULL )
		{
			isset(
				$_SERVER['HTTP_ACCEPT_ENCODING'],
				$_SERVER['HTTP_ACCEPT_LANGUAGE'],
				$_SERVER['HTTP_USER_AGENT'],
				$_SERVER['QUERY_STRING'],
				$_SERVER['REQUEST_TIME'] ) || exit;
			$_GET = preg_match_all( '/\((\d)\)([\%\+\-\.\/\=\w]+)?/', '(0)' . $_SERVER['QUERY_STRING'], $_GET )
				? array_combine( $_GET[1], $_GET[2] )
				: [];
			register_shutdown_function( $q );
		};
	}

	static public function call( string $q, ... $w )
	{
		class_exists( $q, FALSE ) || require self::$lib . $q . '.php';
		return call_user_func_array( [ new reflectionclass( $q ), 'newinstance' ], $w );
	}

	static public function entime( int $q = NULL ):string
	{
		$q = $q === NULL ? getdate() : getdate( $q );
		$w = self::code;
		
		return $q['year'] . $w[ $q['mon'] ] . $w[ $q['mday'] ] . $w[ $q['hours'] ] . $w[ $q['minutes'] ] . $w[ $q['seconds'] ];
	}

	static public function detime( string $q ):int
	{
		$w = self::code;
		if ( strlen( $q ) === 9 && ltrim( $q, $w ) === '' )
		{
			$q = mktime( strpos( $w, $q[6] ), strpos( $w, $q[7] ), strpos( $w, $q[8] ), strpos( $w, $q[4] ), strpos( $w, $q[5] ), substr( $q, 0, 4 ) | 0 );
			return $q > 0 ? $q : 0;
		};
		return 0;
	}

	static public function encrypt( string $q ):string
	{
		return base64_encode( mcrypt_encrypt( MCRYPT_RIJNDAEL_128, self::$key, $q, MCRYPT_MODE_ECB ) );
	}

	static public function decrypt( string $q ):string
	{
		return rtrim( mcrypt_decrypt( MCRYPT_RIJNDAEL_128, self::$key, base64_decode( $q, TRUE ), MCRYPT_MODE_ECB ) );
	}

	static public function debug_time( &$q ):int
	{
		return $q = microtime( TRUE ) - $q;
	}

	static public function short_hash( string $q, callable $w = NULL ):string
	{
		for ( $q = ( $w ?? 'md5' )( $q, TRUE ), $w = self::code, $e = '', $t = 0, $r = 0; $r < 8; ++$r )
		{
			$e .= $w[ ( $t = ord( $q[ $r ] ) ^ ord( $q[ $r + 8 ] ) ^ $t ) & 31 ];
		};
		return $e;
	}

	static public function full_char( string $q, bool $w = FALSE )
	{
		preg_match_all( '/[\x01-\x7f]|[\xc2-\xdf][\x80-\xbf]|\xe0[\xa0-\xbf][\x80-\xbf]|[\xe1-\xef][\x80-\xbf][\x80-\xbf]|\xf0[\x90-\xbf][\x80-\xbf][\x80-\xbf]|[\xf1-\xf7][\x80-\xbf][\x80-\xbf][\x80-\xbf]/', $q, $q );
		return $w ? $q[0] : join( $q[0] );
	}

	static public function str_omit( string $q, int $w = 42 ):string
	{
		if ( strlen( $q = trim( $q ) ) > $w )
		{
			$r = strlen( $e = self::full_char( substr( $q, 0, $w * 0.5 | 0 ) ) );
			$r += strlen( $q = self::full_char( substr( $q, -( $w - $r - 2 ) ) ) );
			$q = $e . substr( '....', 0, $w - $r ) . $q;
		};
		return $q;
	}

	static public function hsl_color( float $q, float $w = 1, float $e = 0.5 ):int
	{
		$w = $e > 0.5 ? $e + $w - $e * $w : $e * ( $w + 1 );
		if ( $w > 0 )
		{
			$e = $e + $e - $w;
			$q *= 6;
			$r = $q | 0;
			$t = $w * ( ( $w - $e ) / $w ) * ( $q - $r );
			$y = $e + $t;
			$u = $w - $t;
			switch ( $r )
			{
				case 0: list( $q, $w ) = [ $w, $y ]; break;
				case 1: $q = $u; break;
				case 2: list( $q, $e ) = [ $e, $y ]; break;
				case 3: list( $q, $w, $e ) = [ $e, $u, $w ]; break;
				case 4: list( $q, $w, $e ) = [ $y, $e, $w ]; break;
				case 5: list( $q, $w, $e ) = [ $w, $e, $u ]; break;
			};
			return $q * 255 << 16 | $w * 255 << 8 | $e * 255;
		};
		return 0;
	}

	static public function rgb_hsl( int $q, int $w, int $e ):array
	{
		$q /= 255;
		$w /= 255;
		$e /= 255;
		$r = min( $q, $w, $e );
		$t = max( $q, $w, $e );
		$y = $t - $r;
		if ( $y == 0 )
		{
			return [ 0, 0, 0 ];
		};
		$u = ( ( $t - $w ) / 6 + $y * 0.5 ) / $y;
		$i = ( ( $t - $e ) / 6 + $y * 0.5 ) / $y;
		if ( $q == $t )
		{
			$e = $i - $u;
		}
		else
		{
			$q = ( ( $t - $q ) / 6 + $y * 0.5 ) / $y;
			$e = $w == $t ? 1 / 3 + $q - $i : 2 / 3 + $u - $q;
		};
		$e < 0 && $e += 1;
		$e > 1 && $e -= 1;
		$q = ( $t + $r ) * 0.5;
		return [ $e, $q > 0.5 ? $y / ( 2 - $y ) : $y / ( $t + $r ), $q ];
	}

	static public function ip_pton( string $q ):string
	{
		return sprintf( '%032s', bin2hex( inet_pton( $q ) ) );
	}

	static public function ip_ntop( string $q ):string
	{
		return inet_ntop( hex2bin( $q ) );
	}
};