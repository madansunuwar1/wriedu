<?php
namespace App\Services;

use App\Models\User;

class DataProtectionService
{
    // Encrypt sensitive data before sending to frontend
    public function protectSensitiveData($data, $userRole)
    {
        if (!$this->canViewFullData($userRole)) {
            return $this->obfuscateData($data);
        }

        // Add watermark information
        $data['_security'] = [
            'watermark' => $this->generateWatermark(auth()->user()),
            'timestamp' => time(),
            'session_id' => session()->getId()
        ];

        return $data;
    }

    protected function canViewFullData($userRole)
    {
        $allowedRoles = ['administrator', 'manager', 'senior_staff'];
        return in_array($userRole, $allowedRoles);
    }

    protected function obfuscateData($data)
    {
        // Implement field-level obfuscation
        $sensitiveFields = ['email', 'phone', 'passport_number', 'address'];
        
        if (is_array($data)) {
            foreach ($data as $key => $value) {
                if (in_array($key, $sensitiveFields)) {
                    $data[$key] = $this->maskString($value);
                } elseif (is_array($value)) {
                    $data[$key] = $this->obfuscateData($value);
                }
            }
        }

        return $data;
    }

    protected function maskString($value)
    {
        if (strlen($value) <= 4) {
            return str_repeat('*', strlen($value));
        }

        $start = substr($value, 0, 2);
        $end = substr($value, -2);
        $middle = str_repeat('*', strlen($value) - 4);

        return $start . $middle . $end;
    }

    protected function generateWatermark($user)
    {
        return base64_encode(json_encode([
            'user_id' => $user->id,
            'name' => $user->name,
            'email' => $user->email,
            'timestamp' => time(),
            'ip' => request()->ip()
        ]));
    }
}