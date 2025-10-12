<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Artisan;

class ApiEnryptedCommand extends Command
{
    protected $signature = 'onelab:api {encrypted_data : Encrypted data for API configuration}';
    protected $description = 'Set the API configuration for ONELAB';
    
    public function handle()
    {
        $encryptedData = $this->argument('encrypted_data');
        $decryptedData = $this->simpleDecrypt($encryptedData);
        $parts = explode(' ', $decryptedData);

        $id = $parts[0];
        $link = $parts[1];
        $key = $parts[2];

        $this->updateEnvVariable('LABORATORY_ID', $id);
        $this->updateEnvVariable('API_LINK', $link);
        $this->updateEnvVariable('API_KEY', $key);
        Artisan::call('optimize');
        $this->info('API configuration set successfully!');
    }

    protected function updateEnvVariable($key, $value)
    {
        $envFilePath = base_path('.env');
        $envContent = File::get($envFilePath);
        $updatedEnvContent = preg_replace("/^{$key}=.*/m", "$key=$value", $envContent);
        File::put($envFilePath, $updatedEnvContent);
    }

    protected function simpleDecrypt($data) {
        $key = "KradWorkZ";
        $data = base64_decode($data);
        $result = '';
        for ($i = 0; $i < strlen($data); $i++) {
            $result .= $data[$i] ^ $key[$i % strlen($key)];
        }
        return $result;
    }
}
