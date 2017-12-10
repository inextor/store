<?php

namespace Truck;

include_once(__DIR__.'/app.php');

use AKOU\ApiResponse;
use AKOU\DBTable;
use AKOU\SystemException;
use AKOU\ValidationException;
use AKOU\Utils;
use AKOU\ChromePhp;

$response 	= new ApiResponse();

App::init();
DBTable::autocommit( FALSE );

//error_log('Registered Here, wtf???');
try
{


	$product			= new product();
	$product->assignFromArray( $_POST['product'] );

	if( empty( $product->name ) )
		throw new ValidationException('Name can\'t be empty');

	if( empty( $product->qty ) )
		throw new ValidationException('Qty can\'t be empty');

	if( empty( $product->price ) )
		throw new ValidationException('price can\'t be empty');

	if( !$product->insertDb() )
	{
		error_log( DBTable::$connection->error );
		throw new SystemException('An error occurred while saving the product data, please try again later');
	}

	$product_attrs	= array();

	//error_log('NAAA Here, wtf???');
	foreach( $_POST['product_attr_values'] as $i=>$values )
	{
		$product_attr_value				= new product_attr_value();
		$product_attr_value->product_id	= $product->id;
		$product_attr_value->assignFromArray( $values, 'product_attr_id','values' ); 

		if( !$product_attr_value->insertDb() )
		{
			throw new SystemException('An error occurred while saving the product info, please try again later');
		}

		$product_attrs[]				= $product_attr_value->toArray();
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

$response->setResult( 0 );
$response->output();
