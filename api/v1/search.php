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
	$limit	= 30;
	$offset	= 0;

	if( !empty( $_REQUEST['limit'] ) && filter_var( $_REQUTES['limit'], FILTER_VALIDATE_INT) !== false )
	{
		$limit	= intval( $_REQUEST['limit'] );

		if( $limit > 50  && $limit > 0 )
			$limit = 50;
	}

	if( !empty( $_REQUEST['offset'] ) && filter_var( $_REQUTES['offset'], FILTER_VALIDATE_INT) !== false )
	{
		$offset	= intval( $_REQUEST['offset'] )*$limit;

		if( $offset < 0 )
			$offset	= 0;
	}

	
	App::init();

	$constraints	= array();

	if( !empty( $_REQUEST['search'] ) )
	{
		$constraints[]  =	' name LIKE "%'.DBTable::escape($_POST['search']).'%"';
	}

	if( !empty( $_REQUEST['ids'] ) )
	{
		$constraints[]	=  ' id IN ('.DBTable::escapeArrayValues( $_POST['ids'] ).') ';
	}

	if( count( $constraints ) == 0)
	{
		$constraints[]	= "1";
	}

	$sql_constraints	= implode(' AND ',$constraints );


	$sqlSearch				= 'SELECT  SQL_CALC_FOUND_ROWS * 
			FROM product 
			WHERE  '.$sql_constraints.'
			LIMIT '.$limit.' 
			OFFSET '.$offset;

	ChromePhp::log( 'SQL is ',$sqlSearch );

	$products				= DBTable::getArrayFromQuery( $sqlSearch );
	$totalRes				= DBTable::query('SELECT FOUND_ROWS()');
	$total					= count( $products );

	if( $total > 0 && $totalRes !== FALSE )
	{
		$row	= $totalRes->fetch_row();
		if( $row )
			$total	= $row[0];
	}

	$product_ids			= array_keys( $products );
	$product_attr_values	= array();
	$product_attrs			= array();

	if( count( $product_ids ) > 0 )
	{
		$pav_sql				= 'SELECT * FROM product_attr_value WHERE product_id IN ('.DBTable::escapeArrayValues( $product_ids ).')';
		$product_attr_values	= DBTable::getArrayFromQuery( $pav_sql, 'id' );
		$pa_ids					= array_keys( $pavs );

		if( count( $pa_ids ) > 0 )
		{
			$pa_sql			= 'SELECT * FROM product_attr WHERE id IN('.DBTable::escape( $pa_ids ).')';
			$product_attrs	= DBTable::query( $pa_sql, 'id' );
		}
	}

	$response->setResult( 1 );
	$response->setData
	([
		'products'				=> array_values( $products )
		,'product_attr_values'	=> array_values( $product_attr_values )
		,'product_attrs'		=> array_values( $product_attrs )
		,'total'				=> $total
	]);

	$response->output();
}
catch(\Exception $e)
{
	$response->setError( $e );
}

$response->setResult( 0 );
$response->output();
