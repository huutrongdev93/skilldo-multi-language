<?php

class AdminLanguage
{
    static function registerSystem($tabs)
    {
        if(Auth::hasCap('system_language')) {
            $tabs['plugin-language'] = [
                'label' => trans('system.lang.label'), //'Ngôn ngữ'
                'description' => trans('system.lang.description'), //'Quản lý lý các ngôn ngữ của website.'
                'callback' => 'AdminLanguage::language',
                'icon' => '<i class="fa-duotone fa-language"></i>',
                'form' => false,
            ];
        }
        if(Auth::hasCap('system_translations')) {
            $tabs['plugin-language-translations'] = [
                'label' => trans('system.translations.label'), //'Bản dịch ngôn ngữ'
                'description' => trans('system.translations.description'), //'Quản lý các bản dịch cho website'
                'callback' => 'AdminLanguage::translations',
                'icon' => '<i class="fa-duotone fa-language"></i>',
                'form' => false,
            ];
        }

        return $tabs;
    }

    static function language(): void
    {
        $language = Language::list();

        $optionsTemp = Storage::disk('plugin')->json('skd-multi-language/assets/flags-vi.json');

        $options = [];

        foreach ($optionsTemp as $key => $op) {
            $options[$op['key']] = $op['name'];
        }

        $formList = form();

        $formList->text('label', [
            'label' => trans('system.lang.form.label'), //Tên hiển thị
            'note'  => trans('system.lang.form.label.note'), //Tên là cách nó được hiển thị trên trang web của bạn (ví dụ: tiếng Anh)
        ]);

        $formList->text('locale', [
            'label' => trans('system.lang.form.locale'), //Locale
            'note'  => trans('system.lang.form.locale.note', ['path' => '/language/en']), //Locale cho ngôn ngữ (ví dụ en:). Bạn sẽ cần tạo /language/en thư mục nếu ngôn ngữ này không tồn tại
        ]);

        $formList->select('flag', $options, [
            'label' => trans('system.lang.form.flag'), //Cờ',
        ]);


        $formSetting = form();

        $formSetting->radio('language_display', [
            'all'  => trans('system.lang.form.display.options.all'), //Hiển thị tất cả cờ và tên
            'flag' => trans('system.lang.form.display.options.flag'), //Chỉ hiển thị cờ
            'name' => trans('system.lang.form.display.options.name'), //Chỉ hiển tên
        ], [
            'label' => trans('system.lang.form.display'), //Hiển thị ngôn ngữ
        ], LangHelper::setting('display'));

        $formSetting->radio('language_switcher_display', [
            'dropdown'  => trans('system.lang.form.display.switcher.options.dropdown'), //Danh sách thả xuống
            'list' => trans('system.lang.form.display.switcher.options.list'), //Danh sách
        ], [
            'label' => trans('system.lang.form.display.switcher'), //Hiển thị trình chuyển đổi ngôn ngữ
        ], LangHelper::setting('switcher'));


        $formSetting->color('language_color_bg', [
            'label' => trans('system.lang.form.display.colorBg'), //Màu nền button
            'start' => 6
        ], LangHelper::setting('bg'));

        $formSetting->color('language_color_txt', [
            'label' => trans('system.lang.form.display.colorTxt'), //Màu chữ button
            'start' => 6
        ], LangHelper::setting('txt'));

        $formSetting->color('language_color_bg_hover', [
            'label' => trans('system.lang.form.display.colorBg.hover'), //Màu nền button (hover)
            'start' => 6
        ], LangHelper::setting('bgHover'));

        $formSetting->color('language_color_txt_hover', [
            'label' => trans('system.lang.form.display.colorTxt.hover'), //Màu chữ button (hover)
            'start' => 6
        ], LangHelper::setting('txtHover'));

        Plugin::view('skd-multi-language', 'views/language', [
            'language' => $language,
            'formList' => $formList,
            'formSetting' => $formSetting
        ]);
    }

    static function translations(): void
    {
        $language   = Language::list();

        $defaultKey = Language::default();

        $default    = Language::list($defaultKey);

        $currentKey = Str::clear(Request::get('ref_lang'));

        if(empty($currentKey)) $currentKey = $defaultKey;

        $current = Language::list($currentKey);

        Plugin::view('skd-multi-language', 'views/translations', [
            'language'   => $language,
            'defaultKey' => $defaultKey,
            'default'    => $default,
            'currentKey' => $currentKey,
            'current'    => $current
        ]);
    }
}

add_filter('skd_system_tab', 'AdminLanguage::registerSystem', 20);
