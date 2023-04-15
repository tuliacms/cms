export default class View {
    constructor(root, instanceId, translator, eventBus) {
        this.translator = translator;
        this.instanceId = instanceId;
        this.root = root;
        this.eventBus = eventBus;
        this.editor = null;
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
        this.renderEditorWindow();

        this.root.find('.tued-preview-wrapper').click(() => {
            this.open();
        });

        this.eventBus.dispatch('admin.view.ready');
    };

    open () {
        $('body').addClass('tued-editor-opened');
        this.editor.addClass('tued-editor-opened');

        this.eventBus.dispatch('editor.opened');
    }

    close () {
        this.editor.removeClass('tued-editor-opened');
        $('body').removeClass('tued-editor-opened');

        this.eventBus.dispatch('editor.closed');
    }

    renderModalsContainer() {
        if ($('#tued-modals-container').length) {
            return;
        }

        $('body').append('<div id="tued-modals-container"></div>');
    }

    renderEditorWindow () {
        this.editor = $(`<div class="tued-editor-window" data-tulia-editor-instance="${this.instanceId}">
            <div id="tued-editor-window-inner-${this.instanceId}"></div>
        </div>`);
        this.editor.appendTo('body');
    };
}
