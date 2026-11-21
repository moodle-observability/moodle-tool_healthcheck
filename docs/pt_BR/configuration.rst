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
* A página de configurações do plugin só informa se o token está configurado; nunca mostra nem edita o valor.

Configurações previstas
-----------------------

As configurações ficarão em *Administração do site > Plugins > Ferramentas de administração > tool_healthcheck*:

* Verificações habilitadas no endpoint de readiness.
* Exibir ou não o detalhe de cada verificação.
