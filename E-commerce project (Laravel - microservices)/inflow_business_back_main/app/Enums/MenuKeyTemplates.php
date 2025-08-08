<?php

namespace App\Enums;

enum MenuKeyTemplates: string
{
    case SETTINGS = 'settings';
    case ABOUT_COMPANY = 'about_company';
    case ABOUT_APP = 'about_app';
    case BRANCHES = 'branches';
    case HTML_TEXT = 'html_text';

    public function systemName(): string
    {
        return match($this) {
            self::SETTINGS => 'Настройки',
            self::ABOUT_COMPANY => 'О компании',
            self::ABOUT_APP => 'О приложении',
            self::BRANCHES => 'Наши офисы',
            self::HTML_TEXT => 'Текстовая страница',
        };
    }

    public function renameable(): bool
    {
        return match($this) {
            self::SETTINGS, self::ABOUT_APP => false,
            default => true
        };
    }

    public function isCustom(): bool
    {
        return match($this) {
            self::HTML_TEXT => true,
            default => false
        };
    }
}
