<?php
namespace Nerrad\BuildMachine\WebHookListener;

/**
 * Config
 * Wimply contains the default configuration class for the app.  Note you will need to add all your configuration
 * details in the constructor of this class.
 *
 * @package Nerrad\BuildMachine\WebHookListener
 * @author  Darren Ethier
 * @since   1.0.0
 */
class Config
{
    public $access_token = '';
    public $map = array();
    public $grunt_path = '';
    public $grunt_src = '';
    public $server_git_email = '';


    /**
     * Variables are set via app-config.php which is already loaded.
     * Config constructor.
     */
    public function __construct()
    {
        include CB_WEBHOOK_BASE_PATH . 'app-config.php';
        $this->access_token = isset($access_token) ? $access_token : '';
        $this->map = isset($map_file) ? $this->generateMapFromMapSourceFile($map_file) : array();
        $this->grunt_path = isset($grunt_path) ? $grunt_path : '';
        $this->grunt_src_path = isset($grunt_src_path) ? $grunt_src_path : '';
        $this->server_git_email = isset($server_git_email) ? $server_git_email : '';
    }

    /**
     * Generates map from the given path
     *
     * @param string $map_source_file_path
     * @return array
     */
    private function generateMapFromMapSourceFile($map_source_file_path)
    {
        $file_contents = file_get_contents($map_source_file_path);
        return json_decode($file_contents, true);
    }
}
