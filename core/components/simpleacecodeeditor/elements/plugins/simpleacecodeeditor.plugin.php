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
 * and OnPluginSave to force cache refresh
 * 
 * Properties:
 *
 *     AcePath: URL or path to ACE javascript file
 *              default: https://cdnjs.cloudflare.com/ajax/libs/ace/1.3.3/ace.js
 *
 *     Theme: editor theme name (you can test them all here: https://ace.c9.io/build/kitchen-sink.html)
 *            default: monokai
 *
 *     ReplaceCTRLDKbdShortcut: Replace the CTRL-D (or CMD-D) keyboard shortcut to perform a more sensible action
 *                              duplicate the current line or selection (instead of deleting, which is the default behavior)
 *                              default: true
 *
 *     Autocompletion: Enable Auto-completion: none, basic (show on CTRL-Space) or live (show on typing)
 *                     Note that "ext-language_tools.js" must be available alongside ace.js (will be retrieve from <AcePath>/ext-language_tools.js)
 *                     default: basic
 *
 *     SettingsMenu: Add a settings menu accessible with CTR-Q (or CMD-Q)
 *                   Note that "ext-settings_menu.js" must be available alongside ace.js (will be retrieve from <AcePath>/ext-settings_menu.js)
 *                   default: false
 *
 *     Spellcheck: Enable spell-check
 *                 Note that "ext-spellcheck.js" must be available alongside ace.js (will be retrieve from <AcePath>/ext-spellcheck.js)
 *                 default: false
 *
 *     EmmetPath: URL or path to Emmet js file
 *                For more information, see https://github.com/cloud9ide/emmet-core
 *                default: https://cloud9ide.github.io/emmet-core/emmet.js
 *
 *     Emmet: Enable Emmet
 *            Note that Emmet JS file must be loaded first (see EmmetPath, it must be correctly set)
 *            Note that "ext-emmet.js" must be available alongside ace.js (will be retrieve from <AcePath>/ext-emmet.js)
 *            It is recommended to disable ReplaceCTRLDKbdShortcut property when using Emmet (as it replace an Emmet shortcut CTRL-D)
 *            default: false
 *
 *     AcePrintMarginColumn: Print margin column position
 *                           Set the character position of the print margin (for instance useful if you like to code with 80 chars wide max)
 *                           Set to 0 to disable it completely
 *                           default: 0 (disabled)
 *
 *     ChunkDetectMIMEShebang: Enable 'shebang-style' MIME detection for chunks (in description or in the first line of chunk content)
 *                             This is particularly useful if your chunk contains directly JS, or SASS, or anything different than HTML...
 *                             Supported MIME values are text/x-smarty, text/html, application/xhtml+xml, text/css, text/x-scss, 
 *                                                       text/x-sass, text/x-less, image/svg+xml, application/xml, text/xml, text/javascript, 
 *                                                       application/javascript, application/json, text/x-php, application/x-php, text/x-sql, 
 *                                                       text/x-markdown, text/plain, text/x-twig
 *                             default: true
 * 
 *
 * If you want to edit a property, create your own property set first.
 *
 *
 * Based on Ace Source Editor Plugin by Danil Kostin
 *
 * @package SimpleAceCodeEditor
 *
 * @var array $scriptProperties
 * @var Ace $ace
 */

/** Package information (set at build) **/
$pluginName = '__PKG_NAME__';
$pluginVersion = '__PKG_VERSION__-__PKG_RELEASE__';

/** Force mgr refresh on plugin save **/
if ($modx->event->name == 'OnPluginSave') {
    if ($plugin->get('name') === $pluginName) {
        $modx->cacheManager->refresh(array(
            'context_settings' => array('contexts' => array('mgr'))
        ));
    }
    return;
}

/** Register RTE **/
if ($modx->event->name == 'OnRichTextEditorRegister') {
    $modx->event->output($pluginName);
    return;
}

/** Check if RTE (element) setting is set to this **/
if ($modx->getOption('which_element_editor', null) !== $pluginName) {
    return;
}

/** Get properties **/
$AcePath = $modx->getoption('AcePath', $scriptProperties, $modx->getOption($pluginName . '.AcePath', null, "https://cdnjs.cloudflare.com/ajax/libs/ace/1.3.3/ace.js"));
$EmmetPath = $modx->getoption('EmmetPath', $scriptProperties, $modx->getOption($pluginName . '.EmmetPath', null, "https://cloud9ide.github.io/emmet-core/emmet.js"));
$AceTheme = $modx->getoption('Theme', $scriptProperties, $modx->getOption($pluginName . '.Theme', null, 'monokai'));
$AceReplaceCTRLDKbdShortcut = $modx->getoption('ReplaceCTRLDKbdShortcut', $scriptProperties, $modx->getOption($pluginName . '.ReplaceCTRDKbdShortcut', null, true));
$AceAutocompletion = $modx->getoption('Autocompletion', $scriptProperties, $modx->getOption($pluginName . '.Autocompletion', null, 'basic'));
$AceSettingsMenu = $modx->getoption('SettingsMenu', $scriptProperties, $modx->getOption($pluginName . '.SettingsMenu', null, false));
$AceSpellcheck = $modx->getoption('Spellcheck', $scriptProperties, $modx->getOption($pluginName . '.Spellcheck', null, false));
$AceEmmet = $modx->getoption('Emmet', $scriptProperties, $modx->getOption($pluginName . '.Emmet', null, false));
$AcePrintMarginColumn = $modx->getoption('AcePrintMarginColumn', $scriptProperties, $modx->getOption($pluginName . '.AcePrintMarginColumn', null, 0));
$AceChunkDetectMIMEShebang = $modx->getoption('ChunkDetectMIMEShebang', $scriptProperties, $modx->getOption($pluginName . '.ChunkDetectMIMEShebang', null, true));

/** Inits script options **/
$AceAssetsUrl = $modx->getOption('assets_url') . 'components/' . strtolower($pluginName);
$AceBasePath = dirname($AcePath);
$scriptPaths = array($AcePath);
$editorOptions = array();
$rendererOptions = array(
    'theme' => "ace/theme/$AceTheme",
    'showPrintMargin' => $AcePrintMarginColumn > 0 ? true : false,
    'printMarginColumn' => $AcePrintMarginColumn > 0 ? $AcePrintMarginColumn : 80,
);
$editorAdditionalScript = "\n";

/** Handle proper CTRL-D **/
if ($AceReplaceCTRLDKbdShortcut == true) {
    $editorAdditionalScript .= <<<JSSCRIPT
        editor.commands.removeCommand('del');
        editor.commands.addCommand({
            name: "del",
            bindKey: {win: "Delete",  mac: "Delete|Shift-Delete"},
            exec: function(editor) { editor.remove("right"); },
            multiSelectAction: "forEach",
            scrollIntoView: "cursor"
        });
        editor.commands.addCommand({
            name: "Duplicate Selection",
            bindKey: {win: "Ctrl-D", mac: "Command-D"},
            exec: function(editor) { editor.duplicateSelection(); },
            scrollIntoView: "cursor",
            multiSelectAction: "forEach"
        });
JSSCRIPT;
}

/** Handle autocompletion extension **/
if ($AceAutocompletion === 'live' || $AceAutocompletion === 'basic') {
    $editorOptions['enableBasicAutocompletion'] = true;
    $editorOptions['enableLiveAutocompletion'] = $AceAutocompletion === 'live';
    array_push($scriptPaths, "$AceBasePath/ext-language_tools.js");
}

/** Handle settings_menu extension **/
if ($AceSettingsMenu == true) {
    $editorAdditionalScript .= <<<JSSCRIPT
        var RequiresettingsMenu = ace.require('ace/ext/settings_menu');
        if (RequiresettingsMenu) {
            // Init with current editor
            RequiresettingsMenu.init(editor);
            // Set CTRL-Q shortcut
        	editor.commands.addCommands([{
        		name: "showSettingsMenu",
        		bindKey: {win: "Ctrl-q", mac: "Ctrl-q"},
        		exec: function(editor) {
        			editor.showSettingsMenu();
        		},
        		readOnly: true
        	}]);
        }
JSSCRIPT;
    array_push($scriptPaths, "$AceBasePath/ext-settings_menu.js");
} 

/** Handle Spellcheck extension **/
if ($AceSpellcheck == true) {
    $editorOptions['spellcheck'] = true;
    array_push($scriptPaths, "$AceBasePath/ext-spellcheck.js");
} 

/** Handle Emmet extension **/
if ($AceEmmet == true) {
    $editorOptions['enableEmmet'] = true;
    array_push($scriptPaths, $EmmetPath);
    array_push($scriptPaths, "$AceBasePath/ext-emmet.js");
}

/** Corresponding arrays **/
$mimeTypeToMode = array(
    'text/x-smarty'                     => 'smarty',
    'text/html'                         => 'html',
    'application/xhtml+xml'             => 'html',
    'text/css'                          => 'css',
    'text/x-scss'                       => 'scss',
    'text/x-sass'                       => 'scss',
    'text/x-less'                       => 'less',
    'image/svg+xml'                     => 'svg',
    'application/xml'                   => 'xml',
    'text/xml'                          => 'xml',
    'text/javascript'                   => 'javascript',
    'application/javascript'            => 'javascript',
    'application/json'                  => 'json',
    'text/x-php'                        => 'php',
    'application/x-php'                 => 'php',
    'text/x-sql'                        => 'sql',
    'application/sql'                   => 'sql',
    'text/x-markdown'                   => 'markdown',
    'text/markdown'                     => 'markdown',
    'text/plain'                        => 'text',
    'text/x-twig'                       => 'twig',
    'application/x-extension-htaccess'  => 'apache_conf',
    'application/vnd.coffeescript'      => 'coffee',
    'application/x-typescript'          => 'typescript',
    'text/x-ini'                        => 'ini',
    'text/x-ejs'                        => 'ejs',
    'application/x-perl'                => 'perl',
);

$extensionMap = array(
    'tpl'       => 'text/html',
    'htm'       => 'text/html',
    'html'      => 'text/html',
    'css'       => 'text/css',
    'scss'      => 'text/x-scss',
    'sass'      => 'text/x-sass',
    'less'      => 'text/x-less',
    'svg'       => 'image/svg+xml',
    'xml'       => 'application/xml',
    'xsl'       => 'application/xml',
    'js'        => 'application/javascript',
    'json'      => 'application/json',
    'php'       => 'application/x-php',
    'sql'       => 'text/x-sql',
    'txt'       => 'text/plain',
    'htaccess'  => 'application/x-extension-htaccess',
    'coffee'    => 'application/vnd.coffeescript',
    'litcoffee' => 'application/vnd.coffeescript',
    'ts'        => 'application/x-typescript',
    'ini'       => 'text/x-ini',
    'ejs'       => 'text/x-ejs',
    'md'        => 'text/markdown',
    'sql'       => 'application/x-perl',
);


/** Adapt field/mime depending on event type **/
$mimeType = false;
$field = false;
$mixedMode = true;
switch ($modx->event->name) {
    case 'OnSnipFormPrerender':
        // Snippets are PHP
        $field = 'modx-snippet-snippet';
        $mimeType = 'application/x-php';
        $mixedMode = false;
        break;
    case 'OnTempFormPrerender':
        // Templates are HTML
        $field = 'modx-template-content';
        $mimeType = 'text/html';
        $mixedMode = true;
        break;
    case 'OnChunkFormPrerender':
        // Chunks are HTML
        // unless it is static then we look at the file extension
        // unless it a proper mime type is set in description or first line of chunk!
        $field = 'modx-chunk-snippet';
        $mixedMode = true;
        
        if ($modx->controller->chunk) {
            /** Try to detect shebang **/
            if ($AceChunkDetectMIMEShebang) {
                // Retrieve description
                $chunkDescription = $modx->controller->chunk->get('description');
                // Retrieve first line of chunk content
                $chunkContentFirstLine = strtok($modx->controller->chunk->getContent(), "\n");
                // Loop through known mime
                foreach($mimeTypeToMode as $currMimeType => $mode) {
                    if (strpos($chunkDescription, $currMimeType) !== FALSE || 
                        strpos($chunkContentFirstLine, $currMimeType) !== FALSE) 
                    {
                        $mimeType = $currMimeType;
                        break;
                    }
                }
            }
            
            /** For static file, try to detect through file extension **/
            if (!$mimeType && $modx->controller->chunk->isStatic()) {
                $extension = pathinfo($modx->controller->chunk->getSourceFile(), PATHINFO_EXTENSION);
                $mimeType = isset($extensionMap[$extension]) ? $extensionMap[$extension] : 'text/plain';
            }
        }
        
        /* Default to HTML */
        if (!$mimeType) {
            $mimeType = 'text/html';
        }
        
        break;
    case 'OnPluginFormPrerender':
        // Plugins are PHP
        $field = 'modx-plugin-plugincode';
        $mimeType = 'application/x-php';
        $mixedMode = false;
        break;
    case 'OnFileCreateFormPrerender':
        // On file creation, use plain text
        $field = 'modx-file-content';
        $mimeType = 'text/plain';
        $mixedMode = true;
        break;
    case 'OnFileEditFormPrerender':
        // For file editing, we look at the file extension
        $field = 'modx-file-content';
        // Identify mime type according to extension
        $extension = pathinfo($scriptProperties['file'], PATHINFO_EXTENSION);
        $mimeType = isset($extensionMap[$extension]) ? $extensionMap[$extension] : 'text/plain';
        $mixedMode = true;
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
        $mixedMode = true;
        break;
    default:
        return;
}

/** If field found, include the javascript code to load Ace **/
if ($field) {
    // Get corresponding Ace mode according to mime type
    $mode = isset($mimeTypeToMode[$mimeType]) ? $mimeTypeToMode[$mimeType] : 'text';
    
    // Handle mixed mode
    if ($mixedMode == true) {
        // Mixed mode, set needed files and functions 
        
        array_push($scriptPaths, "$AceAssetsUrl/modx_highlight_rules.js");
        
        $setModeScript = <<<JSSCRIPT
            /** 
             * Function to create a mixed mode with MODX tags
             * Based on the work of danyaPostfactum, see link below
             * https://github.com/danyaPostfactum/modx-ace/blob/master/assets/components/ace/modx.texteditor.js
             */
            var createModxMixedMode = function(Mode) {
                var oop = ace.require("ace/lib/oop");
                
                /* Create the new mixed mode */
                var ModxMixedMode = function() {
                    Mode.call(this);
                    
                    // Save the parent rules to be able to call them later
                    var parentHighlightRules = this.HighlightRules;
                    
                    /* Create the new mixed rules */
                    var mixedHighlightRules = function() {
                        // Set parent rules
                        parentHighlightRules.call(this);
                        
                        // Set modx rules (function available in file modx_highlight_rules.js already loaded)
                        modxCustomHighlightRules.call(this);
                        
                        // Normalized!
                        this.normalizeRules();
                    }
                    
                    // Inherit prototype from parent rules
                    oop.inherits(mixedHighlightRules, parentHighlightRules);
                    
                    // Set mixed highlight rules
                    this.HighlightRules = mixedHighlightRules;
                }
                
                // Inherit prototype from parent Mode
                oop.inherits(ModxMixedMode, Mode);
                
                // Handle the case were a worker is defined in parent mode
                if (Mode.prototype.createWorker) {
                    ModxMixedMode.prototype.createWorker = function(session) {
                        // Call parent without 'this'
                        var worker = Mode.prototype.createWorker(session);
                        if (worker) {
                            // Replace onError function to handle modx tag
                            worker.on("error", function(e) {
                                var annotations = [];
                                var idx_max = e.data.length;
                                // Loop through errors, and silence errors when a modx tag [[ exists
                                for(var i = 0 ; i < idx_max ; i++) {
                                    // Get line
                                    var line = session.getLine(e.data[i].row);
                                    if (line.indexOf('[[') === -1) {
                                        // No modx tag, add to annotations
                                        annotations.push(e.data[i]);
                                    }
                                }
                                session.setAnnotations(annotations);
                            });
                        }
                        return worker;
                    };
                }
                
                // We're done. Return the new mixed mode
                return new ModxMixedMode();
            };
            
            /** 
             * Function to set a mixed mode
             */
            var setMixedMode = function(editor, mode) {
                var config = ace.require('ace/config');
                config.loadModule(["mode", 'ace/mode/' + mode], function(module) {
                    var mode = createModxMixedMode(module.Mode);
                    editor.session.setMode(mode);
                }.bind(this));
            }
                
            setMixedMode(editor, "{$mode}");
JSSCRIPT;

    } else {
        // No mixed mode, simply set mode
        $setModeScript = "editor.session.setMode('ace/mode/{$mode}');";
    }
    
    // Convert options to JSON object
    $editorOptions = json_encode($editorOptions, JSON_FORCE_OBJECT);
    $rendererOptions = json_encode($rendererOptions, JSON_FORCE_OBJECT);
    
    // Generate cache busting query string
    // Based on current plugin version + hash of all properties
    $propertiesHash = md5("$AcePath $EmmetPath $AceTheme $AceReplaceCTRLDKbdShortcut $AceAutocompletion $AceSettingsMenu $AceSpellcheck $AceEmmet $AceChunkDetectMIMEShebang");
    $CacheBustingQSValue = "?v=$pluginVersion-$propertiesHash";

    // Generate final script!
    $script = "";
    foreach($scriptPaths as $scriptPath) {
        // Include file
        $script .= "<script src='$scriptPath$CacheBustingQSValue' type='text/javascript' charset='utf-8'></script>\n";
    }
    
    // The script...
    $script .= <<<JSSCRIPT
<script type="text/javascript">
    (function() {
        "use strict";
    
        // Max number of tries
        var MAX_TRIES = 10;
        
        // Time in ms to wait between each tries
        var WAIT_BETWEEN_TRIES_MS = 100;
        
        // Hold the current try number
        var currentTry = 0;
        
        // Will hold the textarea DOM element
        var textarea;
        
        // Useful dom lib
        var dom = ace.require("ace/lib/dom");
        
        /** 
         * Function Init ACE editor
         * Uses textarea variable
         */
        var initAceCodeEditor = function() {
            // Set parent element to relative position
            // Hence the Ace Editor div absolute positionning will be relative to it
            textarea.parentNode.style.position = 'relative';
            
            // Create div element for Ace
            var aceEditorDiv = document.createElement("div");
            setEditorSize(aceEditorDiv);
            
            // Append to DOM before the textarea
            textarea.parentNode.insertBefore(aceEditorDiv, textarea);
            
            // Hide textarea
            textarea.style.visibility = 'hidden';
            
            // Create Ace editor !
            var editor = ace.edit(aceEditorDiv);
            
            // Additional scripts
            {$editorAdditionalScript}
            
            // Fullscreen toggle support
            editor.commands.addCommand({
                name: "Toggle Fullscreen",
                bindKey: "F11",
                exec: function(editor) {
                    // Toggle class
                    dom.toggleCssClass(editor.container, "fullScreen");
                    // Get current situation
                    var isFullScreen = dom.hasCssClass(editor.container, "fullScreen");
                    // Set size and resize as needed
                    setEditorSize(editor.container, isFullScreen);
                    editor.resize();
                    // Handle searchbox position as needed
                    handleSearchBoxPosition(editor, isFullScreen);
                }
            });
            
            // Search while fullscreen support
            editor.commands.addCommand({
                name: 'CustomFind',
                bindKey: {win: 'Ctrl-F', mac: 'Command-F'},
                exec: function(editor) { handleSearchBox(editor); }
            });
            
            // Replace while fullscreen support
            editor.commands.addCommand({
                name: 'CustomReplace',
                bindKey: {win: 'Ctrl-H', mac: 'Command-Option-F'},
                exec: function(editor) { handleSearchBox(editor, true); }
            });
            
            // Additionnal Replace command
            editor.commands.addCommand({
                name: 'additionnalReplace',
                bindKey: {win: 'Ctrl-R', mac: 'Command-R'},
                exec: function(editor) { handleSearchBox(editor, true); }
            });
        
            // Ace Editor settings
            editor.setOptions({$editorOptions});
            editor.renderer.setOptions({$rendererOptions});
            
            {$setModeScript}
            
            editor.getSession().setValue(textarea.value);
            
            // Keep Ace and textarea synchronized
            editor.on("change", function() {
                textarea.value = editor.getSession().getValue();
            });
            
        }
        
        /** 
         * Function search for the textarea
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
        
        /** 
         * Function to set editor size between fullscreen or not
         */
        var setEditorSize = function(editorContainer, isFullScreen) {
            if (isFullScreen) {
                editorContainer.style.position = 'fixed';
                editorContainer.style.top = '55px';
                editorContainer.style.bottom = '0';
                editorContainer.style.left = '0';
                editorContainer.style.right = '0';
                editorContainer.style['z-index'] = '10'; // Top right menu has z-index of 11
                
                editorContainer.style.width = null;
                editorContainer.style.height = null;
            } else {
                editorContainer.style.position = 'absolute';
                editorContainer.style.width = '100%';
                editorContainer.style.height = '100%';
                
                editorContainer.style.top = null;
                editorContainer.style.bottom = null;
                editorContainer.style.left = null;
                editorContainer.style.right = null;
                editorContainer.style['z-index'] = null;
            }
        }
        
        /** 
         * Function to handle searchbox (show/hide)
         */
        var handleSearchBox = function(editor, isReplace) {
            // Load extension
            ace.config.loadModule("ace/ext/searchbox", function(e) {
                // Launch searchbox
                e.Search(editor, isReplace);
                // Handle searchbox position
                handleSearchBoxPosition(editor, dom.hasCssClass(editor.container, "fullScreen"));
            });
        }
        
        /** 
         * Function to handle searchbox position depending on fullscreen or not
         */
        var handleSearchBoxPosition = function(editor, isFullScreen) {
            if (!editor.searchBox) return;
            
            if (isFullScreen) {
                // If fullscreen, put searchbox on bottom
                editor.searchBox.element.style.top = 'auto';
                editor.searchBox.element.style.bottom = '0';
            } else {
                // If not, unset any specific style value previously set
                editor.searchBox.element.style.top = null;
                editor.searchBox.element.style.bottom = null;
            }
        }
        
        // Start searching!
        tryToGetTextArea();
    })();
</script>
JSSCRIPT;

    $modx->controller->addHtml($script);
}
