# AuthAPI
- XpressEngine3에서 REST API를 사용하는 App을 생성하고 Key를 관리할 수 있도록 도와줍니다.
- Command Line을 지원합니다.
- 멀티사이트 플러그인을 지원합니다.


## Service
아래와같이 AuthAPI의 서비스를 호출할 수 있습니다.
```php
$authApiService = app('amuz.authapi');
```
서비스는 아래와같은 기능을 제공합니다.

- 새로운 Key의 생성
- AppKey의 삭제
- AppKey의 활성화/비활성화 Toggle
- AppName의 유효성 검사


### 로그인된 사용자의 APP List
로그인 된 사용자의 AuthAPI App리스트를 반환합니다.
- 이 메서드의 파라미터들은 모두 기본값이 설정되어 있고, $userId가 전달되지 않으면 자동으로 로그인된 세션에서 ID를 가져옵니다.
- $siteKey가 전달되지 않으면 도메인을 기준으로 현재 접속한 사이트의 key를 가져옵니다.
- $withTrashed에 true 를 주입하면, SoftDelete된 App리스트를 모두 가져올 수 있습니다.
```php
$authApiService->listApiKeys($userId = null, $siteKey = false, $withTrashed = false);
```
### 허용된 IP 또는 도메인

### 허용된 REST API 서비스
#### 서비스 등록
REST API 서비스는 서드파티 플러그인이 필요에 의해 XeRegister를 통해 다음과 같이 AuthAPI 플러그인으로 등록을 요청할 수 있습니다.
```php
\XeRegister::push('authapi/services', 'group_id', [
    'title' => '권한그룹',
    'description' => '이 권한을 가지면 모든 하위 서비스권한을 갖는것과 같습니다.',
    'ordering' => 500
]);
\XeRegister::push('authapi/services', 'group_id.service_id', [
    'title' => '읽기권한',
    'description' => '읽기권한만 허용합니다.',
    'ordering' => 5
]);
```
#### 등록된 서비스의 조회
XeRegister를 통해 등록된 서비스를 모두 가져옵니다.
이 메서드는 그룹단위의 서비스 중첩구조를 모두 자동으로 만들어줍니다.
```php
$authApiService->getServices();
```
#### 서비스 권한의 확인
Eloquent ORM에 등록된 다음 메서드를 활용하여 API접근이 발견되는 경우 접근한 AppID가 해당 서비스에 허용된 권한을 가졌는지 확인할 수 있습니다.
```php
$apiKey = \Amuz\XePlugin\AuthAPI\Models\ApiKey::find(1);
$hasService = $apiKey->hasService($group_id,$service_id);
if(!$hasService){
    throw new Exception("허용되지 않은 접근");
}
```
이 동작은 자동으로 그룹의 권한을 가졌으면 그룹에 속한 모든 서비스의 권한을 가진것으로 간주하지만, 그룹의 권한만을 검사하려면 다음과 같이 사용할 수 있습니다.
```php
if($apiKey->hasGroup($group_id)){
    //allow all services in group
}
```

## 아티산 커맨드
- 효율적인 관리를 위한 아티산 커맨드를 지원합니다. 대부분의 커맨드는 SiteKey를 사용하여 멀티사이트를 지원하도록 설계되었지만 UserSession을 검사하지 않으므로 가능한 list 조회, 삭제 등을 제외하고는 사용하지 않을 것을 권장합니다.

php artisan command를 사용해 AppName으로 다음과 같이 RestAPI APP을 활성화 할 수 있습니다.
```php
xe-plugin:apikey-activate {AppName} {site_key=default}
```
php artisan command를 사용해 AppName으로 다음과 같이 RestAPI APP을 비활성화 할 수 있습니다.
```php
xe-plugin:apikey-deactivate {AppName} {site_key=default}
```
php artisan command를 사용해 AppName으로 다음과 같이 RestAPI APP을 삭제할 수 있지만, SoftDelete가 적용됩니다.
```php
xe-plugin:apikey-delete {AppName} {site_key=default}
```
php artisan command를 사용해 AppName으로 다음과 같이 RestAPI APP을 생성 합니다.
```php
xe-plugin:apikey-generate {AppName} {site_key=default}
```

php artisan command를 사용해 AppName으로 다음과 같이 RestAPI APP의 목록을 site별로 볼 수 있습니다.
```php
xe-plugin:apikey-list {site_key=default}
```