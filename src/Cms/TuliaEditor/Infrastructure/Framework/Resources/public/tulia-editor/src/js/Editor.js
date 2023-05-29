import './../css/tulia-editor.editor.scss';
import Location from "core/Shared/Utils/Location";
import Container from "core/Editor/DependencyInjection/Container";
import Messenger from "core/Shared/Bus/WindowsMessaging/Messenger";

export default class Editor {
    instanceId = null;

    constructor (TuliaEditor) {
        this.instanceId = Location.getQueryVariable('tuliaEditorInstance');

        const messenger = new Messenger(this.instanceId, 'editor', 'admin');
        messenger.setDestinationWindow(window.top);
        messenger.receive('init.options', (data) => {
            this.container = new Container(data.options);
            this.container.set('editor', this);
            this.container.set('messenger', messenger);
            this.container.setParameter('options.translations', data.options.translations);
            this.container.setParameter('options.directives', TuliaEditor.directives);
            this.container.setParameter('options.controls', TuliaEditor.controls);
            this.container.setParameter('options.extensions', TuliaEditor.extensions);
            this.container.setParameter('options.blocks', TuliaEditor.blocks);
            this.container.setParameter('instanceId', this.instanceId);
            this.container.build();

            this.container.get('view').render();
            this.container.get('structure.store').update(data.structure);
            this.container.get('eventBus').dispatch('editor.ready');
            messenger.send('editor.ready');
        });

        messenger.send('init.editor');
    }
}
