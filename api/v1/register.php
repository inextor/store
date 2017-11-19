<?php

	include('app.php');
	//Add Validation
try
{

	$user = new user();
	$user->assign_from_array( $_POST,true);
	$user->created = date('Y-m-d H:i:s');
	$user->password = App::getPasswordHash($_POST['password'],$user->created);

	DBTable::autocommit( FALSE );

	if($user->insertDb())
	{
		$user->id				= mysql_insert_id();
		$project				= new project();
		$project->owner_user_id = $user->id;
		$project->name			= 'Personal';
		$project->type			= 'PERSONAL_LIST';
		if($project->insertDb())
		{
			$user_project				= new user_project();
			$user_project->user_id	 = $project->owner_user_id;
			$user_project->project_id	= mysql_insert_id();
			$user_project->insertDb();
		}
		addMysqlError();

		/* INSERTAR INVITACIONES NUEVAS	*/
		$sql_invitations = 'SELECT *
							FROM new_user_invitation
							WHERE email = "'.$user->email.'"';

		$invitations_result = DBTable::query( $sql_invitations );
		addMysqlError();
		if( $invitations_result )
		{
			while( $invitation =	$invitations_result ->fetch_object())
			{
				$rui			 = new registered_user_invitation();
				$rui->project_id = $invitation->project_id;
				$rui->user_id	= $user->id;
				if(!$rui->insertDb() )
				{
					addMysqlError();
				}
			}
		}
		/* TERMINAR INSERTAR INVITACIONES NUEVAS	*/

		$user			= new user();
		$user->id		= $project->owner_user_id;
		$user->load();
		addMysqlError();
		$session_secret = App::getNewSessionSecret( $user );

		DBTable::commit();
		$respone->setResult( 1 );
		$response->setData( 'session_secret'=>$session_secret));
		$response->output();
	}

	$err = DBTable::$connection->connect_errno;
	addMysqlError();
	DBTable::rollback();

	if( $err == 1062 )
	{
		throw new ValidationException('User already exist');
	}

	throw new SystemException('An error occour '.$err));
}
catch($e)
{
	$response->setError( $e );
}
$response->output();
