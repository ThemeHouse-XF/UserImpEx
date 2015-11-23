<?php
if (false) {

    class XFCP_ThemeHouse_UserImpEx_Extend_XenForo_ControllerAdmin_User extends XenForo_ControllerAdmin_User
    {
    }
}

class ThemeHouse_UserImpEx_Extend_XenForo_ControllerAdmin_User extends XFCP_ThemeHouse_UserImpEx_Extend_XenForo_ControllerAdmin_User
{

    public function actionBatchUpdate()
    {
        $export = $this->_input->filterSingle('export', XenForo_Input::STRING);
        
        if ($export) {
            return $this->responseReroute(__CLASS__, 'export');
        }
        
        return parent::actionBatchUpdate();
    }

    public function actionExport()
    {
        $this->_assertPostOnly();
        
        $userIds = $this->_input->filterSingle('user_ids', XenForo_Input::ARRAY_SIMPLE);
        if (!$userIds) {
            return $this->responseError(new XenForo_Phrase('th_please_select_at_least_one_user_userimpex'));
        }
        
        $this->_routeMatch->setResponseType('xml');
        
        $viewParams = array(
            'xml' => $this->_getUserModel()->getUsersXml($userIds)
        );
        
        return $this->responseView('ThemeHouse_UserImpEx_ViewAdmin_User_Export', '', $viewParams);
    }

    public function actionImport()
    {
        return $this->responseView('ThemeHouse_UserImpEx_ViewAdmin_User_Import', 'th_user_import_userimpex',
            array());
    }

    public function actionImportForm()
    {
        $this->_checkCsrfFromToken($this->_request->getParam('_xfToken'));
        
        $input = $this->_input->filter(
            array(
                'options' => XenForo_Input::ARRAY_SIMPLE,
                'mode' => XenForo_Input::STRING
            ));
        
        $xenOptions = XenForo_Application::get('options');
        
        $input['options'] = array_merge(
            array(
                'start_row' => 0,
                'row_count' => $xenOptions->th_userImpEx_batchImportUsers,
                'filename' => ''
            ), $input['options']);
        
        $userModel = $this->_getUserModel();
        
        if ($input['mode'] == 'upload') {
            $upload = XenForo_Upload::getUploadedFile('upload');
            if (!$upload) {
                return $this->responseError(new XenForo_Phrase('th_please_upload_valid_users_xml_file_userimpex'));
            }
            
            $document = $this->getHelper('Xml')->getXmlFromFile($upload);
            $users = $userModel->getUsersFromXml($document);
        } elseif ($input['mode'] == 'uploadcsv') {
            if (!$input['options']['filename']) {
                $upload = XenForo_Upload::getUploadedFile('uploadcsv');
                if (!$upload) {
                    return $this->responseError(
                        new XenForo_Phrase('th_please_upload_valid_users_csv_file_userimpex'));
                }
                
                $tempFile = $upload->getTempFile();
                
                if ($input['options']['row_count']) {
                    $internalDataPath = XenForo_Helper_File::getInternalDataPath();
                    XenForo_Helper_File::createDirectory($internalDataPath . '/userimpex/');
                    $filename = $internalDataPath . '/userimpex/' . XenForo_Application::$time . '.csv';
                    
                    copy($tempFile, $filename);
                } else {
                    $filename = $tempFile;
                }
            } else {
                $filename = $input['options']['filename'];
            }
            
            $users = $this->getHelper('ThemeHouse_UserImpEx_ControllerHelper_Csv')->getCsvFromFile($filename,
                $input['options']['start_row'], $input['options']['row_count']);
            if (count($users) == $input['options']['row_count']) {
                $input['options']['start_row'] = $input['options']['start_row'] + $input['options']['row_count'];
                $input['options']['filename'] = $filename;
            } else {
                unset($input['options']['start_row'], $input['options']['row_count']);
            }
        } else {
            $users = $this->_input->filterSingle('users', XenForo_Input::ARRAY_SIMPLE);
        }
        
        $userCount = count($users);
        
        $users = $userModel->massImportUsers($users);
        
        $usersImported = $userCount - count($users);
        
        /* @var $userChangeLogModel XenForo_Model_UserChangeLog */
        $userChangeLogModel = $this->getModelFromCache('XenForo_Model_UserChangeLog');
        
        $fields = array();
        foreach ($users as $user) {
            foreach ($user as $fieldName => $fieldValue) {
                if (!isset($fields[$fieldName])) {
                    $field = array(
                        'field' => $fieldName,
                        'old_value' => '',
                        'new_value' => ''
                    );
                    $field = $userChangeLogModel->prepareField($field);
                    $fields[$fieldName] = $field['name'];
                }
            }
        }
        
        $viewParams = array(
            'options' => $input['options'],
            'mode' => $input['mode'],
            'users' => $users,
            'usersImported' => $usersImported,
            'fields' => $fields
        );
        
        if (!$users && empty($input['options']['filename'])) {
            return $this->responseRedirect(XenForo_ControllerResponse_Redirect::SUCCESS, 
                XenForo_Link::buildAdminLink('users/list'));
        }
        
        return $this->responseView('ThemeHouse_UserImpEx_ViewAdmin_User_ImportForm',
            'th_user_import_form_userimpex', $viewParams);
    }
}