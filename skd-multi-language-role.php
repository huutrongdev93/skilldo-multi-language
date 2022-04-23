<?php
function mtlang_role_label( $label ) {

	$label['mtlang_role'] 				= 'Quản lý ngôn ngữ';

	$label['mtlang_role_language'] 		= 'Cấu hình ngôn ngữ';

	$label['mtlang_role_translate'] 	= 'Phiên dịch ngôn ngữ';

	return $label;
}

add_filter( 'user_role_editor_label', 'mtlang_role_label' );

function mtlang_role_group( $group ) {

	$group['mtlang'] = array(
		'label' => __('Đa ngôn ngữ'),
		'capbilities' => array(
			'mtlang_role',
			'mtlang_role_language',
			'mtlang_role_translate',
		)
	);

	return $group;
}

add_filter( 'user_role_editor_group', 'mtlang_role_group' );