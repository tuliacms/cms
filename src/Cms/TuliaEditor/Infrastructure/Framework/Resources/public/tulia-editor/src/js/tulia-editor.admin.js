import './../css/tulia-editor.admin.scss';
import Messanger from './shared/Messenger';
import MessageBroker from './shared/MessageBroker';

window.TuliaEditorAdmin = function (selector, options) {
    this.selector = selector;
    this.options = options;
    this.instanceId = null;
    this.root = null;
    this.editor = null;
    this.messanger = null;
    this.messageBroker = null;

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
        if (this.editor) {
            this.editor.removeClass('tueda-editor-open');
            return;
        }

        this.renderEditorWindow();
    };

    this.closeEditor = function () {
        if (this.editor) {
            this.editor.addClass('tueda-editor-open');
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

    this.renderEditorWindow = function () {
        this.editor = $('<div class="tueda-editor-window"><iframe src="' + this.options.editor.view + '?tuliaEditorInstance=' + this.instanceId + '"></iframe></div>');
        this.editor.appendTo('body');
    };

    this.renderMainWindow = function () {
        this.root.append('<div class="tueda-main-window">' +
            '<div class="tueda-header">' +
                '<div class="tueda-preview-headline">' +
                    '<span class="tueda-logo">Tulia Editor</span> - podgląd treści' +
                '</div>' +
                '<button type="button" class="tuedr-btn" data-tued-action="edit">Edytuj</button>' +
            '</div>' +
            '<div class="tueda-preview"></div>' +
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
    start_point: 'default'
};
