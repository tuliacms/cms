import './../css/tulia-editor.admin.scss';

const Vue = require('vue');
const Fixer = require('shared/Structure/Fixer.js').default;
const Translator = require('shared/I18n/Translator.js').default;
const Messenger = require('shared/Messaging/Messenger.js').default;
const AdminRoot = require("components/Admin/Root.vue").default;
const ObjectCloner = require("shared/Utils/ObjectCloner.js").default;

let instances = 0;

export default class Editor {
    selector = null;
    options = null;
    instanceId = null;
    root = null;
    editor = null;
    vue = null;
    container = {};

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
            .fixStructure(this.options.structure.source);

        this.container.editor = this;
        this.container.editor = this;
        this.container.messenger = new Messenger(this.instanceId, 'admin', [window]);
        this.container.translator = new Translator(
            this.options.locale,
            this.options.fallback_locales,
            this.options.translations
        );

        TuliaEditor.instances[this.instanceId] = this;

        this.renderMainWindow();
        this.renderModalsContainer();
        this.bindEvents();

        this.container.messenger.operation('editor.init.fetch', (params, success, fail) => {
            success(this.options);
        });

        if (this.options.start_point === 'editor') {
            this.openEditor();
        }
    }

    loadExtensions (vueApp) {
        for (let i in TuliaEditor.extensions) {
            vueApp.component(i, TuliaEditor.extensions[i]);
        }
    }

    loadBlocks (vueApp) {
        for (let i in TuliaEditor.blocks) {
            vueApp.component('block-' + TuliaEditor.blocks[i].code + '-manager', TuliaEditor.blocks[i].manager);
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

    updateContent (structure, content, style) {
        document.querySelector(this.options.sink.structure).value = JSON.stringify(structure);
        document.querySelector(this.options.sink.content).value = content + `<style>${style}</style>`;
    };

    renderEditorWindow () {
        this.editor = $(`<div class="tued-editor-window tued-editor-opened" data-tulia-editor-instance="${this.instanceId}">
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

    renderModalsContainer () {
        if ($('#tued-modals-container').length) {
            return;
        }

        $('body').append('<div id="tued-modals-container"></div>');
    }

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
            availableBlocks: TuliaEditor.blocks,
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
        this.loadBlocks(this.vue);

        this.vue.config.devtools = true;
        this.vue.config.performance = true;
        this.vue.mount(`#tued-editor-window-inner-${this.instanceId}`);
    };

    toggleRenderPreview () {
        this.container.messenger.execute('editor.canvas.preview.toggle');
    }
}
