<?php
Class MultiLanguage {
    static public function default() {
        return [
            'vi' => array( 'label' => 'Tiếng việt', 'flag' => '', ),
            'en' => array( 'label' => 'Tiếng Anh', 'flag' => '', ),
        ];
    }
    static public function load($langfile = '', $language_key = 'vi') {
        $langfile = str_replace('.php', '', $langfile);

        $langfile .= '_lang.php';

        //Core
        if ( file_exists( FCPATH . APPPATH . 'language/'.$language_key.'/'.$langfile) ) include( FCPATH . APPPATH . 'language/'.$language_key.'/'.$langfile );

        //Plugin
        $plugins = get_option('plugin_active', array());

        if( have_posts($plugins) ) {

            foreach ($plugins as $name => $value) {
                if (file_exists(FCPATH.'views/plugins/'.$name.'/language/'.$language_key.'/'.$langfile)) {
                    include(FCPATH.'views/plugins/'.$name.'/language/'.$language_key.'/'.$langfile);
                }
            }
        }

        if ( ! isset($lang))
        {
            log_message('error', 'Language file contains no data: language/'.$language_key.'/'.$langfile);

            return array();
        }

        return $lang;
    }
    static public function loadCustom( $language_key = 'vi' ) {

        $langfile = 'mtlang_custom_'.$language_key;

        $path = FCPATH . SML_PATH . 'language/'.$langfile;

        $data = array();

        //Core
        if (file_exists($path)) {

            $data = read_file( $path );

            $data = unserialize($data);
        }
        else {
            self::saveTranslate([], $language_key );
        }

        return $data;
    }
    static public function translate( $language_key = 'vi', $translate_file = null ) {
        if( $translate_file == null ) $translate_file     = MultiLanguage::load('general', $language_key);
        $translate_custom   = MultiLanguage::loadCustom( $language_key );
        $translate = array_merge( $translate_file, $translate_custom );
        return $translate;
    }
    static public function saveTranslate( $translate = array(), $language_key = 'vi' ) {

        $langfile = 'mtlang_custom_'.$language_key;

        $path = FCPATH . SML_PATH . 'language/'.$langfile;

        if (write_file( $path , serialize($translate))) {
            @chmod( $path, 0777);
            return true;
        }

        return false;
    }
    static public function getUrl($languageKey = '') {

        $listKey    = Language::listKey();

        $temp       = [];

        $url        = '';

        foreach (get_instance()->uri as $key => $v) {
            if( $key == 'segments') { $temp =  (array)$v; break; }
        }

        if(in_array($languageKey, $listKey) !== false) {
            if(empty($temp)) {
                $url = Url::base().$languageKey.'/trang-chu';
            }
            else if(in_array($temp[1], $listKey) !== false){
                $url = Url::base().$languageKey.'/'.$temp[2];
            }
            else {
                $url = Url::base().$languageKey.'/'.$temp[1];
            }
            $url = rtrim($url,'/');
        }
        return $url;
    }
    static public function render($args = []) {

        $element = (!isset($args['element'])) ? 'div' : $args['element'];

        $flag    = (!isset($args['flag'])) ? true : $args['flag'];

        $label   = (!isset($args['label'])) ? false : $args['label'];

        $str     = '';

        $languageList = Language::list();

        $languageKey = Language::listKey();

        if( have_posts($languageList) && count($languageList) > 1) {

            $slug = Url::segment(1);

            $temp = Url::segment();

            $languageRender = [];

            if(in_array($slug, $languageKey) !== false ) {
                foreach ($languageList as $key => $val) {
                    $languageRender[$key] = [];
                    $languageRender[$key]['url'] = rtrim(Url::base().$key.'/'.$temp[2],'/');
                    $languageRender[$key]['flag'] = Template::imgLink($val['flag']);
                    $languageRender[$key]['label'] = $val['label'];
                }
            }
            else {
                foreach ($languageList as $key => $val) {
                    $languageRender[$key] = [];
                    $languageRender[$key]['url']    = Url::base().$key.'/'.rtrim(((isset($temp[1])) ? $temp[1] : '/trang-chu'), '/');
                    $languageRender[$key]['flag']   = Template::imgLink($val['flag']);
                    $languageRender[$key]['label']  = $val['label'];
                }
            }

            foreach ($languageRender as $key => $item) {
                if($element == 'div') $str .= '<div class="language-item" id="language-item-'.$key.'"><a href="'.$item['url'].'">';
                if($element == 'li') $str .= '<li class="language-item" id="language-item-'.$key.'"><a href="'.$item['url'].'">';
                if($element == 'option') $str .= '<option class="language-item" id="language-item-'.$key.'" data-url="'.$item['url'].'">';
                if($flag == true) {
                    $str .= '<img src="'.$item['flag'].'" alt="'.$item['label'].'" style="width:30px;">';
                }
                if($label == true) {
                    $str .= '<span>'.$item['label'].'</span>';
                }
                if($element == 'div') $str .= '</a></div>';
                if($element == 'li') $str .= '</a></li>';
                if($element == 'option') $str .= '</option>';
            }

            return $str;
        }
    }
}

function mtlang_list_icon_li() {

    $str = null;

    $ci =& get_instance();

    $language_list = $ci->language['list'];

    $language_key = array_keys($language_list);

    if( have_posts($language_list) && count($language_list) > 1 ) {

        $slug = $ci->uri->segment(1);

        $temp = array();

        foreach ($ci->uri as $key => $v) { if( $key == 'segments') { $temp =  (array)$v; break; }  }

        if(in_array($ci->uri->segment(1), $language_key) !== false ) {

            foreach ($language_list as $key => $val) {

                $url = base_url().$key.'/'.$temp[2];

                $url = rtrim($url,'/');

                $label = (empty($val['flag'])) ? $val['label'] : '<img src="'.get_img_link($val['flag']).'" alt="'.$val['label'].'" style="width:30px;">';

                $str .= '<li><a href="'.$url.'">'.$label.$val['label'].'</a></li>';
            }

        }
        else {

            foreach ($language_list as $key => $val) {

                if( isset($temp[1]) )
                    $url = base_url().$key.'/'.$temp[1];
                else
                    $url = base_url().$key.'/trang-chu';

                $url = rtrim($url,'/');

                $label = (empty($val['flag'])) ? $val['label'] : '<img src="'.get_img_link($val['flag']).'" alt="'.$val['label'].'" style="width:30px;">';

                $str .= '<li><a href="'.$url.'">'.$label.$val['label'].'</a></li>';
            }

        }

        return $str;
    }
}