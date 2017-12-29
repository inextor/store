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

try
{

	if( empty( $_POST['id'] ) )
		throw new ValidationException('id can\'t be empty');

	DBTable::autocommit( FALSE );

	$product_type		= new product_type();
	$product_type->id	= $_POST['id'];

	if( ! $product_type->load() )
		throw new NotFoundException('Product type was not found'.$product_type->getLastQuery);

	$product_type->assignFromArray( $_POST, 'name' );

	if( !$product_type->updateDb() )
	{
		throw new SystemException('An exception occurred please try again later');
	}

	foreach( $_POST['attributes'] as $i=>$values )
	{
		if( empty( $values['id'] ) )
			throw new ValidationException('Product attr ids can\'t be empty');

		$product_attr		= new product_attr();
		$product_attr->id	= $values['id'];
		if( !$product_attr->load() )
			throw new ValidationException('Product attr with id : "'.$values['id'].'" was not found ');

		$product_attr->assignFromArray( $values, 'id','name','values_description' );

		if( !$product_attr->updateDb() )
			throw new ValidationException();

	}

	DBTable::commit();

	$response->setResult( 1 );
	$response->setData( 'Success' );
	$response->output();
}
catch(\Exception $e)
{
	$response->setError( $e );
}
DBTable::rollback();

$response->setResult( 0 );
$response->output();
