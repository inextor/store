<?php

namespace Truck;


include_once(__DIR__.'/app.php');
include_once( __DIR__.'/lib/akou/src/Image.php' );

use AKOU\ApiResponse;
use AKOU\DBTable;
use AKOU\SystemException;
use AKOU\ValidationException;
use AKOU\Utils;
use AKOU\ChromePhp;

error_reporting(E_ALL);
// Same as error_reporting(E_ALL);
ini_set('error_reporting', E_ALL);

$response 	= new ApiResponse();

//print_r( gd_info ( ) );
//Array
//(
//    [GD Version] => 2.2.5
//    [FreeType Support] => 1
//    [FreeType Linkage] => with freetype
//    [GIF Read Support] => 1
//    [GIF Create Support] => 1
//    [JPEG Support] => 1
//    [PNG Support] => 1
//    [WBMP Support] => 1
//    [XPM Support] => 1
//    [XBM Support] => 1
//    [WebP Support] => 1
//    [BMP Support] => 1
//    [JIS-mapped Japanese Font Support] => 
//)

App::init();

//error_log('Registered Here, wtf???');
try
{
	ChromePhp::log( 'FILE IS ',print_r( $_FILES, TRUE ) );
	error_log( print_r( $_FILES, TRUE ) ); 
	//print_r( $_FILES['file'] );

	if( !isset( $_FILES['file'] ) )
	{
		throw new StoreException
		(
			'Faltan los datos de la imágen'
			,'El nombre del parametro de la imagen debe ser '
				.'"image_file"'
		);
	}

	$DEFAULT_IMAGE_DIRECTORY	= '/srv/http/domains/127.0.0.6/uimgs';

	if( $_FILES['file']['error'] !== UPLOAD_ERR_OK  )
	{
		throw new SystemException('An error occurred while uploading the image, please try again later');
	}
	//$x = exif_imagetype( $_FILES['file']['type'] );
	//print_r( $x );
	if( !strstr($_FILES['file']['type'],'image' ) )
	{
		throw new ValidationException('Image type not supported'.print_r( $_FILES['file']['type'], true ) );
	}

	$img		= new \AKOU\Image(); 
	$img->loadFromFile($_FILES['file']['tmp_name'] );
	$resized	= FALSE;

	if( $_FILES['file']['size'] > 2000000 )//5242880 )
	{
		$max_width	= 1280;
		$max_height	= 720;
		$img->resizeSameRatio( 1280 ,720 );
		$resized	= TRUE;
	}

	$image					= new image();
	$image->content_type	= $img->getContentType();
	$image->name			= $_FILES['file']['name'];
	$image->created			= 'CURRENT_TIMESTAMP';

	if( !$resized )
	{
		$image->size	= $_FILES['file']['size'];
	}

	if( $image->insertDb() )
	{
		if( $resized )
		{
			$img->saveToFile( $DEFAULT_IMAGE_DIRECTORY.'/'.$image->id );
			error_log('resized SAVING image->save TO '.$DEFAULT_IMAGE_DIRECTORY.'/'.$image->id );
			$image->setWhereString( true );
			$image->size	= $img->getSize();

			if( !empty( $image->size ) && $image->size != -1 )
				$image->updateDb('size');

			error_log( $image->getLastQuery() );
		}
		else
		{
			//echo 'MOVING FILES TO '.$DEFAULT_IMAGE_DIRECTORY.'/'.$image->id; 
			\move_uploaded_file( $_FILES['file']['tmp_name'], $DEFAULT_IMAGE_DIRECTORY.'/'.$image->id );
		}
	}

	$response->setResult( 1 );
	$response->setData( $image->toArray() );
	//$response->setImage( 1 );
	$response->output();
}
catch(\Exception $e)
{
	error_log('Exception it reach here'.$e->getMessage() );
	$response->setData( $e->getMessage() );
	$response->setError( $e );
}
$response->output();
