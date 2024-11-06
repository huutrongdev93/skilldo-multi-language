<?php
Class LangLoad {

    function __construct()
    {
        add_filter('get_url', 'LangLoad::setUrl', 1);
        add_filter('load_lang_translate', 'LangLoad::transCustom', 1, 2 );
        add_action('theme_custom_assets', 'LangLoad::assets', 20 );
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