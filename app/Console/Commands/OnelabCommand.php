<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Artisan;

class OnelabCommand extends Command
{
    protected $signature = 'onelab:installation
    {--database= : The name of the database}
    {--username=root : The database username}
    {--password= : The database password}';

    protected $description = 'Install and configure ONELAB';

    public function handle()
    {
        $this->install();
        $this->configureDatabase();

        Artisan::call('key:generate');
        Artisan::call('optimize');

        $this->info('Configuration Installed | Welcome to ONELAB Application | Kradworkz');
    }

    protected function install(){
        $sourceFilePath = base_path('.env.example');
        $destinationFilePath = base_path('.env');

        if (!File::exists($sourceFilePath)) {
            $this->error('.env.example file not found.');
            return;
        }
    }

    protected function configureDatabase(){
        $database = $this->option('database');
        $username = $this->option('username') ?: 'root';
        $password = $this->option('password') ?: '';

        if (empty($database)) {
            $this->error('Database name is required.');
            return;
        }

        $this->updateEnvVariable('DB_DATABASE', $database);
        $this->updateEnvVariable('DB_USERNAME', $username);
        $this->updateEnvVariable('DB_PASSWORD', $password);

        $this->updateEnvVariable('LABORATORY_ID', '');
        $this->updateEnvVariable('API_LINK', '');
        $this->updateEnvVariable('API_KEY', '');
    }

    protected function updateEnvVariable($key, $value)
    {
        $envFilePath = base_path('.env');
        $envContent = File::get($envFilePath);
        $updatedEnvContent = preg_replace("/^{$key}=.*/m", "$key=$value", $envContent);
    
        if (strpos($envContent, "{$key}=") === false) {
            if ($key == 'LABORATORY_ID') {
                $updatedEnvContent .= "\n";
            }
            $updatedEnvContent .= "{$key}={$value}\n";
        }
        File::put($envFilePath, $updatedEnvContent);
    }
}
