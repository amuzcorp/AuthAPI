# AuthAPI
- XpressEngine3에서 REST API를 사용하는 App을 생성하고 Key를 관리할 수 있도록 도와줍니다.
- Command Line을 지원합니다.
- 멀티사이트 플러그인을 지원합니다.


### php artisan
php artisan command를 사용해 AppName으로 다음과 같이 RestAPI APP을 활성화 할 수 있습니다.
```
xe-plugin:apikey-activate {AppName} {site_key=default}
```
php artisan command를 사용해 AppName으로 다음과 같이 RestAPI APP을 비활성화 할 수 있습니다.
```
xe-plugin:apikey-deactivate {AppName} {site_key=default}
```
php artisan command를 사용해 AppName으로 다음과 같이 RestAPI APP을 삭제할 수 있지만, SoftDelete가 적용됩니다.
```
xe-plugin:apikey-delete {AppName} {site_key=default}
```
php artisan command를 사용해 AppName으로 다음과 같이 RestAPI APP을 생성 합니다.
```
xe-plugin:apikey-generate {AppName} {site_key=default}
```

php artisan command를 사용해 AppName으로 다음과 같이 RestAPI APP의 목록을 site별로 볼 수 있습니다.
```
xe-plugin:apikey-list {site_key=default}
```