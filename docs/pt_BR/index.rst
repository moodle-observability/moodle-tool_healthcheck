moodle-tool_healthcheck
=======================

**Probes de liveness e readiness para o Moodle.**

Oferece endpoints HTTP de liveness e readiness para os probes do Kubernetes e para os health checks de balanceadores de carga.

.. note::

   Em desenvolvimento, com foco no milestone ``v0.1.0 – MVP``. Ainda não há versão publicada.
   Acompanhe o andamento nas `issues <https://github.com/moodle-observability/moodle-tool_healthcheck/issues>`_.

Funcionalidades previstas
-------------------------

* Autenticação por token configurado no ``config.php`` (``$CFG``), que funciona sem o banco de dados.
* Endpoint de liveness leve, sem acessar o banco de dados.
* Endpoint de readiness que verifica banco de dados, caches (Redis/MUC), moodledata gravável e sessões.
* Resposta JSON com status e tempo de cada verificação; detalhes sensíveis ocultos por padrão.
* Exemplos de probes do Kubernetes e de configuração do HAProxy / Nginx.
* Comparação com o ``tool_heartbeat`` e quando usar cada um.

Requisitos
----------

* Moodle 5.3 ou posterior.
* Versão do PHP suportada pela sua versão do Moodle.
* PostgreSQL ou MariaDB.

Links
-----

* `Código-fonte <https://github.com/moodle-observability/moodle-tool_healthcheck>`_
* `Issues <https://github.com/moodle-observability/moodle-tool_healthcheck/issues>`_
* `Moodle Observability <https://moodle-observability.github.io/>`_

.. toctree::
   :hidden:
   :maxdepth: 1

   installation
   configuration
   usage
