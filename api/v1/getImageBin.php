<?php

namespace Truck;


include_once(__DIR__.'/app.php');
include_once( __DIR__.'/lib/akou/src/Image.php' );

use AKOU\ApiResponse;
use AKOU\DBTable;
use AKOU\SystemException;
use AKOU\ValidationException;
use AKOU\NotFoundException;
use AKOU\Utils;
use AKOU\ChromePhp;

//error_reporting(E_ALL);
//// Same as error_reporting(E_ALL);
//ini_set('error_reporting', E_ALL);


// 531a3850a3815.jpg <- esa esta en el server

App::init();
$response 	= new ApiResponse();
try
{
	if( empty( $_REQUEST['id']  ) )
		throw new ValidationException('Id can\'t be empty');

	$image		= new image();
	$image->id	= $_REQUEST['id'];

	if( !$image->load() )
		throw new NotFoundException('Image not found');

	$DEFAULT_IMAGE_DIRECTORY	= '/srv/http/domains/127.0.0.6/uimgs';
	$filename					= $DEFAULT_IMAGE_DIRECTORY.'/'.$image->id; 

	if( !\file_exists( dirname( $filename ) ) ) 
	{
		throw new NotFoundException('File not found');
	}


    $width  = isset( $_GET['width'] ) ? $_GET['width'] : '';
    $height = !empty( $_GET['height'] ) ? $_GET['height'] : 0;
    
    if( $width > 0 && $width < 2000 )
    {
        header( 'Cache-Control: max-age=259200'); 
        header( 'Content-type: ' . $image->content_type );

		$img	= new \AKOU\Image();
		$img->loadFromFile( $filename, $image->content_type );
		$img->resize_image( $width, $height );
		$img->outputImage( $image->content_type );
		exit;
    }
    else
    {
        header( 'Cache-Control: max-age=1036800'); 
        header( 'Content-type: ' . $image->content_type);
		if( !empty( $image->size  ) )
		{
        	header( 'Content-Length: ' . $obj_image->file_size );
		}

		echo file_get_contents( $name_file_raw );
		exit;
    }
}
catch(\Exception $e)
{
    
	$response->setData( $e->getMessage() );
	$response->setError( $e );
}

$response->output();



