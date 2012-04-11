<?php if (!defined('FLUX_ROOT')) exit; 
require_once("facebook.php");
include 'FBRegistration.php';
$reg_tbl = Flux::config('FluxTables.REGISTER');
$api_id = Flux::config('FB_API_KEY');
$api_secret = Flux::config('FB_API_SECRET_KEY');
$loginDB = $server->loginDatabase;	
$serverNames = $this->getServerNames();
$error = "";
$submit = $params->get('fb_return');


 $fb_config = array();
 $fb_config['appId'] = $api_id;
 $fb_config['secret'] = $api_secret;
 $fb_config['fileUpload'] = false; // optional
 $fb_config['cookie'] = true; // optional
  
 $fb = new Facebook($fb_config); 

 $loginURL = $fb->getLoginUrl(array( 'scope' => 'email'));
 try
 {
	if(!$fb->getUser())
	{
		header("Location: ".$loginURL);
	}
	else
	{
		try
		{
			$token  = $fb->getAccessToken();
			$fb->setAccessToken($token);
			$resp =  $fb->api('/me','GET');
			if(!$resp['verified'])
			{
				throw new Exception("Email Address not verified.");
			}
		}
		catch(FacebookException $e)
		{
			try
			{
				throw new Exception($e->getMessage());
			}
			catch(Exception $e)
			{				
				$error = $e->getMessage();
			}
		}
	 }
}
catch(Exception $e)
{
 $error = $e->getMessage();
}




if(!empty($submit) && empty($error))
{
	try
	{	
		if ($_REQUEST) {
		  $response = parse_signed_request($_REQUEST['signed_request'],Flux::config('FB_API_SECRET_KEY'));
		  $info = $response['registration'];
		} else {
			throw new Exception("No Request Found.");
		}

		$servername = $info['server'];
		$username 	= $info['username'];
		$password 	= $info['password'];
		$email 		= $info['email'];
		$birthdate 	= $info['birthday'];
		$gender 	= $info['gender'];

		if(strtolower($gender) == "male")
			$gender = "M";
		if(strtolower($gender) == "female")
			$gender = "F";
		
		
		
		if (preg_match('/[^' . Flux::config('UsernameAllowedChars') . ']/', $username)) {
			throw new Exception('Invalid character(s) used in username');
		}
		elseif (strlen($username) < Flux::config('MinUsernameLength')) {
			throw new Exception('Username is too short');
		}
		elseif (strlen($username) > Flux::config('MaxUsernameLength')) {
			throw new Exception('Username is too long');
		}
		elseif (strlen($password) < Flux::config('MinPasswordLength')) {
			throw new Exception('Password is too short');
		}
		elseif (strlen($password) > Flux::config('MaxPasswordLength')) {
			throw new Exception('Password is too long');
		}
		elseif (!in_array(strtoupper($gender), array('M', 'F'))) {
			throw new Exception('Invalid gender');
		}					
			
			$sql  = "SELECT userid FROM {$loginDB}.login WHERE ";
			if (Flux::config('NoCase')) {
				$sql .= 'LOWER(userid) = LOWER(?) ';
			}
			else {
				$sql .= 'BINARY userid = ? ';
			}
			$sql .= 'LIMIT 1';
			$sth  = $server->connection->getStatement($sql);
			$sth->execute(array($username));
			$res = $sth->fetch();
			
			
			if ($res) {
				throw new Exception('Username is already taken');
			}
		

			if (!Flux::config('AllowDuplicateEmails')) {
				$sql = "SELECT email FROM {$loginDB}.login WHERE email = ? LIMIT 1";
				$sth = $server->connection->getStatement($sql);
				$sth->execute(array($email));
				$res = $sth->fetch();
				if ($res) {
					throw new Exception('E-mail address is already in use');
				}
			}
			
			if (Flux::config('UseMD5')) {
				$password = Flux::hashPassword($password);
			}
			$sql = "INSERT INTO {$loginDB}.login (userid, user_pass, email, sex, group_id, birthdate) VALUES (?, ?, ?, ?, ?, ?)";
			$sth = $server->connection->getStatement($sql);
			$res = $sth->execute(array($username, $password, $email, $gender, (int)Flux::config('level'),$birthdate));

			if ($res) {
				$idsth = $server->connection->getStatement("SELECT LAST_INSERT_ID() AS account_id");
				$idsth->execute();
				
				$idres = $idsth->fetch();
				$account_id = $idres->account_id;
				
				$sql  = "INSERT INTO {$loginDB}.{$reg_tbl} (fb_userid,account_id,username,password,ip_address) ";
				$sql .= "VALUES (?,?,?,?,?)";
				$sth  = $server->connection->getStatement($sql);				
				$sth->execute(array($response['user_id'],$idres->account_id, $username, $password,$_SERVER['REMOTE_ADDR']));
			}
			else {
				throw new Exception("Error in recording the facebook account in the database. Kindly inform the administrator about this error.");
			}
			$error = "You have successfully registered.";
			$this->redirect('?module=account&action=login');
	}
	catch(Exception $e)
	{
		$error = $e->getMessage();
	}
}
?>