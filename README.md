Webhook Connector between Repo and Grunt Build Machine
================

> **Note:** this is a companion utility for the [WordPress Plugin Buildmachine for Grunt](https://github.com/eventespresso/grunt-wp-plugin-buildmachine). It's purpose is to provide a webhook endpoint for repositories that ping provided webhooks when the repository receives a commit.  It makes it possible for you to automatically trigger actions in the WordPress Plugin Buildmachine for Grunt.

**Currently Supports**
- CodebaseHQ
- Github
- Gitlab

## Configuration
- Clone this into a web accessible folder and run `composer install`.  
- Copy `app-config.sample.php` to `app-config.php` and follow the inline docs in the file to add the various configuration.
- Register the webhook address with your repository that will be notifying.

**Note: Currently this is designed to connect with the WordPress Plugin Buildmachine for Grunt located on the same server as this repo.**
