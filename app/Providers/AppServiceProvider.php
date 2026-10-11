<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        spl_autoload_register(function ($class) {
            if ($class === 'Dompdf\\Cpdf') {
                $file = base_path('vendor/dompdf/dompdf/lib/Cpdf.php');
                if (file_exists($file)) {
                    require_once $file;
                    return;
                }
            }

            $prefixes = [
                'Dompdf\\' => base_path('vendor/dompdf/dompdf/src/'),
                'Masterminds\\' => base_path('vendor/masterminds/html5/src/'),
                'FontLib\\' => base_path('vendor/dompdf/php-font-lib/src/FontLib/'),
                'Svg\\' => base_path('vendor/dompdf/php-svg-lib/src/Svg/'),
                'Sabberworm\\CSS\\' => base_path('vendor/sabberworm/php-css-parser/src/'),
            ];

            foreach ($prefixes as $prefix => $baseDir) {
                $len = strlen($prefix);
                if (strncmp($prefix, $class, $len) === 0) {
                    $relativeClass = substr($class, $len);
                    $file = $baseDir . str_replace('\\', '/', $relativeClass) . '.php';
                    if (file_exists($file)) {
                        require_once $file;
                        return;
                    }
                }
            }
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
