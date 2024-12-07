<div class="box">
	<div class="box-header">
		<h4 class="box-title">{{ trans('general.translation') }}</h4>
	</div>
	<div class="box-content">
		{!! Admin::loading() !!}
		<div class="row">
			<div class="col-md-6">
				<p>{!! trans('general.translation.from.to', ['from' => $default['label'], 'to' => $current['label']]) !!}</b></p>
			</div>
			<div class="col-md-6">
				<div class="text-end">
					<div class="mb-3 text-end d-flex gap-2 justify-content-start justify-content-lg-end align-items-center">
						<p class="mb-0"><b>{{ trans('general.translation') }}:</b></p>
						<div class="d-flex gap-3 align-items-center">
                            @foreach ($language as $key => $lang)
								@if($key == $currentKey)
									@continue;
								@endif
	                            <a href="{!! Url::admin('system/plugin-language-translations?ref_lang='.$key) !!}" class="text-decoration-none">
		                            <img src="{!! LangHelper::flagLink($lang['flag']) !!}" title="{{$lang['label']}}" class="flag" style="height: 16px" alt="{!! $lang['label'] !!}">
                                    {!! $lang['label'] !!}
	                            </a>
							@endforeach
						</div>
					</div>
				</div>
			</div>
		</div>
		<div class="row">
			<input type="hidden" id="ref_lang" value="{!! $currentKey !!}">
			<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
				<input id="input-translations-key" class="form-control" placeholder="{{ trans('general.translation.form.key.placeholder') }}" required>
			</div>
			<div class="col-xs-4 col-sm-4 col-md-4 col-lg-4">
				<input id="input-translations-value" class="form-control" placeholder="{{ trans('general.translation.form.value.placeholder') }}" required>
			</div>
			<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2">
				{!! Admin::button('add', ['id' => 'btn-translations-add']) !!}
			</div>
			<div class="col-xs-2 col-sm-2 col-md-2 col-lg-2 text-right">
				{!! Admin::button('reload', ['id' => 'btn-translations-reload']) !!}
			</div>
		</div>
		<table class="table table-hover mt-3">
			<thead>
				<tr>
					<th>Key</th>
					<th>Dịch</th>
				</tr>
			</thead>
			<tbody id="translations_tbody"></tbody>
		</table>
	</div>
</div>

<script type="text/javascript">
	$(function() {
		class TranslationsHandler {
			constructor() {
				this.inputKey       = $('#input-translations-key')
				this.inputValue     = $('#input-translations-value')
				this.buttonAdd      = SkilldoUtil.buttonLoading('#btn-translations-add')
				this.languageKey    = $('#ref_lang').val();
				this.load()
			}
			load() {

				let self = this;

				$('.loading').show();

				let data = {
					'action' : 'AdminLanguageAjax::translateLoad',
					'language' : this.languageKey
				};

				request.post(ajax, data).then(function(response) {

					SkilldoMessage.response(response);

					$('.loading').hide();

					let str = '';

					if(response.status === 'success') {
						if(Object.keys(response.data).length !== 0) {
							for (const [key, trans] of Object.entries(response.data)) {
								str += `<tr>
											<td><a href="#" data-key="${self.languageKey}" data-key-lang="${key}" data-name="key" data-pk="${key}" class="edit-table-lang-text" >${key}</a></td>
											<td><a href="#" data-key="${self.languageKey}" data-key-lang="${key}" data-name="label" data-pk="${trans}" class="edit-table-lang-text" >${trans}</a></td>
										</tr>`
							}
						}

						$('#translations_tbody').html(str);

						self.setEditable()
					}
				});

				return false;
			}
			add() {

				let self = this;

				let data = {
					'action' : 'AdminLanguageAjax::translateAdd',
					'language' : this.languageKey,
					'key' : this.inputKey.val(),
					'label' : this.inputValue.val()
				};

				this.buttonAdd.start();

				request.post(ajax, data).then(function(response) {

					SkilldoMessage.response(response);

					self.buttonAdd.stop()

					if(response.status === 'success') {

						let str = `<tr>
									<td><a href="#" data-key="${self.languageKey}" data-key-lang="${data.key}" data-name="key" data-pk="${data.key}" class="edit-table-lang-text" >${data.key}</a></td>
									<td><a href="#" data-key="${self.languageKey}" data-key-lang="${data.key}" data-name="label" data-pk="${data.label}" class="edit-table-lang-text" >${data.label}</a></td>
								</tr>`

						$('#translations_tbody').prepend(str);

						self.setEditable()
					}
				});

				return false;
			}
			setEditable() {
				$('.edit-table-lang-text').editable({
					type: 'text',
					params: function(params) {
						params.action = 'AdminLanguageAjax::translateSave';
						params.language = $(this).editable().attr('data-key');
						params.key 		= $(this).editable().attr('data-key-lang');
						return params;
					},
					url: ajax,
				});
			}
		}

		let translations = new TranslationsHandler();

		$(document)
			.on('click', '#btn-translations-reload', function() { return translations.load($(this))})
			.on('click', '#btn-translations-add', function() { return translations.add($(this))})
	})
</script>