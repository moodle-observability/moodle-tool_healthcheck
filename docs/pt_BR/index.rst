moodle-tool_healthcheck
=======================

**Probes de liveness e readiness para o Moodle.**

Dois endpoints HTTP protegidos por token, para os probes do Kubernetes e os health checks de balanceadores de carga:

* ``/admin/tool/healthcheck/live.php``: o PHP responde e o ``config.php`` carrega. Nunca abre conexão com o banco de dados, então uma queda do banco não reinicia todos os pods.
* ``/admin/tool/healthcheck/ready.php``: o nó pode receber tráfego: banco de dados, moodledata gravável, cache de aplicação (MUC) e handler de sessões, além dos status checks do core (opcionais) e de checks de outros plugins.

.. note::

   Versão 0.1.0 (alfa). Acompanhe o andamento nas `issues <https://github.com/moodle-observability/moodle-tool_healthcheck/issues>`_.

Funcionalidades
---------------

* Token no ``config.php`` (``$CFG->tool_healthcheck_token``), lido sem o banco de dados; ``401`` para token ausente ou errado, ``503`` quando não está configurado.
* Resposta JSON com o status e o tempo de cada verificação; mensagens de erro só quando habilitadas.
* Verificações sem efeitos colaterais: os probes usam nome fixo por nó, são removidos num bloco ``finally`` e nunca se acumulam.
* ``503`` com ``"database": "fail"`` mesmo quando o próprio setup do Moodle não alcança o banco.
* Ponto de extensão para checks de outros plugins, como object storage (:doc:`extending`).
* Exemplos para Kubernetes, HAProxy e Nginx.

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
   heartbeat
   extending
