<?php

namespace Amuz\XePlugin\AuthAPI\Commands;

use Amuz\XePlugin\AuthAPI\Models\ApiKey;
use Illuminate\Console\Command;

class DeleteApiKey extends Command
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
    protected $signature = 'xe-plugin:apikey-delete {AppName} {site_key=default : 구동할 사이트 키를 입력합니다.} ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'AppName으로 키를 삭제합니다.';
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

        $confirmMessage = '정말 \'' . $name . '\' 키를 삭제할까요?';

        if (!$this->confirm($confirmMessage)) {
            return;
        }

        $key = ApiKey::where('name', $name)->where('site_key',$this->site_key)->first();
        $key->delete();

        $this->info('Deleted key: ' . $name);
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
