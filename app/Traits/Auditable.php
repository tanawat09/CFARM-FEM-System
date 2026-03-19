<?php

namespace App\Traits;

use App\Models\AuditLog;

trait Auditable
{
    public static function bootAuditable()
    {
        static::created(function ($model) {
            $model->logAudit('created', [], $model->getAttributes());
        });

        static::updated(function ($model) {
            $dirty = $model->getDirty();
            if (empty($dirty)) {
                return;
            }

            $oldValues = [];
            foreach (array_keys($dirty) as $key) {
                $oldValues[$key] = $model->getOriginal($key);
            }

            $model->logAudit('updated', $oldValues, $dirty);
        });

        static::deleted(function ($model) {
            $model->logAudit('deleted', $model->getOriginal(), []);
        });
    }

    protected function logAudit(string $action, array $oldValues, array $newValues): void
    {
        // Skip logging password fields
        $hidden = ['password', 'remember_token'];
        $oldValues = array_diff_key($oldValues, array_flip($hidden));
        $newValues = array_diff_key($newValues, array_flip($hidden));

        // Skip if only timestamps changed
        $ignoreFields = ['created_at', 'updated_at', 'deleted_at'];
        $changedKeys = array_keys($action === 'updated' ? $newValues : []);
        if ($action === 'updated' && empty(array_diff($changedKeys, $ignoreFields))) {
            return;
        }

        try {
            AuditLog::create([
                'user_id' => auth()->id(),
                'action' => $action,
                'auditable_type' => get_class($this),
                'auditable_id' => $this->getKey(),
                'old_values' => !empty($oldValues) ? $oldValues : null,
                'new_values' => !empty($newValues) ? $newValues : null,
                'ip_address' => request()?->ip(),
                'user_agent' => request()?->userAgent(),
            ]);
        } catch (\Throwable $e) {
            // Don't let audit logging break the main operation
            \Log::error('Audit log failed: ' . $e->getMessage());
        }
    }

    public function auditLogs()
    {
        return $this->morphMany(AuditLog::class, 'auditable');
    }
}
