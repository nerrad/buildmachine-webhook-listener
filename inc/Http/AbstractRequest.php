<?php
namespace Nerrad\BuildMachine\WebHookListener\Http;

abstract class AbstractRequest implements RequestInterface
{
    /**
     * Holds the incoming request
     * @var array
     */
    private $request;


    /**
     * Incoming content type for request.
     * @var string
     */
    private $content_type;

    public function __construct(array $request)
    {
        $this->request = $request;
        $this->content_type = isset($_SERVER['CONTENT_TYPE']) ? trim($_SERVER['CONTENT_TYPE']) : '';
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
        return isset($this->request['token'])
            ? $this->request['token']
            : '';
    }


    /**
     * Determine if this request has a json body.
     *
     */
    public function isJson()
    {
        return strcasecmp($this->content_type, 'application/json') === 0;
    }
}