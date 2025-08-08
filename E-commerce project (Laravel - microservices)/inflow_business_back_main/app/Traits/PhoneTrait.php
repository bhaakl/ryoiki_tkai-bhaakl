<?php


namespace App\Traits;


trait PhoneTrait
{
    public function formatPhone($phone): string
    {
        $number = ltrim($phone, '+78');
        $number = preg_replace("/[^\d]/", "", $number);
        $number = substr($number, 0, 10);

        return preg_replace("/^1?(\d{3})(\d{3})(\d{2})(\d{2})$/", "7$1$2$3$4", $number);
    }
}
