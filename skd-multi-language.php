<?php
/**
Plugin name     : Multi Language
Plugin class    : skd_multi_language
Plugin uri      : http://sikido.vn
Description     : Plugin giúp tạo website đa ngôn ngữ cho phép bạn có thể biên tập nhiều hơn một ngôn ngữ trên website
Author          : Hữu Trọng
Version         : 2.3.1
*/
const SML_NAME = 'skd-multi-language';

define( 'SML_PATH', plugin_dir_path( SML_NAME ) );

class skd_multi_language {

    private $name = 'skd_MultiLanguage';

    function __construct() {}

    public function active() {

        Option::update('language_default', 'vi');

        Option::update('language', MultiLanguage::default());

        $role = Role::get('root');

        $role->add_cap('mtlang_role_language');

        $role->add_cap('mtlang_role_translate');

        $role->add_cap('mtlang_role');
    }

    public function uninstall() {

        Option::delete( 'language_default' );

        Option::delete( 'language' );

        $role  = skd_roles()->get_names();

        foreach ( $role as $name => $label ) {

            $role = get_role( $name );

            $role->remove_cap('mtlang_role_language');

            $role->remove_cap('mtlang_role_translate');

            $role->remove_cap('mtlang_role');
        }
    }
}

include 'skd-multi-language-cache.php';

include 'skd-multi-language-function.php';

include 'skd-multi-language-ajax.php';

include 'skd-multi-language-role.php';

include 'admin/mtlang_admin.php';


Class MultiLanguage_Load {
    function __construct() {
        add_action('init', 'MultiLanguage_Load::setLanguage', 1);
        add_filter('get_url', 'MultiLanguage_Load::setUrl', 1);
        add_action('init', 'MultiLanguage_Load::setMenuLanguage', 1);
        add_filter('get_data_menu_capcheID', 'MultiLanguage_Load::setMenuCacheLanguage');
        add_filter('get_data_menu', 'MultiLanguage_Load::getMenuCacheLanguage');
        add_filter('load_lang_translate', 'MultiLanguage_Load::loadTranslateCustom', 1, 2 );
        add_filter('load_lang_translate', 'MultiLanguage_Load::loadTranslateTheme', 2, 2 );
    }

    static function setLanguage() {
        $ci = &get_instance();
        $ci->language['default']   = Option::get('language_default', 'vi' );
        $ci->language['current']   = Option::get('language_default', 'vi' );
        $ci->language['list']      = Option::get('language', MultiLanguage::default());
    }

    static function setUrl($slug) {
        return Language::current().'/'.$slug;
    }
    /**
     *  Custom language cho menu
     */
    static function setMenuLanguage() {
        foreach (Language::list() as $key => $lang) {
            if($key == Language::default()) continue;
            $option = ['field' => 'name_'.$key, 'type' => 'text', 'label' => 'Tiêu đề ('.$lang['label'].')'];
            add_menu_option('name_'.$key , $option);
        }
    }
    static function setMenuCacheLanguage($cacheID) {
        if(Language::current() == Language::default()) return $cacheID;
        return $cacheID.'_'.Language::current();
    }
    static function getMenuCacheLanguage($menu) {

        if(Language::current() == Language::default()) return $menu;

        foreach ($menu as $key => $item) {
            foreach (Language::list() as $lang_key => $lang_name) {

                if( isset( $item->data['name_'.$lang_key]) && !empty($item->data['name_'.$lang_key]) ) {
                    if($lang_key == Language::current()) $item->name = $item->data['name_'.$lang_key];
                }
            }
            if( isset( $item->child ) && have_posts($item->child)) {
                $item->child = MultiLanguage_Load::getMenuCacheLanguage( $item->child );
            }
        }

        return $menu;
    }
    /**
     *  load language
     */
    static function loadTranslateCustom($langList, $languageCurent) {
        return MultiLanguage::translate($languageCurent, $langList);
    }

    static function loadTranslateTheme($langList, $languageCurent) {

        foreach (glob(FCPATH.VIEWPATH.'*') as $filename) {
            if(!is_dir($filename)) continue;
            if($filename == FCPATH.VIEWPATH.'backend') continue;
            if($filename == FCPATH.VIEWPATH.'cache') continue;
            if($filename == FCPATH.VIEWPATH.'log') continue;
            if($filename == FCPATH.VIEWPATH.'plugins') continue;
            if(file_exists($filename.'/language/'.$languageCurent)) {
                foreach (glob($filename.'/language/'.$languageCurent.'/*') as $file) {
                    if(file_exists($file)) {
                        include_once $file;
                    }
                }
            }
        }
        if(!empty($lang) && have_posts($lang)) {
            $langList = array_merge($langList, $lang);
        }
        return $langList;
    }
}
new MultiLanguage_Load();
