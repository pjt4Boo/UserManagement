<?php

namespace App\Traits;

use App\Models\AuditLog;

trait Auditable
{
    public static function bootAuditable()
    {
        static::created(function ($model) {
            static::createAuditLog('created', $model, null, $model->getAttributes());
        });

        static::updated(function ($model) {
            $changes = [];
            foreach ($model->getChanges() as $key => $newValue) {
                $oldValue = $model->getOriginal($key);
                if ($oldValue !== $newValue) {
                    $changes[$key] = [
                        'old' => $oldValue,
                        'new' => $newValue,
                    ];
                }
            }

            if (!empty($changes)) {
                static::createAuditLog('updated', $model, $changes);
            }
        });

        static::deleted(function ($model) {
            $action = method_exists($model, 'isForceDeleting') && $model->isForceDeleting() ? 'force_deleted' : 'deleted';
            static::createAuditLog($action, $model, null, $model->getAttributes());
        });

        // Handle deactivation
        if (method_exists(static::class, 'deactivated')) {
            static::deactivated(function ($model) {
                static::createAuditLog('deactivated', $model, [
                    'status' => ['old' => 'active', 'new' => 'inactive'],
                ]);
            });
        }
    }

    protected static function createAuditLog($action, $model, $changes = null, $oldAttributes = null)
    {
        AuditLog::create([
            'user_id' => $model->id ?? null,
            'actor_id' => auth()->id() ?? null,
            'action' => $action,
            'model_type' => $model::class,
            'model_id' => $model->id,
            'changes' => $changes ?: $oldAttributes,
        ]);
    }
}
