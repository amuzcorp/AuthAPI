<?php

namespace Amuz\XePlugin\AuthAPI\Commands;

use Amuz\XePlugin\AuthAPI\Models\ApiKey;
use Illuminate\Console\Command;

class ListApiKeys extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'xe-plugin:apikey-list {--D|deleted} {site_key=default : 구동할 사이트 키를 입력합니다.}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '모든 API키 목록을 보여줍니다.';
    protected $site_key;

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->site_key = $this->argument('site_key');
        $keys = $this->option('deleted')
            ? ApiKey::withTrashed()->where('site_key',$this->site_key)->orderBy('name')->get()
            : ApiKey::where('site_key',$this->site_key)->orderBy('name')->get();

        if ($keys->count() === 0) {
            $this->info('There are no API keys');
            return;
        }

        $headers = ['Name', 'ID', 'Status', 'Status Date', 'Key'];

        $rows = $keys->map(function($key) {

            $status = $key->active    ? 'active'  : 'deactivated';
            $status = $key->trashed() ? 'deleted' : $status;

            $statusDate = $key->deleted_at ?: $key->updated_at;

            return [
                $key->name,
                $key->id,
                $status,
                $statusDate,
                $key->key
            ];

        });

        $this->table($headers, $rows);
    }
}
