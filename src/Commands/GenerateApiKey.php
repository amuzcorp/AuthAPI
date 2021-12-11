<?php

namespace Amuz\XePlugin\AuthAPI\Commands;

use Amuz\XePlugin\AuthAPI\Models\ApiKey;
use Illuminate\Console\Command;

class GenerateApiKey extends Command
{
    /**
     * Error messages
     */
    const MESSAGE_ERROR_INVALID_NAME_FORMAT = 'Invalid name.  Must be a lowercase alphabetic characters, numbers and hyphens less than 255 characters long.';
    const MESSAGE_ERROR_NAME_ALREADY_USED   = 'Name is unavailable.';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'xe-plugin:apikey-generate {AppName} {site_key=default : 구동할 사이트 키를 입력합니다.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '새로운 API키를 생성합니다.';
    protected $site_key;

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = $this->argument('AppName');
        $this->site_key = $this->argument('site_key');

        $error = $this->validateName($name,$this->site_key);

        if ($error) {
            $this->error($error);
            return;
        }

        $apiKey       = new ApiKey;
        $apiKey->name = $name;
        $apiKey->key  = ApiKey::generate();
        $apiKey->site_key = $this->site_key;
        $apiKey->save();

        $this->info('API key created');
        $this->info('AppName: ' . $apiKey->name);
        $this->info('Key: '  . $apiKey->key);
        $this->info('Site: '  . $this->site_key);
    }

    /**
     * Validate name
     *
     * @param string $name
     * @return string
     */
    protected function validateName($name,$site_key = null)
    {
        if (!ApiKey::isValidName($name)) {
            return self::MESSAGE_ERROR_INVALID_NAME_FORMAT;
        }
        if (ApiKey::nameExists($name,$site_key)) {
            return self::MESSAGE_ERROR_NAME_ALREADY_USED;
        }
        return null;
    }
}
