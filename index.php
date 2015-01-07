<?php
/**
 * This is a simple webhook for processing incoming http posts from codebase gith webhooks and triggering actions.
 * Note that you should really ensure that this hook is protected by basic authentication (which you can add to the
 * codebase settings) otherwise there is no protection against running this.
 */

namespace Nerrad\CodebaseWebhook;
require 'vendor/autoload.php';

use Nerrad\Codebase\Http\Request;

define( 'CB_WEBHOOK_BASE_PATH', dirname( __FILE__ ) );

//grab request and assing to React class.
$request = new Request( $_REQUEST );

//react
try {
	$react = new React( $request );
} catch ( \Exception $e ) {
	$msg = $e->getMessage();
	header( $msg, true, 501 );
	exit();
}
