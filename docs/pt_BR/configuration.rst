Configuração
============

Token de autenticação
---------------------

Os dois endpoints exigem um token. Como o endpoint de liveness não pode acessar o banco de dados, o token **não** é uma configuração do plugin gravada no banco: ele é definido no ``config.php``, antes do ``require_once`` do ``lib/setup.php``:

.. code-block:: php

   $CFG->tool_healthcheck_token = getenv('MOODLE_HEALTHCHECK_TOKEN') ?: '';

* Use um valor aleatório longo (por exemplo ``openssl rand -hex 32``) e guarde-o num cofre de segredos ou variável de ambiente, fora do controle de versão.
* Sem token configurado, os dois endpoints respondem ``503`` (não configurado). Token ausente ou errado na requisição recebe ``401``.
* O token vai no header ``Authorization: Bearer <token>``. Ele nunca é aceito na query string, que acaba nos logs de acesso.
* A página de configurações só informa se o token está configurado; nunca mostra nem edita o valor.

Configurações
-------------

*Administração do site > Plugins > Ferramentas de administração > Health check*:

* **Verificações de readiness**: moodledata, cache de aplicação, handler de sessões, checks de outros plugins (ligadas por padrão) e status checks do core (desligadas por padrão: alguns são lentos ou alertam sobre configuração, não sobre disponibilidade). O banco de dados é sempre verificado.
* **Exibir detalhes**: inclui a mensagem de erro das verificações com falha na resposta. As mensagens podem revelar nomes de host; deixe desligado, a menos que o endpoint só seja acessível de dentro da sua rede.

Resposta
--------

.. code-block:: json

   {"status": "fail", "time_ms": 1447.25, "checks": {
     "database": {"status": "ok", "time_ms": 0.48},
     "dataroot": {"status": "ok", "time_ms": 0.17},
     "cache": {"status": "ok", "time_ms": 0.32},
     "sessions": {"status": "fail", "time_ms": 1420.52}}}

HTTP ``200`` quando todas as verificações passam, ``503`` caso contrário. O tempo de cada verificação ajuda a encontrar uma dependência lenta; configure timeouts curtos de conexão no Moodle (banco de dados, Redis) para que um probe nunca demore mais que o timeout do probe.
