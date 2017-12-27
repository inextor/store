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

	$product			= new product();
	$product->id		= $_REQUEST['id'];

	if( !$product->load() )
		throw new ValidationException('Product not found');

	$parents	= [];
	$attrs		= [];

	if( !empty( $product->product_type_id ) )
	{
		$product_type		= new product_type();
		$product_type->id	= $product->product_type_id;


		$pav_sql				= 'SELECT * FROM product_attr_value WHERE product_id = '.$product->id;
		$product_attr_values	= DBTable::getArrayFromQuery( $pav_sql );

		if( !$product_type->load() )
			throw new NotFoundException('The product type was not found');

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

		if( $ids !== "" )
		{
			$sqlAttrs	= 'SELECT * FROM product_attr WHERE product_type_id IN ('.$ids.')';
			$attrs		= DBTable::getArrayFromQuery( $sqlAttrs );
		}
	}

	$images		= [];
	$sql_images	= 'SELECT image.*,product_image.order 
		FROM product_image 
		JOIN image ON image.id = product_image.image_id
		WHERE product_image.product_id = "'.DBTable::escape( $_POST['id'] ).'" ORDER BY product_image.order ASC';



	$images_res	 =	DBTable::query( $sql_images );

	while( $row = $images_res->fetch_assoc() )
	{
		$image		= image::createFromArray( $row );
		$images[] 	= $image->toArray();
	}

	$response->setResult( 1 );
	$response->setData
	([
		'product'				=> $product->toArray()
		,'product_type'			=> $product_type !== NULL ? $product_type->toArray() : null
		,'parents'				=> $parents
		,'product_type_attrs'	=> $attrs
		,'product_attr_values'	=> $product_attr_values
		,'images'				=> $images
	]);

	$response->output();
}
catch(\Exception $e)
{
	$response->setError( $e );
}

$response->setResult( 0 );
$response->output();
