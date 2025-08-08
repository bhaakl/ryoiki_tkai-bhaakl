<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class Icon extends Component
{
    /**
     * Создание нового экземпляра компонента.
     *
     * @return void
     */
    public function __construct(
        public string $name,
        public string $prefix = 'fa-solid'
    ) {
        $this->name = $name;
        $this->prefix = $prefix;
    }

    /**
     * Получение представления/содержимого, отображающего компонент.
     */
    public function render(): View
    {
        return view('components.icon');
    }
}
