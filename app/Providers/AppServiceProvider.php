<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;

class AppServiceProvider extends ServiceProvider
{
    public function register()
    {
        //
    }

    public function boot()
    {
        Blade::directive('vite', function ($expression) {
            return "<?php
                \$manifestPath = public_path('build/manifest.json');
                \$devServer = (config('app.env') === 'local' && !file_exists(\$manifestPath)) ? 'http://127.0.0.1:5173' : null;
                \$assets = {$expression};

                if (!is_array(\$assets)) {
                    \$assets = [\$assets];
                }

                if (\$devServer) {
                    echo '<script type=\"module\" src=\"' . \$devServer . '/@vite/client\"></script>' . PHP_EOL;
                    foreach (\$assets as \$asset) {
                        echo '<script type=\"module\" src=\"' . \$devServer . '/' . \$asset . '\"></script>' . PHP_EOL;
                    }
                } elseif (file_exists(\$manifestPath)) {
                    \$manifest = json_decode(file_get_contents(\$manifestPath), true);
                    foreach (\$assets as \$asset) {
                        if (!isset(\$manifest[\$asset])) continue;
                        \$entry = \$manifest[\$asset];
                        if (!empty(\$entry['css'])) {
                            foreach (\$entry['css'] as \$css) {
                                echo '<link rel=\"stylesheet\" href=\"' . asset('build/' . \$css) . '\">' . PHP_EOL;
                            }
                        }
                        if (!empty(\$entry['file'])) {
                            echo '<script type=\"module\" src=\"' . asset('build/' . \$entry['file']) . '\"></script>' . PHP_EOL;
                        }
                    }
                }
            ?>";
        });
    }
}
