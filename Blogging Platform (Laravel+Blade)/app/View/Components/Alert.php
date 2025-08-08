<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class Alert extends Component
{
    /**
     * Создание нового экземпляра компонента.
     *
     * @return void
     */
    public function __construct(
        public string $type,
        public ?string $dismissible = null
    ) {
        $this->type = $type;
        $this->dismissible = isset($dismissible) && $dismissible;
    }

    /**
     * Получение представления/содержимого, отображающего компонент.
     */
    public function render(): View
    {
        return view('components.alert');
    }
}
