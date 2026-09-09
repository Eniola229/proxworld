<?php

namespace App\Traits;

use Spatie\Activitylog\Models\Activity;

trait LogsAdminActivity
{
    /**
     * Log a generic admin action. $subjectType/$subjectId are optional —
     * pass null for actions not tied to a specific record (e.g. "viewed list").
     */
    protected function logActivity(string $event, string $description, ?string $subjectType = null, $subjectId = null, array $properties = []): void
    {
        $properties = array_merge(['status' => 'success'], $properties);

        $log = activity($subjectType ? strtolower($subjectType) : 'admin')
            ->causedBy(auth('admin')->user())
            ->event($event)
            ->withProperties($properties);

        if ($subjectType && $subjectId) {
            $modelClass = $this->resolveSubjectClass($subjectType);
            if ($modelClass) {
                $instance = new $modelClass;
                $instance->setAttribute($instance->getKeyName(), $subjectId);
                $instance->exists = true;
                $log->performedOn($instance);
            }
        }

        $log->tap(function (Activity $activity) {
            $activity->causer_guard = 'admin';
            $activity->ip_address = request()->ip();
            $activity->method = request()->method();
            $activity->url = request()->fullUrl();
        })->log($description);
    }

    protected function logViewed(string $subjectType, $subjectId, string $description): void
    {
        $this->logActivity('viewed', $description, $subjectType, $subjectId);
    }

    protected function logUpdated(string $subjectType, $subjectId, string $description, array $changes = []): void
    {
        $this->logActivity('updated', $description, $subjectType, $subjectId, ['changes' => $changes]);
    }

    /**
     * Map a short subject name (as passed by controllers, e.g. 'Reseller')
     * to its fully-qualified model class. Add entries here as new subject
     * types start getting logged.
     */
    protected function resolveSubjectClass(string $subjectType): ?string
    {
        return match ($subjectType) {
            'Reseller' => \App\Models\Reseller::class,
            'User' => \App\Models\User::class,
            'Order' => \App\Models\Order::class,
            default => null,
        };
    }
}