import './../css/tulia-editor.admin.scss';
import Messenger from './shared/Messenger.js';
import MessageBroker from './shared/MessageBroker.js';
import Structure from './shared/Structure.js';
import Translator from './shared/I18n/Translator.js';
import ObjectCloner from './shared/Utils/ObjectCloner.js';
import Canvas from './admin/Vue/Components/Admin/Canvas/Canvas.vue';
import Sidebar from './admin/Vue/Components/Admin/Sidebar/Sidebar.vue';

import TuliaEditor from './TuliaEditor.js';
window.TuliaEditor = TuliaEditor;

Vue.config.devtools = true;

window.TuliaEditorAdmin = function (selector, options) {
    this.selector = selector;
    this.options = options;
    this.instanceId = null;
    this.root = null;
    this.editor = null;
    this.messenger = null;
    this.messageBroker = null;
    this.translator = null;
    this.vue = null;

    this.init = function () {
        this.instanceId = ++TuliaEditorAdmin.instances;
        this.root = $(this.selector);
        this.messenger = new Messenger(this.instanceId, window, 'root');
        this.messageBroker = new MessageBroker(this.instanceId, [window]);
        this.options = $.extend({}, TuliaEditorAdmin.defaults, this.options);
        this.translator = new Translator(this.options.locale, this.options.fallback_locales);

        TuliaEditor.loadExtensions();
        TuliaEditor.loadBlocks();

        this.options.structure.source = Structure.ensureAllIdsAreUnique(this.options.structure.source);
        this.options.structure.source = Structure.ensureStructureHasTypeInAllElements(this.options.structure.source);
        this.options.structure.source = Structure.ensureColumnsHasSizesPropertyInStructure(this.options.structure.source);

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
        this.messenger.listen('editor.init.fetch', () => {
            this.messenger.send('editor.init.data', this.options);
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

        let editor = this;

        this.vue = new Vue({
            el: `#tued-editor-window-inner-${this.instanceId}`,
            template: `<div class="tued-editor-window-inner">
                <div class="tued-container">
                    <Canvas
                        :editorView="\`${options.editor.view}?tuliaEditorInstance=${this.instanceId}\`"
                        :canvas="canvas"
                    ></Canvas>
                    <Sidebar
                        :availableBlocks="availableBlocks"
                        :structure="currentStructure"
                        :messenger="messenger"
                        :canvas="canvas"
                        :translator="translator"
                    ></Sidebar>
                </div>
            </div>`,
            components: {
                Canvas,
                Sidebar
            },
            data () {
                let breakpoints = ObjectCloner.deepClone(editor.options.canvas.size.breakpoints);
                let defaultBreakpoint = {
                    name: 'xl',
                    width: 1200,
                };

                for (let i in breakpoints) {
                    if (breakpoints[i].name === editor.options.canvas.size.default) {
                        defaultBreakpoint.name = breakpoints[i].name;
                        defaultBreakpoint.width = breakpoints[i].width;
                    }
                }

                return {
                    instanceId: editor.instanceId,
                    options: ObjectCloner.deepClone(editor.options),
                    messenger: editor.messenger,
                    translator: editor.translator,
                    availableBlocks: TuliaEditor.blocks,
                    // currentStructure store structure live updated from Editor iframe instance.
                    // Default value of this field is a value from options.
                    currentStructure: ObjectCloner.deepClone(editor.options.structure.source),
                    previousStructure: ObjectCloner.deepClone(editor.options.structure.source),
                    canvas: {
                        size: {
                            breakpoints: breakpoints,
                            breakpoint: defaultBreakpoint
                        }
                    }
                };
            },
            methods: {
                restorePreviousStructure: function () {
                    this.currentStructure = ObjectCloner.deepClone(this.previousStructure);
                    this.messenger.send('editor.structure.restored');
                },
                useCurrentStructureAsPrevious: function () {
                    this.previousStructure = ObjectCloner.deepClone(this.currentStructure);
                }
            },
            mounted () {
                this.$root.$on('editor.save', () => {
                    this.messenger.listen('structure.rendered.data', (content) => {
                        this.useCurrentStructureAsPrevious();
                        this.messenger.send('structure.synchronize.from.admin', this.currentStructure);
                        editor.updateFields(
                            ObjectCloner.deepClone(this.currentStructure),
                            content
                        );
                        editor.closeEditor();
                        this.messenger.send('editor.save');
                    });

                    this.messenger.send('structure.rendered.fetch');
                });
                this.$root.$on('editor.cancel', () => {
                    this.restorePreviousStructure();
                    this.messenger.send('structure.synchronize.from.admin', this.currentStructure);
                    editor.closeEditor();
                    this.messenger.send('editor.cancel');
                });
                this.$root.$on('device.size.changed', (size) => {
                    this.messenger.send('device.size.changed', size);
                });
                this.$root.$on('structure.selection.outsite', () => {
                    this.messenger.send('structure.selection.deselected');
                });
                this.$root.$on('structure.column.resize', (columnsId, size) => {
                    this.messenger.send('structure.synchronize.from.admin', this.currentStructure);
                });



                this.messenger.listen('structure.synchronize.from.editor', (structure) => {
                    this.currentStructure = structure;
                    this.messenger.send('structure.updated');
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
window.TuliaEditorAdmin.translations = {
    en: {
        save: 'Save',
        cancel: 'Cancel'
    }
};

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
