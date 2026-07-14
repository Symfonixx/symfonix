<?php

namespace Modules\Support\app\Imports;

use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Maatwebsite\Excel\Concerns\WithValidation;
use Modules\Support\Models\Subscriber;

class SubscriberImport implements ToModel, WithHeadingRow, WithValidation
{
    public function model(array $row)
    {
        // WithHeadingRow slugifies headers: Email → email, IP Address → ip_address, etc.
        $email = $row['email'] ?? $row['Email'] ?? null;

        if (! $email) {
            return null; // Skip rows without email
        }

        // Check if subscriber with this email already exists
        $subscriber = Subscriber::where('email', $email)->first();

        $ipAddress = $row['ip_address'] ?? $row['IP Address'] ?? null;
        $ipAddress = filled($ipAddress) ? trim((string) $ipAddress) : null;
        $lang = $row['language'] ?? $row['Language'] ?? $row['lang'] ?? 'en';

        // Handle blocked field - can be Yes/No, 1/0, true/false
        $blocked = $row['blocked'] ?? $row['Blocked'] ?? 'No';
        $isBlocked = false;
        if (is_string($blocked)) {
            $blockedLower = strtolower(trim($blocked));
            $isBlocked = in_array($blockedLower, ['yes', '1', 'true', 'y']);
        } elseif (is_numeric($blocked)) {
            $isBlocked = $blocked == 1;
        } elseif (is_bool($blocked)) {
            $isBlocked = $blocked;
        }

        if ($subscriber) {
            // Update existing subscriber (ignore ID and Created At from import)
            $subscriber->update([
                'ip_address' => $ipAddress ?? $subscriber->ip_address,
                'lang' => $lang ?? $subscriber->lang ?? 'en',
                'blocked' => $isBlocked,
            ]);

            return null; // Don't create a new model
        }

        // Create new subscriber (ignore ID and Created At from import)
        return new Subscriber([
            'email' => $email,
            'ip_address' => $ipAddress ?? '0.0.0.0',
            'lang' => $lang ?? 'en',
            'blocked' => $isBlocked,
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
}
