<form action="" id="form-language-setting">
    <div class="box-content">
        <div class="row">
            {!! $formSetting->html() !!}
        </div>
        @if(Admin::isRoot())
            {!! Admin::alert('info', 'sử dụng function <b>LangHelper::display()</b>  để hiển thị trình huyển đổi ngôn ngữ'); !!}
            <div class="text-left">{!! Admin::button('red', ['text' => 'Build file ngôn ngữ (Js)', 'id' => 'js_language_btn_build']) !!}</div>
        @endif
    </div>
    <hr>
    <div class="box-footer d-flex justify-content-between">
        <div class="text-left"></div>
        <div class="text-right">{!! Admin::button('save') !!}</div>
    </div>
</form>


<script>
    $(function() {
        $(document).on('submit', '#form-language-setting', function() {

            let loading = SkilldoUtil.buttonLoading($(this).find('button[name="save"]'));

            let data 		= $(this).serializeJSON();

            data.action     =  'AdminLanguageAjax::setting';

            loading.loading();

            request.post(ajax, data).then(function(response) {

                loading.success();

                SkilldoMessage.response(response);
            });

            return false;
        })

        $(document).on('click', '#js_language_btn_build', function() {

            let loading = SkilldoUtil.buttonLoading($(this));

            let data 		= {
                action: 'AdminLanguageAjax::buildJs'
            }

            loading.loading();

            request.post(ajax, data).then(function(response) {

                loading.success();

                SkilldoMessage.response(response);
            });

            return false;
        })
    })
</script>