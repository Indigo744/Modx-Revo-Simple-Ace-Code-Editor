<?php
/**
 * Resolver to set which_element_editor to SimpleAceCodeEditor
 * 
 * @package SimpleAceCodeEditor
 * @subpackage build
 */
$success= true;
if ($pluginid = $object->get('id')) {
    switch ($options[xPDOTransport::PACKAGE_ACTION]) {
        case xPDOTransport::ACTION_INSTALL:
        case xPDOTransport::ACTION_UPGRADE:
        
            /* Attempt to set default element editor */
            $object->xpdo->log(xPDO::LOG_LEVEL_INFO,'Attempting to set which_element_editor setting to SimpleAceCodeEditor.');
            $setting = $object->xpdo->getObject('modSystemSetting',array('key' => 'which_element_editor'));
            if ($setting) {
                $setting->set('value','SimpleAceCodeEditor');
                $setting->save();
            }
            unset($setting);
            
            /* Attempt to refresh cache */
            $object->xpdo->log(xPDO::LOG_LEVEL_INFO,'Attempting to refresh manager cache.');
            $object->xpdo->cacheManager->refresh(array(
                'context_settings' => array('contexts' => array('mgr'))
            ));
            break;
            
        case xPDOTransport::ACTION_UNINSTALL:
            $success= true;
            break;
    }
}

return $success;