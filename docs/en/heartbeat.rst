tool_heartbeat
==============

`tool_heartbeat <https://moodle.org/plugins/tool_heartbeat>`_ (Catalyst IT) is a mature plugin for monitoring Moodle with Nagios-style tools. Both plugins can be installed together.

.. list-table::
   :header-rows: 1

   * - Topic
     - tool_healthcheck
     - tool_heartbeat
   * - Main use
     - Kubernetes liveness/readiness probes and load balancer checks
     - Monitoring systems (Nagios, Icinga and similar) and uptime checks
   * - Liveness without the database
     - Yes (``live.php`` stops right after ``config.php``)
     - See its documentation
   * - Authentication
     - Bearer token in ``config.php``, required
     - See its documentation
   * - Response
     - JSON with the status and time of each check; HTTP 200/503
     - Plain text oriented to monitoring tools
   * - Cron and task checks
     - Not here: use ``tool_taskmonitor`` or ``tool_prometheus``
     - Yes, it is one of its main features
   * - Checks of other plugins
     - Hook ``readiness_checks`` and, optionally, core status checks
     - See its documentation

When to use each
----------------

* Use ``tool_healthcheck`` to decide whether a node should receive traffic or be restarted: fast, authenticated, without side effects, with liveness separated from readiness.
* Use ``tool_heartbeat`` when an existing Nagios/Icinga setup monitors the site, especially for cron and task alerts.
* Using both is fine: the probes of the orchestrator call ``tool_healthcheck`` and the monitoring system calls ``tool_heartbeat``.
