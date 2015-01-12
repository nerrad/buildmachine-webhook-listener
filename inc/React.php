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

		//verify we have a valid request
		if ( empty( $this->_request->repository ) ) {
			$msg = 'Invalid package received.';
			syslog( LOG_DEBUG, $msg );
			header( 'HTTP/1.1 400 Bad Request' );
			exit( $msg );
		}

		$has_run = false;
		foreach ( $this->_config->map as $url => $slug ) {
			if ( $url == $this->_request->repository->url ) {
				$this->_trigger_grunt( $slug );
				$has_run = true;
			}
		}
		//message about no support
		if ( $has_run ) {
			$msg = 'The grunt tasks associated with ' . $this->_request->repository->url . ' completed successfully.';
			syslog( LOG_DEBUG, $msg );
			header( 'HTTP/1.1 200 OK' );
			exit( $msg );

		} else {
			$msg = 'There are no grunt tasks associated with ' . $this->_request->repository->url . '.';
			syslog( LOG_DEBUG, $msg );
			header( 'HTTP/1.1 200 OK' );
			exit( $msg );
		}
	}


	protected function _trigger_grunt( $slug ) {
		//if latest commit by EE DevBox server then do NOT run grunt
		$i = 0;
		$output = $output2 = '';

		if ( empty( $this->_request ) || ! isset( $this->_request->commits ) ) {
			$msg = 'No commits to process.  Looks like a bad package.';
			syslog( LOG_DEBUG, $msg );
			header( 'HTTP/1.1 400 Bad Request');
			exit( $msg );
		}

		foreach ( $this->_request->commits as $commit ) {
			//error_log( print_r( $commit, true ) );
			if ( $commit->author->email == $this->_config->server_git_email && $i == 0 ) {
				$msg = 'Most recent commit made by grunt so will not run recursively!';
				syslog( LOG_DEBUG, $msg );
				header( 'HTTP/1.1 202 Accepted');
				exit( $msg );
			}
			$i++;
		}

		//what branch are we going to checkout?
		$ref = str_replace( 'refs/heads/', '', $this->_request->ref );

		$expected_refs = array( 'master', 'beta', 'alpha' );
		if ( ! in_array( $ref, $expected_refs ) ) {
			$msg = 'Grunt is only run on master, alpha or beta branches. The ref in the package does not match one of those branches.';
			syslog( LOG_DEBUG, $msg );
			header( 'HTTP/1.1 202 Accepted');
			exit( $msg );
		}

		$this->_do_grunt( $slug, $ref );
	}


	protected function _do_grunt( $slug, $ref ) {
		$output = $output2 = '';
		$bump_command = 'cd ' . $this->_config->grunt_path . $slug . ' && unset GIT_DIR && grunt bumprc_' . $ref;
		$sandbox_command = 'cd ' . $this->_config->grunt_path . $slug . ' && unset GIT_DIR && grunt updateSandbox_' . $ref;
		exec( $bump_command, $output );
		syslog( LOG_DEBUG, print_r( $output, true ) );

		sleep(3);

		exec( $sandbox_command, $output2 );
		syslog( LOG_DEBUG, print_r( $output2, true ) );
	}


} //end React
