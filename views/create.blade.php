<h1>{{ $selectedApp->id ? "앱 정보 수정" : "새로운 앱 생성" }}</h1>
<p>{{ $selectedApp->id ? $selectedApp->name . "을 수정합니다." : "REST API 사용을 위한 App을 생성합니다." }}</p>

<form method="post" action="{{route('AuthAPI::createKey',['appId' => $selectedApp->id])}}" enctype="multipart/form-data">
    {{ csrf_field() }}
<div class="setting-card">
        <div class="__xe_setting">
            @if($selectedApp->id)
            <div class="setting-group">
                <a href="#" class="__xe_editBtn">
                    <div class="setting-left">
                        <p>앱 이름</p><em class="text-gray">{{ $selectedApp->name }}</em>
                    </div>
                </a>
            </div>
            @endif
            <div class="setting-detail" {!! $selectedApp->id ? "style='display:none;'" : "" !!}>
                <div class="setting-detail-content">
                    <p>앱 이름 변경</p>
                    <em class="text-gray2">앱 이름은 영문,숫자 또는 하이픈(-)만을 사용하여 200자 이내로 입력해야 합니다.</em>
                    <input type="text" class="__xe_nameInput xe-form-control" name="app_name" value="{{ $selectedApp->name }}" />
                    <em class="__xe_message text-message"></em>
                </div>
            </div>

        </div>
        @if($selectedApp->id)
        <div class="__xe_setting">
            <div class="setting-group">
                <a href="#" class="__xe_editBtn">
                    <div class="setting-left">
                        <p>API Key 및 Secret</p><em class="text-gray">키를 확인하려면 클릭합니다.</em>
                    </div>
                </a>
            </div>
            <div class="setting-detail" style="display: none;">
                <div class="setting-detail-content">
                    <p>API Key</p>
                    <em class="text-gray2">대부분의 REST API는 MiddleWare에 의해 이 키를 요구받습니다.</em>
                    <div class="xe-input-group">
                        <span class="xe-input-group-addon" onclick="return doCopyToClipBoard(jQuery(this).next('input'))"><i class="xi-documents"></i></span>
                        <input type="text" class="xe-form-control" readonly="readonly" value="{{ $selectedApp->key }}">
                    </div>
                    <em class="__xe_message text-message"></em>

                    <p>API Secret</p>
                    <em class="text-gray2">모든 POST API는 반드시 Secret이 전달 되어야 합니다.</em>
                    <div class="xe-input-group">
                        <span class="xe-input-group-addon" onclick="return doCopyToClipBoard(jQuery(this).next('textarea'))"><i class="xi-documents"></i></span>
                        <textarea class="xe-form-control" readonly="readonly">{{ $selectedApp->secret }}</textarea>
                    </div>
                    <em class="__xe_message text-message"></em>
                </div>
            </div>
        </div>
        <div class="__xe_setting">
            <div class="setting-group">
                <a href="#" class="__xe_editBtn">
                    <div class="setting-left">
                        <p>활성화</p><em class="text-gray">{!! $selectedApp->active ? '사용중' : '사용중단' !!}</em>
                    </div>
                </a>
            </div>
            <div class="setting-detail" style="display: none;">
                <div class="setting-detail-content">
                    <p>활성화</p>
                    <em class="text-gray2">필요한 경우 이 앱의 사용을 중단할 수 있습니다.</em>
                    <div class="xe-form-inline">
                        <label class="xe-label">
                            <input type="radio" id="__xe_chk_mail_{{ $selectedApp->id }}" name="active" value="Y" {!! $selectedApp->active ? 'checked="checked"' : '' !!} >
                            <span class="xe-input-helper"></span>
                            <span class="xe-label-text">사용</span>
                        </label>
                        <label class="xe-label">
                            <input type="radio" id="__xe_chk_mail_{{ $selectedApp->id }}" name="active" value="N" {!! !$selectedApp->active ? 'checked="checked"' : '' !!} >
                            <span class="xe-input-helper"></span>
                            <span class="xe-label-text">사용 중단</span>
                        </label>
                    </div>
                    <em class="__xe_message text-message"></em>
                </div>
            </div>
        </div>
        <div class="__xe_setting">
            <div class="setting-group">
                <a href="#" class="__xe_editBtn">
                    <div class="setting-left">
                        <p>화이트리스트</p><em class="text-gray">{{ count(array_filter($selectedApp->getAllowIps())) }}개의 IP 또는 도메인이 허용 되었습니다.</em>
                    </div>
                </a>
            </div>
            <div class="setting-detail" style="display: none;">
                <div class="setting-detail-content">
                    <p>화이트리스트</p>
                    <em class="text-gray2">IP Address 또는 도메인을 추가하여 접근을 허용할 수 있습니다.</em>
                    @foreach(array_filter($selectedApp->getAllowIps()) as $ipAddress)
                        <div class="xe-input-group" style="margin-bottom:12px;">
                            <span class="xe-input-group-addon" onclick="jQuery(this).parent().remove();">
                               <i class="xi-minus"></i>
                            </span>
                            <input type="hidden" name="allow_ips[]" value="{{ $ipAddress }}">
                            <input type="text" class="xe-form-control" value="{{ $ipAddress }}" disabled="disabled">
                        </div>
                    @endforeach

                    <div id="whiteLabelList">
                        <div id="whiteLabelTemplate">
                        <div class="xe-input-group" style="margin-bottom:12px;">
                            <span class="xe-input-group-addon" onclick="jQuery('#whiteLabelList').append(jQuery('#whiteLabelTemplate').html())">
                               <i class="xi-plus"></i>
                            </span>
                            <input type="text" name="allow_ips[]" class="xe-form-control" value="" placeholder="http://abc.com or 10.11.12.13">
                        </div>
                        </div>
                    </div>
                    <em class="__xe_message text-message"></em>
                </div>
            </div>
        </div>
        <div class="__xe_setting">
            <div class="setting-group">
                <a href="#" class="__xe_editBtn">
                    <div class="setting-left">
                        <p>허용된 서비스</p><em class="text-gray">이 앱이 접근할 수 있는 서비스 권한을 설정합니다.</em>
                    </div>
                </a>
            </div>
            <div class="setting-detail" style="display: none;">
                <div class="setting-detail-content">
                    <p>허용된 서비스</p>
                    <em class="text-gray2">IP Address 또는 도메인을 추가하여 접근을 허용할 수 있습니다.</em>
                    @foreach($apiServices as $group_id => $group)
                        <div class="xe-form-inline">
                            <label class="xe-label">
                                <input type="checkbox" id="group_{{$group_id}}" onclick="$(this).is(':checked') ? $('#group_{{$group_id}}_list').hide() : $('#group_{{$group_id}}_list').show();" name="allow_services[]" value="{{ $group_id }}" @if($selectedApp->hasGroup($group_id)) checked="checked" @endif >
                                <span class="xe-input-helper"></span>
                                <span class="xe-label-text">{{ $group['title'] }} <small>{{ $group['description'] }}</small></span>
                            </label>
                        </div>
                        <div id="group_{{$group_id}}_list" class="children_list" style="@if($selectedApp->hasGroup($group_id)) display:none; @endif">
                            @foreach($group['children'] as $service_id => $service)
                                <div class="xe-form-inline">
                                    <label class="xe-label">
                                        <input type="checkbox" name="allow_services[]" @if($selectedApp->hasService($group_id,$service_id)) checked="checked" @endif  data-service-group="{{ $group_id }}" id="group_{{$group_id}}_{{$service_id}}" value="{{ $service_id }}" />
                                        <span class="xe-input-helper"></span>
                                        <span class="xe-label-text">{{ $service['title'] }} <small>{{ $service['description'] }}</small></span>
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                    <em class="__xe_message text-message"></em>
                </div>
            </div>
        </div>
        @endif
        <div class="xe-btn-group-all">
            <button class="__xe_saveBtn xe-btn xe-btn-primary">{{ xe_trans($selectedApp->id ? 'xe::applyModified' : 'xe::create') }}</button>
        </div>
</div>
</form>

@if($selectedApp->id)
<div class="setting-card">
    <h2>앱 삭제</h2>
    <div class="__xe_setting __xe_settingLeave">
        <div class="setting-group setting-account">
            <div class="setting-group-content">
                <div class="setting-left">
                    <p>이 앱을 더이상 사용하지 않습니까?</p>
                    <em class="text-gray">앱을 삭제하여 더이상의 API호출이 되지 않도록 하십시오.</em>
                </div>
                <div class="setting-right">
                    <button class="__xe_editBtn xe-btn xe-btn-link text-blue">{{ xe_trans('xe::deleteOk') }}</button>
                </div>
            </div>
        </div>
        <div class="setting-detail" style="display: none;">
            <form method="post" class="__xe_form" action="{{route('AuthAPI::deleteKey',['appId' => $selectedApp->id])}}" enctype="multipart/form-data"  onsubmit="return ($('#checkDelete').val() == '{{$selectedApp->name}}') ? confirm('이 작업은 되돌릴 수 없습니다.\n계속 할까요?') : false;">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <div class="setting-detail-content">
                    <p>앱 삭제 확인</p>
                    <em class="text-gray">앱을 삭제하기 위해 삭제할 앱 이름을 입력하세요.</em>
                    <input id="checkDelete" type="text" class="xe-form-control" value="" />
                    <em class="__xe_message text-message">{{ $selectedApp->name }}를 정확히 입력해야 합니다.</em>
                </div>
                <div class="xe-btn-group-all">
                    <button class="__xe_saveBtn xe-btn xe-btn-primary">{{ xe_trans('xe::confirm') }}</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endif

<script>
function doCopyToClipBoard(obj){
    $(obj).select();
    document.execCommand( 'Copy' );
    alert( '클립보드로 복사되었습니다.' );
}
jQuery(document).ready(function($){
   $(".__xe_editBtn").click(function(){
       $('div.setting-detail').hide();
       $('div.setting-group').show();
       $(this).parents('div.setting-group').hide();
       $(this).parents('div.setting-group').next('div.setting-detail').show();
       return false;
   });
});
</script>
