<?php
class AdminMultipleLanguage {
    static function registerSystem($tabs) {
        $tabs['plugin-language']   = [
            'label'       => 'Đa ngôn ngữ',
            'description' => 'Quản lý lý các ngôn ngữ của website.',
            'callback'    => 'AdminMultipleLanguage::render',
            'icon'        => '<i class="fa-duotone fa-language"></i>',
            'form'        => false,
        ];
        return $tabs;
    }
    static function tabs() {
        if(Auth::hasCap('mtlang_role_language')) $tabs['general'] 	    = array('label' => 'Cấu hình', 	 'callback' => 'AdminMultipleLanguage::general');
        if(Auth::hasCap('mtlang_role_translate')) $tabs['translate'] 	= array('label' => 'Phiên dịch', 'callback' => 'AdminMultipleLanguage::translate');
        return apply_filters('mtlang_setting_tabs', $tabs);
    }
    static function render() {
        $tabs = AdminMultipleLanguage::tabs();
        $current_tab = (Request::get('tab') != '') ? Request::get('tab') :'general';
        include 'html/mtlang_setting.php';
    }
    static function general() {
        $language = Language::list();
        include 'html/mtlang_setting_general.php';
    }
    static function translate() {
        $language  = Language::list();
        $translate = [];
        foreach ($language as $key => $label) {
            $translate[$key] = MultiLanguage::translate( $key );
        }
        include 'html/mtlang_setting_translate.php';
    }
}

add_filter('skd_system_tab' , 'AdminMultipleLanguage::registerSystem', 20);
