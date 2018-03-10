<?php
/**
 * Indicate what type of repository is sending the webhook.
 * Currently supports:
 * - codebase
 */
$repository_type = 'codebase';


/**
 * This is used to indicate what email address is used by the git user that the grunt build machine makes commits with.
 *  This allows the webhook to detect the most recent commit and prevent recursive commits.
 *
 * @var string
 */
$server_git_email = 'admin@eventespresso.com';


/**
 * Your webhook address will be whatever domain you attach this to plus ?token=some-random-string. So something like:
 * 'http://cbwebhook.mydomain.com/?token=some-random-string'
 * So what you want to do here is enter a random string.  An empty string means every request is accepted and is an
 * option in case you just want to to basic auth at the webserver configuration level.
 */
$access_token = 'some-random-string';


/**
 * Path to grunt build machine
 * This is used by this webhook listener to know where the working directory for the grunt machine is on the server for
 * executing tasks.
 *
 * @var string
 */
$grunt_path = '~/buildmachine/';
$grunt_src_path  = '~/buildmachine/buildsrc/';

/**
 * path to map json file (an object where keys are folder names, and values are the corresponding repos for the folder).
 * Absolute path required here.
 *
 * This is usually a json file that is auto generated by the Grunt Build Machine.
 */
$map_file = '/home/eeservice/buildmachine/installedReposMap.json';

