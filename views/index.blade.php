<h1>REST API</h1>
<p>REST API 사용을 위한 App을 생성하고 Key를 관리할 수 있습니다.</p>
<div class="setting-card">
    <h2>등록된 App</h2>

    <div class="appList">
        <a class="appCard" href="{{ route('user.settings', ['section' => 'apiAppCreate']) }}">
            <i class="xi-plus"></i>
            <h4>새 앱 추가</h4>
        </a>
        @foreach($apiKeys as $apiApp)
            <a class="appCard primary-border" href="{{ route('user.settings', ['section' => 'apiAppCreate','appId' => $apiApp->id]) }}">
                <i class="xi-icon">{{ subStr($apiApp->key,0,3) }}</i>
                <h4>{{ $apiApp->name }}</h4>
                <span class="badge active__{{$apiApp->active}}">{{ $apiApp->active === 1 ? "사용" : "사용중단" }}</span>
            </a>
        @endforeach

        <a class="appCard hidden" href="#"></a><a class="appCard hidden" href="#"></a>
    </div>
</div>
