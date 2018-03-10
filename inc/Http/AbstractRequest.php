<?php
namespace Nerrad\BuildMachine\WebHookListener\Http;

abstract class AbstractRequest implements RequestInterface
{
    /**
     * Holds the incoming request
     * @var array
     */
    private $request;

    public function __construct(array $request)
    {
        $this->request = $request;
    }


    /**
     * @return array
     */
    protected function request()
    {
        return $this->request;
    }


    /**
     * Security token.
     * @return string
     */
    public function token()
    {
        return isset($this->_request['token'])
            ? $this->_request['token']
            : '';
    }
}