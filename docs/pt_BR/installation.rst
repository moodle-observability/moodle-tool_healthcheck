Instalação
==========

1. Baixe ``tool_healthcheck-<versão>.zip`` em `releases <https://github.com/moodle-observability/moodle-tool_healthcheck/releases>`_, ou clone o repositório em ``public/admin/tool/healthcheck``:

   .. code-block:: bash

      git clone https://github.com/moodle-observability/moodle-tool_healthcheck.git public/admin/tool/healthcheck

2. Rode ``php admin/cli/upgrade.php`` (ou abra *Administração do site > Notificações*).
3. Defina o token no ``config.php`` (:doc:`configuration`).

Desinstalação: *Administração do site > Plugins > Visão geral dos plugins*, ``tool_healthcheck``, *Desinstalar*. Remova também o ``$CFG->tool_healthcheck_token`` do ``config.php``.
