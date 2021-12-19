<?php
namespace Amuz\XePlugin\AuthAPI;

use Amuz\XePlugin\AuthAPI\Models\ApiKey;
use XeFrontend;
use XePresenter;
use Xpressengine\Http\Request;
use App\Http\Controllers\Controller as BaseController;

class Controller extends BaseController
{

    private $site_key;

    public function __construct(){
        $this->site_key = \XeSite::getCurrentSiteKey();
    }

    public function createKey(Request $request, $appId = false)
    {
        //auth api service 에서는 로그인하지않아도 키생성이 가능하게 되어있으므로 여기서 체크해주어야함
        if(!\Auth::check()){
            return redirect()->route('user.settings', ['section' => 'apiAppCreate'])
                ->with('alert', ['type' => 'failed', 'message' => '로그인 세션정보가 잘못 되었습니다.']);
        }

        $authApiService = app('amuz.authapi');
        $name = $request->get('app_name');

        if($appId){
            $apiKeys = $authApiService->listApiKeys();
            $selectedApp = $apiKeys->filter(function($item) use ($appId){
                return $item->id == $appId;
            })->first();

            if($selectedApp == null){
                $appId = false;
            }else{
                if($name != $selectedApp->name){
                    $error = $authApiService->validateName($name,$this->site_key);
                    if ($error) {
                        return redirect()->route('user.settings', ['section' => 'apiAppCreate','appId' => $appId])
                            ->with('alert', ['type' => 'failed', 'message' => 'App NAME이 잘못되었습니다.']);
                    }
                }

                $selectedApp->name = $name;
                $selectedApp->active = $request->get('active','Y') == "Y" ? 1 : 0;
                $selectedApp->allow_ip = json_enc(array_filter($request->get('allow_ips',[])));
                $selectedApp->allow_services = json_enc($request->get('allow_services',[]));
                $selectedApp->save();
                $message = 'API APP이 정상적으로 수정 되었습니다.';

                return redirect()->route('user.settings', ['section' => 'apiAppCreate','appId' => $appId])
                    ->with('alert', ['type' => 'success', 'message' => $message]);
            }
        }

        if(!$appId){
            $error = $authApiService->validateName($name,$this->site_key);
            if ($error) {
                return redirect()->route('user.settings', ['section' => 'apiAppCreate'])
                    ->with('alert', ['type' => 'failed', 'message' => 'App NAME이 잘못되었습니다.']);
            }
            $apiKey = $authApiService->generate($name,$this->site_key);
            $message = 'API APP이 정상적으로 생성 되었습니다.';
        }

        return redirect()->route('user.settings', ['section' => 'authApi'])
            ->with('alert', ['type' => 'success', 'message' => $message]);
    }

    public function deleteKey(Request $request, $appId = false)
    {
        //auth api service 에서는 로그인하지않아도 키생성이 가능하게 되어있으므로 여기서 체크해주어야함
        if(!\Auth::check()){
            return redirect()->route('sms_provider::restapi')
                ->with('alert', ['type' => 'failed', 'message' => '로그인 세션정보가 잘못 되었습니다.']);
        }

        $authApiService = app('amuz.authapi');
        //이렇게만 보내면 서비스 내에서 user_id를 체크하고, 관리자인경우 통과시켜줌.
        $authApiService->deleteApiKey($appId);

        return redirect()->route('user.settings', ['section' => 'authApi'])
            ->with('alert', ['type' => 'success', 'message' => '정상적으로 삭제가 완료되었습니다.']);
    }
}
