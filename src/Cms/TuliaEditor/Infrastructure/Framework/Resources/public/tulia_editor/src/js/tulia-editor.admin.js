/**!
 * Tulia Editor
 * @author	Adam Banaszkiewicz <adam@codevia.com>
 * @license MIT only with Tulia CMS package. Usage outside the Tulia CMS package is prohibited.
 */

import './../css/tulia-editor.admin.scss';

const Vue = require('vue');
const Messenger = require('shared/Messenger.js').default;
const MessageBroker = require('shared/MessageBroker.js').default;
const Fixer = require('shared/Structure/Fixer.js').default;
const Translator = require('shared/I18n/Translator.js').default;
const EventDispatcher = require('shared/EventDispatcher.js').default;
const AdminRoot = require("components/Admin/Root.vue").default;
const ObjectCloner = require("shared/Utils/ObjectCloner.js").default;
const extensions = require("extensions/extensions.js").default;
const blocks = require("blocks/blocks.js").default;

let instances = 0;

export class TuliaEditor {
    selector = null;
    options = null;
    instanceId = null;
    root = null;
    editor = null;
    vue = null;
    container = {};

    static extensions = {};

    static blocks = {};

    static translations = {
        en: {
            save: 'Save',
            cancel: 'Cancel',
            newSection: 'New section',
            newBlock: 'New block',
            section: 'Section',
            row: 'Row',
            column: 'Column',
            block: 'Block',
            selected: 'Selected',
            structure: 'Structure',
        }
    };

    static defaults = {
        structure: {},
        editor: {
            view: null,
        },
        /**
         * 'default' - default view.
         * 'editor' - opens editor immediately
         */
        start_point: 'default',
        sink: {
            // HTML input/textarea selector, where to store the structure.
            structure: null,
            // HTML input/textarea selector, where to store the rendered content.
            content: null
        },
        canvas: {
            size: {
                default: 'xl',
                breakpoints: [
                    { name: 'xxl', width: 1440 },
                    { name: 'xl', width: 1220 },
                    { name: 'lg', width: 1000 },
                    { name: 'md', width: 770 },
                    { name: 'sm', width: 580 },
                    { name: 'xs', width: 320 },
                ]
            }
        },
        locale: 'en_en',
        fallback_locales: ['en']
    };

    constructor (selector, options) {
        this.selector = selector;
        this.options = options;

        this.init();
    }

    init () {
        this.root = $(this.selector);
        this.instanceId = ++instances;
        this.options = $.extend({}, TuliaEditor.defaults, this.options);
        this.options.translations = TuliaEditor.translations;

        this.options.structure.source = (new Fixer())
            .fix(this.options.structure.source);

        this.container.editor = this;
        this.container.messenger = new Messenger(this.instanceId, window, 'root');
        this.container.messageBroker = new MessageBroker(this.instanceId, [window]);
        this.container.translator = new Translator(
            this.options.locale,
            this.options.fallback_locales,
            this.options.translations
        );
        this.container.eventDispatcher = new EventDispatcher();

        this.renderMainWindow();
        this.bindEvents();
        this.startMessaging();

        if (this.options.start_point === 'editor') {
            this.openEditor();
        }
    }

    loadExtensions (vueApp) {
        for (let i in extensions) {
            vueApp.component(i, extensions[i]);
        }
    }

    openEditor () {
        $('body').addClass('tued-editor-opened');

        if (this.editor) {
            this.editor.addClass('tued-editor-opened');
        } else {
            this.renderEditorWindow();
        }
    }

    closeEditor () {
        if (this.editor) {
            this.editor.removeClass('tued-editor-opened');
            $('body').removeClass('tued-editor-opened');
        }
    }

    bindEvents () {
        $('[data-tued-action=edit]').click(() => {
            this.openEditor();
        });
    }

    startMessaging () {
        this.container.messenger.listen('editor.init.fetch', () => {
            this.container.messenger.send('editor.init.data', this.options);
        });

        this.container.messageBroker.start();
    };

    updateContent (structure, content) {
        document.querySelector(this.options.sink.structure).value = JSON.stringify(structure);
        document.querySelector(this.options.sink.content).value = content;
    };

    renderEditorWindow () {
        this.editor = $(`<div class="tued-editor-window tued-editor-opened">
            <div id="tued-editor-window-inner-${this.instanceId}"></div>
        </div>`);
        this.editor.appendTo('body');

        this.createVueApp();
    };

    renderMainWindow () {
        this.root.append('<div class="tued-main-window">' +
            '<div class="tued-header">' +
                '<div class="tued-preview-headline">' +
                    '<span class="tued-logo">Tulia Editor</span> - podgląd treści' +
                '</div>' +
                '<button type="button" class="tued-btn" data-tued-action="edit">Edytuj</button>' +
            '</div>' +
            '<div class="tued-preview"></div>' +
        '</div>');
    };

    createVueApp () {
        let breakpoints = ObjectCloner.deepClone(this.options.canvas.size.breakpoints);
        let defaultBreakpoint = {
            name: 'xl',
            width: 1200,
        };

        for (let i in breakpoints) {
            if (breakpoints[i].name === this.options.canvas.size.default) {
                defaultBreakpoint.name = breakpoints[i].name;
                defaultBreakpoint.width = breakpoints[i].width;
            }
        }

        const data = {
            editor: this,
            container: this.container,
            instanceId: this.instanceId,
            options: ObjectCloner.deepClone(this.options),
            availableBlocks: blocks,
            structure: ObjectCloner.deepClone(this.options.structure.source),
            canvas: {
                size: {
                    breakpoints: breakpoints,
                    breakpoint: defaultBreakpoint
                }
            }
        };

        this.vue = Vue.createApp(AdminRoot, data);

        this.loadExtensions(this.vue);

        this.vue.config.devtools = true;
        this.vue.config.performance = true;
        this.vue.mount(`#tued-editor-window-inner-${this.instanceId}`);
    };
}

