<?php

namespace Modules\Support\app\Imports;

use Maatwebsite\Excel\Concerns\SkipsEmptyRows;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Modules\Support\Models\Subscriber;

class SubscriberImport implements SkipsEmptyRows, ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row)
    {
        // WithHeadingRow slugifies headers: Email → email, IP Address → ip_address, etc.
        $email = trim((string) ($row['email'] ?? ''));

        if ($email === '') {
            return null;
        }

        $subscriber = Subscriber::where('email', $email)->first();

        $ipAddress = $row['ip_address'] ?? null;
        $ipAddress = filled($ipAddress) ? trim((string) $ipAddress) : null;

        $lang = $row['language'] ?? $row['lang'] ?? null;
        $lang = filled($lang) ? substr(trim((string) $lang), 0, 2) : null;

        $hasBlocked = array_key_exists('blocked', $row)
            && $row['blocked'] !== null
            && $row['blocked'] !== '';
        $isBlocked = $hasBlocked ? $this->parseBlocked($row['blocked']) : null;

        if ($subscriber) {
            $data = [];
            if ($ipAddress !== null) {
                $data['ip_address'] = $ipAddress;
            }
            if ($lang !== null) {
                $data['lang'] = $lang;
            }
            if ($isBlocked !== null) {
                $data['blocked'] = $isBlocked;
            }

            if ($data !== []) {
                $subscriber->update($data);
            }

            return null;
        }

        return new Subscriber([
            'email' => $email,
            'ip_address' => $ipAddress ?? '0.0.0.0',
            'lang' => $lang ?? 'en',
            'blocked' => $isBlocked ?? false,
        ]);
    }

    public function rules(): array
    {
        // WithHeadingRow slugifies column names (e.g. "Email" → "email", "IP Address" → "ip_address")
        return [
            'email' => 'nullable|email',
            'ip_address' => 'nullable|ip',
            'language' => 'nullable|string|max:2',
            'blocked' => 'nullable',
        ];
    }

    private function parseBlocked(mixed $blocked): bool
    {
        if (is_bool($blocked)) {
            return $blocked;
        }

        if (is_numeric($blocked)) {
            return (int) $blocked === 1;
        }

        if (is_string($blocked)) {
            return in_array(strtolower(trim($blocked)), ['yes', '1', 'true', 'y'], true);
        }

        return false;
    }
}
