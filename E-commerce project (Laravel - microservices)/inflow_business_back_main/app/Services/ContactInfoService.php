<?php

namespace App\Services;

use App\Data\ChiefInfoData;
use App\Models\AppSetting;
use App\Models\SocialLink;
use App\Models\Tenant;
use App\Models\User;

final class ContactInfoService
{
    protected Tenant $tenant;

    public function __construct()
    {
        /** @var User $user */
        $user = auth('api')->user();
        /** @var Tenant $tenant */
        $tenant = $user->tenant;
        $tenant->makeCurrent();
        $this->tenant = $tenant;
    }

    public function updateContactInfo(array $data): Tenant
    {
        $chiefContactInfo = ChiefInfoData::from($data);

        $this->tenant->update([
            'email' => $chiefContactInfo->email,
            'phone' => $chiefContactInfo->phone,
        ]);

        $this->updateSocialLinks($chiefContactInfo->social_links);

        return $this->tenant;
    }

    private function updateSocialLinks(array $socialLinks): void
    {
        $incomingNetworks = array_column($socialLinks, 'network');

        foreach ($socialLinks as $socialLink) {
            SocialLink::updateOrCreate([
                'network' => $socialLink['network'],
            ], [
                'link'         => $socialLink['link'],
                'android_link' => $socialLink['link'],
                'ios_link'     => $socialLink['link'],
            ]);
        }

        SocialLink::whereNotIn('network', $incomingNetworks)->delete();
    }
}
