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

try
{

	if( empty( $_POST['name'] ) )
		throw new ValidationException('Name can\'t be empty');

	$product_type		= new product_type();
	$product_type->name = $_POST['name'];
	$product_type->assignFromArray( $_POST );
	//$product_type->unsetEmptyValues();

	//j$product_type->parent_product_type = NULL;
	//j$product_type->parent_product_type = '';

	if( !empty( $_POST['parent_product_type_id'] ) )
		$product_type->parent_product_type_id = $_POST['parent_product_type_id'];

	if( !$product_type->insertDb() )
	{
		throw new SystemException('An error occurred please try again later');
	}

	$response->setResult( 1 );
	$response->setData( $product_type->toArray() );
	$response->output();
}
catch(\Exception $e)
{
	$response->setError( $e );
}

$response->setResult( 0 );
$response->output();
