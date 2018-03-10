<?php
namespace Nerrad\BuildMachine\WebHookListener;

use Nerrad\BuildMachine\WebHookListener\Http\Request;


/**
 * React
 * Reacts to what may be in an incoming webhook payload and handles accordingly.
 *
 * @package Nerrad\BuildMachine\WebHookListener
 * @author  Darren Ethier
 * @since   1.0.0
 */
class React
{

    private $request;
    private $config;

    public function __construct(Request $request, Config $config)
    {
        //keeping things simple for the first go.  All we want to do is parse the incoming request and make sure that
        // we have a non EE4server request for triggering grunt.
        ini_set('log_errors_max_len', 0);
        $this->request = $request->getAll();
        $this->config = $config;

        $this->validateRequest();
        $this->triggerGruntTask();
    }


    /**
     * Validates that the incoming request is a valid/authorized request.
     */
    private function validateRequest()
    {
        //verify there is a valid access token and it matches our config. Bail if not present
        $incoming_access_token = $this->request->token;
        if ($incoming_access_token !== $this->config->access_token && ! empty($this->config->access_token)) {
            $msg = 'Access denied due to invalid token.';
            syslog(LOG_DEBUG, $msg);
            header('HTTP/1.1 403 Access Denied.');
            exit();
        }

        //verify we have a valid request
        if (empty($this->request->repository)) {
            $msg = 'Invalid package received.';
            syslog(LOG_DEBUG, $msg);
            header('HTTP/1.1 400 Bad Request');
            exit($msg);
        }
    }


    /**
     * Triggers any grunt tasks for the incoming request.
     */
    private function triggerGruntTask()
    {
        $has_run = false;
        foreach ($this->config->map as $slug => $clone_url) {
            if ($clone_url === $this->request->repository->clone_url) {
                $this->triggerGrunt($slug);
                $has_run = true;
            }
        }
        //message about no support
        if ($has_run) {
            $msg = 'The grunt tasks associated with ' . $this->request->repository->url . ' completed successfully.';
            syslog(LOG_DEBUG, $msg);
            header('HTTP/1.1 200 OK');
            exit($msg);

        }
        $msg = 'There are no grunt tasks associated with ' . $this->request->repository->url . '.';
        syslog(LOG_DEBUG, $msg);
        header('HTTP/1.1 200 OK');
        exit($msg);
    }


    private function triggerGrunt($slug)
    {
        //if latest commit by EE DevBox server then do NOT run grunt
        $i = 0;
        $output = $output2 = '';

        if (empty($this->request) || ! isset($this->request->commits)) {
            $msg = 'No commits to process.  Looks like a bad package.';
            syslog(LOG_DEBUG, $msg);
            header('HTTP/1.1 400 Bad Request');
            exit($msg);
        }

        foreach ($this->request->commits as $commit) {
            //error_log( print_r( $commit, true ) );
            if ($commit->author->email === $this->config->server_git_email && $i === 0) {
                $msg = 'Most recent commit made by grunt so will not run recursively!';
                syslog(LOG_DEBUG, $msg);
                header('HTTP/1.1 202 Accepted');
                exit($msg);
            }
            $i++;
        }

        //what branch are we going to checkout?
        $ref = str_replace('refs/heads/', '', $this->request->ref);

        if ($this->canProcess($slug)) {
            $this->setProcessingLock($slug);
            $this->doGrunt($slug, $ref);
            $this->removeProcessingLock($slug);
        } else {
            $msg = "There is already a task for the $slug being processed.";
            syslog(LOG_DEBUG, $msg);
            header('HTTP/1.1 409 Request Conflict');
            exit($msg);
        }
    }


    /**
     * Determines whether there is a processing lock for the current slug being processed. Prevents potential race
     * conditions.
     *
     * @todo implement a queue system instead so no lock necessary, just execute on a queue.
     * @param $slug
     * @return bool
     */
    protected function canProcess($slug)
    {
        $locks = json_decode(file_get_contents('.locks'));
        return ! isset($locks->{$slug});
    }


    /**
     * Sets the lock for a processing request.
     *
     * @param $slug
     */
    protected function setProcessingLock($slug)
    {
        $locks = json_decode(file_get_contents('.locks'));
        $locks->{$slug} = true;
        file_put_contents('.locks', json_encode($locks));
    }


    /**
     * Removes the processing lock for a processing request.
     *
     * @param $slug
     */
    protected function removeProcessingLock($slug)
    {
        $locks = json_decode(file_get_contents('.locks'));
        unset($locks->{$slug});
        file_put_contents('.locks', json_encode($locks));
    }


    /**
     * @param string $slug Slug of plugin/add-on being processed.
     * @param string $ref  Branch being operated on
     */
    protected function doGrunt($slug, $ref)
    {
        $output = $output2 = $sandbox_command = '';
        if ($ref === 'master') {
            $bump_command = 'cd ' . $this->config->grunt_path . ' && grunt bumprc_' . $ref . ':' . $slug;
            $sandbox_command = 'cd ' . $this->config->grunt_path . ' && grunt updateRemotes:' . $slug;
        } else {
            $bump_command = 'cd ' . $this->config->grunt_path . ' && grunt githubsync:' . $slug . ':' . $ref;
        }
        exec($bump_command, $output);
        syslog(LOG_DEBUG, print_r($output, true));

        if (! empty($sandbox_command)) {
            sleep(3);
            exec($sandbox_command, $output2);
            syslog(LOG_DEBUG, print_r($output2, true));
        }
    }
}
