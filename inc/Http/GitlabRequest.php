<?php
namespace Nerrad\BuildMachine\WebHookListener\Http;

use Exception;
use stdClass;

/**
 * GitlabRequest
 * GitlabRequest class for simply receiving the requests
 *
 * @package Nerrad\BuildMachine\WebHookListener\Http
 * @author  Darren Ethier
 * @since   1.0.0
 */
class GitlabRequest extends AbstractRequest
{
    /**
     * Created from json.
     * @var stdClass
     */
    private $payload;

    /**
     * GithubRequest constructor.
     *
     * @param array $request
     * @throws Exception
     */
    public function __construct(array $request)
    {
        parent::__construct($request);
        $this->setPayload();
    }


    /**
     * @throws Exception
     */
    private function setPayload()
    {
        if (! $this->isJson()) {
            throw new Exception('Unable to handle payload because the request is not a JSON POST');
        }
        //raw
        $payload = trim(file_get_contents('php://input'));
        $this->payload = json_decode($payload);
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
        return isset($this->payload->project, $this->payload->project->ssh_url)
            ? $this->payload->project->ssh_url
            : '';
    }

    /**
     * The url to the web page for the repository.
     *
     * @return string
     */
    public function url()
    {
        return isset($this->payload->project, $this->payload->project->homepage)
            ? $this->payload->project->homepage
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
