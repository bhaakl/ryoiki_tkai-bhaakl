<?php

namespace App\Enums;

enum SocialNetworks: string
{
    case Vk = 'Vk';
    case Telegram = 'Telegram';
    case Youtube = 'Youtube';
    case WhatsApp = 'WhatsApp';
    case Rutube = 'Rutube';
    case Viber = 'Viber';

    public static function getAll(): array
    {
        return array_map(function ($case) {
            return [
                'title' => $case->value,
                'icon'  => $case->icon(),
            ];
        }, self::cases());
    }

    public static function random(): self
    {
        $count = count(self::cases()) - 1;

        return self::cases()[rand(0, $count)];
    }

    public function icon(): string
    {
        return match ($this) {
            SocialNetworks::Vk => config('app.url').'/images/icons/social/VK_Logo.png',
            SocialNetworks::Telegram => config('app.url').'/images/icons/social/VK_Logo.png',
            SocialNetworks::Youtube => config('app.url').'/images/icons/social/VK_Logo.png',
            SocialNetworks::WhatsApp => config('app.url').'/images/icons/social/VK_Logo.png',
            SocialNetworks::Rutube => config('app.url').'/images/icons/social/VK_Logo.png',
            SocialNetworks::Viber => config('app.url').'/images/icons/social/VK_Logo.png',
        };
    }

    public function iconAlt(): string
    {
        return match ($this) {
            SocialNetworks::Vk => config('app.url').'/images/icons/social/VK_Logo.png',
            SocialNetworks::Telegram => config('app.url').'/images/icons/social/VK_Logo.png',
            SocialNetworks::Youtube => config('app.url').'/images/icons/social/VK_Logo.png',
            SocialNetworks::WhatsApp => config('app.url').'/images/icons/social/VK_Logo.png',
            SocialNetworks::Rutube => config('app.url').'/images/icons/social/VK_Logo.png',
            SocialNetworks::Viber => config('app.url').'/images/icons/social/VK_Logo.png',
        };
    }
}
