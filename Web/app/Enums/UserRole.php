<?php

namespace App\Enums;

class UserRole 
{
    public const ADMIN = 'admin';
    public const SISWA = 'siswa';

    public static function values(): array 
    {
        return [
            self::ADMIN,
            self::SISWA
        ];
    }

    public static function labels(): array 
    {
        return [
            self::ADMIN => 'Administrator',
            self::SISWA => 'Siswa'
        ];
    }
} 