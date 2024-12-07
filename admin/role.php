<?php
class LanguageRole {
    static function label( $label ) {
        $label['system_language'] 		= 'Cấu hình ngôn ngữ';
        $label['system_translations'] 	= 'Phiên dịch ngôn ngữ';
        return $label;
    }
    static function group( $group ) {
        $group['language'] = array(
            'label' => __('Đa ngôn ngữ'),
            'capabilities' => array(
                'system_language',
                'system_translations',
            )
        );
        return $group;
    }
}

add_filter('user_role_editor_label', 'LanguageRole::label');
add_filter('user_role_editor_group', 'LanguageRole::group');