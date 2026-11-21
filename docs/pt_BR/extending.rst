Extensão
========

Outros plugins acrescentam verificações de readiness pelo hook ``\tool_healthcheck\hook\readiness_checks``. Por exemplo, um plugin separado pode verificar o object storage (S3, R2, MinIO) usado pelo ``tool_objectfs``, sem que o ``tool_healthcheck`` dependa dele.

``db/hooks.php``:

.. code-block:: php

   $callbacks = [
       [
           'hook' => \tool_healthcheck\hook\readiness_checks::class,
           'callback' => [\local_objectstorecheck\callbacks::class, 'readiness'],
       ],
   ];

Callback e verificação:

.. code-block:: php

   public static function readiness(\tool_healthcheck\hook\readiness_checks $hook): void {
       $hook->add_check('local_objectstorecheck', new \local_objectstorecheck\bucket_check());
   }

   final class bucket_check implements \tool_healthcheck\local\check {
       public function get_name(): string {
           return 'bucket';
       }

       public function run(): void {
           // Só leitura: HeadBucket ou ListObjectsV2 com max-keys=1. Lance exceção se o bucket não responder.
       }
   }

A verificação aparece como ``local_objectstorecheck:bucket`` na resposta.

Regras
------

* **Sem efeitos colaterais.** Só leitura sempre que possível: uma verificação de object storage usa ``HeadBucket`` ou ``ListObjectsV2`` com ``max-keys=1``, nunca ``PutObject``, ``DeleteObject`` ou ``CopyObject``. Quando algo precisar ser gravado, use nome fixo por nó e remova num bloco ``finally``.
* Rápida, com timeouts curtos: a verificação roda em todo probe de todo nó.
* Nenhum dado sensível (nomes de bucket, endpoints, credenciais) nas mensagens de exceção.
* Uma verificação que lança exceção só falha ela mesma; um callback que lança exceção faz falhar a verificação ``extensions``.
