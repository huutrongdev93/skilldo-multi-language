<?php
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
        $role  = Role::make()->getNames();
        foreach ($role as $name => $label ) {
            $role = Role::get($name);
            $role->remove('system_language');
            $role->remove('system_translations');
        }
    }
}

include 'autoload/autoload.php';
