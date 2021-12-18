<?php

namespace Amuz\XePlugin\AuthAPI;

use Amuz\XePlugin\AuthAPI\Models\ApiKey;
use Auth;

class AuthApiService
{
    /**
     * Error messages
     */
    const MESSAGE_ERROR_INVALID_NAME_FORMAT = '앱id는 소문자 알파벳과 하이픈, 숫자로만 구성해야 하고, 255자를 넘길 수 없습니다.';
    const MESSAGE_ERROR_NAME_ALREADY_USED   = '이미 등록된 앱 이름입니다.';

    private $services = [];

    public function __construct(){
        $this->setServices();
    }

    public function getServices(){
        return $this->services;
    }

    public function setServices(){
        $registeredServices = \XeRegister::get('authapi/services') ?: [];
        if(count($registeredServices) < 1) return null;

        $children = [];
        //그룹이 나중에 선언될 가능성이 있어서 children을 따로 저장했다가 나중에 붙임
        foreach($registeredServices as $key => $service){
            $service['id'] = $key;
            $group_id = explode(".",$key);
            $service['is_use'] = "Y";
            if(count($group_id) > 1){
                if(!isset($childs[$group_id[0]])) $childs[$group_id[0]] = [];
                $children[$group_id[0]][$group_id[1]] = $service;
            }else{
                $this->services[$group_id[0]] = $service;
            }
        }
        foreach($children as $group_id => $services) $this->services[$group_id]["children"] = $services;
    }

    public function listApiKeys($userId = null, $siteKey = false, $withTrashed = false){
        $siteKey = $siteKey ?: \XeSite::getCurrentSiteKey();
        $apiKeys = ApiKey::where('site_key',$siteKey);

        if($withTrashed) $apiKeys->withTrashed();

        if (!is_null($userId) || Auth::check()) $apiKeys->where('user_id', Auth::id());

        return $apiKeys->orderBy('name')->get();
    }

    public function generate($appName, $siteKey = false){
        $siteKey = $siteKey ?: \XeSite::getCurrentSiteKey();

        $apiKey       = new ApiKey;
        $apiKey->name = $appName;
        $apiKey->key  = ApiKey::generate();
        $apiKey->secret  = ApiKey::genSecret();
        if(Auth::check()) $apiKey->user_id = Auth::id();
        $apiKey->site_key = $siteKey;
        $apiKey->save();

        return $apiKey;
    }

    public function toggleApiKey($appId,$active = false){
        $apiKey = ApiKey::find($appId);

        if($apiKey->user_id != Auth::id() && !$this->isSuper() && !$this->isManager()){
            throw new \Exception("잘못된 키 접근이 시도 되었습니다.");
        }

        $apiKey->active = $active ?: $apiKey->active == 0 ? 1 : 0;
        $apiKey->save();
        return $appId;
    }

    public function deleteApiKey($appId){
        $apiKey = ApiKey::find($appId);

        if($apiKey->user_id != Auth::id() && !$this->isSuper() && !$this->isManager()){
            throw new \Exception("잘못된 키 삭제를 시도했습니다.");
        }
        $apiKey->delete();
        return $appId;
    }

    /**
     * Validate name
     *
     * @param string $name
     * @return string
     */
    public function validateName($name,$site_key = null)
    {
        if (!ApiKey::isValidName($name)) {
            return self::MESSAGE_ERROR_INVALID_NAME_FORMAT;
        }
        if (ApiKey::nameExists($name,$site_key)) {
            return self::MESSAGE_ERROR_NAME_ALREADY_USED;
        }
        return null;
    }

    private function isManager(){
        return Auth::user()->getRating() == 'manager';
    }

    private function isSuper(){
        return Auth::user()->getRating() == 'super';
    }
}