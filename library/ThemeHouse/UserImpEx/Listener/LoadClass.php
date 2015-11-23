<?php

class ThemeHouse_UserImpEx_Listener_LoadClass extends ThemeHouse_Listener_LoadClass
{

    protected function _getExtendedClasses()
    {
        return array(
            'ThemeHouse_UserImpEx' => array(
                'controller' => array(
                    'XenForo_ControllerAdmin_User'
                ),
                'model' => array(
                    'XenForo_Model_User'
                ),
                'datawriter' => array(
                    'XenForo_DataWriter_User'
                ),
            ),
        );
    }

    public static function loadClassController($class, array &$extend)
    {
        $extend = self::createAndRun('ThemeHouse_UserImpEx_Listener_LoadClass', $class, $extend, 'controller');
    }

    public static function loadClassModel($class, array &$extend)
    {
        $extend = self::createAndRun('ThemeHouse_UserImpEx_Listener_LoadClass', $class, $extend, 'model');
    }

    public static function loadClassDataWriter($class, array &$extend)
    {
        $extend = self::createAndRun('ThemeHouse_UserImpEx_Listener_LoadClass', $class, $extend, 'datawriter');
    }
}