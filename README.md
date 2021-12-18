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

## php artisan
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