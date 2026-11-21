moodle-tool_healthcheck
=======================

**Liveness and readiness probes for Moodle.**

Two token-protected HTTP endpoints for Kubernetes probes and load balancer health checks:

* ``/admin/tool/healthcheck/live.php``: PHP answers and ``config.php`` loads. It never opens a database connection, so a database outage does not restart every pod.
* ``/admin/tool/healthcheck/ready.php``: the node can receive traffic: database, writable moodledata, application cache (MUC) and session handler, plus optional core status checks and checks contributed by other plugins.

.. note::

   Version 0.1.0 (alpha). Follow the progress in the `issues <https://github.com/moodle-observability/moodle-tool_healthcheck/issues>`_.

Features
--------

* Token in ``config.php`` (``$CFG->tool_healthcheck_token``), read without the database; ``401`` for a missing or wrong token, ``503`` when it is not configured.
* JSON response with the status and the time of each check; error messages only when enabled.
* Checks without side effects: probes use a fixed name per node, are removed in a ``finally`` block and never accumulate.
* ``503`` with ``"database": "fail"`` even when the Moodle setup itself cannot reach the database.
* Extension point for checks of other plugins, such as object storage (:doc:`extending`).
* Examples for Kubernetes, HAProxy and Nginx.

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
   heartbeat
   extending
