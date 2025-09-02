<?php

namespace App\Enum\User;

enum UserTypeEnum: string
{
    case ADMIN = 'admin';
    case USER = 'user';
    case TEACHER = 'teacher';
    case STUDENT = 'student';

    /**
     * Get all enum values as an array
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }

    /**
     * Get enum labels for display
     */
    public function label(): string
    {
        return match($this) {
            self::ADMIN => 'Administrator',
            self::USER => 'Regular User',
            self::TEACHER => 'Teacher',
            self::STUDENT => 'Student',
        };
    }

    /**
     * Check if the user type is admin
     */
    public function isAdmin(): bool
    {
        return $this === self::ADMIN;
    }

    /**
     * Check if the user type is teacher
     */
    public function isTeacher(): bool
    {
        return $this === self::TEACHER;
    }

    /**
     * Check if the user type is student
     */
    public function isStudent(): bool
    {
        return $this === self::STUDENT;
    }
}
