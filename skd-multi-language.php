<?php
/**
Plugin name     : Đa ngôn ngữ
Plugin class    : skd_multi_language
Plugin uri      : http://sikido.vn
Description     : Plugin giúp tạo website đa ngôn ngữ cho phép bạn có thể biên tập nhiều hơn một ngôn ngữ trên website
Author          : Hữu Trọng
Version         : 3.0.0
*/
const SML_NAME = 'skd-multi-language';

define('SML_PATH', Path::plugin( SML_NAME ) );

class skd_multi_language {

    private string $name = 'skd_multi_language';

    function __construct() {}

    public function active(): void
    {
        Option::update('language_default', 'vi');
        Option::update('language', LangHelper::default());
        $role = Role::get('root');
        $role->add('system_language');
        $role->add('system_translations');
    }

    public function uninstall(): void
    {
        Option::delete( 'language_default' );
        Option::delete( 'language' );
        $role  = skd_roles()->getNames();
        foreach ( $role as $name => $label ) {
            $role = Role::get( $name );
            $role->remove('system_language');
            $role->remove('system_translations');
        }
    }
}

include 'include/cache.php';

include 'include/function.php';

include 'include/ajax.php';

include 'include/role.php';

include 'include/menu.php';

include 'include/load.php';

include 'admin/admin.php';
