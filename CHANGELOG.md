# Changelog

## 0.1.0 - 2026-11-21

First release (alpha).

- `live.php`: liveness endpoint that stops right after `config.php` (no database).
- `ready.php`: readiness endpoint checking the database, writable moodledata, the application cache and the session
  handler; optional core status checks; `503` with a failed database even when the Moodle setup fails.
- Token in `config.php` (`$CFG->tool_healthcheck_token`), sent as `Authorization: Bearer`.
- JSON response with the status and time of each check; error messages only when enabled.
- Checks without side effects (fixed probe name per node, removed in `finally`).
- `readiness_checks` hook for checks of other plugins.
- Examples for Kubernetes, HAProxy and Nginx; comparison with tool_heartbeat.
