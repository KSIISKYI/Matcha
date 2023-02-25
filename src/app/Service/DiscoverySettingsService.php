<?php

namespace App\Service;

use DI\Container;

class DiscoverySettingsService
{
    public static function update(Container $container, array $data)
    {
        $profile = $container->get('user')->profile;
        $discovery_settings = $container->get('user')->profile->discovery_settings;
        $data['gender_id'] = $data['gender'] ? $data['gender'] : null;

        $discovery_settings->update($data);
        $discovery_settings->interests()->sync($data['interests']);

        if ($data['position_x'] and $data['position_y']) {
            $profile->position_x = $data['position_x'];
            $profile->position_y = $data['position_y'];
            $profile->save();
        }
    
        $discovery_settings->save();
    }
}
