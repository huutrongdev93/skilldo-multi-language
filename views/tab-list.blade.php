<div class="row">
    <div class="col-md-4">
        <form action="" id="form-language">
            <div class="box-content">
                <input type="hidden" name="id" value="">
                {!! $formList->html() !!}
            </div>
            <hr>
            <div class="box-footer text-right">
                <button type="submit" class="btn btn-icon btn-blue" id="btn-language-submit">Thêm ngôn ngữ mới</button>
            </div>
        </form>
    </div>
    <div class="col-md-8">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>Tên ngôn ngữ</th>
                    <th>Locale</th>
                    <th>Cờ</th>
                    <th class="text-center">Là mặc định?</th>
                    <th>#</th>
                </tr>
            </thead>
            <tbody id="js_system_language_tbody">
            @foreach ($language as $key => $lang)
                <tr class="tr_{!! $key !!}" data-id="{!! $key !!}" data-label="{!! $lang['label'] !!}" data-flag="{!! $lang['flag'] ?? '' !!}">
                    <td class="language-label">{!! $lang['label'] !!}</td>
                    <td class="language-locale">{{$key}}</td>
                    <td class="language-flag"><img src="{!! LangHelper::flagLink($lang['flag'] ?? '') !!}" alt="{{$key}}"/></td>
                    <td class="text-center">
                        @if ($key == Language::default())
                            <div class="language-default"><i class="fa-solid fa-stars"></i></div>
                        @else
                            <div class="language-default btn-set-default" data-bs-toggle="tooltip" data-bs-title="Chọn {{$lang['label']}} làm ngôn ngữ mặc định"><i class="fa-solid fa-stars"></i></div>
                        @endif
                    </td>
                    <td class="action">
                        <button type="button" class="btn btn-blue btn-lang-edit">{!! Admin::icon('edit') !!}</button>
                        {!! Admin::btnConfirm('red', [
                            'id' => $key,
                            'action' => 'delete',
                            'tooltip' => trans('general.delete'),
                            'ajax' => 'AdminLanguageAjax::delete'
                        ]) !!}
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>

<style>
    .language-default {
        font-size: 30px;
        color: #bd660c;
    }
    .btn-set-default {
        color: #484747;
        cursor: pointer;
        opacity: 0.1;
    }
    .btn-set-default:hover {
        opacity: 1;
    }

    img.img-flag {
        width:25px;
    }
</style>

<script type="text/javascript">
	$(function() {

		class LanguageHandler {
			constructor() {
				this.isEdit = false;
				this.submitButton = $('#btn-language-submit')
				this.submitLoading = new ButtonLoading(this.submitButton)
			}
			onClickEdit(element) {
				let box = element.closest('tr');
				$('input[name="label"]').val(box.data('label'));
				$('input[name="locale"]').val(box.data('id'));
				$('input[name="id"]').val(box.data('id'));
				$('select[name="flag"]').val(box.data('flag')).change();
				this.submitButton.html('Cập nhật');
				this.isEdit = true;
				return false;
			}
			onClickActive(element) {

				let buttonLoading = new ButtonLoading(element);

				let box = element.closest('tr');

				let data = {
					'action' : 'AdminLanguageAjax::active',
					'key'	 : box.attr('data-id')
				};

				buttonLoading.start()

				request.post(ajax, data).then(function(response) {

					buttonLoading.stop()

					SkilldoMessage.response(response);

					if(response.status === 'success') {

						$('#js_system_language_tbody .language-default').addClass('btn-set-default');

						element.removeClass('btn-set-default')
					}
				});

				return false;
			}
			onClickSave(element) {

				let self = this;

				let data 		= element.serializeJSON();

				data.action     =  'AdminLanguageAjax::save';

				self.submitLoading.start();

                request.post(ajax, data).then(function(response) {

                    SkilldoMessage.response(response);

					self.submitLoading.stop();

					if(self.isEdit === false ) {
						$('#js_system_language_tbody').append(`<tr class="tr_${response.data.locale}" data-id="${response.data.locale}" data-label="${response.data.label}" data-flag="${response.data.flag}">
								<td class="language-label">${response.data.label}</td>
								<td class="language-locale">${response.data.locale}</td>
								<td class="language-flag"><img src="${response.data.flag}" style="width:50px;"  alt=""/></td>\
								<td class="text-center">
                                    <div class="language-default btn-set-default" data-bs-toggle="tooltip" data-bs-title="Chọn ${response.data.label} làm ngôn ngữ mặc định"><i class="fa-solid fa-stars"></i></div>
								</td>
								<td class="action">
									<button type="button" class="btn btn-blue btn-lang-edit"><i class="fa-duotone fa-pencil"></i></button>
									<button class="btn btn-red js_btn_confirm" data-action="delete" data-ajax="AdminLanguageAjax::delete" data-model="" data-heading="Xóa Dữ liệu" data-description="Bạn chắc chắn muốn xóa dữ liệu này ?" data-trash="disable" data-id="${response.data.locale}" type="button"><i class="fa-duotone fa-trash"></i></button>
								</td>
							</tr>`);
					}
					else {
						$('tr[data-id="'+ data.id +'"]')
							.attr('data-id', response.data.locale)
							.attr('data-label', response.data.label)
							.attr('data-flag', response.data.flag)

						$('tr[data-id="'+ data.id +'"] td.language-locale').html(response.data.locale);
						$('tr[data-id="'+ data.id +'"] td.language-label').html(response.data.label);
						$('tr[data-id="'+ data.id +'"] td.language-flag img').attr('src', response.data.flag);

						self.isEdit = false;
						self.submitButton.html('Thêm ngôn ngữ mới');
						element.trigger('reset');
					}
				});

				return false;
			}
		}

		let language = new LanguageHandler();

		$(document)
			.on('click', '.btn-lang-edit', function() { return language.onClickEdit($(this))})
			.on('click', '.btn-set-default', function() { return language.onClickActive($(this))})
			.on('submit', '#form-language', function() { return language.onClickSave($(this))})

		function formatStateFlags (state) {

			if (!state.id) {
				return state.text;
			}
			let baseUrl = "/views/plugins/skd-multi-language/assets/images/flags/";

			return $(
				'<span><img src="' + baseUrl + '/' + state.element.value.toLowerCase() + '.svg" class="img-flag" /> ' + state.text + '</span>'
			);
		};

		$("#flag").select2({
			templateResult: formatStateFlags
		});

	});
</script>