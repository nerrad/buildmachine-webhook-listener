<?php
namespace Nerrad\BuildMachine\WebHookListener\Http;

use stdClass;

/**
 * CodebaseRequest
 * CodebaseRequest class for simply receiving the requests
 *
 * @package Nerrad\BuildMachine\WebHookListener\Http
 * @author  Darren Ethier
 * @since   1.0.0
 */
class CodebaseRequest extends AbstractRequest
{
    /**
     * Created from json.
     * @var stdClass
     */
    private $payload;

    /**
     * CodebaseRequest constructor.
     *
     * @param array $request
     */
    public function __construct(array $request)
    {
        parent::__construct($request);
        $this->setPayload();
    }

    /**
     *
     */
    private function setPayload()
    {
        $request = $this->request();
        if (! empty($request['payload'])) {
            $this->payload = is_array($request['payload'])
                ? json_decode(json_encode($request['payload']))
                : json_decode($request['payload']);
        }
    }

    public function isValid()
    {
        return $this->payload !== null
            && $this->cloneUrl()
            && $this->url()
            && $this->mostRecentCommitAuthorEmail()
            && $this->branch();
    }

    /**
     * @return string
     */
    public function cloneUrl()
    {
        return isset($this->payload->repository, $this->payload->repository->clone_url)
            ? $this->payload->repository->clone_url
            : '';
    }

    /**
     * The url to the web page for the repository.
     *
     * @return string
     */
    public function url()
    {
        return isset($this->payload->repository, $this->payload->repository->url)
            ? $this->payload->repository->url
            : '';
    }

    /**
     * Returns the author email for the most recent commit.
     *
     * @return string
     */
    public function mostRecentCommitAuthorEmail()
    {
       if (isset($this->payload->commits)) {
           $commit = reset($this->payload->commits);
           if (isset($commit->author, $commit->author->email)) {
               return $commit->author->email;
           }
       }
       return '';
    }

    /**
     * The branch represented by the commit in the request.
     *
     * @return string
     */
    public function branch()
    {
        return isset($this->payload->ref)
            ? str_replace('refs/heads/', '', $this->payload->ref)
            : '';
    }
}
