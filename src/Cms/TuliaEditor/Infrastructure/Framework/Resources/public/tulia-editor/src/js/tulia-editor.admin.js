import './../css/tulia-editor.admin.scss';
import Messanger from './shared/Messenger';
import MessageBroker from './shared/MessageBroker';
import Canvas from './admin/Vue/Components/Canvas/Canvas.vue';
import Sidebar from './admin/Vue/Components/Sidebar/Sidebar.vue';

Vue.config.devtools = true;

window.TuliaEditorAdmin = function (selector, options) {
    this.selector = selector;
    this.options = options;
    this.instanceId = null;
    this.root = null;
    this.editor = null;
    this.messanger = null;
    this.messageBroker = null;
    this.vue = null;

    this.init = function () {
        this.instanceId = ++TuliaEditorAdmin.instances;
        this.root = $(this.selector);
        this.messanger = new Messanger(this.instanceId, window, 'root');
        this.messageBroker = new MessageBroker(this.instanceId, [window]);
        this.options = $.extend({}, TuliaEditorAdmin.defaults, this.options);

        this.renderMainWindow();
        this.bindEvents();
        this.startMessaging();

        if (this.options.start_point === 'editor') {
            this.openEditor();
        }
    };

    this.openEditor = function () {
        $('body').addClass('tued-editor-opened');

        if (this.editor) {
            this.editor.addClass('tued-editor-opened');
        } else {
            this.renderEditorWindow();
        }
    };

    this.closeEditor = function () {
        if (this.editor) {
            this.editor.removeClass('tued-editor-opened');
            $('body').removeClass('tued-editor-opened');
        }
    };

    this.bindEvents = function () {
        $('[data-tued-action=edit]').click(() => {
            this.openEditor();
        });
    };

    this.startMessaging = function () {
        this.messanger.listen('editor.init.fetch', () => {
            this.messanger.send('editor.init.data', this.options);
        });

        this.messageBroker.start();
    };

    this.updateFields = function (structure, content) {
        document.querySelector(this.options.sink.structure).value = JSON.stringify(structure);
        document.querySelector(this.options.sink.content).value = content;
    };

    this.renderEditorWindow = function () {
        this.editor = $(`<div class="tued-editor-window tued-editor-opened">
            <div id="tued-editor-window-inner-${this.instanceId}"></div>
        </div>`);
        this.editor.appendTo('body');

        let self = this;

        this.vue = new Vue({
            el: `#tued-editor-window-inner-${this.instanceId}`,
            template: `<div class="tued-editor-window-inner">
                <div class="tued-container">
                    <Canvas :editorView="\`${options.editor.view}?tuliaEditorInstance=${this.instanceId}\`"></Canvas>
                    <Sidebar :availableBlocks="availableBlocks"></Sidebar>
                </div>
            </div>`,
            components: {
                Canvas,
                Sidebar
            },
            data: {
                instanceId: this.instanceId,
                options: this.options,
                messanger: this.messanger,
                availableBlocks: TuliaEditor.blocks
            },
            mounted () {
                self.messanger.listen('editor.structure.data', (structure, content) => {
                    self.closeEditor();
                    self.updateFields(structure, content);
                });
                self.messanger.listen('editor.structure.restored', () => {
                    self.closeEditor();
                });

                this.$root.$on('editor.save', () => {
                    self.messanger.send('editor.save');
                    self.messanger.send('editor.structure.fetch');
                });
                this.$root.$on('editor.cancel', () => {
                    self.messanger.send('editor.cancel');
                    self.messanger.send('editor.structure.restore');
                });
                this.$root.$on('device.size.changed', (size) => {
                    self.messanger.send('device.size.changed', size);
                });
                this.$root.$on('structure.selectable.outsite', () => {
                    self.messanger.send('structure.selectable.outsite');
                });
            }
        });
    };

    this.renderMainWindow = function () {
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

    this.init();
};

window.TuliaEditorAdmin.instances = 0;

window.TuliaEditorAdmin.defaults = {
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
};
