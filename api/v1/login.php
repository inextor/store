<?php

namespace TodoEZ;

include_once('app.php');

use akou\ApiResponse;
use akou\DBTable;
use akou\SystemException;
use akou\ValidationException;

$response	= new ApiResponse();

try
{

	if( empty( $_POST['email'] ) )
		throw new ValidationException('Email can\'t be empty');

	App::init();

	$user = new user();
	$user->email = trim($_POST['email']);

	if( !$user->load() )
	{
		error_log( $user->email.$user->getLastQuery() );
		throw new ValidationException('The username or password you entered is incorrect.',$sql_user);
	}


	$encripted_password = App::getPasswordHash( $_POST['password'], $user->created);

	if( $user->password == $encripted_password	)
	{
		$user_info	= App::getUserInfo( $user );
		$session_secret= App::getNewSessionSecret( $user );

		if( !$session_secret)
		{
			throw new SystemException('Ocurrio un error inesperado');
		}

		$response->setResult( 1 );

		$response->setData
		([
			'user_info'=>$user_info
			,'session_secret'=>$session_secret
		]);

		$response->output();
	}

	$password1 = App::getPasswordHash( $user->password, $user->created );
	$password2 = App::getPasswordHash( $_POST['password'], $user->created );

//	echo $_POST['password']."\n";
 // echo $user->user_created."\n";

	throw new ValidationException('The username or password you entered is incorrect.	'.$password1.'	'.$password2);

}
catch(\Exception $e)
{
	$response->setError( $e );
}

$response->output();
