<?php
/**
 * [mtlang_delete_cache_menu xóa capche đa ngôn ngữ khi edit có 1 hành động làm thay đổi menu]
 * @param  [type] $id [description]
 * @return [type]     [description]
 */
function mtlang_delete_cache_menu( $id )
{

	$ci = &get_instance();

	foreach ($ci->language['list'] as $key => $name) {

        if( $key == $ci->language['default']  ) continue;

        if( $ci->cache->get('menu-'.$id.'_'.$key) )$ci->cache->delete('menu-'.$id.'_'.$key);
    }
}

//Thêm menu thành công
add_action('ajax_menu_add_success', 'mtlang_delete_cache_menu' );
//Xóa menu thành công
add_action('ajax_menu_del_success', 'mtlang_delete_cache_menu' );
//Sắp sếp menu thành công
add_action('ajax_menu_sort_success', 'mtlang_delete_cache_menu' );
//xóa menu item thành công
add_action('ajax_menu_item_del_success', 'mtlang_delete_cache_menu' );
//luu menu item thành công
add_action('ajax_menu_item_save_success', 'mtlang_delete_cache_menu' );
