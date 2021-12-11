<?php

namespace Amuz\XePlugin\AuthAPI\Commands;

use Amuz\XePlugin\AuthAPI\Models\ApiKey;
use Illuminate\Console\Command;

class DeactivateApiKey extends Command
{
    /**
     * Error messages
     */
    const MESSAGE_ERROR_INVALID_NAME        = 'Invalid name.';
    const MESSAGE_ERROR_NAME_DOES_NOT_EXIST = 'Name does not exist.';

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'xe-plugin:apikey-deactivate {AppName} {site_key=default : 구동할 사이트 키를 입력합니다.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'AppName으로 앱 키를 비활성화 합니다.';
    protected $site_key;

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = $this->argument('AppName');
        $this->site_key = $this->argument('site_key');

        $error = $this->validateName($name);

        if ($error) {
            $this->error($error);
            return;
        }

        $key = ApiKey::where('name', $name)->where('site_key',$this->site_key)->first();

        if (!$key->active) {
            $this->info('이 키는 이미 비활성화 되어 있습니다.');
            return;
        }

        $key->active = 0;
        $key->save();

        $this->info('Deactivated key: ' . $name);
    }

    /**
     * Validate name
     *
     * @param string $name
     * @return string
     */
    protected function validateName($name)
    {
        if (!ApiKey::isValidName($name)) {
            return self::MESSAGE_ERROR_INVALID_NAME;
        }
        if (!ApiKey::nameExists($name)) {
            return self::MESSAGE_ERROR_NAME_DOES_NOT_EXIST;
        }
        return null;
    }
}
