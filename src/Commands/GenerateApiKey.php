<?php

namespace Amuz\XePlugin\AuthAPI\Commands;

use Amuz\XePlugin\AuthAPI\Models\ApiKey;
use Illuminate\Console\Command;

class GenerateApiKey extends Command
{

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
        $authApiService = app('amuz.authapi');
        $name = $this->argument('AppName');
        $this->site_key = $this->argument('site_key');

        $error = $authApiService->validateName($name,$this->site_key);

        if ($error) {
            $this->error($error);
            return;
        }

        $apiKey = $authApiService->generate($name,$this->site_key);

        $this->info('API key created');
        $this->info('AppName: ' . $apiKey->name);
        $this->info('Key: '  . $apiKey->key);
        $this->info('Site: '  . $this->site_key);
    }

}
