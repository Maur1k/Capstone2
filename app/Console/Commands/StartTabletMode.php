<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class StartTabletMode extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'tablet:start';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Start the application in tablet mode (serving on local network IP)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info("Initializing Tablet Mode...");

        // Simply serve on 0.0.0.0 to expose to all interfaces
        $host = '0.0.0.0';
        $port = 8000;

        $ip = gethostbyname(gethostname());
        // Attempt to find a better IP if the default is localhost
        if ($ip == '127.0.0.1') {
            // Fallback attempt to get local IP (Windows specific)
            exec("ipconfig", $output);
            foreach ($output as $line) {
                if (preg_match('/IPv4 Address.*: (.*)/', $line, $matches)) {
                    $ip = trim($matches[1]);
                    break;
                }
            }
        }

        $url = "http://{$ip}:{$port}";

        $this->line("");
        $this->info("🚀  Tablet Mode Server Starting...");
        $this->line("");
        $this->comment("📢  Access from your tablet using this URL:");
        $this->info("    {$url}");
        $this->line("");
        $this->comment("📝  Note: Ensure your tablet is connected to the same Wi-Fi network.");
        $this->line("");

        // Pass through the artisan serve command
        $this->call('serve', [
            '--host' => $host,
            '--port' => $port
        ]);
    }
}
