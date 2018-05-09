<?php
/**
 * Default plugin events
 *
 * @package SimpleAceCodeEditor
 * @subpackage build
 */
$events = array();

$events['OnManagerPageBeforeRender'] = $modx->newObject('modPluginEvent');
$events['OnManagerPageBeforeRender']->fromArray(array(
    'event' => 'OnManagerPageBeforeRender'
),'',true,true);

$events['OnRichTextEditorRegister'] = $modx->newObject('modPluginEvent');
$events['OnRichTextEditorRegister']->fromArray(array(
    'event' => 'OnRichTextEditorRegister'
),'',true,true);

$events['OnSnipFormPrerender'] = $modx->newObject('modPluginEvent');
$events['OnSnipFormPrerender']->fromArray(array(
    'event' => 'OnSnipFormPrerender'
),'',true,true);

$events['OnTempFormPrerender'] = $modx->newObject('modPluginEvent');
$events['OnTempFormPrerender']->fromArray(array(
    'event' => 'OnTempFormPrerender'
),'',true,true);

$events['OnChunkFormPrerender'] = $modx->newObject('modPluginEvent');
$events['OnChunkFormPrerender']->fromArray(array(
    'event' => 'OnChunkFormPrerender'
),'',true,true);

$events['OnPluginFormPrerender'] = $modx->newObject('modPluginEvent');
$events['OnPluginFormPrerender']->fromArray(array(
    'event' => 'OnPluginFormPrerender'
),'',true,true);

$events['OnFileCreateFormPrerender'] = $modx->newObject('modPluginEvent');
$events['OnFileCreateFormPrerender']->fromArray(array(
    'event' => 'OnFileCreateFormPrerender'
),'',true,true);

$events['OnFileEditFormPrerender'] = $modx->newObject('modPluginEvent');
$events['OnFileEditFormPrerender']->fromArray(array(
    'event' => 'OnFileEditFormPrerender'
),'',true,true);

$events['OnDocFormPrerender'] = $modx->newObject('modPluginEvent');
$events['OnDocFormPrerender']->fromArray(array(
    'event' => 'OnDocFormPrerender'
),'',true,true);

$events['OnPluginSave'] = $modx->newObject('modPluginEvent');
$events['OnPluginSave']->fromArray(array(
    'event' => 'OnPluginSave'
),'',true,true);

return $events;