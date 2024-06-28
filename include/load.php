<?php
Class LangLoad {

    function __construct() {
        add_action('init', 'LangLoad::setLanguage', 1);
        add_filter('get_url', 'LangLoad::setUrl', 1);
        add_filter('load_lang_translate', 'LangLoad::transCustom', 1, 2 );
        add_action('theme_custom_assets', 'LangLoad::assets', 20 );
    }

    static function setLanguage(): void
    {
        $language = Cms::get('language');

        $language['default']   = Option::get('language_default', 'vi');

        $language['current']   = $language['default'];

        $language['list']      = Option::get('language', LangHelper::default());

        Cms::set('language', $language);
    }

    static function setUrl($slug): string
    {
        return (Language::isMulti()) ? Language::current().'/'.$slug : $slug;
    }

    static function transCustom($langList, $languageKey): ?array
    {
        return LangHelper::loadCustom($langList, $languageKey);
    }

    static function assets(AssetPosition $header): void
    {
        $header->add('language', 'views/plugins/skd-multi-language/assets/css/language.build.css', ['minify' => true]);
    }
}
new LangLoad();