<?php
/**
 * Request class for simply receiving the requests
**/
namespace Nerrad\CodebaseWebhook\Http;

class Request {

	private $_req;


	public function __construct(  $request ) {
		if ( ! isset( $request['payload'] ) ) {
			throw new \Exception( 'Incoming request does not have "payload" param' );
		}
		$this->_req = is_array( $request['payload'] ) ?  json_decode( json_encode( $request['payload'] ) ) : json_decode( $request['payload'] );
	}


	public function get_all() {
		return $this->_req;
	}


	public function get( $var ) {
		return isset( $this->_req->$var) ? $this->_req->$var : null;
	}
}
