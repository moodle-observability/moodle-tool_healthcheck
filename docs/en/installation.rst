Installation
============

1. Download ``tool_healthcheck-<version>.zip`` from the `releases <https://github.com/moodle-observability/moodle-tool_healthcheck/releases>`_, or clone the repository into ``public/admin/tool/healthcheck``:

   .. code-block:: bash

      git clone https://github.com/moodle-observability/moodle-tool_healthcheck.git public/admin/tool/healthcheck

2. Run ``php admin/cli/upgrade.php`` (or open *Site administration > Notifications*).
3. Define the token in ``config.php`` (:doc:`configuration`).

Uninstall: *Site administration > Plugins > Plugins overview*, ``tool_healthcheck``, *Uninstall*. Remove ``$CFG->tool_healthcheck_token`` from ``config.php`` too.
