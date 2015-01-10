<?php
/**
 * Reacts to what may be in an incoming webhook payload and handles accordingly.
 */
namespace Nerrad\CodebaseWebhook;

use Nerrad\CodebaseWebhook\Http\Request;
use Nerrad\CodebaseWebhook\Config;

class React {

	private $_request;
	private $_config;

	public function __construct( Request $request ) {
		//keeping things simple for the first go.  All we want to do is parse the incoming request and make sure that we have a non EE4server request for triggering grunt.
		ini_set( 'log_errors_max_len', 0 );
		$this->_request = $request->get_all();
		$this->_config = Config::instance();

		switch ( $this->_request->repository->url ) {
			case "https://events.codebasehq.com/projects/event-espresso/repositories/32-core" :
				$this->_trigger_grunt( 'ee_core' );
				break;
			case "https://events.codebasehq.com/projects/event-espresso/repositories/eea-barcode-scanning" :
				$this->_trigger_grunt( 'eea_barcode_scanner' );
				break;

			default :
				echo 'No tasks to run';
				exit();
		}
	}


	protected function _trigger_grunt( $repo ) {
		//if latest commit by EE DevBox server then do NOT run grunt
		$i = 0;

		if ( empty( $this->_request ) || ! isset( $this->_request->commits ) ) {
			echo 'no commits to process';
			exit();
		}

		foreach ( $this->_request->commits as $commit ) {
			//error_log( print_r( $commit, true ) );
			if ( $commit->author->email == 'admin@eventespresso.com'  && $i == 0 ) {
				echo 'Commit likely made by grunt so let\'s not run grunt recursively!';
				exit();
			}
			$i++;
		}

		//what branch are we going to checkout?
		$ref = str_replace( 'refs/heads/', '', $this->_request->ref );

		$expected_refs = array( 'master', 'beta', 'alpha' );
		if ( ! in_array( $ref, $expected_refs ) ) {
			echo 'Grunt is only run on master, alpha or beta branches';
			exit();
		}

		switch ( $repo ) {
			case 'ee_core' :
				//attempt to navigate to grunt folder and run task!
				$script =  'hooks/event-espresso-core/grunt-' . $ref . '-start.sh';
				$this->start_script( $script );
				break;

			case 'eea_barcode_scanner' :
				//attempt to navigate to grunt folder and run task!
				 $output =shell_exec( 'whoami && cd ~/buildmachine/eea-barcode-scanner && grunt bumprc_' . $ref . ' 2>&1' );
				 //let's output to syslog
				 syslog( LOG_DEBUG, print_r( $output, true ) );
				break;

			default :
				throw new \Exception( "The type sent to this webhook is not supported yet" );

		}

		echo 'success!';
		exit();
	}




	protected function start_script( $path ) {
		$curl = curl_init(  $this->_config->baseurl . $path );
		curl_setopt( $curl, CURLOPT_HTTPAUTH, CURLAUTH_BASIC );
		curl_setopt_array( $curl, array(
			CURLOPT_RETURNTRANSFER => 1,
			CURLOPT_CONNECTTIMEOUT => 60,
			CURLOPT_TIMEOUT => 120,
			CURLOPT_USERPWD => $this->_config->username . ':' . $this->_config->password
			));
		$curl_response = curl_exec( $curl );
		curl_close( $curl );
		syslog( LOG_DEBUG, $curl_response );
		exit();
	}


} //end React
