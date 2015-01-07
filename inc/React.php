<?php
/**
 * Reacts to what may be in an incoming webhook payload and handles accordingly.
 */
namespace Nerrad\CodebaseWebhook;

use Nerrad\CodebaseWebhook\Http\Request;

class React {

	private $_request;

	public function __construct( Request $request ) {
		//keeping things simple for the first go.  All we want to do is parse the incoming request and make sure that we have a non EE4server request for triggering grunt.

		$this->_request = $request->get_all();

		switch ( $request->repository->url ) {
			case "https://events.codebasehq.com/event-espresso/32-core.git" :
				$this->_trigger_grunt( 'ee_core' );
				break;

			default :
				echo 'No tasks to run';
				exit();
		}
	}


	protected function _trigger_grunt( $repo ) {
		//if latest commit by EE DevBox server then do NOT run grunt
		$i = 0;
		foreach ( $this->_request->commits as $commit ) {
			if ( $commit->author->name == 'EE Devbox Server'  && $i = 0 ) {
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
				$output =shell_exec( 'cd ~/buildmachine/event-espresso-core && grunt testingbumprc_' . $ref );
				break;

			default :
				throw new \Exception( "The type sent to this webhook is not supported yet" );

		}
	}


} //end React
