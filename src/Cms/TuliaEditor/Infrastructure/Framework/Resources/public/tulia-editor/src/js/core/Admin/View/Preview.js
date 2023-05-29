export default class Preview {
    firstLoaded = false;
    previewHeightWatcherInterval;

    constructor(root, eventBus, translator, instanceId, options) {
        this.root = root;
        this.eventBus = eventBus;
        this.translator = translator;
        this.instanceId = instanceId;
        this.options = options;
    }

    render() {
        this.root.find('.tued-main-window').append(
            '<div class="tued-preview-wrapper tued-preview-loading">' +
                '<div class="tued-preview-loader"><div class="tued-preview-notch"><i class="fas fa-circle-notch fa-spin"></i><span>Loading preview...</span></div></div>' +
                '<iframe class="tued-preview" src="' + this.options.editor.preview + '?tuliaEditorInstance=' + this.instanceId + '"></iframe>' +
            '</div>'
        );

        const self = this;

        this.root.find('iframe.tued-preview').on('load', function() {
            self.previewRoot = this.contentWindow.document.body;
            self.previewRoot.querySelector('.tued-preview-wrapper').addEventListener('click', () => {
                self.eventBus.dispatch('preview.clicked');
            });

            self.root.find('.tued-preview-wrapper').removeClass('tued-preview-loading');

            if (!self.firstLoaded) {
                self.eventBus.dispatch('preview.ready');
                self.firstLoaded = true;
            } else {
                self.eventBus.dispatch('preview.loaded');
            }

            self._createPreviewHeightWatcher();
            self.updatePreviewHeight();
        });
    }

    refresh(preview) {
        const form = this.previewRoot.querySelector('#tulia-editor-preview-form');
        const input = form.querySelector('input');

        if (!preview) {
            preview = '<div class="tued-empty-content">' + this.translator.trans('startCreatingNewContent') + '</div>';
        }

        this.root.find('.tued-preview-wrapper').addClass('tued-preview-loading');

        input.value = preview;
        form.submit();
    }

    updatePreviewHeight () {
        let newHeight = this.previewRoot.offsetHeight;

        if (newHeight !== this.previewHeight) {
            this.previewHeight = newHeight;
            this.root.find('iframe.tued-preview').height(newHeight);
        }
    }

    _createPreviewHeightWatcher () {
        clearInterval(this.previewHeightWatcherInterval);

        this.previewHeightWatcherInterval = setInterval(() => {
            this.updatePreviewHeight();
        }, 2000);
    }
}
