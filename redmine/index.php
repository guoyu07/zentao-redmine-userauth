<?php

/* Set the error reporting. */
error_reporting(0);

//include('Logger.php');
//$logger = Logger::getInstance();  
//$logger->debug(var_export($_SERVER, true)); 

/* Start output buffer. */
ob_start();

/* Load the framework. */
include '../../framework/router.class.php';
include '../../framework/control.class.php';
include '../../framework/model.class.php';
include '../../framework/helper.class.php';

/* Log the time and define the run mode. */
$startTime = getTime();

class RedmineUser {
   public $login;
   public $firstname;
   public $lastname = '';
   public $mail;
}

class RedmineCurrent {
	public $user;
	
	public function __construct($login, $firstname = '', $mail = ''){
		$this->user = new RedmineUser;
		
		$this->user->login = $login;
		$this->user->firstname = $firstname;
		$this->user->mail = $mail;
	}
}

class user extends control
{
	
	public function login() {
        
		if((isset($_SERVER['PHP_AUTH_USER']) and isset($_SERVER['PHP_AUTH_PW']))) {
            $account  = trim($_SERVER['PHP_AUTH_USER']);
            $password = $_SERVER['PHP_AUTH_PW'];
		} elseif ((isset($_REQUEST['PHP_AUTH_USER']) and isset($_REQUEST['PHP_AUTH_PW']))) {
			$account  = trim($_REQUEST['PHP_AUTH_USER']);
            $password = $_REQUEST['PHP_AUTH_PW'];
		} else {
			die(helper::removeUTF8Bom(json_encode(new RedmineCurrent('')))));
		}

            if($this->user->checkLocked($account))
            {
                $failReason = sprintf($this->lang->user->loginLocked, $this->config->user->lockMinutes);
                die(helper::removeUTF8Bom(json_encode(new RedmineCurrent('')))));
            }
            
            $user = $this->user->identify($account, $password);

            if($user)
            {
                $this->user->cleanLocked($account);
				
                $data = $this->user->getDataInJSON($user);
                
                die(helper::removeUTF8Bom(json_encode(new RedmineCurrent($user->account, $user->realname, $user->email))));
            }
            else
            {
                $fails = $this->user->failPlus($account);
                
                die(helper::removeUTF8Bom(json_encode(new RedmineCurrent('')))));
                
            }
        
	}
}

/* Instance the app. */
$app = router::createApp('pms', dirname(dirname(dirname(__FILE__))));

/* Run the app. */
$app->setModuleName('user');

/* Call the method. */
$module = new user();
$module->login();

/* Flush the buffer. */
echo helper::removeUTF8Bom(ob_get_clean());
