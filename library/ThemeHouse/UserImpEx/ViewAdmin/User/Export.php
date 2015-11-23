<?php

/**
 * Helper to get the user export data (likely in XML format).
 */
class ThemeHouse_UserImpEx_ViewAdmin_User_Export extends XenForo_ViewAdmin_Base
{

    /**
     * Render the exported date to XML.
     *
     * @return string
     */
    public function renderXml()
    {
        $this->setDownloadFileName('users.xml');
        return $this->_params['xml']->saveXml();
    }
}