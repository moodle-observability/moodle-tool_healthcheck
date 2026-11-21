Usage
=====

Use the liveness endpoint in ``livenessProbe`` and the readiness endpoint in ``readinessProbe`` (or in the load balancer health check). Every request must send the token configured in ``$CFG->tool_healthcheck_token`` (see :doc:`configuration`).

Kubernetes, with the token read from a ``Secret`` exposed as an environment variable of the pod:

.. code-block:: yaml

   readinessProbe:
     httpGet:
       path: /admin/tool/healthcheck/ready.php
       port: 80
       httpHeaders:
         - name: Authorization
           value: Bearer $(MOODLE_HEALTHCHECK_TOKEN)

.. note::

   This page describes the planned behaviour and will be updated when the plugin is released. Endpoint paths may change.
