<?php
/**
 * Cấu hình language cho menu
 */
Class LangMenu {

    function __construct()
    {
        add_action('admin_init', 'LangMenu::setMenuLanguage', 1);

        add_filter('get_data_menu_cacheID', 'LangMenu::setMenuCacheLanguage');

        add_filter('get_data_menu', 'LangMenu::getMenuCacheLanguage');

        //Thêm menu thành công
        add_action('ajax_menu_add_success', 'LangMenu::deleteMenuCacheLanguage');
        //Xóa menu thành công
        add_action('ajax_menu_del_success', 'LangMenu::deleteMenuCacheLanguage');
        //Sắp sếp menu thành công
        add_action('ajax_menu_sort_success', 'LangMenu::deleteMenuCacheLanguage');
        //xóa menu item thành công
        add_action('ajax_menu_item_del_success', 'LangMenu::deleteMenuCacheLanguage');
        //luu menu item thành công
        add_action('ajax_menu_item_save_success', 'LangMenu::deleteMenuCacheLanguage');
    }

    static function setMenuLanguage(): void
    {
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

        foreach ($menu as $key => $item)
        {
            foreach (Language::list() as $lang_key => $lang_name)
            {
                if( isset( $item->data['name_'.$lang_key]) && !empty($item->data['name_'.$lang_key]) )
                {
                    if($lang_key == Language::current()) $item->name = $item->data['name_'.$lang_key];
                }
            }
            if( isset( $item->child ) && have_posts($item->child)) {
                $item->child = LangMenu::getMenuCacheLanguage( $item->child );
            }
        }

        return $menu;
    }

    static function deleteMenuCacheLanguage($id): void
    {
        foreach (Language::listKey() as $local)
        {
            if($local == Language::default()) continue;

            $cacheId = 'menu-'.$id.'_'.$local;

            if(\SkillDo\Cache::has($cacheId))
            {
                \SkillDo\Cache::delete($cacheId);
            }
        }
    }
}
new LangMenu();