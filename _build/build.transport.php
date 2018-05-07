<?php
/**
 * SimpleAceCodeEditor build script
 *
 * @package SimpleAceCodeEditor
 * @subpackage build
 */

function getFileContentsWithoutPHPTags($filename) {
    $o = file_get_contents($filename);
    $o = trim(str_replace(array('<?php','?>'),'',$o));
    return $o;
}

echo "<PRE>";
$tstart = microtime(true);
set_time_limit(0);
/* define version */
define('PKG_NAME','SimpleAceCodeEditor');
define('PKG_NAMESPACE','simpleacecodeeditor');
define('PKG_VERSION','1.3.1');
define('PKG_RELEASE','pl');
define('PKG_DESCRIPTION',sprintf('Ace Code Editor *simple* integration - %s-%s', PKG_VERSION, PKG_RELEASE));
/* define sources */
$root = dirname(dirname(__FILE__)).'/';
$sources = array(
    'root' => $root,
    'dist' => $root . '_dist/',
    'build' => $root . '_build/',
    'data' => $root . '_build/data/',
    'documents' => $root.'core/components/'.PKG_NAMESPACE.'/documents/',
    'elements' => $root.'core/components/'.PKG_NAMESPACE.'/elements/',
    'source_assets' => $root.'assets/components/'.PKG_NAMESPACE,
    'source_core' => $root.'core/components/'.PKG_NAMESPACE,
);
unset($root);
/* load modx */
require_once dirname(__FILE__) . '/build.config.php';
require_once MODX_CORE_PATH . 'model/modx/modx.class.php';
$modx= new modX();
$modx->initialize('mgr');
$modx->setLogLevel(modX::LOG_LEVEL_INFO);
$modx->setLogTarget('ECHO'); flush();
$modx->loadClass('transport.modPackageBuilder','',false, true);
$builder = new modPackageBuilder($modx);
$builder->createPackage(PKG_NAME,PKG_VERSION,PKG_RELEASE);
$builder->registerNamespace(PKG_NAMESPACE,false,true,'{core_path}components/'.PKG_NAMESPACE.'/');
/* create the plugin object */
$plugin= $modx->newObject('modPlugin');
$plugin->set('id',1);
$plugin->set('name', PKG_NAME);
$plugin->set('description', PKG_DESCRIPTION);
$plugin->set('plugincode', getFileContentsWithoutPHPTags($sources['elements'].'plugins/'.PKG_NAMESPACE.'.plugin.php'));
$plugin->set('category', 0);
/* add plugin events */
$events = include $sources['data'].'transport.plugin.events.php';
if (is_array($events) && !empty($events)) {
    $plugin->addMany($events);
} else {
    $modx->log(xPDO::LOG_LEVEL_ERROR,'Could not find plugin events!');
}
$modx->log(xPDO::LOG_LEVEL_INFO,'Packaged in '.count($events).' Plugin Events.'); flush();
unset($events);
/* add plugin properties */
$properties = include $sources['data'].'transport.plugin.properties.php';
if (is_array($properties) && !empty($properties)) {
    $plugin->setProperties($properties);
} else {
    $modx->log(xPDO::LOG_LEVEL_ERROR,'Could not find plugin properties!');
}
$modx->log(xPDO::LOG_LEVEL_INFO,'Packaged in '.count($properties).' Plugin Properties.'); flush();
unset($properties);

$attributes= array(
    xPDOTransport::UNIQUE_KEY => 'name',
    xPDOTransport::PRESERVE_KEYS => false,
    xPDOTransport::UPDATE_OBJECT => true,
    xPDOTransport::RELATED_OBJECTS => true,
    xPDOTransport::RELATED_OBJECT_ATTRIBUTES => array (
        'PluginEvents' => array(
            xPDOTransport::PRESERVE_KEYS => true,
            xPDOTransport::UPDATE_OBJECT => false,
            xPDOTransport::UNIQUE_KEY => array('pluginid','event'),
        ),
    ),
);
$vehicle = $builder->createVehicle($plugin, $attributes);
$modx->log(modX::LOG_LEVEL_INFO,'Adding file resolvers to plugin...');
$vehicle->resolve('file',array(
    'source' => $sources['source_assets'],
    'target' => "return MODX_ASSETS_PATH . 'components/';",
));
$vehicle->resolve('php',array(
    'source' => $sources['data'].'transport.resolver.php',
	'name' => 'resolve',
	'type' => 'php'
));
$builder->putVehicle($vehicle);
$modx->log(modX::LOG_LEVEL_INFO,'Adding package attributes...');
$builder->setPackageAttributes(array(
    'license' => file_get_contents($sources['documents'] . 'licence.txt'),
    'readme' => file_get_contents($sources['documents'] . 'readme.txt'),
    'changelog' => file_get_contents($sources['documents'] . 'changelog.txt')
));
/* zip up package */
$modx->log(modX::LOG_LEVEL_INFO,'Packing up transport package zip...');
$builder->pack();
/* Renaming ZIP file
$modx->log(modX::LOG_LEVEL_INFO,'Renaming transport package zip...');
$final_filename = sprintf("%s-%s-%s.transport.zip", PKG_NAME,PKG_VERSION,PKG_RELEASE);
rename($builder->directory . $builder->filename, $builder->directory . $final_filename);
 */
/* Copy locally */
$modx->log(modX::LOG_LEVEL_INFO,'Copying locally transport package zip...');
//copy($builder->directory . $final_filename, $sources['dist'] . $final_filename);
copy($builder->directory . $builder->filename, $sources['dist'] . $builder->filename);
/* End!!! */
$tend= microtime(true);
$totalTime= ($tend - $tstart);
$totalTime= sprintf("%2.4f s", $totalTime);
$modx->log(modX::LOG_LEVEL_INFO,"Package built in {$totalTime}\n");
exit ();