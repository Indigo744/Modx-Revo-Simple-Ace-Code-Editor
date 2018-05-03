<?php
/**
 * Default plugin properties
 *
 * @package SimpleAceCodeEditor
 * @subpackage build
 */
$properties = array(
    array(
        'name' => 'AcePath',
        'desc' => 'URL or path to ACE javascript file',
        'type' => 'textfield',
        'options' => '',
        'value' => "https://cdnjs.cloudflare.com/ajax/libs/ace/1.3.1/ace.js",
    ),
    array(
        'name' => 'AceTheme',
        'desc' => 'editor theme name (you can test them all here: https://ace.c9.io/build/kitchen-sink.html)',
        'type' => 'textfield',
        'options' => '',
        'value' => 'monokai',
    )
);
return $properties;