<?php

namespace Modules\Notification\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Notification\App\Models\NotificationType;

class NotificationDatabaseSeeder extends Seeder
{
    public function run()
    {
        $types = config('notification.default_types', []);
        
        foreach ($types as $type) {
            NotificationType::firstOrCreate(
                ['slug' => $type['slug']],
                $type
            );
        }
    }
}