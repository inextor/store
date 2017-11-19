<?php

namespace Truck;

include_once(__DIR__.'/app.php');

use akou\ApiResponse;
use akou\DBTable;
use akou\SystemException;
use akou\ValidationException;
use akou\Utils;
use akou\ChromePhp;

$response 	= new ApiResponse();

try
{
	App::init();

	$data	= DBTable::getArrayFromQuery('Select * from product_type ORDER BY name ASC');

	$byKey	= array();

	foreach( $data as $i=>$product_type )
	{
		$byKey[ $product_type['id'] ] = $product_type;
	}


	foreach($data as $i=>$product_type)
	{
		$parent_category = $product_type['parent_product_type_id'];
		$path			 = '';

		while($parent_category != NULL )
		{
			if( $path == '' )
				$path	= $byKey[ $parent_category ]['name'];
			else
				$path	= $byKey[ $parent_category ]['name'].'->'.$path;

			$parent_category = $byKey[ $parent_category ]['parent_product_type_id'];
		}

		$data[$i]['path'] = $path == '' ? $data[ $i ]['name'] : $path.'->'.$data[ $i ]['name'];
	}


	$response->setResult( 1 );
	$response->setData( $data );
	$response->output();
}
catch(Exception $e)
{
	$response->setError( $e );
}
$response->setResult( 0 );
$response->output();
