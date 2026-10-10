<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;

class CreateStorageLink extends Command
{
    protected $signature = 'storage:link-custom';
    protected $description = 'Create storage symlink if it does not exist';

    public function handle()
    {
        $target = storage_path('app/public');
        $link = public_path('storage');

        if (file_exists($link)) {
            $this->info('Storage symlink already exists.');
            return 0;
        }

        if (!file_exists($target)) {
            File::makeDirectory($target, 0755, true);
            $this->info('Created storage/app/public directory.');
        }

        if (PHP_OS_FAMILY === 'Windows') {
            // Windows: Use junction or symlink
            if (is_dir($target)) {
                exec("mklink /J \"$link\" \"$target\"", $output, $return);
                if ($return === 0) {
                    $this->info('Storage symlink created successfully (Windows junction).');
                    return 0;
                }
            }
        } else {
            // Unix/Linux: Use symlink
            if (symlink($target, $link)) {
                $this->info('Storage symlink created successfully.');
                return 0;
            }
        }

        $this->error('Failed to create storage symlink.');
        $this->info('Please run: php artisan storage:link');
        return 1;
    }
}

