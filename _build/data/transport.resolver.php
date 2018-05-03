<?php
/**
 * Resolver to set which_element_editor to SimpleAceCodeEditor
 * 
 * @package SimpleAceCodeEditor
 * @subpackage build
 */
$success= true;
if ($pluginid= $object->get('id')) {
    switch ($options[xPDOTransport::PACKAGE_ACTION]) {
        case xPDOTransport::ACTION_INSTALL:
        case xPDOTransport::ACTION_UPGRADE:
            $object->xpdo->log(xPDO::LOG_LEVEL_INFO,'Attempting to set which_element_editor setting to SimpleAceCodeEditor.');
            // set CKEditor as default element editor
            $setting = $object->xpdo->getObject('modSystemSetting',array('key' => 'which_element_editor'));
            if ($setting) {
                $setting->set('value','SimpleAceCodeEditor');
                $setting->save();
            }
            unset($setting);
            break;
        case xPDOTransport::ACTION_UNINSTALL:
            $success= true;
            break;
    }
    switch ($options[xPDOTransport::PACKAGE_ACTION]) {
        case xPDOTransport::ACTION_UPGRADE:
            break;
	}
}

return $success;