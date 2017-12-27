<?php

namespace Truck;

include_once(__DIR__.'/app.php');

use AKOU\ApiResponse;
use AKOU\DBTable;
use AKOU\SystemException;
use AKOU\ValidationException;
use AKOU\Utils;
use AKOU\ChromePhp;
use AKOU\NotFoundException;

$response 	= new ApiResponse();

App::init();
DBTable::autocommit( FALSE );

try
{

	if( empty( $_POST['product_type_id'] ) )
		throw new ValidationException('product type id cant be empty');

	if( !is_array( $_POST['product_attrs'] ) )
	{
		throw new ValidationException('Error bad formed request');
	}

	$product_type		= new product_type();
	$product_type->id	= $_POST['product_type_id'];

	if( !$product_type->load() )
		throw new NotFoundException('The product type was not found');

	$newAttrs	= [];

	foreach( $_POST['product_attrs'] as $i=>$values )
	{
		$product_attr = new product_attr();
		$product_attr->assignFromArray( $values,'name','values_description');
		$product_attr->product_type_id	= $_POST['product_type_id'];

		if( ! $product_attr->insertDb() )
		{
			//If duplicate throw 409 Maybe
			//https://stackoverflow.com/questions/3290182/rest-http-status-codes-for-failed-validation-or-invalid-duplicate
			throw new ValidationException('Error on inserting the attribute "'.$product_attr->name.'"');
		}

		$newAttrs[] = $product_attr->toArray();
	}

	DBTable::commit();
	$response->setResult(1);
	$response->setData( $newAttrs );
	$response->output();
}
catch(\Exception $e)
{
	$response->setError( $e );
}
DBTable::rollback();

$response->setResult( 0 );
$response->output();
