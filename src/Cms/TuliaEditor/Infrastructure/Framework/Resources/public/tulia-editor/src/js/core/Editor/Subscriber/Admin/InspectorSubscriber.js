export default class InspectorSubscriber {
    constructor(messenger) {
        this.messenger = messenger;
    }

    static getSubscribedEvents() {
        return {
            'editor.ready': 'registerReceivers',
        };
    }

    registerReceivers() {
        const self = this;

        this.messenger.receive('structure.element.inspect', (data) => self.inspect(data.id, data.type));
    }

    inspect(id, type) {
        /**
         * @todo Move this prototype code in proper usecase.
         */
        const elm = document.getElementById(`tued-structure-${type}-${id}`);

        if (!elm) {
            return;
        }

        elm.scrollIntoView();
    }
}
