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
* The settings page only shows whether the token is configured; it never shows or edits the value.

Settings
--------

*Site administration > Plugins > Admin tools > Health check*:

* **Readiness checks**: moodledata, application cache, session handler, checks of other plugins (on by default) and core status checks (off by default: some of them are slow or warn about configuration, not availability). The database is always checked.
* **Show details**: include the error message of failing checks in the response. Messages may reveal host names; keep it off unless the endpoint is only reachable from inside your network.

Response
--------

.. code-block:: json

   {"status": "fail", "time_ms": 1447.25, "checks": {
     "database": {"status": "ok", "time_ms": 0.48},
     "dataroot": {"status": "ok", "time_ms": 0.17},
     "cache": {"status": "ok", "time_ms": 0.32},
     "sessions": {"status": "fail", "time_ms": 1420.52}}}

HTTP ``200`` when every check passes, ``503`` otherwise. The time of each check helps to spot a slow dependency; set short connection timeouts in Moodle (database, Redis) so a probe never hangs longer than the probe timeout.
