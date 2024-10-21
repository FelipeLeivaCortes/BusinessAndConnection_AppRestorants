<?php

namespace App\Http\Controllers\Install;

use App\Http\Controllers\Controller;
use App\Utilities\Installer;
use Illuminate\Support\Facades\Artisan;

class UpdateController extends Controller {

    public function update_migration() {
        $app_version = '1.5';
        
        Artisan::call('migrate', ['--force' => true]);

        //Update Version Number
        Installer::updateEnv([
            'APP_VERSION' => $app_version,
        ]);

        update_option('APP_VERSION', $app_version);
        echo "Migration Updated Sucessfully";
    }
}
