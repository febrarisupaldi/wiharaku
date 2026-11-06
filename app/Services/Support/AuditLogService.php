<?php

namespace App\Services\Support;

use App\Repositories\MySQL\AuditLogRepository;

class AuditLogService
{
    public function __construct(
        protected AuditLogRepository $repository
    ) {}

    public function record(
        string $entityType,
        int $entityId,
        string $action,
        ?array $oldValues,
        ?array $newValues
    ): void {
        $this->repository->create([
            'user_id' => auth()->id(),
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'action' => $action,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
