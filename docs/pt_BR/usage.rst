Uso
===

A pasta ``examples/`` do repositório tem os arquivos completos.

Kubernetes
----------

O token vem de um ``Secret`` exposto como variável de ambiente do pod (a mesma variável lida pelo ``config.php``):

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

O Moodle redireciona requisições cujo host é diferente do ``$CFG->wwwroot``. Os probes do kubelet enviam o IP do pod como host; acrescente um header ``Host`` com o host do ``wwwroot`` em ``httpHeaders`` (``- name: Host`` / ``value: moodle.example.com``).

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

Health checks ativos (``health_check``) só existem no NGINX Plus. O Nginx open source só tem checks passivos (``max_fails`` e ``fail_timeout`` em cada ``server``), que não chamam o endpoint de readiness. Nesse caso, use os probes do orquestrador ou um verificador externo.
