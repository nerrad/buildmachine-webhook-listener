<?php
/**
 * simply contains the default configuration class for the app.  Note you will need to add all your configuration details in the constructor of this class.
 */
namespace Nerrad\CodebaseWebhook;

class Config {
	private static $_instance = NULL;
	public $username = '';
	public $password = '';
	public $baseurl = '';

	public function instance() {
		if ( ! self::$_instance instanceof Config ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}


	private function __construct() {
		include 'app-config.php';
		$this->username = $username;
		$this->password = $password;
		$this->baseurl = $baseurl;
	}
}
