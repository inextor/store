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

try
{

	if( empty( $_REQUEST['id'] ) )
		throw new ValidationException('Id cant be empty');

	App::init();

	$product_type		= new product_type();
	$product_type->id	= $_REQUEST['id'];

	if( !$product_type->load() )
		throw new NotFoundException('The product type was not found');

	$parents		= [];
	$parents_ids	= [ $product_type->id ];

	$previous		= $product_type;

	while( $previous->parent_product_type_id !== NULL )
	{
		$ppp 		= new product_type();
		$ppp->id	= $previous->parent_product_type_id;

		if( !$ppp->load() )
		{
			throw new SystemException('An error occurred please try again later');
			//??? WHAT TO DO
		}

		$parents[] 		= $ppp->toArray();
		$parents_ids[]	= $ppp->id;
		$previous	= $ppp;
	}

	$ids	= DBTable::escapeArrayValues( $parents_ids );
	$attrs	= [];

	if( $ids !== "" )
	{
		$sqlAttrs	= 'SELECT * FROM product_attr WHERE product_type_id IN ('.$ids.')';
		$attrs		= DBTable::getArrayFromQuery( $sqlAttrs );
	}

	$response->setResult( 1 );
	$response->setData
	([
		'product_type'			=> $product_type->toArray()
		,'parents'				=> $parents
		,'product_type_attrs'	=> $attrs
	]);

	$response->output();
}
catch(\Exception $e)
{
	$response->setError( $e );
}

$response->setResult( 0 );
$response->output();
