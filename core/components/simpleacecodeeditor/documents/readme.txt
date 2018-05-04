--------------------
Extra: Simple Ace Code Editor
--------------------
Version: 1.3.0
Created: 04 May. 2018
Since: 17 Feb. 2018
Author: Indigo744 <https://github.com/Indigo744>
Based on the work of Danil Kostin <danya.postfactum@gmail.com> of Ace extra
License: GNU GPLv3

Integrates Ace Code Editor into MODx Revolution in a simple way.

Available properties are:

    AcePath: URL or path to ACE javascript file
             default: https://cdnjs.cloudflare.com/ajax/libs/ace/1.3.1/ace.js

    Theme: editor theme name (you can test them all here: https://ace.c9.io/build/kitchen-sink.html)
           default: monokai

    ReplaceCTRLDKbdShortcut: Replace the CTRL-D (or CMD-D) keyboard shortcut to perform a more sensible action
                             duplicate the current line or selection (instead of deleting, which is the default behavior)
                             default: true

    Autocompletion: Enable Autocompletion: none, basic (show on CTRL-Space) or live (show on typing)
                    Note that "ext-language_tools.js" must be available alongside ace.js
                    default: basic

    SettingsMenu: Add a settings menu accessible with CTR-Q (or CMD-Q)
                  Note that "ext-settings_menu.js" must be available alongside ace.js
                  default: false

    Spellcheck: Enable spellcheck
                Note that "ext-spellcheck.js" must be available alongside ace.js
                default: false

    Emmet: Enable emmet
           Note that "ext-emmet.js" must be available alongside ace.js
           default: false

    ChunkDetectMIMEShebang: Enable 'shebang-style' MIME detection for chunks (in description or in the first line of chunk content)
                            This is particularly useful if your chunk contains directly JS, or SASS, or anything different than HTML...
                            Supported MIME values are text/x-smarty, text/html, application/xhtml+xml, text/css, text/x-scss, 
                                                      text/x-sass, text/x-less, image/svg+xml, application/xml, text/xml, text/javascript, 
                                                      application/javascript, application/json, text/x-php, application/x-php, text/x-sql, 
                                                      text/x-markdown, text/plain, text/x-twig
                            default: true

