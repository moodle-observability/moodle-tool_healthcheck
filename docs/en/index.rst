moodle-tool_healthcheck
=======================

**Liveness and readiness probes for Moodle.**

Provides liveness and readiness HTTP endpoints for Kubernetes probes and load balancer health checks.

.. note::

   Under development, targeting the ``v0.1.0 – MVP`` milestone. There is no release yet.
   Follow the progress in the `issues <https://github.com/moodle-observability/moodle-tool_healthcheck/issues>`_.

Planned features
----------------

* Lightweight liveness endpoint that does not touch the database.
* Readiness endpoint checking the database, caches (Redis/MUC), writable moodledata and sessions.
* JSON response with the status and timing of each check; sensitive details hidden by default.
* Example Kubernetes probes and HAProxy / Nginx configuration.
* Comparison with ``tool_heartbeat`` and when to use each.

Requirements
------------

* Moodle 5.3 or later.
* PHP version supported by your Moodle release.
* PostgreSQL or MariaDB.

Links
-----

* `Source code <https://github.com/moodle-observability/moodle-tool_healthcheck>`_
* `Issues <https://github.com/moodle-observability/moodle-tool_healthcheck/issues>`_
* `Moodle Observability <https://moodle-observability.github.io/>`_

.. toctree::
   :hidden:
   :maxdepth: 1

   installation
   configuration
   usage
