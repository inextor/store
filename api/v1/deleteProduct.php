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

	if( empty( $_POST['id'] ) )
		throw new ValidationException('Product id can\'t be empty');


	$product		= new product();
	$product->id	= $_POST['id'];

	if( !$product->load() )
		throw new NotFoundException('Product Not Found');

	$product->status	= 'DELETED';
	if( !$product->updateDb() )
		throw new SystemException('An error occourred please try again later');

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
	//error_log('Exception it reach here'.$e->getMessage() );
	//Crome::log('Error occurred',$e->getMessage() );
	$response->setData( $e->getMessage() );
	$response->setError( $e );
}

DBTable::rollback();
$response->setResult( 0 );
$response->output();
