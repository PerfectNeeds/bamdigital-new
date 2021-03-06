/**
 * @license Copyright (c) 2003-2015, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.md or http://ckeditor.com/license
 */

CKEDITOR.editorConfig = function (config) {

    // Define changes to default configuration here.
    // For complete reference see:
    // http://docs.ckeditor.com/#!/api/CKEDITOR.config


    // Toolbar
    // ------------------------------

    // The toolbar groups arrangement, optimized for two toolbar rows.
    config.toolbarGroups = [
        {name: 'clipboard', groups: ['clipboard', 'undo']},
        {name: 'editing', groups: ['find', 'selection', 'spellchecker']},
        {name: 'links'},
        {name: 'insert'},
//        {name: 'forms'},
        {name: 'tools'},
        {name: 'document', groups: ['mode', 'document', 'doctools']},
        {name: 'others'},
        '/',
        {name: 'basicstyles', groups: ['basicstyles', 'cleanup']},
        {name: 'paragraph', groups: ['list', 'indent', 'blocks', 'align', 'bidi']},
        {name: 'styles'},
        {name: 'colors'},
        {name: 'pbckcode'}
    ];


    // Extra config
    // ------------------------------

    // Remove some buttons provided by the standard plugins, which are
    // not needed in the Standard(s) toolbar.
    config.removeButtons = 'Underline,Subscript,Superscript';

    // Set the most common block elements.
    config.format_tags = 'p;h1;h2;h3;pre';

    // Simplify the dialog windows.
    config.removeDialogTabs = 'image:advanced;link:advanced';

    // Allow content rules
    config.allowedContent = true;

    // Language
    config.language = 'en';
    config.defaultLanguage = 'en';


    // Extra plugins
    // ------------------------------

    // CKEDITOR PLUGINS LOADING
    config.extraPlugins = 'pbckcode'; // add other plugins here (comma separated)
    config.extraPlugins = 'wordcount'; // add other plugins here (comma separated)

    // PBCKCODE CUSTOMIZATION
    config.pbckcode = {
        // An optional class to your pre tag.
        cls: '',
        // The syntax highlighter you will use in the output view
        highlighter: 'PRETTIFY',
        // An array of the available modes for you plugin.
        // The key corresponds to the string shown in the select tag.
        // The value correspond to the loaded file for ACE Editor.
        modes: [['HTML', 'html'], ['CSS', 'css'], ['PHP', 'php'], ['JS', 'javascript']],
        // The theme of the ACE Editor of the plugin.
        theme: 'textmate',
        // Tab indentation (in spaces)
        tab_size: '4',
        // the root path of ACE Editor. Useful if you want to use the plugin
        // without any Internet connection
        js: "http://cdn.jsdelivr.net//ace/1.1.4/noconflict/"
    };

    config.wordcount = {
        // Whether or not you want to show the Paragraphs Count
        showParagraphs: true,
        // Whether or not you want to show the Word Count
        showWordCount: true,
        // Whether or not you want to show the Char Count
        showCharCount: false,
        // Whether or not you want to count Spaces as Chars
        countSpacesAsChars: false,
        // Whether or not to include Html chars in the Char Count
        countHTML: false,
        // Maximum allowed Word Count, -1 is default for unlimited
        maxWordCount: -1,
        // Maximum allowed Char Count, -1 is default for unlimited
        maxCharCount: -1,
        // Add filter to add or remove element before counting (see CKEDITOR.htmlParser.filter), Default value : null (no filter)
        filter: new CKEDITOR.htmlParser.filter({
            elements: {
                div: function (element) {
                    if (element.attributes.class == 'mediaembed') {
                        return false;
                    }
                }
            }
        })
    };

};
