<?php

namespace App\View\Components;

use Illuminate\View\Component;
use Illuminate\View\View;

class Card extends Component
{
    /**
     * Создание нового экземпляра компонента.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Получение представления/содержимого, отображающего компонент.
     */
    public function render(): View
    {
        return view('components.card');
    }
}
