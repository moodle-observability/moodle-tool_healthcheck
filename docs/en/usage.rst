Usage
=====

The ``examples/`` folder of the repository has complete files.

Kubernetes
----------

The token comes from a ``Secret`` exposed as an environment variable of the pod (the same variable read by ``config.php``):

.. code-block:: yaml

   env:
     - name: MOODLE_HEALTHCHECK_TOKEN
       valueFrom:
         secretKeyRef:
           name: moodle-healthcheck
           key: token
   livenessProbe:
     httpGet:
       path: /admin/tool/healthcheck/live.php
       port: 80
       httpHeaders:
         - name: Authorization
           value: Bearer $(MOODLE_HEALTHCHECK_TOKEN)
     periodSeconds: 10
     timeoutSeconds: 3
     failureThreshold: 3
   readinessProbe:
     httpGet:
       path: /admin/tool/healthcheck/ready.php
       port: 80
       httpHeaders:
         - name: Authorization
           value: Bearer $(MOODLE_HEALTHCHECK_TOKEN)
     periodSeconds: 10
     timeoutSeconds: 5
     failureThreshold: 3

Moodle redirects requests whose host differs from ``$CFG->wwwroot``. Kubelet probes send the pod IP as the host; add a ``Host`` header with the ``wwwroot`` host in ``httpHeaders`` (``- name: Host`` / ``value: moodle.example.com``).

HAProxy
-------

.. code-block:: text

   backend moodle
       option httpchk
       http-check send meth GET uri /admin/tool/healthcheck/ready.php ver HTTP/1.1 hdr Host moodle.example.com hdr Authorization "Bearer ${MOODLE_HEALTHCHECK_TOKEN}"
       http-check expect status 200
       server web1 10.0.0.11:80 check inter 10s fall 3 rise 2

Nginx
-----

Active health checks (``health_check``) exist only in NGINX Plus. Open source Nginx only has passive checks (``max_fails`` and ``fail_timeout`` on each ``server``), which do not call the readiness endpoint. Use the probes of the orchestrator or an external checker in that case.
