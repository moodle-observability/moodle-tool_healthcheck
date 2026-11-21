Uso
===

Use o endpoint de liveness no ``livenessProbe`` e o de readiness no ``readinessProbe`` (ou no health check do balanceador). Toda requisição deve enviar o token configurado em ``$CFG->tool_healthcheck_token`` (veja :doc:`configuration`).

Kubernetes, com o token lido de um ``Secret`` exposto como variável de ambiente do pod:

.. code-block:: yaml

   readinessProbe:
     httpGet:
       path: /admin/tool/healthcheck/ready.php
       port: 80
       httpHeaders:
         - name: Authorization
           value: Bearer $(MOODLE_HEALTHCHECK_TOKEN)

.. note::

   Esta página descreve o comportamento previsto e será atualizada quando o plugin for publicado. Os caminhos dos endpoints podem mudar.
