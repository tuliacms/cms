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
        '</div>');

        this.renderModalsContainer();
        this.renderEditorWindow();

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
