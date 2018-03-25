<?php
namespace Nerrad\BuildMachine\WebHookListener\Http;

use Exception;
use InvalidArgumentException;

/**
 * RequestFactory
 * Used to build instances of RequestInterface.
 *
 * @package Nerrad\BuildMachine\WebHookListener\Http
 * @author  Darren Ethier
 * @since   1.0.0
 */
class RequestFactory
{

    const REPOSITORY_TYPE_GITHUB = 'github';
    const REPOSITORY_TYPE_CODEBASE = 'codebase';
    const REPOSITORY_TYPE_GITLAB = 'gitlab';


    private static function detectRequestType()
    {
        if (isset($_SERVER['HTTP_X_GITLAB_EVENT'])) {
            return self::REPOSITORY_TYPE_GITLAB;
        }
        if (isset($_SERVER['HTTP_X_GITHUB_EVENT'])) {
            return self::REPOSITORY_TYPE_GITHUB;
        }
        return self::REPOSITORY_TYPE_CODEBASE;
    }

    /**
     * Return the request handler for the detected incoming request type.
     *
     * @param array $request
     * @return RequestInterface|null
     * @throws InvalidArgumentException
     * @throws Exception
     */
    public static function getRequestForRepositoryType(array $request)
    {
        switch (self::detectRequestType())
        {
            case self::REPOSITORY_TYPE_CODEBASE:
                return new CodebaseRequest($request);
            case self::REPOSITORY_TYPE_GITHUB:
                return new GithubRequest($request);
            case self::REPOSITORY_TYPE_GITLAB:
                return new GitlabRequest($request);
            default:
                throw new InvalidArgumentException(
                    'The incoming request type could not be detected.  Currently this webhook listener only supports Github, Gitlab, or CodebaseHQ webhooks.'
                );
        }
    }
}