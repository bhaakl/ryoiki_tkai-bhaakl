<?php

namespace App\Services;

use App\Models\AndroidSetting;
use App\Models\AppSetting;
use App\Models\BonusLevel;
use App\Models\IosSetting;
use App\Models\Media;
use Illuminate\Support\Facades\DB;

class ApplicationService
{
    public function __construct(
        public AppSetting $app,
    ) {
    }

    public function show()
    {
        return $this->app;
    }

    public function update(array $params): AppSetting
    {
        DB::beginTransaction();
        $this->app->update($params);
        app('currentTenant')->update([
            'name' => $params['company_name'],
        ]);
        DB::commit();

        return $this->app;
    }

    public function updateIosSettings(array $params): void
    {
        $iosSetting = IosSetting::query()->first();
        $iosSetting->fill($params);

        DB::beginTransaction();
        $iosSetting->save();

        $this->updateMediaCollection(Media::IOS_COLLECTIONS, $iosSetting);

        DB::commit();
    }

    public function updateAndroidSettings(array $params): void
    {
        $androidSetting = AndroidSetting::query()->first();
        $androidSetting->fill($params);

        DB::beginTransaction();
        $androidSetting->save();

        $this->updateMediaCollection(Media::ANDROID_COLLECTIONS, $androidSetting);

        DB::commit();
    }

    private function updateMediaCollection(array $mediaCollection, IosSetting|AndroidSetting $settings): void
    {
        foreach (Media::IOS_COLLECTIONS as $collection) {
            if (empty($params["{$collection}_media_ids"])) {
                continue;
            }
            $existingMediaIds = $settings->getMedia($collection)->pluck('id')->toArray();
            $mediaIdsToRemove = array_diff($existingMediaIds, $params["{$collection}_media_ids"]);
            if (!empty($mediaIdsToRemove)) {
                Media::query()->whereIn('id', $mediaIdsToRemove)->delete();
            }
            Media::query()
                ->whereIn('id', $params["{$collection}_media_ids"])
                ->update([
                    'model_id' => $settings->id,
                    'model_type' => $settings::class,
                    'collection_name' => $collection,
                ]);
        }
    }
}
