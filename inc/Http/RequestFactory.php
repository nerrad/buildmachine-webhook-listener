<?php
namespace Nerrad\BuildMachine\WebHookListener\Http;

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

    /**
     * Return instance of
     *
     * @param array  $request
     * @param string $type The type of repo to build the request object for.
     * @return RequestInterface|null
     * @throws InvalidArgumentException
     */
    public static function getRequestForRepositoryType(array $request, $type)
    {
        switch ($type)
        {
            case $type === self::REPOSITORY_TYPE_CODEBASE:
                return new CodebaseRequest($request);
            default:
                throw new InvalidArgumentException(
                    sprintf(
                        'The provided type (%s) is not a valid request type.',
                        $type
                    )
                );
        }
    }
}