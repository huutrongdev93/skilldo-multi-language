<?php
if(Auth::hasCap('mtlang_role')) {
    AdminMenu::addSub('system', 'mtlang', 'Đa ngôn ngữ', 'plugins?page=mtlang', ['callback' => 'mtlang_setting_callback', 'position' => 'audit-log']);
}

function mtlang_setting_tabs() {
	if( Auth::hasCap('mtlang_role_language')) $tabs['general'] 	    = array('label' => 'Cấu hình', 	 'callback' => 'mtlang_setting_general');
	if( Auth::hasCap('mtlang_role_translate')) $tabs['translate'] 	= array('label' => 'Phiên dịch', 'callback' => 'mtlang_setting_translate');
	return apply_filters('mtlang_setting_tabs', $tabs);
}

function mtlang_setting_callback() {

	$tabs = mtlang_setting_tabs();

	$current_tab = (Request::Get('tab') != '') ? Request::Get('tab') :'general';

	include 'html/mtlang_setting.php';
}

function mtlang_setting_general() {

	$language = Language::list();

	include 'html/mtlang_setting_general.php';
}

function mtlang_setting_translate() {

	$ci = &get_instance();

	$language = $ci->language;

	$translate = array();

	foreach ($language['list'] as $key => $label) {
		$translate[$key] = MultiLanguage::translate( $key );
	}

	include 'html/mtlang_setting_translate.php';
}
