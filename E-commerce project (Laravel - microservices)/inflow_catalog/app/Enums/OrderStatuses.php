<?php

namespace App\Enums;

enum OrderStatuses: string
{
    case CREATED = 'created';
    case PROCESSING = 'processing';
    case DELIVERING = 'delivering';
    case DONE = 'done';
    case CANCELED = 'canceled';

    public function defaultName(): string {
        return match($this) {
            self::CREATED => 'Создан',
            self::PROCESSING => 'В работе',
            self::DELIVERING => 'Передан в доставку',
            self::DONE => 'Завершён',
            self::CANCELED => 'Отменён',
        };
    }

    public function isInitial(): bool {
        return match($this) {
            self::CREATED => true,
            default => false
        };
    }

    public function isFinal(): bool {
        return match($this) {
            self::DONE, self::CANCELED => true,
            default => false
        };
    }

    public function isSuccessful(): bool {
        return match($this) {
            self::DONE => true,
            default => false
        };
    }

    public function isCanceled(): bool {
        return match($this) {
            self::CANCELED => true,
            default => false
        };
    }
}
