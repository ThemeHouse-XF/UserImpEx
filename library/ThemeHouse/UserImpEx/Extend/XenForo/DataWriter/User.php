<?php
if (false) {

    class XFCP_ThemeHouse_UserImpEx_Extend_XenForo_DataWriter_User extends XenForo_DataWriter_User
    {
    }
}

class ThemeHouse_UserImpEx_Extend_XenForo_DataWriter_User extends XFCP_ThemeHouse_UserImpEx_Extend_XenForo_DataWriter_User
{

	public function disableUserIdVerification()
	{
        unset($this->_fields['xf_user']['user_id']['verification']);
    }
}