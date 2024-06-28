<?php

class AdminLanguage
{
    static function registerSystem($tabs)
    {
        if(Auth::hasCap('system_language')) {
            $tabs['plugin-language'] = [
                'label' => 'Ngôn ngữ',
                'description' => 'Quản lý lý các ngôn ngữ của website.',
                'callback' => 'AdminLanguage::language',
                'icon' => '<i class="fa-duotone fa-language"></i>',
                'form' => false,
            ];
        }
        if(Auth::hasCap('system_translations')) {
            $tabs['plugin-language-translations'] = [
                'label' => 'Bản dịch ngôn ngữ',
                'description' => 'Quản lý các bản dịch cho website',
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
            'label' => 'Tên hiển thị',
            'note'  => 'Tên là cách nó được hiển thị trên trang web của bạn (ví dụ: tiếng Anh).'
        ]);

        $formList->text('locale', [
            'label' => 'Locale',
            'note'  => 'Locale cho ngôn ngữ (ví dụ en:). Bạn sẽ cần tạo /language/en thư mục nếu ngôn ngữ này không tồn tại'
        ]);

        $formList->select('flag', $options, [
            'label' => 'Cờ',
            'note'  => 'Locale cho ngôn ngữ (ví dụ en:). Bạn sẽ cần tạo /language/en thư mục nếu ngôn ngữ này không tồn tại'
        ]);


        $formSetting = form();

        $formSetting->radio('language_display', [
            'all'  => 'Hiển thị tất cả cờ và tên',
            'flag' => 'Chỉ hiển thị cờ',
            'name' => 'Chỉ hiển tên',
        ], [
            'label' => 'Hiển thị ngôn ngữ',
        ], LangHelper::setting('display'));

        $formSetting->radio('language_switcher_display', [
            'dropdown'  => 'Danh sách thả xuống',
            'list' => 'Danh sách',
        ], [
            'label' => 'Hiển thị trình chuyển đổi ngôn ngữ',
        ], LangHelper::setting('switcher'));


        $formSetting->color('language_color_bg', [
            'label' => 'Màu nền button',
            'start' => 6
        ], LangHelper::setting('bg'));

        $formSetting->color('language_color_txt', [
            'label' => 'Màu chữ button',
            'start' => 6
        ], LangHelper::setting('txt'));

        $formSetting->color('language_color_bg_hover', [
            'label' => 'Màu nền button (hover)',
            'start' => 6
        ], LangHelper::setting('bgHover'));

        $formSetting->color('language_color_txt_hover', [
            'label' => 'Màu chữ button (hover)',
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
