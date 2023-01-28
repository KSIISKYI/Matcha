<?php

namespace App\Service;

use DI\Container;

use App\Models\DiscoverySetting;

class DiscoverySettingsService
{
    static function update(Container $container, array $data)
    {
        $discovery_settings = $container->get('user')->profile->discovery_settings;
        $data['gender_id'] = $data['gender'];

        $discovery_settings->update($data);
        $discovery_settings->interests()->sync($data['interests']);

        $discovery_settings->save();
    }
}