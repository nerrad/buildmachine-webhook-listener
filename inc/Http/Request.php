<?php
namespace Nerrad\BuildMachine\WebHookListener\Http;

use RuntimeException;

/**
 * Request
 * Request class for simply receiving the requests
 *
 * @package Nerrad\BuildMachine\WebHookListener\Http
 * @author  Darren Ethier
 * @since   1.0.0
 */
class Request
{

    private $request;


    /**
     * Request constructor.
     *
     * @param $request
     * @throws RuntimeException
     */
    public function __construct($request)
    {
        if (! isset($request['payload'])) {
            throw new RuntimeException('Incoming request does not have "payload" param');
        }
        $this->request = is_array($request['payload'])
            ? json_decode(json_encode($request['payload']))
            : json_decode($request['payload']);
    }


    public function getAll()
    {
        return $this->request;
    }


    public function get($var)
    {
        return isset($this->request->{$var}) ? $this->request->{$var} : null;
    }
}
