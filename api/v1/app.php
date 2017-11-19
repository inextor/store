<?php

namespace Truck;

include_once( __DIR__.'/lib/akou/src/DBTable.php' );
include_once( __DIR__.'/lib/akou/src/LoggableException.php' );
include_once( __DIR__.'/lib/akou/src/ApiResponse.php' );
include_once( __DIR__.'/lib/akou/src/Utils.php' );
include_once( __DIR__.'/lib/akou/src/ChromePhp.php' );

use akou\ApiResponse;
use akou\DBTable;
use akou\SystemException;
use akou\ValidationException;
use akou\Utils;

//include_once( __DIR__.'/schema.php' );

class App
{
	// create function to connect to database
	public static function init()
	{

		ini_set('error_reporting', 1);
		ini_set('display_errors', E_ALL);
		// ---------------- Some constants (START) ----------------------

		Utils::$DEBUG	= false;

		/* Local DETAIL*/
		if( Utils::isDebugEnviroment() )
		{
			/* Local DETAIL*/
			DBTable::init('127.0.0.1','root','new-password','trucks');
		}
		else
		{
			DBTable::init('localhost','idoegqdo_trucks','trucksRocks1','idoegqdo_trucks');
		}

		DBTable::importDbSchema('Truck');
	}

	public static function getRandomString($length = 10)
	{
		$characters = '0123456789abcdefghijklmnopqrstuvwxyz';
		$string = '';
		for ($p = 0; $p < $length; $p++)
		{
			$string .= $characters[mt_rand(0, strlen($characters))];
		}
		return $string;
	}

	public static function getNewSessionSecret( $user )
	{
		$session						= new session();
		$session->user_id	= $user->id;
		$session->user_agent = isset($_SERVER['HTTP_USER_AGENT']) ? $_SERVER['HTTP_USER_AGENT']:'';

		$counter = 5;
		$result = false;

		while(!$result && ($counter--))
		{
			$session->secret = app::getRandomString( 20 );
			$result			= $session->insertDb();
		}

		if( !$result )
			return FALSE;

		return $session->secret;
	}


	public static function getPasswordHash( $password, $timestamp	)
	{
		$thehash = sha1( $timestamp.'2doez'.$password );
		return $thehash;
	}

	public static function getUserBySessionSecret(	$hash	)
	{
		$user = null;
		$sql_user = 'SELECT user.* FROM session
					JOIN user ON session.user_id = user.id
					WHERE secret= "'.$hash.'" LIMIT 1';

		$result =	DBTable::query(	$sql_user	);
		while(	$result && $row = 	$result->fetch_assoc()	)
		{
			$user = user::createFromArray( $row );
		}
		return $user;
	}

	public static function userToArray( $user )
	{
		return $user->toArrayExclude('password');
	}
}
