<?php

namespace Truck;

include_once(__DIR__.'/app.php');

use AKOU\ApiResponse;
use AKOU\DBTable;
use AKOU\SystemException;
use AKOU\ValidationException;
use AKOU\NotFoundException;
use AKOU\Utils;
use AKOU\ChromePhp;

$response 	= new ApiResponse();

App::init();
DBTable::autocommit( FALSE );

//error_log('Registered Here, wtf???');
try
{

	if( empty( $_POST['product'] ) )
		throw new ValidationException('Product can\'t be empty');

	if( empty( $_POST['product']['id'] ) )
		throw new ValidationException('Product id can\'t be empty');


	$product		= new product();
	$product->id	= $_POST['product']['id'];

	if( !$product->load() )
		throw new NotFoundException('Product Not Found');

	$product->assignFromArray( $_POST['product'] );

	if( empty( $product->name ) )
		throw new ValidationException('Name can\'t be empty');

	if( empty( $product->qty ) )
		throw new ValidationException('Qty can\'t be empty');

	if( empty( $product->price ) )
		throw new ValidationException('price can\'t be empty');

	
	if( !$product->updateDb() )
	{
		error_log( 'LAST QUERY '.$product->getLastQuery() );
		error_log( DBTable::$connection->error );
		ChromePhp::log( 'Last Sql was ',$product->getLastQuery() );
		throw new SystemException('An error occurred while saving the product data, please try again later');
	}

	$product_attrs	= array();
	$deleteQuery	= 'DELETE FROM product_attr_value WHERE product_id="'.DBTable::escape($product->id ).'"';
	DBTable::query( $deleteQuery );
	ChromePhp::log( 'DELETE QUERY IS ', $deleteQuery );

	//error_log('NAAA Here, wtf???');
	foreach( $_POST['product_attr_values'] as $i=>$values )
	{
		$product_attr_value				= new product_attr_value();
		$product_attr_value->product_id	= $product->id;
		$product_attr_value->assignFromArray( $values, 'product_attr_id','values' ); 

		if( !empty( $product_attr_value->values ) && $product_attr_value->values !== '' )
		{
			if( !$product_attr_value->insertDb() )
			{
				throw new SystemException('An error occurred while saving the product info, please try again later');
			}

			$product_attrs[]				= $product_attr_value->toArray();
		}
	}


	if( !empty( $_POST['images_ids'] ) )
	{
		$deleteQuery	= 'DELETE FROM product_image WHERE product_id="'.DBTable::escape($product->id ).'"';
		DBTable::query( $deleteQuery );
		ChromePhp::log( 'DELETE QUERY IS ', $deleteQuery );

		foreach( $_POST['images_ids'] as $i=>$image_id )
		{
			$image = new image();
			$image->id = $image_id;
			if( !$image->load() )
			{
				throw new ValidationException('Image with id"'.$image_id.' Does not exists');
			}
			$product_image =  new product_image();
			$product_image->image_id	= $image_id;
			$product_image->product_id	= $product->id;
			$product_image->order		= $i;

			if( !$product_image->insertDb() )
				throw new SystemException('An error occurred while saving the images, please try again later');
		}
	}

	$response->setResult( 1 );
	$response->setData(array
	(
		'product'				=> $product->toArray()
		,'product_attr_values'	=> $product_attrs
	));
	DBTable::commit();
	$response->output();
}
catch(\Exception $e)
{
	error_log('Exception it reach here'.$e->getMessage() );
	$response->setData( $e->getMessage() );
	$response->setError( $e );
}

DBTable::rollback();
$response->setResult( 0 );
$response->output();
