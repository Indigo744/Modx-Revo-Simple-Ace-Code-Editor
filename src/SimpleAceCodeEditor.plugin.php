<?php
/**
 * Simple Ace Source Editor Plugin
 * https://github.com/Indigo744/Modx-Revo-Simple-Ace-Code-Editor
 *
 * Create plugin and paste this code or install it from Package Manager
 * Set which_element_editor system option to SimpleAceCodeEditor
 *
 * Events: OnManagerPageBeforeRender, OnRichTextEditorRegister, OnSnipFormPrerender,
 * OnTempFormPrerender, OnChunkFormPrerender, OnPluginFormPrerender,
 * OnFileCreateFormPrerender, OnFileEditFormPrerender, OnDocFormPrerender
 * 
 * Properties:
 *     AcePath: URL or path to ACE javascript file
 *              default: https://cdnjs.cloudflare.com/ajax/libs/ace/1.3.1/ace.js
 *     AceTheme: editor theme name (you can test them all here: https://ace.c9.io/build/kitchen-sink.html)
 *              default: monokai
 * 
 * If you want to edit a property, create your own property set first.
 *
 * Based on Ace Source Editor Plugin by Danil Kostin
 *
 * @package ace
 *
 * @var array $scriptProperties
 * @var Ace $ace
 */


$pluginName = "SimpleAceCodeEditor";


/** Register RTE **/
if ($modx->event->name == 'OnRichTextEditorRegister') {
    $modx->event->output($pluginName);
    return;
}


/** Check if RTE (element) setting is set to this **/
if ($modx->getOption('which_element_editor', null) !== $pluginName) {
    return;
}


/** Get options **/
$AcePath = $modx->getoption('AcePath', $scriptProperties, $modx->getOption($pluginName . '.AcePath', null, ''));
$AceTheme = $modx->getoption('AceTheme', $scriptProperties, $modx->getOption($pluginName . '.AceTheme', null, 'monokai'));


/** Corresponding arrays **/
$mimeTypeToMode = array(
    'text/x-smarty'         => 'smarty',
    'text/html'             => 'html',
    'application/xhtml+xml' => 'html',
    'text/css'              => 'css',
    'text/x-scss'           => 'scss',
    'text/x-less'           => 'less',
    'image/svg+xml'         => 'svg',
    'application/xml'       => 'xml',
    'text/xml'              => 'xml',
    'text/javascript'       => 'javascript',
    'application/javascript'=> 'javascript',
    'application/json'      => 'json',
    'text/x-php'            => 'php',
    'application/x-php'     => 'php',
    'text/x-sql'            => 'sql',
    'text/x-markdown'       => 'markdown',
    'text/plain'            => 'text',
    'text/x-twig'           => 'twig'
);

$extensionMap = array(
    'tpl'   => 'text/html',
    'htm'   => 'text/html',
    'html'  => 'text/html',
    'css'   => 'text/css',
    'scss'  => 'text/x-scss',
    'less'  => 'text/x-less',
    'svg'   => 'image/svg+xml',
    'xml'   => 'application/xml',
    'xsl'   => 'application/xml',
    'js'    => 'application/javascript',
    'json'  => 'application/json',
    'php'   => 'application/x-php',
    'sql'   => 'text/x-sql',
    'txt'   => 'text/plain',
);


/** Adapt field/mime depending on event type **/
$mimeType = false;
$field = false;
switch ($modx->event->name) {
    case 'OnSnipFormPrerender':
        // Snippets are PHP
        $field = 'modx-snippet-snippet';
        $mimeType = 'application/x-php';
        break;
    case 'OnTempFormPrerender':
        // Templates are HTML
        $field = 'modx-template-content';
        $mimeType = 'text/html';
        break;
    case 'OnChunkFormPrerender':
        // Chunks are HTML, unless it is static then we look at the file extension
        $field = 'modx-chunk-snippet';
        if ($modx->controller->chunk && $modx->controller->chunk->isStatic()) {
            $extension = pathinfo($modx->controller->chunk->getSourceFile(), PATHINFO_EXTENSION);
            $mimeType = isset($extensionMap[$extension]) ? $extensionMap[$extension] : 'text/plain';
        } else {
            $mimeType = 'text/html';
        }
        break;
    case 'OnPluginFormPrerender':
        // Plugins are PHP
        $field = 'modx-plugin-plugincode';
        $mimeType = 'application/x-php';
        break;
    case 'OnFileCreateFormPrerender':
        // On file creation, use plain text
        $field = 'modx-file-content';
        $mimeType = 'text/plain';
        break;
    case 'OnFileEditFormPrerender':
        // For file editing, we look at the file extension
        $field = 'modx-file-content';
        // Identify mime type according to extension
        $extension = pathinfo($scriptProperties['file'], PATHINFO_EXTENSION);
        $mimeType = isset($extensionMap[$extension]) ? $extensionMap[$extension] : 'text/plain';
        break;
    case 'OnDocFormPrerender':
        // For document, we look at the content type
        // But we wont show anything if another RTE is set (e.g. CKEditor or TinyMCE)
        if (!$modx->controller->resourceArray) {
            return;
        }
        if ($modx->getOption('use_editor')) {
            $richText = $modx->controller->resourceArray['richtext'];
            $classKey = $modx->controller->resourceArray['class_key'];
            if ($richText || in_array($classKey, array('modStaticResource','modSymLink','modWebLink','modXMLRPCResource'))) {
                return;
            }
        }
        $field = 'ta';
        $mimeType = $modx->getObject('modContentType', $modx->controller->resourceArray['content_type'])->get('mime_type');
        break;
    default:
        return;
}

/** If mime type and field found, include the javascript code to load Ace **/
if ($mimeType && $field && array_key_exists($mimeType, $mimeTypeToMode)) {
    // Get corresponding Ace mode according to mime type
    $mode = $mimeTypeToMode[$mimeType];
    
    $script = <<<JSSCRIPT
<script src="{$AcePath}" type="text/javascript" charset="utf-8"></script>
<script type="text/javascript">
    (function() {
    
        // Max number of tries
        var MAX_TRIES = 10;
        
        // Time in ms to wait between each tries
        var WAIT_BETWEEN_TRIES_MS = 100;
        
        // Hold the current try number
        var currentTry = 0;
        
        // Will hold the textarea DOM element
        var textarea;
        
        /* Function Init ACE editor
         * Uses textarea variable
         */
        var initAceCodeEditor = function() {
            // Set parent element to relative position
            // Hence the Ace Editor div absolute positionning will be relative to it
            textarea.parentNode.style.position = 'relative';
            
            // Create div element for Ace
            var aceEditorDiv = document.createElement("div");
            aceEditorDiv.style.position = 'absolute';
            aceEditorDiv.style.width = '100%';
            aceEditorDiv.style.height = '100%';
            
            // Append to DOM before the textarea
            textarea.parentNode.insertBefore(aceEditorDiv, textarea);
            
            // Hide textarea
            textarea.style.visibility = 'hidden';
            
            // Create Ace editor !
            var editor = ace.edit(aceEditorDiv);
            
            // Ace Editor settings
            editor.setTheme("ace/theme/{$AceTheme}");
            editor.session.setMode("ace/mode/{$mode}");
            editor.getSession().setValue(textarea.value);
            
            // Keep Ace and textarea synchronized
            editor.on("change", function() {
                textarea.value = editor.getSession().getValue();
            });
            
        }
        
        /* Function search for the textarea
         * Recursive function
         * If textarea is not found, wait a bit and search again
         */
        var tryToGetTextArea = function() {
            // Try to find the textarea
            textarea = document.getElementById("{$field}");
            
            if (textarea) {
                // Element found, init!
                initAceCodeEditor(textarea);
            } else {
                // Damn, not found. Wait a bit and try again
                setTimeout(function() {
                    currentTry++;
                    if (currentTry <= MAX_TRIES) {
                        tryToGetTextArea();
                    }
                }, WAIT_BETWEEN_TRIES_MS);
            }
        }
        
        // Start searching!
        tryToGetTextArea();
    })();
</script>
JSSCRIPT;

    $modx->controller->addHtml($script);
}