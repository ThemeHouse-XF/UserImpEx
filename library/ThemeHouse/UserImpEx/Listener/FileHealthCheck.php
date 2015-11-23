<?php

class ThemeHouse_UserImpEx_Listener_FileHealthCheck
{

    public static function fileHealthCheck(XenForo_ControllerAdmin_Abstract $controller, array &$hashes)
    {
        $hashes = array_merge($hashes,
            array(
                'library/ThemeHouse/UserImpEx/ControllerHelper/Csv.php' => '4de915085bc3964705005bde499b31e5',
                'library/ThemeHouse/UserImpEx/Extend/XenForo/ControllerAdmin/User.php' => '9b3f447a4e77ebd19b15e05e358c7437',
                'library/ThemeHouse/UserImpEx/Extend/XenForo/DataWriter/User.php' => '6be4dbc5fe62150e43c5ce7f397c4e61',
                'library/ThemeHouse/UserImpEx/Extend/XenForo/Model/User.php' => '0eab197d9e217576528a13f447718c11',
                'library/ThemeHouse/UserImpEx/Install/Controller.php' => '22aa132a49baea0f43d1bc2022a429c2',
                'library/ThemeHouse/UserImpEx/Listener/LoadClass.php' => '118ca9b0296e07cca7b01f6dbc1c10ad',
                'library/ThemeHouse/UserImpEx/ViewAdmin/User/Export.php' => '68d07c36ad0cd15343baedc8bced355b',
                'library/ThemeHouse/Install.php' => '18f1441e00e3742460174ab197bec0b7',
                'library/ThemeHouse/Install/20151109.php' => '2e3f16d685652ea2fa82ba11b69204f4',
                'library/ThemeHouse/Deferred.php' => 'ebab3e432fe2f42520de0e36f7f45d88',
                'library/ThemeHouse/Deferred/20150106.php' => 'a311d9aa6f9a0412eeba878417ba7ede',
                'library/ThemeHouse/Listener/ControllerPreDispatch.php' => 'fdebb2d5347398d3974a6f27eb11a3cd',
                'library/ThemeHouse/Listener/ControllerPreDispatch/20150911.php' => 'f2aadc0bd188ad127e363f417b4d23a9',
                'library/ThemeHouse/Listener/InitDependencies.php' => '8f59aaa8ffe56231c4aa47cf2c65f2b0',
                'library/ThemeHouse/Listener/InitDependencies/20150212.php' => 'f04c9dc8fa289895c06c1bcba5d27293',
                'library/ThemeHouse/Listener/LoadClass.php' => '5cad77e1862641ddc2dd693b1aa68a50',
                'library/ThemeHouse/Listener/LoadClass/20150518.php' => 'f4d0d30ba5e5dc51cda07141c39939e3',
            ));
    }
}