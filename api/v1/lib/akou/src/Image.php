<?php
namespace AKOU;

Class Image
{
	public function __construct() 
	{
        //parent::__construct();
		$this->content_type	= 'image/jpeg';
		$this->size			= -1;
		$this->image		= NULL;
		$this->debug		= TRUE;
    }


	public function saveToFile( $path ,$content_type  = NULL)
	{
		$name_file_raw	= $path;

		if( !\file_exists( dirname( $path  ) ) ) 
		{
			if( !\mkdir( dirname( $path ) )) 
			{
				throw new StoreException
				(
					'Ocurrio el error no se pudo crear la carpeta donde se guardan los archivos en servidor local'
					,'No se creo '.$path_raw.'/'.$dirname
				);
			}
		}

		$data		= $this->getImageData( $content_type );
		$this->size	= \strlen( $data );

		if( $this->debug )
			error_log('SAVING '.$this->size.' OF DATA ');

		$fp			= \fopen( $name_file_raw, 'w' );
		\fwrite( $fp, $data );
		\fclose( $fp );
		return TRUE;
	}

	public function getSize()
	{
		return $this->size;
	}

	public function loadFromFile( $name_file_raw ,$type = NULL )
	{
		if( $this->debug  )
			error_log('TRU TO LOAD'.$name_file_raw );

		$content_type	= $type;

		if( $content_type === NULL )
		{
			$ct	= $this->getMime( $name_file_raw );
			$content_type = empty( $ct ) ? NULL : $ct;
		}

		if( $content_type !== NULL )
		{
			$content_types	= array( IMAGETYPE_GIF => 'image/gif' , IMAGETYPE_JPEG=>'image/jpeg', IMAGETYPE_PNG=>'image/png' );
			$exif_type		= \exif_imagetype( $name_file_raw );

			$content_type = isset( $content_types[ $exif_type ] ) 
				? $content_types[ $exif_type ] 
				: $content_type;
		}

		if( $this->debug )
			error_log('CURRENT IS '.$content_type );

		switch ( $content_type )
		{
			case 'image/jpg':
			case 'image/jpeg':
				$this->content_type	= 'image/jpeg';
				$this->image		= imagecreatefromjpeg( $name_file_raw );
				return;
			case 'image/gif':
				$this->content_type = $content_type;
				$this->image		= \imagecreatefromgif( $name_file_raw );
				return;
			case 'image/png':
				$this->content_type = $content_type;
				$this->image		= \imagecreatefrompng( $name_file_raw );
				return;
			case 'image/bmp':
				$this->content_type = $content_type;
				$this->image		= \imagecreatefromwbmp( $name_file_raw );
				return;
			default:
				$this->content_type	= 'image/jpg';
				$this->image		= \imagecreatefromjpeg( $name_file_raw );
				return;
		}

		if( !$this->image && \file_exists( $name_file_raw ) && \filetype( $name_file_raw ) != 'dir' )
		{
			if( $this->debug )
				error_log('not Loaded even from default' );

			$image_content_string	= \file_get_contents( $name_file_raw );
			$this->size				= \strlen( $image_content_string );
			$this->image			= \imagecreatefromstring( $image_content_string );
		}

		if( !$this->image )
			throw new SystemException('Image type not supported'. print_r( $_FILES['file'] ) );
	}

	public function getContentType()
	{
		return $this->content_type;
	}

	public function getMime( $file )
	{
		if ( \function_exists( 'finfo_file' ) )
		{
			$finfo  = \finfo_open( FILEINFO_MIME_TYPE ); // return mime type ala mimet
			$mime	= \finfo_file( $finfo, $file );
			\finfo_close( $finfo );
			return $mime;
		}
		else if ( \function_exists( 'mime_content_type' ) )
		{
			return \mime_content_type( $file );
		}

		return FALSE;
	}

	static function resize_image( $width, $height=0 )
	{
		//Get the new DImensions
		//list($original_width, $original_height) = getimagesize( $image );
		$imagen_p			= $this->image;
		$original_width		= \imagesx( $this->image );
		$original_height	= \imagesy( $this->image );

		//If is bigger do nothing;
		if( $original_width <= $width && $height == 0 )
		{
			if( $this->debug )
				error_log('original_width'.$original_width.' height'.$height );
			return;
		}

		/*RULE OF 3 
			IF A = B
			X = Y

			Y = (B*X)/A


			$original_width = $original_height
			$width		  = $height		 

			$height		 = $original_height * $width/$original_width
			1280x1022
			453*361
		*/


		$ratio_original	 = $original_width / $original_height;

		if( $heigth == 0 )
		{
			$request_height = $original_height*$width / $original_width;
		}
		else
		{
			$request_height  = $height;
		}


		$request_width	= $width;
		$ratio_new		= $request_width / $request_height;

		if( $ratio_new < $ratio_original )
		{
			if( $this->debug )
				error_log('ration_new < original_ratio '.$ratio_new.' '.$ratio_original);

			// resize en heigth
			$ratio		= $this->getRatio( $original_height, $request_height );
			$R_width		= $original_width * $ratio;
			$R_height		= $original_height * $ratio;
			// crop
			$excedente_width	= $R_width-$request_width;
			$margin_left		= $excedente_width / 2;
			// Resize
			$imagen_p			= \imagecreatetruecolor( $request_width, $request_height );
			\imagealphablending( $imagen_p, true);
			\imagesavealpha( $imagen_p,true);

			$transparent		= \imagecolorallocatealpha($imagen_p, 255, 255, 255, 127);
			$transparent1		= \imagecolortransparent( $imagen_p, $transparent );

			\imagefill($imagen_p, 0, 0, $transparent1);

			//crea la imagen
			\imagecopyresampled
			(
				$imagen_p,
				$this->image,
				0,
				0,
				$margin_left / $ratio,
				0,
				$R_width,
				$request_height,
				$original_width,
				$original_height
			);

			$this->image	= $imagen_p;
		}
		else 
		{
			if( $this->debug )
				error_log('ration_new >= original_ratio '.$ratio_new.' '.$ratio_original);
			// resize en heigth
			$ratio		= $this->getRatio( $original_width,$request_width );
			$R_width	= $original_width * $ratio;
			$R_height	= $original_height * $ratio;
			// crop
			$excedente_height	= $R_height-$request_height;
			$margin_top		 = $excedente_height / 2;
			// Redimensionar
			$imagen_p = \imagecreatetruecolor( $request_width, $request_height );

			\imagealphablending( $imagen_p, true);
			\imagesavealpha( $imagen_p,true);

			$transparent		= \imagecolorallocatealpha($imagen_p, 255, 255, 255, 127);
			$transparent1		= \imagecolortransparent( $imagen_p, $transparent );
			\imagefill($imagen_p, 0, 0, $transparent1);
			//imagefilledrectangle( $imagen_p, 0, 0, $request_width, $request_height, $transparent1 );


			/***
			echo ''.
				'imagen_destino : '				. $imagen_p				 . '<br>' .
				'$imagen_origen : '					. $image					. '<br>' .
				'destino x	  : '						. 0						 . '<br>' .
				'destino y	  : '						. 0						 . '<br>' .
				'origen x		: '						. 0						 . '<br>' .
				'origen y		: '. $margin_top / $ratio . '<br>' .
				'destino_width  : '		. $request_width		 . '<br>' .
				'destino_height : '				. $R_height				 . '<br>' .
				'origen_width	: '		  . $original_width			. '<br>' .
				'origen_height	: '		 . $original_height
			;
			/**/

			\imagecopyresampled
			(
				$imagen_p,
				$this->image,
				0,
				0,
				0,
				$margin_top / $ratio,
				$request_width,
				$R_height,
				$original_width,
				$original_height
			);
			$this->image	= $imagen_p;
		}
	}

	function resizeSameRatio($max_width, $max_height )
	{
		$imagen_p			= $this->image;
		$original_width		= \imagesx( $this->image );
		$original_height	= \imagesy( $this->image );

		error_log('WIDTH '.$original_width.' height '.$original_height );
		//fools test
		if( $original_width <= $max_width && $original_height<= $max_height )
		{
			error_log('SOME ERROR HEEEEEEEE');
			return;
		}

		$data_resize		= $this->getResizeData( $original_width, $original_height, $max_width, $max_height );

		$solicitado_width	= $data_resize['new_width'];
		$solicitado_height	= $data_resize['new_height'];

		// Resize
		$imagen_p			= \imagecreatetruecolor( $solicitado_width, $solicitado_height );
		\imagealphablending( $imagen_p ,false );
		\imagesavealpha( $imagen_p ,true);
		$transparent		= \imagecolorallocatealpha( $imagen_p, 255, 255, 255, 127 );
		\imagefilledrectangle( $imagen_p, 0, 0, $solicitado_width, $solicitado_height, $transparent );

		\imagecopyresampled
		(
			$imagen_p,
			$this->image,
			0, //dest X
			0, //des Y
			0, //src X
			0, //src Y
			$solicitado_width,
			$solicitado_height,
			$original_width,
			$original_height
		);

		$this->image	= $imagen_p;
	}

	function getImageData( $content_type = NULL )
	{
		$ct	= $content_type;
		if( $ct	=== NULL )
			$ct = $this->content_type;

		if( empty( $this->image ) )
			error_log('THE FUUU image is empty');
	
		\ob_start();
		switch ( strtolower( $ct ) )
		{
			case 'image/png':
				error_log('THe image is '.$ct );
				\imagealphablending($this->image, true);
				\imagesavealpha($this->image, TRUE);
				\imagepng( $this->image,NULL,9,PNG_ALL_FILTERS );
				break;
			//case 'image/bmp':
			//	imagewbmp( $this->image );
			//	break;
			case 'image/gif':
				error_log('THe image is '.$ct );
				\imagegif( $this->image );
				break;
			default:
			{
				error_log('THe image is '.$ct );
				\imagejpeg( $this->image,NULL,97 );
			}
		}

		$image_content_string	= \ob_get_contents();
		\ob_end_clean();
		error_log('SIZE IS '.\strlen( $image_content_string ) );

		return $image_content_string;
	}

	function outputImage( $content_type )
	{
		\header( 'Cache-Control: max-age=259200'); 
		\header( 'Content-type: ' . $content_type );
		$image_content_string	= $this->getImageData( $content_type ); 
		$image_size				= \strlen( $image_content_string );
		\header( 'Content-Length: ' . $image_size );
		return $image_content_string;
	}

	function getResizeData($image_width, $image_height, $max_width, $max_height)
	{
		$new_width	= $image_width;
		$new_height	= $image_height;

		if( $image_width > $max_width )
		{
			$ratio		= $max_width / $image_width;
			$new_height	= $image_height * $ratio;
			$new_width	= $max_width;
		}

		if( $new_height > $max_height )
		{
			$ratio		= $max_height / $new_height;
			$new_width	= $new_width * $ratio;
			$new_height = $max_height;
		}
		return array('new_width'=>$new_width, 'new_height'=>$new_height );
	}

	function getRatio( $original, $custom )
	{
		$temp	= ( $custom * 100) / $original;
		$temp	= $temp / 100; // decimal
		return $temp;
	}
}



