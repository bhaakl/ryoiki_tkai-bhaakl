<?php

namespace App\Traits;

trait HelperTrait
{
    public function endingByNumber(int $n, array $titles)
    {
        $cases = array(2, 0, 1, 1, 1, 2);

        return $titles[($n % 100 > 4 && $n % 100 < 20) ? 2 : $cases[min($n % 10, 5)]];
    }

    public function getTenantDomain()
    {
        $app_url = (explode('://', config('app.url')));
        $url = $app_url[0] . '://' . app('currentTenant')->domain;

        return $url;
    }
}
