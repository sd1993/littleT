<?php
/*
	����Ҫ core ��֧�� captcha v1.1s
	$this = new captcha()
	mixed	$this->image_output( string ������ĸ )		�������ҽ���ĸд��ͼ��
	DEBUG:
	void	$this->image_text( string ���� )			������д��ͼƬ
	void	$this->image_wave()							ͼƬ���벨��Ч��
	void	$this->image_revert()						�ָ�ͼƬԭʼ��С
*/
class captcha
{
	function __construct()
	{
		$this->img_width		= 168;					#ͼ��
		$this->img_height		= 84;					#ͼ��
		$this->img_scale		= 2;					#ͼ����
		$this->img_maxlength	= 8;					#ͼ����ַ�����
		$this->img_maxrotation	= 8;					#ͼ����ַ���ת
		$this->img_color		= array( 0, 0, 255 );	#ͼ������ɫ
		$this->img_fonts		= array					#ͼ�����(�ļ���,��࣬��С,���)
		(
			array( './data/fonts/Inder_Regular.otf',		6,	24,	30 ),
			array( './data/fonts/ArchitectsDaughter_R.ttf',	3,	18,	24 ),
			array( './data/fonts/SofadiOne_Regular.ttf',	4,	26,	32 ),
			array( './data/fonts/SourceCodePro_Light.otf',	5,	26,	32 )
		);
		$this->img_blur			= false;				#ͼƬģ��
		$this->img_wave			= array(				#����Ч��Ĭ�Ͽ������ر�Ҫ���� $this->img_wave = false;
			'x0' => 11,
			'x1' => 5,
			'y0' => 12,
			'y1' => 14
		);
		shuffle( $this->img_color );					#�����ɫ
		$this->img_fonts = is_string( $this->img_fonts )#����������ʹ���Զ�������
			? array( $this->img_fonts, -1, 32, 38, )
			: $this->img_fonts[ array_rand( $this->img_fonts ) ];
	}
	function image_text( $q )
	{
		for
		(
			$w = 0,
			$e = $this->img_scale * ( 1 + ( ( $this->img_maxlength - strlen( $q ) ) * 0.09 ) ),
			$r = imagecolorallocate( $this->image, $this->img_color[0], $this->img_color[1], $this->img_color[2] ),
			$t = round( ( $this->img_height * 27 / 40 ) * $this->img_scale ),
			$y = $this->img_scale * 20,
			$k = strlen( $q );
			$w < $k;
			$l = imagettftext( $this->image, rand( $this->img_fonts[2], $this->img_fonts[3] ) * $e, rand( -$this->img_maxrotation, $this->img_maxrotation ), $y, $t, $r, $this->img_fonts[0], $q[ $w ] ),
			$y += ( $l[2] - $y ) + ( $this->img_fonts[1] * $this->img_scale ),
			$w++
		);
	}
	function image_wave()
	{
		for
		(
			$q = 0,
			$w = $this->img_scale * $this->img_wave[ 'x0' ] * mt_rand( 1 ,3 ),
			$e = mt_rand( 0, 100 ),
			$r = $this->img_scale * $this->img_wave[ 'x1' ],
			$t = $this->img_y,
			$y = $this->img_x;
			$q < $y;
			imagecopy( $this->image, $this->image, $q - 1, sin( $e + $q / $w ) * $r, $q, 0, 1, $t ),
			$q++
		);
		for
		(
			$q = 0,
			$w = $this->img_scale * $this->img_wave[ 'y0' ] * mt_rand( 1 ,3 ),
			$e = mt_rand( 0, 100 ),
			$r = $this->img_scale * $this->img_wave[ 'y1' ],
			$t = $this->img_x,
			$y = $this->img_y;
			$q < $y;
			imagecopy( $this->image, $this->image, sin( $e + $q / $w ) * $r, $q - 1, 0, $q, $t, 1 ),
			$q++
		);
	}
	function image_revert()
	{
		$q = imagecreatetruecolor( $this->img_width, $this->img_height );
		imagecopyresampled( $q, $this->image, 0, 0, 0, 0, $this->img_width, $this->img_height, $this->img_x, $this->img_y );
		imagedestroy( $this->image );
		$this->image = $q;
	}
	function image_output( $q = NULL )
	{
		$this->img_x = $this->img_width * $this->img_scale;
		$this->img_y = $this->img_height * $this->img_scale;
		$this->image = imagecreatetruecolor( $this->img_x, $this->img_y );
		imagefilledrectangle( $this->image, 0, 0, $this->img_x, $this->img_y, imagecolorallocate( $this->image, 255, 255, 255 ) );
		if ( is_string( $q ) )
		{
			$this->image_text( substr( $q, 0, $this->img_maxlength ) );
			$this->img_wave && $this->image_wave();
			$this->img_blur && function_exists( 'imagefilter' ) && imagefilter( $this->image, 7 );
			$this->image_revert();
		};
		header( 'Content-Type: image/jpeg' );
		imagejpeg( $this->image );
		imagedestroy( $this->image );
		exit;
	}
};