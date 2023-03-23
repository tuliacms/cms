export default class View {
    editor;

    constructor(root, instanceId, translator, admin, eventBus) {
        this.translator = translator;
        this.instanceId = instanceId;
        this.root = root;
        this.admin = admin;
        this.eventBus = eventBus;
    }

    render() {
        this.root.append('<div class="tued-main-window">' +
            '<div class="tued-header">' +
                '<div class="tued-preview-headline">' +
                '<span class="tued-logo">Tulia Editor</span> - ' + this.translator.trans('contentPreview') +
                '</div>' +
            '</div>' +
            '<div class="tued-preview-wrapper tued-preview-loading-EE">' +
                '<div class="tued-preview-loader"><div class="tued-preview-notch"><i class="fas fa-circle-notch fa-spin"></i><span>Loading preview...</span></div></div>' +
                '<img class="tued-preview" src="https://placehold.co/1095x400" style="height:400px">' +
                /*'<iframe class="tued-preview" src="' + this.options.editor.preview + '?tuliaEditorInstance=' + this.instanceId + '"></iframe>' +*/
            '</div>' +
        '</div>');

        this.renderModalsContainer();

        this.root.find('.tued-preview-wrapper').click(() => {
            this.admin.openEditor();
        });
    };

    open () {
        $('body').addClass('tued-editor-opened');

        if (this.editor) {
            this.editor.addClass('tued-editor-opened');
        } else {
            this.renderEditorWindow();
        }
    }

    close () {
        if (this.editor) {
            this.editor.removeClass('tued-editor-opened');
            $('body').removeClass('tued-editor-opened');
        }
    }

    renderModalsContainer() {
        if ($('#tued-modals-container').length) {
            return;
        }

        $('body').append('<div id="tued-modals-container"></div>');
    }

    renderEditorWindow () {
        this.editor = $(`<div class="tued-editor-window tued-editor-opened" data-tulia-editor-instance="${this.instanceId}">
            <div id="tued-editor-window-inner-${this.instanceId}"></div>
        </div>`);
        this.editor.appendTo('body');

        this.eventBus.dispatch('admin.view.ready');
    };
}
