<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AuditLogService
{
    public function log(
        string $actionType,
        string $status,
        array $context = [],
        ?Request $request = null
    ): void {
        DB::insert(
            'INSERT INTO audit_logs
                (user_id, customer_id, account_number, action_type, entity_type, entity_id, status, message, ip_address, user_agent, request_payload, response_payload, created_at, updated_at)
             VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), NOW())',
            [
                $context['user_id'] ?? null,
                $context['customer_id'] ?? null,
                $context['account_number'] ?? null,
                $actionType,
                $context['entity_type'] ?? null,
                $context['entity_id'] ?? null,
                $status,
                $context['message'] ?? null,
                $request?->ip(),
                $request?->userAgent(),
                $this->encodePayload($context['request_payload'] ?? null),
                $this->encodePayload($context['response_payload'] ?? null),
            ]
        );
    }

    private function encodePayload(mixed $payload): ?string
    {
        if ($payload === null) {
            return null;
        }

        return json_encode($this->sanitizePayload($payload), JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }

    private function sanitizePayload(mixed $payload): mixed
    {
        if (!is_array($payload)) {
            return $payload;
        }

        $sanitized = [];

        foreach ($payload as $key => $value) {
            $normalizedKey = strtolower((string) $key);

            if (in_array($normalizedKey, ['password', 'quick_action_password', 'otp', 'otp_hash'], true)) {
                $sanitized[$key] = '[REDACTED]';
                continue;
            }

            if (is_array($value)) {
                $sanitized[$key] = $this->sanitizePayload($value);
                continue;
            }

            $sanitized[$key] = $value;
        }

        return $sanitized;
    }
}
