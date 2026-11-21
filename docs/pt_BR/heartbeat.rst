tool_heartbeat
==============

O `tool_heartbeat <https://moodle.org/plugins/tool_heartbeat>`_ (Catalyst IT) é um plugin maduro para monitorar o Moodle com ferramentas no estilo Nagios. Os dois plugins podem ser instalados juntos.

.. list-table::
   :header-rows: 1

   * - Tema
     - tool_healthcheck
     - tool_heartbeat
   * - Uso principal
     - Probes de liveness/readiness do Kubernetes e checks de balanceadores
     - Sistemas de monitoramento (Nagios, Icinga e similares) e verificações de disponibilidade
   * - Liveness sem banco de dados
     - Sim (``live.php`` para logo depois do ``config.php``)
     - Veja a documentação dele
   * - Autenticação
     - Bearer token no ``config.php``, obrigatório
     - Veja a documentação dele
   * - Resposta
     - JSON com status e tempo de cada verificação; HTTP 200/503
     - Texto simples voltado a ferramentas de monitoramento
   * - Verificação de cron e tarefas
     - Não aqui: use o ``tool_taskmonitor`` ou o ``tool_prometheus``
     - Sim, é uma das principais funções dele
   * - Checks de outros plugins
     - Hook ``readiness_checks`` e, opcionalmente, os status checks do core
     - Veja a documentação dele

Quando usar cada um
-------------------

* Use o ``tool_healthcheck`` para decidir se um nó deve receber tráfego ou ser reiniciado: rápido, autenticado, sem efeitos colaterais, com liveness separado de readiness.
* Use o ``tool_heartbeat`` quando um Nagios/Icinga existente monitora o site, principalmente para alertas de cron e tarefas.
* Usar os dois não tem problema: os probes do orquestrador chamam o ``tool_healthcheck`` e o sistema de monitoramento chama o ``tool_heartbeat``.
