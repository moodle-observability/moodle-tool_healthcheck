Configuration
=============

Authentication token
--------------------

Both endpoints require a token. The liveness endpoint must not touch the database, so the token is **not** a plugin setting stored in the database: it is defined in ``config.php``, before the ``require_once`` of ``lib/setup.php``:

.. code-block:: php

   $CFG->tool_healthcheck_token = getenv('MOODLE_HEALTHCHECK_TOKEN') ?: '';

* Use a long random value (for example ``openssl rand -hex 32``) and keep it in a secret store or environment variable, not in version control.
* Without a token, both endpoints answer ``503`` (not configured). A missing or wrong token in the request gets ``401``.
* The token is sent in the ``Authorization: Bearer <token>`` header. It is never accepted in the query string, which ends up in access logs.
* The plugin settings page only shows whether the token is configured; it never shows or edits the value.

Planned settings
----------------

The settings will be in *Site administration > Plugins > Admin tools > tool_healthcheck*:

* Checks enabled in the readiness endpoint.
* Whether to show detailed check output.
