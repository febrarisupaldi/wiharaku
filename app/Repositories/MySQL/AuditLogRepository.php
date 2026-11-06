<?php

namespace App\Repositories\MySQL;

use Illuminate\Support\Facades\DB;

class AuditLogRepository
{
    public function create(array $data): void
    {
        DB::table('audit_logs')->insert([
            'user_id' => $data['user_id'],
            'entity_type' => $data['entity_type'],
            'entity_id' => $data['entity_id'],
            'action' => $data['action'],
            'old_values' => $data['old_values']
                ? json_encode($data['old_values'])
                : null,
            'new_values' => $data['new_values']
                ? json_encode($data['new_values'])
                : null,
            'ip_address' => $data['ip_address'],
            'user_agent' => $data['user_agent'],
            'created_at' => now(),
        ]);
    }
}
