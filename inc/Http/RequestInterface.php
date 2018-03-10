<?php
namespace Nerrad\BuildMachine\WebHookListener\Http;

/**
 * Interface RequestInterface
 * An interface for the webhook listener request instances.
 *
 * @package Nerrad\BuildMachine\WebHookListener\Http
 * @subpackage
 * @author  Darren Ethier
 * @since   1.0.0
 */
interface RequestInterface
{
    /**
     * Returns the token that was on the request.
     * @return string
     */
    public function token();


    /**
     * Simply returns whether this request is a valid request for a repository webhook
     * @return string
     */
    public function isValid();


    /**
     * The url for cloning the repository that is in the request payload.
     * @return mixed
     */
    public function cloneUrl();


    /**
     * The url to the web page for the repository.
     * @return string
     */
    public function url();


    /**
     * Returns the author email for the most recent commit.
     * @return string
     */
    public function mostRecentCommitAuthorEmail();


    /**
     * The branch represented by the commit in the request.
     * @return string
     */
    public function branch();
}