<?php
class thumbnail
{
	function __construct()
	{
		$this->et = $a;
		$this->new_x = 160;
		$this->new_y = 120;
		$this->out_type = 'jpeg';
	}
	function imagecreatefrombmp( $a )
	{
		if ( !$a = @fopen( $a, 'rb' ) )
		{
			return false;
		};
		$s = @unpack( 'vtype/Vsize/Vreserved/Voffset', fread( $a, 14 ) );
		$d = @unpack( 'Vsize/Vwidth/Vheight/vplanes/vbits/Vcompression/Vbitmap/Vresolution0/Vresolution1/Vused/Vimportant', fread( $a, 40 ) );
		if ( !isset( $s['type'] ) || !isset( $d['bits'] ) || ( $s['type'] != 19778 ) || !ereg( '1|4|8|16|24', $d['bits'] ) )
		{
			fclose( $a );
			return false;
		};
		$d['colors'] = pow( 2, $d['bits'] );
		$d['bitmap'] == 0 && $d['bitmap'] = $s['size'] - $s['offset'];
		$d['bytes'] = $d['bits'] / 8; #ceil
		$d['decal'] = 4 - ( 4 * ( ( $d['width'] * $d['bytes'] / 4 ) - floor( $d['width'] * $d['bytes'] / 4 ) ) );
		$d['decal'] == 4 && $d['decal'] = 0;
		$d['empty'] = chr(0);
		$s = $d['colors'] < 16777216 ? unpack( 'V' . $d['colors'], fread( $a, $d['colors'] * 4 ) ) : array();
		$f = fread( $a, $d['bitmap'] );
		fclose( $a );
		$a = imagecreatetruecolor( $d['width'], $d['height'] );
		$g = 0;
		$h = $d['height'] - 1;
		while ( $h >= 0 )
		{
			$j = 0;
			while ( $j < $d['width'] )
			{
				switch ( $d['bits'] )
				{
					case 24 :
						$k = unpack( 'V', substr( $f, $g , 3 ) . $d['empty'] );
						break;
					case 16 :
						$k = unpack( 'n', substr( $f, $g , 2 ) );
						$k[1] = $s[ $k[1] + 1 ];
						break;
					case 8 :
						$k = unpack( 'n', $d['empty'] . substr( $f, $g, 1 ) );
						$k[1] = $s[ $k[1] + 1 ];
						break;
					case 4 :
						$k = unpack( 'n', $d['empty'] . substr( $f, floor( $g ), 1 ) );
						$k[1] = ( $g * 2 ) % 2 == 0 ? $k[1] >> 4 : $k[1] & 0x0F;
						$k[1] = $s[ $k[1] + 1 ];
						break;
					default :
						$k = unpack( 'n', $d['empty'] . substr( $f, floor( $g ), 1 ) );
						switch ( ( $g * 8 ) % 8 )
						{
							case 1 : $k[1] = ( $k[1] & 0x40 ) >> 6; break;
							case 2 : $k[1] = ( $k[1] & 0x20 ) >> 5; break;
							case 3 : $k[1] = ( $k[1] & 0x10 ) >> 4; break;
							case 4 : $k[1] = ( $k[1] & 0x8 ) >> 3; break;
							case 5 : $k[1] = ( $k[1] & 0x4 ) >> 2; break;
							case 6 : $k[1] = ( $k[1] & 0x2 ) >> 1; break;
							case 7 : $k[1] = ( $k[1] & 0x1 ); break;
							default : $k[1] = $k[1] >> 7; break;
						};
						$k[1] = $s[$k[1]+1];
						break;
				};
				imagesetpixel( $a, $j, $h, $k[1] );
				$j++;
				$g += $d['bytes'];
			};
			$h--;
			$g += $d['decal'];
		};
		return $a;
	}
	function image_load( $a )
	{
		if ( $s = @getimagesize( $a ) )
		{
			switch ( $s[2] )
			{
				case 1 : return imagecreatefromgif( $a );
				case 2 : return imagecreatefromjpeg( $a );
				case 3 : return imagecreatefrompng( $a );
				case 6 : return $this->imagecreatefrombmp( $a );
				#case 15 : return @imagecreatefromwbmp( $a );
				#case 16 : return imagecreatefromxbm( $a );
				default : return false;
			};
		};
		return false;
	}
	function create( $a, $s )
	{
		if ( $a = $this->image_load( $a ) )
		{
			$d = imagesx( $a );
			$f = imagesy( $a );
			$g = $this->new_x / $d;
			$h = $this->new_y / $f;
			$j = $g < $h ? $g : $h;
			$g = $d * $j;
			$h = $f * $j;
			$j = imagecreatetruecolor( $g, $h );
			imagecopyresampled( $j, $a, 0, 0, 0, 0, $g, $h, $d, $f );
			imagedestroy( $a );
			$a = 'image' . $this->out_type;
			$a = @$a( $j, $s );
			imagedestroy( $j );
		};
		return $a;
	}
	function display( $a )
	{
		is_file( $a ) && core::send( $a, $this->out_type );
		header( 'Content-type: image/png', true );
		for
		(
			$s = 0,
			$d = imagecreate( $this->new_x, $this->new_y ),
			imagefilledrectangle( $d, 0, 0, $this->new_x, $this->new_y, imagecolorallocate( $d, 0, 0, 255 ) ),
			$f = imagecolorallocate( $d, 255, 255, 255 ),
			$g = $this->new_x / 9 | 0,
			$h = 10,
			$j = strlen( $a );
			$s < $j;
			imagestring( $d, 3, 8, $h += 12, substr( $a, $s, $g ), $f ),
			$s += $g
		);
		imagestring( $d, 3, 4, 4, 'File', $f );
		imagestring( $d, 3, $this->new_x - 76, $this->new_y - 16, 'not exist!', $f );

		
		imagepng( $d );
		imagedestroy( $d );
		exit;
	}
};