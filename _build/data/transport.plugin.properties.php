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
        'desc' => 'URL or path to ACE javascript file (and extensions)',
        'type' => 'textfield',
        'value' => "https://cdnjs.cloudflare.com/ajax/libs/ace/1.3.3/ace.js",
        'options' => '',
    ),
    array(
        'name' => 'Theme',
        'desc' => 'editor theme name (you can test them all here: https://ace.c9.io/build/kitchen-sink.html)',
        'type' => 'list',
        'value' => 'monokai',
        'options' => array(
            array('text' => 'Chrome (bright theme)', 'value' => 'chrome'),
            array('text' => 'Clouds (bright theme)', 'value' => 'clouds'),
            array('text' => 'Crimson Editor (bright theme)', 'value' => 'crimson_editor'),
            array('text' => 'Dawn (bright theme)', 'value' => 'dawn'),
            array('text' => 'Dreamweaver (bright theme)', 'value' => 'dreamweaver'),
            array('text' => 'Eclipse (bright theme)', 'value' => 'eclipse'),
            array('text' => 'GitHub (bright theme)', 'value' => 'github'),
            array('text' => 'IPlastic (bright theme)', 'value' => 'iplastic'),
            array('text' => 'Solarized Light (bright theme)', 'value' => 'solarized_light'),
            array('text' => 'TextMate (bright theme)', 'value' => 'textmate'),
            array('text' => 'Tomorrow (bright theme)', 'value' => 'tomorrow'),
            array('text' => 'XCode (bright theme)', 'value' => 'xcode'),
            array('text' => 'Kuroir (bright theme)', 'value' => 'kuroir'),
            array('text' => 'KatzenMilch (bright theme)', 'value' => 'katzenmilch'),
            array('text' => 'SQL Server (bright theme)', 'value' => 'sqlserver'),
            
            array('text' => 'Ambiance (dark theme)', 'value' => 'ambiance'),
            array('text' => 'Chaos (dark theme)', 'value' => 'chaos'),
            array('text' => 'Clouds Midnight (dark theme)', 'value' => 'clouds_midnight'),
            array('text' => 'Dracula (dark theme)', 'value' => 'dracula'),
            array('text' => 'Cobalt (dark theme)', 'value' => 'cobalt'),
            array('text' => 'Gruvbox (dark theme)', 'value' => 'gruvbox'),
            array('text' => 'Green on Black (dark theme)', 'value' => 'gob'),
            array('text' => 'idle Fingers (dark theme)', 'value' => 'idle_fingers'),
            array('text' => 'krTheme (dark theme)', 'value' => 'kr_theme'),
            array('text' => 'Merbivore (dark theme)', 'value' => 'merbivore'),
            array('text' => 'Merbivore Soft (dark theme)', 'value' => 'merbivore_soft'),
            array('text' => 'Mono Industrial (dark theme)', 'value' => 'mono_industrial'),
            array('text' => 'Monokai (dark theme)', 'value' => 'monokai'),
            array('text' => 'Pastel on dark (dark theme)', 'value' => 'pastel_on_dark'),
            array('text' => 'Solarized Dark (dark theme)', 'value' => 'solarized_dark'),
            array('text' => 'Terminal (dark theme)', 'value' => 'terminal'),
            array('text' => 'Tomorrow Night (dark theme)', 'value' => 'tomorrow_night'),
            array('text' => 'Tomorrow Night Blue (dark theme)', 'value' => 'tomorrow_night_blue'),
            array('text' => 'Tomorrow Night Bright (dark theme)', 'value' => 'tomorrow_night_bright'),
            array('text' => 'Tomorrow Night 80s (dark theme)', 'value' => 'tomorrow_night_eighties'),
            array('text' => 'Twilight (dark theme)', 'value' => 'twilight'),
            array('text' => 'Vibrant Ink (dark theme)', 'value' => 'vibrant_ink'),
        ),
    ),
    array(
        'name' => 'ReplaceCTRLDKbdShortcut',
        'desc' => 'Replace the CTRL-D (or CMD-D) keyboard shortcut to perform a more sensible action: duplicate the current line or selection (instead of deleting, which is the default behavior)',
        'type' => 'combo-boolean',
        'value' => '1',
        'options' => '',
    ),
    array(
        'name' => 'Autocompletion',
        'desc' => 'Enable Autocompletion: none, basic (show on CTRL-Space) or live (show on typing) - Note that "ext-language_tools.js" must be available alongside ace.js',
        'type' => 'list',
        'value' => 'basic',
        'options' => array(
            array('text' => 'None', 'value' => 'none'),
            array('text' => 'Basic (show on CTRL-SPACE)', 'value' => 'basic'),
            array('text' => 'Live (show on typing)', 'value' => 'live'),
        ),
    ),
    array(
        'name' => 'SettingsMenu',
        'desc' => 'Add a settings menu accessible with CTR-Q (or CMD-Q) - Note that "ext-settings_menu.js" must be available alongside ace.js',
        'type' => 'combo-boolean',
        'value' => '0',
        'options' => '',
    ),
    array(
        'name' => 'Spellcheck',
        'desc' => 'Enable spellcheck - Note that "ext-spellcheck.js" must be available alongside ace.js',
        'type' => 'combo-boolean',
        'value' => '0',
        'options' => '',
    ),
    array(
        'name' => 'Emmet',
        'desc' => 'Enable emmet - Note that "ext-emmet.js" must be available alongside ace.js',
        'type' => 'combo-boolean',
        'value' => '0',
        'options' => '',
    ),
    array(
        'name' => 'ChunkDetectMIMEShebang',
        'desc' => "Enable 'shebang-style' MIME detection for chunks (in description or in the first line of chunk content) - \n Supported MIME values are text/x-smarty, text/html, application/xhtml+xml, text/css, text/x-scss, text/x-sass, text/x-less, image/svg+xml, application/xml, text/xml, text/javascript, application/javascript, application/json, text/x-php, application/x-php, text/x-sql, text/x-markdown, text/plain, text/x-twig",
        'type' => 'combo-boolean',
        'value' => '1',
        'options' => '',
    ),
);
return $properties;