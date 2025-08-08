<?php

namespace App\Observers;

use App\Jobs\UpdateLoyaltyTypeJob;
use App\Models\AppSetting;

class AppSettingObserver
{
    public function updating(AppSetting $appSetting): void
    {
        if ($appSetting->isClean('loyalty_type')) {
            return;
        }

        UpdateLoyaltyTypeJob::dispatch($appSetting);
    }
}
