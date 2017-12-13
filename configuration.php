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
	public $map = array();
	public $grunt_path = '';
	public $grunt_src = '';
	public $server_git_email = '';

	public function instance()
    {
		if ( ! self::$_instance instanceof Config ) {
			self::$_instance = new self();
		}
		return self::$_instance;
	}


    /**
     * Variables are set via app-config.php which is already loaded.
     *
     * Config constructor.
     */
	private function __construct()
    {
		include 'app-config.php';
		$this->username = $username;
		$this->password = $password;
		$this->baseurl = $baseurl;
		$this->map = $this->generateMapFromMapSourceFile($map_file);
		$this->grunt_path = $grunt_path;
		$this->grunt_src_path = $grunt_src_path;
		$this->server_git_email = $server_git_email;
	}

    /**
     * Generates map from the given path
     * @param string $map_source_file_path
     * @return array
     */
	private function generateMapFromMapSourceFile($map_source_file_path)
    {
        $file_contents = file_get_contents($map_source_file_path);
        return json_decode($file_contents, true);
    }
}
