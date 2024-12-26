<?php
class LangHelper {

    static function setting($key = null) {

        $setting = Option::get('language_settings');

        if(!have_posts($setting)) $setting =[];

        if(!isset($setting['display'])) {
            $setting['display'] = 'all';
        }

        if(!isset($setting['switcher'])) {
            $setting['switcher'] = 'dropdown';
        }

        if(!isset($setting['bg'])) {
            $setting['bg'] = '#fff';
        }
        if(!isset($setting['txt'])) {
            $setting['txt'] = '#000';
        }

        if(!isset($setting['bgHover'])) {
            $setting['bgHover'] = '';
        }
        if(!isset($setting['txtHover'])) {
            $setting['txtHover'] = '#fff';
        }

        if(!empty($key)) {
            return Arr::get($setting, $key);
        }

        return $setting;
    }

    static function default(): array
    {
        return [
            'vi' => [
                'label' => 'Tiếng việt',
                'flag' => 'vi'
            ],
        ];
    }

    static function flagLink($flagKey): string
    {
        return 'views/plugins/skd-multi-language/assets/images/flags/'.$flagKey.'.svg';
    }

    static function loadCustom($lang, $langKey) {

        $dir = Storage::disk('plugin');

        $path = 'skd-multi-language/assets/lang/language-'.$langKey.'.json';

        if($dir->exists($path)) {

            $contentFiles = $dir->json($path);

            if(have_posts($contentFiles)) {

                foreach($contentFiles as $contentFile) {
                    $lang[$contentFile['key']] = $contentFile['trans'];
                }
            }
        }

        return $lang;
    }

    static function saveTrans($translate, $langKey): void
    {
        $lang = [];

        foreach ($translate as $key => $trans) {
            $lang[] = [
                'key' => $key,
                'trans' => $trans,
            ];
        }

        $path = 'skd-multi-language/assets/lang/language-'.$langKey.'.json';

        $dir = Storage::disk('plugin');

        $dir->put($path, json_encode($lang));
    }

    static function display(): void
    {
        $switcher = LangHelper::setting('switcher');

        $display = LangHelper::setting('display');

        $languageList = Language::list();

        $languageKey = Language::listKey();

        if(have_posts($languageList) && count($languageList) > 1) {

            $languageCurrent = Language::current();

            $langKey = Url::segment(1);

            $url = request()->getRequestUri();

            $base = request()->getBaseUrl();

            $url = Str::after($url, $base);

            $url = trim($url, '/');

            if (in_array($langKey, $languageKey) !== false)
            {
                $url = Str::after($url, $langKey . '/');
            }

            if(empty($url))
            {
                $url =  'trang-chu';
            }

            $languageRender = [];

            foreach ($languageList as $key => $val) {
                $languageRender[$key] = [];
                $languageRender[$key]['url'] = Url::base() . $key . '/' . $url;
                $languageRender[$key]['flag'] = ($display == 'name' || $display == 'key') ? '' : LangHelper::flagLink($val['flag']);
                $languageRender[$key]['label'] = ($display == 'flag') ? '' : $val['label'];
                if($display == 'key' || $display == 'all-key')
                {
                    $languageRender[$key]['label'] = $key;
                }
            }

            if ($switcher == 'list') {
                Plugin::view('skd-multi-language', 'views/components/list', [
                    'languages' => $languageRender,
                    'current' => $languageCurrent
                ]);
            }

            if ($switcher == 'dropdown') {
                Plugin::view('skd-multi-language', 'views/components/dropdown', [
                    'languages'       => $languageRender,
                    'current' => $languageCurrent,
                ]);
            }
        }
    }

    static function buildCss(): void
    {
        $config = LangHelper::setting();

        $cssParams['--language-btn-bg'] = $config['bg'];
        $cssParams['--language-btn-txt'] = $config['txt'];
        $cssParams['--language-btn-bg-hover'] = (!empty($config['bgHover'])) ? $config['bgHover'] : 'var(--theme-color)';
        $cssParams['--language-btn-txt-hover'] = $config['txtHover'];

        $css = ':root {';

        foreach($cssParams as $key => $value) {
            $css .= $key.':'.$value.';';
        }

        $css .= '}';

        $css .= file_get_contents(FCPATH.'/views/plugins/skd-multi-language/assets/css/language.css');

        Storage::disk('plugin')->put('skd-multi-language/assets/css/language.build.css', $css);

        Template::minifyClear('css');
    }
}