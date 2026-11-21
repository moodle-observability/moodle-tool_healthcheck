<img src="docs/logo/logo.svg" alt="moodle-tool_healthcheck logo" width="96" align="right" />

# moodle-tool_healthcheck

[![Documentation](https://github.com/moodle-observability/moodle-tool_healthcheck/actions/workflows/docs.yml/badge.svg)](https://github.com/moodle-observability/moodle-tool_healthcheck/actions/workflows/docs.yml)

Liveness and readiness probes for Moodle (`tool_healthcheck`). Provides liveness and readiness HTTP endpoints for Kubernetes probes and load balancer health checks.

**Status:** 0.1.0 (alpha). Download it from the [releases](https://github.com/moodle-observability/moodle-tool_healthcheck/releases).

Documentation (English and Brazilian Portuguese): https://moodle-observability.github.io/moodle-tool_healthcheck/

Requirements: Moodle 5.3 or later.

Quick start:

1. Install the plugin in `public/admin/tool/healthcheck` and upgrade Moodle.
2. In `config.php`, before `lib/setup.php`: `$CFG->tool_healthcheck_token = getenv('MOODLE_HEALTHCHECK_TOKEN') ?: '';`
3. Probe `/admin/tool/healthcheck/live.php` (liveness) and `/admin/tool/healthcheck/ready.php` (readiness) with
   `Authorization: Bearer <token>`.

[`examples/`](examples) has Kubernetes probes and HAProxy/Nginx configuration.

Part of [Moodle Observability](https://github.com/moodle-observability). Contributions: see the [contributing guide](https://github.com/moodle-observability/.github/blob/main/CONTRIBUTING.md).

License: [GPL-3.0](LICENSE).
