<?php
if (false) {

    class XFCP_ThemeHouse_UserImpEx_Extend_XenForo_Model_User extends XenForo_Model_User
    {
    }
}

class ThemeHouse_UserImpEx_Extend_XenForo_Model_User extends XFCP_ThemeHouse_UserImpEx_Extend_XenForo_Model_User
{

    /**
     * Prepares XML to export the specified users and their containing
     * categories
     *
     * @param array $userIds
     *
     * @return DOMDocument
     */
    public function getUsersXml(array $userIds)
    {
        if ($userIds) {
            $users = $this->getUsersByIds($userIds,
                array(
                    'join' => self::FETCH_USER_PROFILE | self::FETCH_USER_OPTION | self::FETCH_USER_PRIVACY
                ));
        } else {
            $users = array();
        }

        $document = new DOMDocument('1.0', 'utf-8');
        $document->formatOutput = true;

        $rootNode = $document->createElement('users_export');
        $document->appendChild($rootNode);

        $usersNode = $document->createElement('users');
        $userCategories = array();
        foreach ($users as $user) {
            $user = $this->prepareUser($user);

            $userNode = $document->createElement('user');

            $userNode->setAttribute('username', $user['username']);
            $userNode->setAttribute('email', $user['email']);
            $userNode->setAttribute('gender', $user['gender']);
            $userNode->setAttribute('custom_title', $user['custom_title']);
            $userNode->setAttribute('timezone', $user['timezone']);

            $optionsNode = $document->createElement('options');
            $optionsNode->setAttribute('show_dob_year', $user['show_dob_year']);
            $optionsNode->setAttribute('show_dob_date', $user['show_dob_date']);
            $optionsNode->setAttribute('content_show_signature', $user['content_show_signature']);
            $optionsNode->setAttribute('receive_admin_email', $user['receive_admin_email']);
            $optionsNode->setAttribute('email_on_conversation', $user['email_on_conversation']);
            $optionsNode->setAttribute('is_discouraged', $user['is_discouraged']);
            $optionsNode->setAttribute('default_watch_state', $user['default_watch_state']);
            $optionsNode->setAttribute('alert_optout', $user['alert_optout']);
            $optionsNode->setAttribute('enable_rte', $user['enable_rte']);
            $optionsNode->setAttribute('enable_flash_uploader', $user['enable_flash_uploader']);
            $userNode->appendChild($optionsNode);

            $privacyNode = $document->createElement('privacy');
            $privacyNode->setAttribute('allow_view_profile', $user['allow_view_profile']);
            $privacyNode->setAttribute('allow_post_profile', $user['allow_post_profile']);
            $privacyNode->setAttribute('allow_send_personal_conversation', $user['allow_send_personal_conversation']);
            $privacyNode->setAttribute('allow_view_identities', $user['allow_view_identities']);
            $privacyNode->setAttribute('allow_receive_news_feed', $user['allow_receive_news_feed']);
            $userNode->appendChild($privacyNode);

            $profileNode = $document->createElement('profile');
            $profileNode->setAttribute('dob_day', $user['dob_day']);
            $profileNode->setAttribute('dob_month', $user['dob_month']);
            $profileNode->setAttribute('dob_year', $user['dob_year']);
            $profileNode->setAttribute('signature', $user['signature']);
            $profileNode->setAttribute('homepage', $user['homepage']);
            $profileNode->setAttribute('location', $user['location']);
            $profileNode->setAttribute('occupation', $user['occupation']);
            $profileNode->setAttribute('about', $user['about']);

            $customFieldsNode = $document->createElement('custom_fields');
            foreach ($user['customFields'] as $fieldId => $fieldValue) {
                $customFieldNode = $document->createElement('custom_field');
                $customFieldNode->setAttribute('field_id', $fieldId);
                if (is_array($fieldValue)) {
                    $fieldValue = serialize($fieldValue);
                }
                $customFieldNode->setAttribute('field_value', $fieldValue);
                $customFieldsNode->appendChild($customFieldNode);
            }

            $profileNode->appendChild($customFieldsNode);

            $userNode->appendChild($profileNode);

            $usersNode->appendChild($userNode);
        }

        $rootNode->appendChild($usersNode);

        return $document;
    }

    public function getUsersFromXml(SimpleXMLElement $document, &$errors = array())
    {
        if ($document->getName() != 'users_export') {
            throw new XenForo_Exception(new XenForo_Phrase('th_provided_file_is_not_valid_users_xml_userimpex'),
                true);
        }

        $users = array();
        $i = 0;

        foreach ($document->users->user as $user) {
            $options = array();
            if ($user->options) {
                $options = array(
                    'show_dob_year' => (int) $user->options['show_dob_year'],
                    'show_dob_date' => (int) $user->options['show_dob_date'],
                    'content_show_signature' => (int) $user->options['content_show_signature'],
                    'receive_admin_email' => (int) $user->options['receive_admin_email'],
                    'email_on_conversation' => (int) $user->options['email_on_conversation'],
                    'is_discouraged' => (int) $user->options['is_discouraged'],
                    'default_watch_state' => (string) $user->options['default_watch_state'],
                    'alert_optout' => (string) $user->options['alert_optout'],
                    'enable_rte' => (int) $user->options['enable_rte'],
                    'enable_flash_uploader' => (int) $user->options['enable_flash_uploader']
                );
            }

            $privacy = array();
            if ($user->privacy) {
                $privacy = array(
                    'allow_view_profile' => (int) $user->privacy['allow_view_profile'],
                    'allow_post_profile' => (int) $user->privacy['allow_post_profile'],
                    'allow_send_personal_conversation' => (int) $user->privacy['allow_send_personal_conversation'],
                    'allow_view_identities' => (int) $user->privacy['allow_view_identities'],
                    'allow_receive_news_feed' => (int) $user->privacy['allow_receive_news_feed']
                );
            }

            $profile = array();
            $customFields = array();
            if ($user->profile) {
                $profile = array(
                    'dob_day' => (int) $user->profile['dob_day'],
                    'dob_month' => (int) $user->profile['dob_month'],
                    'dob_year' => (int) $user->profile['dob_year'],
                    'signature' => (string) $user->profile['signature'],
                    'homepage' => (string) $user->profile['homepage'],
                    'location' => (string) $user->profile['location'],
                    'occupation' => (string) $user->profile['occupation'],
                    'about' => (string) $user->profile['about']
                );
                foreach ($user->profile->custom_fields->custom_field as $customField) {
                    $customFields[(string) $customField['field_id']] = (string) $customField['field_value'];
                }
            }

            $users[$i] = array_merge(array(
                'username' => (string) $user['username'],
                'email' => (string) $user['email'],
                'gender' => (string) $user['gender'],
                'custom_title' => (string) $user['custom_title'],
                'timezone' => (string) $user['timezone'],
                'custom_fields' => $customFields
            ), $options, $privacy, $profile);

            $i++;
        }

        return $users;
    }

    public function massImportUsers(array $users, &$errors = array())
    {
        $db = $this->_getDb();

        foreach ($users as $userId => $user) {
            $existingUser = array();
            if (!empty($user['email'])) {
                $existingUser = $this->getUserByEmail($user['email']);
            }

            /* @var $dw XenForo_DataWriter_User */
            $dw = XenForo_DataWriter::create('XenForo_DataWriter_User');

            $dw->setOption(XenForo_DataWriter_User::OPTION_ADMIN_EDIT, true);

            $xenOptions = XenForo_Application::get('options');

            if (isset($user['user_id']) && $xenOptions->th_userImpEx_allowUserIdSet) {
                $dw->disableUserIdVerification();
            }

            if ($existingUser) {
                $dw->setExistingData($existingUser);
            }

            if (!empty($user['custom_fields']) && is_array($user['custom_fields'])) {
                $dw->setCustomFields($user['custom_fields']);
                unset($user['custom_fields']);
            }

            if (isset($user['password'])) {
                $dw->setPassword($user['password']);
                unset($user['password']);
            } elseif ($dw->isInsert()) {
                if ($xenOptions->th_userImpEx_randomPassword) {
                    $password = XenForo_Application::generateRandomString(8);
                    $password = strtr($password, array(
                        'I' => 'i',
                        'l' => 'L',
                        '0' => 'O',
                        'o' => 'O'
                    ));
                    $password = trim($password, '_-');
                    $dw->setPassword($password);
                }
                $auth = XenForo_Authentication_Abstract::create('XenForo_Authentication_NoPassword');
                $dw->set('scheme_class', $auth->getClassName());
                $dw->set('data', $auth->generate(''), 'xf_user_authenticate');
            }

            if (!isset($user['user_group_id']) && $dw->isInsert()) {
                $dw->set('user_group_id', XenForo_Model_User::$defaultRegisteredGroupId);
            }

            if (!isset($user['language_id']) && $dw->isInsert()) {
                $dw->set('language_id', XenForo_Visitor::getInstance()->get('language_id'));
            }

            $fieldNames = $dw->getFieldNames();

            foreach ($fieldNames as $fieldName) {
                if (isset($user[$fieldName])) {
                    $dw->set($fieldName, $user[$fieldName]);
                }
            }

            $dwErrors = $dw->getErrors();
            if ($dwErrors) {
                $users[$userId]['dwErrors'] = $dwErrors;
            } else {
                $dw->preSave();
                $dwErrors = $dw->getErrors();
                if ($dwErrors) {
                    $users[$userId]['dwErrors'] = $dwErrors;
                } else {
                    unset($users[$userId]);
                    $dw->save();
                }
            }
        }

        return $users;
    }
}