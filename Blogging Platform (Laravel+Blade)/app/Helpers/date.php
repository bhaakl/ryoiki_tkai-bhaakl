<?php

use Illuminate\Support\Carbon;

/**
 * Возвращает экземпляр Carbon.
 */
function carbon(string $parseString = '', ?string $tz = null): Carbon
{
    return new Carbon($parseString, $tz);
}

/**
 * Возвращает отформатированную дату Carbon.
 */
function humanize_date(Carbon $date, string $format = 'd F Y, H:i'): string
{
    return $date->format($format);
}
