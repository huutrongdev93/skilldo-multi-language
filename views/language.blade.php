<div class="box">
	{!! Admin::tabs([
			'langList' => [
				'label'   => 'Danh sách',
				'content' => Plugin::partial('skd-multi-language', 'views/tab-list', ['formList' => $formList, 'language' => $language])
			],
			'langSetting' => [
				'label'    => 'Cấu hình',
				'content'  => Plugin::partial('skd-multi-language', 'views/tab-setting', ['formSetting' => $formSetting])
			]
		], 'langList')
	!!}
</div>