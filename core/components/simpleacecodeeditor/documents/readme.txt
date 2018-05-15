--------------------
Extra: Simple Ace Code Editor
--------------------
Version: 1.4.4-pl
Created: 15 May. 2018
Since: 17 Feb. 2018
Author: Indigo744 <https://github.com/Indigo744>
Based on the work of Danil Kostin <danya.postfactum@gmail.com> of Ace extra
License: GNU GPLv3

Integrates Ace Code Editor into MODx Revolution in a simple way.


Features:

    Dead simple. Really.
    Works out of the box with sensible default
    CTRL-D to duplicate
    Auto completion on CTRL-SPACE
    MODX syntax highlighting
    Emmet integration
    Full-screen support (F11 while cursor in editor)
    Set a specific MIME type for you chunks (like a shebang)

    
Available properties are:

    AcePath: URL or path to ACE javascript file
             default: https://cdnjs.cloudflare.com/ajax/libs/ace/1.3.3/ace.js

    Theme: editor theme name (you can test them all here: https://ace.c9.io/build/kitchen-sink.html)
           default: monokai

    ReplaceCTRLDKbdShortcut: Replace the CTRL-D (or CMD-D) keyboard shortcut to perform a more sensible action
                             duplicate the current line or selection (instead of deleting, which is the default behavior)
                             default: true

    Autocompletion: Enable Autocompletion: none, basic (show on CTRL-Space) or live (show on typing)
                    Note that "ext-language_tools.js" must be available alongside ace.js (will be retrieve from <AcePath>/ext-language_tools.js)
                    default: basic

    SettingsMenu: Add a settings menu accessible with CTR-Q (or CMD-Q)
                  Note that "ext-settings_menu.js" must be available alongside ace.js (will be retrieve from <AcePath>/ext-settings_menu.js)
                  default: false

    Spellcheck: Enable spellcheck
                Note that "ext-spellcheck.js" must be available alongside ace.js (will be retrieve from <AcePath>/ext-spellcheck.js)
                default: false

    EmmetPath: URL or path to Emmet js file
               For more information, see https://github.com/cloud9ide/emmet-core
               default: https://cloud9ide.github.io/emmet-core/emmet.js

    Emmet: Enable emmet
           Note that Emmet JS file must be loaded first (see EmmetPath, it must be correctly set)
           Note that "ext-emmet.js" must be available alongside ace.js (will be retrieve from <AcePath>/ext-emmet.js)
           It is recommended to disable ReplaceCTRLDKbdShortcut property when using Emmet (as it replace an Emmet shortcut CTRL-D)
           default: false

    AcePrintMarginColumn: Print margin column position
                          Set the character position of the print margin (for instance useful if you like to code with 80 chars wide max)
                          Set to 0 to disable it completely
                          default: 0 (disabled)

    ChunkDetectMIMEShebang: Enable 'shebang-style' MIME detection for chunks (in description or in the first line of chunk content)
                            This is particularly useful if your chunk contains directly JS, or SASS, or anything different than HTML...
                            Supported MIME values are text/x-smarty, text/html, application/xhtml+xml, text/css, text/x-scss, 
                                                      text/x-sass, text/x-less, image/svg+xml, application/xml, text/xml, text/javascript, 
                                                      application/javascript, application/json, text/x-php, application/x-php, text/x-sql, 
                                                      text/x-markdown, text/plain, text/x-twig
                            default: true

