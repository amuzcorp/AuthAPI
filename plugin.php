<?php
namespace Amuz\XePlugin\AuthAPI;

use Amuz\XePlugin\AuthAPI\Commands\ActivateApiKey;
use Amuz\XePlugin\AuthAPI\Commands\DeactivateApiKey;
use Amuz\XePlugin\AuthAPI\Commands\DeleteApiKey;
use Amuz\XePlugin\AuthAPI\Commands\GenerateApiKey;
use Amuz\XePlugin\AuthAPI\Commands\ListApiKeys;
use Amuz\XePlugin\AuthAPI\Middleware\AuthorizeApiKey;
use Route;
use Schema;
use Xpressengine\Plugin\AbstractPlugin;

class Plugin extends AbstractPlugin
{
    /**
     * 이 메소드는 활성화(activate) 된 플러그인이 부트될 때 항상 실행됩니다.
     *
     * @return void
     */
    public function boot()
    {
        // implement code
        app('router')->aliasMiddleware('auth.apikey', AuthorizeApiKey::class);
        $this->route();
    }

    protected function route()
    {
        // implement code

        Route::fixed(
            $this->getId(),
            function () {
                Route::get('/', [
                    'as' => 'AuthAPI::index','uses' => 'Amuz\XePlugin\AuthAPI\Controller@index'
                ]);
            }
        );

    }

    /**
     * 추가된 커맨드라인 구문. artisan 내에서 handle을 실행할 수 있는 클래스를 등록 해 준다.
     * command abstract class를 상속해야하고
     * 사이트키 구분이 필요한경우 내부에서 로직을 따로 구현해야한다.
     * 반드시 Illuminate\Console\Command를 상속받은 클래스를 리턴해야 하며, 여러개가 필요한경우 배열로 리턴할 수 있다.
     */
    public function commandClass($site_key = 'default'){
        return [ActivateApiKey::class, DeactivateApiKey::class, DeleteApiKey::class, GenerateApiKey::class, ListApiKeys::class];
    }

    /**
     * 플러그인이 활성화될 때 실행할 코드를 여기에 작성한다.
     *
     * @param string|null $installedVersion 현재 XpressEngine에 설치된 플러그인의 버전정보
     *
     * @return void
     */
    public function activate($installedVersion = null)
    {
        // implement code
    }

    /**
     * 플러그인을 설치한다. 플러그인이 설치될 때 실행할 코드를 여기에 작성한다
     *
     * @return void
     */
    public function install()
    {
        $migration = new ApiMigration();
        if(Schema::hasTable('api_keys') === false){
            $migration->up();
        }
    }

    /**
     * 해당 플러그인이 설치된 상태라면 true, 설치되어있지 않다면 false를 반환한다.
     * 이 메소드를 구현하지 않았다면 기본적으로 설치된 상태(true)를 반환한다.
     *
     * @return boolean 플러그인의 설치 유무
     */
    public function checkInstalled()
    {
        if(Schema::hasTable('api_keys') === false) return false;

        return parent::checkInstalled();
    }

    /**
     * 플러그인을 업데이트한다.
     *
     * @return void
     */
    public function update()
    {
        $migration = new ApiMigration();
        if(Schema::hasTable('api_keys') === false){
            $migration->up();
        }
    }

    /**
     * 해당 플러그인이 최신 상태로 업데이트가 된 상태라면 true, 업데이트가 필요한 상태라면 false를 반환함.
     * 이 메소드를 구현하지 않았다면 기본적으로 최신업데이트 상태임(true)을 반환함.
     *
     * @return boolean 플러그인의 설치 유무,
     */
    public function checkUpdated()
    {
        if(Schema::hasTable('api_keys') === false) return false;

        return parent::checkUpdated();
    }
}
