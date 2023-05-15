import './../css/tulia-editor.admin.scss';

import Container from "core/Admin/DependencyInjection/Container";
import Messenger from "core/Shared/Bus/WindowsMessaging/Messenger";

let instances = 0;

export default class Admin {
    selector = null;
    options = null;
    instanceId = null;
    root = null;
    editor = null;
    vue = null;
    container = {};
    sink = {
        structure: null,
        content: null,
    };

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

        this.sink.structure = document.querySelector(this.options.sink.structure);
        this.sink.content = document.querySelector(this.options.sink.content);

        this.options.structure = this.sink.structure.value
            ? JSON.parse(this.sink.structure.value)
            : {};

        const messenger = new Messenger(this.instanceId, 'admin', 'editor');

        this.container = new Container(this.options);
        this.container.setParameter('instanceId', this.instanceId);
        this.container.setParameter('options.translations', TuliaEditor.translations);
        this.container.setParameter('options.directives', TuliaEditor.directives);
        this.container.setParameter('options.controls', TuliaEditor.controls);
        this.container.setParameter('options.extensions', TuliaEditor.extensions);
        this.container.setParameter('options.blocks', TuliaEditor.blocks);
        this.container.set('admin', this);
        this.container.set('root', this.root);
        this.container.set('messenger', messenger);
        this.container.build();

        TuliaEditor.instances[this.instanceId] = this;

        /**
         * @todo Set this content here temporary. Needs to be moved in proper place.
         */
        this.container.get('structure.renderer').setContent(this.sink.content.value);

        this.container.get('view').render();
        this.container.get('eventBus').dispatch('admin.ready');
        this.container.get('eventBus').listen('editor.saved', ({ source, content }) => {
            console.log(source, content);

            this.sink.structure.value = JSON.stringify(source);
            this.sink.content.value = content;
        });

        //this.renderPreview();

        //if (this.options.start_point === 'editor') {
        //    this.openEditor();
        //}
    }

    openEditor () {
        this.container.get('usecase.editorWindow').open();
    }

    closeEditor () {
        this.container.get('usecase.editorWindow').cancel();
    }
}
