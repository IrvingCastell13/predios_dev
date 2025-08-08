<?php

namespace App\Providers;

use Illuminate\Contracts\Foundation\Application; // <- Se asegura de usar la clase correcta
use Illuminate\Filesystem\FilesystemAdapter;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Storage;
use League\Flysystem\Local\LocalFilesystemAdapter;
use League\Flysystem\Filesystem;

class StorageServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        Storage::extend('s3-local', function (Application $app, array $config) {
            $adapter = new LocalFilesystemAdapter(
                $config['root']
            );

            $driver = new Filesystem($adapter);

            // Aquí está la corrección: pasamos el driver y el adapter correctos.
            return new class($driver, $adapter, $config) extends FilesystemAdapter {
                public function temporaryUrl($path, $expiration, array $options = [])
                {
                    // En lugar de fallar, simplemente devuelve una URL normal.
                    return $this->url($path);
                }
            };
        });
    }
}
