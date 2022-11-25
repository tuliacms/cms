import './../css/tulia-editor.admin.scss';

const Vue = require('vue');
const Fixer = require('shared/Structure/Fixer.js').default;
const Translator = require('shared/I18n/Translator.js').default;
const Messenger = require('shared/Messaging/Messenger.js').default;
const AdminRoot = require("components/Admin/Root.vue").default;
const ObjectCloner = require("shared/Utils/ObjectCloner.js").default;
const Location = require('shared/Utils/Location.js').default;

let instances = 0;

export default class Editor {
    selector = null;
    options = null;
    instanceId = null;
    root = null;
    previewRoot = null;
    previewHeight = null;
    editor = null;
    vue = null;
    container = {};
    previewHeightWatcherInterval = null;

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

        this.awaitSegmentsReady();
        this.renderMainWindow();
        this.renderModalsContainer();
        this.bindEvents();

        this.container.messenger.operation('editor.init.fetch', (params, success, fail) => {
            success(this.options);
        });

        this.renderPreview();

        if (this.options.start_point === 'editor') {
            this.openEditor();
        }
    }

    loadDirectives (vueApp) {
        for (let i in TuliaEditor.directives) {
            vueApp.directive(i, TuliaEditor.directives[i]);
        }
    }

    loadControls (vueApp) {
        for (let i in TuliaEditor.controls) {
            vueApp.component(i, TuliaEditor.controls[i]);
        }
    }

    loadExtensions (vueApp) {
        for (let i in TuliaEditor.extensions) {
            if (TuliaEditor.extensions[i].Manager) {
                vueApp.component(i + 'Manager', TuliaEditor.extensions[i].Manager);
            }
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

    awaitSegmentsReady () {
        this.container.messenger.on('editor.segment.ready', (segment) => {
            //console.log(segment);
        });
    }

    bindEvents () {
        this.root.find('.tued-preview')[0].onload = () => {
            // Add preview window in canvas and editor Messengers
            //this.container.messenger.addWindow(this.root.find('.tued-preview')[0].contentWindow);
            this.container.messenger.notify('editor.segment.ready', 'preview');
        }
    }

    updateContent (structure, content, style) {
        document.querySelector(this.options.sink.structure).value = JSON.stringify(structure);

        if (!content) {
            this.updatePreview('');
            document.querySelector(this.options.sink.content).value = '';
        } else {
            this.updatePreview(content + `<style>${style}</style>`);
            document.querySelector(this.options.sink.content).value = content + `<style>${style}</style>`;
        }
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
                '<span class="tued-logo">Tulia Editor</span> - ' + this.container.translator.trans('contentPreview') +
                '</div>' +
            '</div>' +
            '<div class="tued-preview-wrapper tued-preview-loading">' +
                '<div class="tued-preview-loader"><div class="tued-preview-notch"><i class="fas fa-circle-notch fa-spin"></i><span>Loading preview...</span></div></div>' +
                '<iframe class="tued-preview" src="' + this.options.editor.preview + '?tuliaEditorInstance=' + this.instanceId + '"></iframe>' +
            '</div>' +
        '</div>');
    };

    renderModalsContainer () {
        if ($('#tued-modals-container').length) {
            return;
        }

        $('body').append('<div id="tued-modals-container"></div>');
    }

    renderPreview () {
        this.root.find('.tued-preview').on('load', () => {
            this.previewRoot = this.root.find('iframe.tued-preview')[0].contentWindow.document.body;
            this.previewRoot.querySelector('.tued-preview-wrapper').addEventListener('click', () => {
                this.openEditor();
            });

            if (!this.previewLoaded) {
                this.updatePreview(this.options.structure.preview);
            } else {
                this.root.find('.tued-preview-wrapper').removeClass('tued-preview-loading');
            }

            this.previewLoaded = true;
            this.createPreviewHeightWatcher();
            this.updatePreviewHeight();
        });
    }

    createPreviewHeightWatcher () {
        clearInterval(this.previewHeightWatcherInterval);
        this.previewHeightWatcherInterval = setInterval(() => {
            this.updatePreviewHeight();
        }, 2000);
    }

    updatePreviewHeight () {
        let newHeight = this.previewRoot.offsetHeight;

        if (newHeight !== this.previewHeight) {
            this.previewHeight = newHeight;
            this.root.find('iframe.tued-preview').height(newHeight);
        }
    }

    updatePreview (preview) {
        const form = this.previewRoot.querySelector('#tulia-editor-preview-form');
        const input = form.querySelector('input');

        if (!preview) {
            preview = '<div class="tued-empty-content">' + this.container.translator.trans('startCreatingNewContent') + '</div>';
        }

        this.root.find('.tued-preview-wrapper').addClass('tued-preview-loading');

        input.value = preview;
        form.submit();
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

        this.loadDirectives(this.vue);
        this.loadControls(this.vue);
        this.loadExtensions(this.vue);
        this.loadBlocks(this.vue);

        // DEV
        //this.vue.config.devtools = true;
        //this.vue.config.performance = true;
        // PROD
        this.vue.config.devtools = false;
        this.vue.config.debug = false;
        this.vue.config.silent = true;

        this.vue.mount(`#tued-editor-window-inner-${this.instanceId}`);

        if (Location.getQueryVariable('showDebugbar') === 'true') {
            this.toggleDebugbar();
        }
    };

    toggleRenderPreview () {
        this.container.messenger.execute('editor.canvas.preview.toggle');
    };

    toggleDebugbar () {
        this.editor.toggleClass('tued-editor-debugar-opened');
    }
}
