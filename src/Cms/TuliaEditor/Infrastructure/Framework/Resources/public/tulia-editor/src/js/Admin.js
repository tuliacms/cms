import './../css/tulia-editor.admin.scss';

/*const Fixer = require('shared/Structure/Fixer.js').default;
const Translator = require('shared/I18n/Translator.js').default;
const Messenger = require('shared/Messaging/Messenger.js').default;
const AdminRoot = require("components/Admin/Root.vue").default;
const ObjectCloner = require("shared/Utils/ObjectCloner.js").default;
const Location = require('shared/Utils/Location.js').default;*/

import Container from "core/Admin/DependencyInjection/Container";

let instances = 0;

export default class Admin {
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
        this.options = $.extend({}, TuliaEditor.config.defaults, TuliaEditor.config.dynamic, this.options);
        this.options.translations = TuliaEditor.translations;
        this.options.directives = TuliaEditor.directives;
        this.options.controls = TuliaEditor.controls;
        this.options.extensions = TuliaEditor.extensions;
        this.options.blocks = TuliaEditor.blocks;

        this.container = new Container(this.options);
        this.container.set('admin', this);
        this.container.set('instanceId', this.instanceId);
        this.container.set('root', this.root);
        this.container.set('options', this.options);
        this.container.build();

        this.container.get('view').render();

        TuliaEditor.instances[this.instanceId] = this;

        //this.renderPreview();

        //if (this.options.start_point === 'editor') {
        //    this.openEditor();
        //}
    }

    openEditor () {
        this.container.get('view').open();
    }

    closeEditor () {
        this.container.get('view').close();
    }

    /*updateContent (structure, content, style) {
        document.querySelector(this.options.sink.structure).value = JSON.stringify(structure);

        if (!content) {
            this.updatePreview('');
            document.querySelector(this.options.sink.content).value = '';
        } else {
            this.updatePreview(content + `<style>${style}</style>`);
            document.querySelector(this.options.sink.content).value = content + `<style>${style}</style>`;
        }
    };*/

    /*renderPreview () {
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
    }*/

    /*createPreviewHeightWatcher () {
        clearInterval(this.previewHeightWatcherInterval);
        this.previewHeightWatcherInterval = setInterval(() => {
            this.updatePreviewHeight();
        }, 2000);
    }*/

    /*updatePreviewHeight () {
        let newHeight = this.previewRoot.offsetHeight;

        if (newHeight !== this.previewHeight) {
            this.previewHeight = newHeight;
            this.root.find('iframe.tued-preview').height(newHeight);
        }
    }*/

    /*updatePreview (preview) {
        const form = this.previewRoot.querySelector('#tulia-editor-preview-form');
        const input = form.querySelector('input');

        if (!preview) {
            preview = '<div class="tued-empty-content">' + this.trans('startCreatingNewContent') + '</div>';
        }

        this.root.find('.tued-preview-wrapper').addClass('tued-preview-loading');

        input.value = preview;
        form.submit();
    }*/

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

        this.vue = this.container.get('vueFactory').factory(
            AdminRoot,
            data,
            this.options.directives,
            this.options.controls,
            this.options.extensions,
            this.options.blocks,
        );
        this.vue.mount(`#tued-editor-window-inner-${this.instanceId}`);

        if (Location.getQueryVariable('showDebugbar') === 'true') {
            this.toggleDebugbar();
        }
    };

    /*toggleRenderPreview () {
        this.container.messenger.execute('editor.canvas.preview.toggle');
    };

    toggleDebugbar () {
        this.editor.toggleClass('tued-editor-debugar-opened');
    }*/

    trans(id) {
        return this.container.get('translator').trans(id);
    }
}
