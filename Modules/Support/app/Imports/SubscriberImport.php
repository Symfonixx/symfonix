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
        // Match export format: ID, Email, IP Address, Language, Blocked, Created At
        // Email is required, others are optional
        $email = $row['email'] ?? $row['Email'] ?? null;
        
        if (!$email) {
            return null; // Skip rows without email
        }
        
        // Check if subscriber with this email already exists
        $subscriber = Subscriber::where('email', $email)->first();
        
        // Get values matching export format exactly
        $ipAddress = $row['ip_address'] ?? $row['IP Address'] ?? null;
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
            'ip_address' => $ipAddress,
            'lang' => $lang ?? 'en',
            'blocked' => $isBlocked,
        ]);
    }

    public function rules(): array
    {
        return [
            'email' => 'required|email',
            'Email' => 'required|email',
            'ip_address' => 'nullable|ip',
            'IP Address' => 'nullable|ip',
            'language' => 'nullable|string|max:2',
            'Language' => 'nullable|string|max:2',
            'lang' => 'nullable|string|max:2',
            'blocked' => 'nullable',
            'Blocked' => 'nullable',
        ];
    }
}

