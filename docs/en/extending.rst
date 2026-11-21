Extending
=========

Other plugins add readiness checks through the ``\tool_healthcheck\hook\readiness_checks`` hook. For example, a separate plugin can check object storage (S3, R2, MinIO) used by ``tool_objectfs``, so ``tool_healthcheck`` does not depend on it.

``db/hooks.php``:

.. code-block:: php

   $callbacks = [
       [
           'hook' => \tool_healthcheck\hook\readiness_checks::class,
           'callback' => [\local_objectstorecheck\callbacks::class, 'readiness'],
       ],
   ];

Callback and check:

.. code-block:: php

   public static function readiness(\tool_healthcheck\hook\readiness_checks $hook): void {
       $hook->add_check('local_objectstorecheck', new \local_objectstorecheck\bucket_check());
   }

   final class bucket_check implements \tool_healthcheck\local\check {
       public function get_name(): string {
           return 'bucket';
       }

       public function run(): void {
           // Read only: HeadBucket or ListObjectsV2 with max-keys=1. Throw when the bucket is unreachable.
       }
   }

The check appears as ``local_objectstorecheck:bucket`` in the response.

Rules
-----

* **No side effects.** Read only whenever possible: an object storage check uses ``HeadBucket`` or ``ListObjectsV2`` with ``max-keys=1``, never ``PutObject``, ``DeleteObject`` or ``CopyObject``. When something must be written, use a fixed name per node and remove it in a ``finally`` block.
* Fast, with short timeouts: the check runs on every probe of every node.
* No sensitive data (bucket names, endpoints, credentials) in exception messages.
* A check that throws only fails itself; a callback that throws makes the ``extensions`` check fail.
