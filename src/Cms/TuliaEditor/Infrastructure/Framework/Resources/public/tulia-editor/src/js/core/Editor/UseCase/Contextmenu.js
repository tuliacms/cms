import EventTransformer from "core/Shared/ContextMenu/EventTransformer";

export default class Contextmenu {
    constructor(messenger) {
        this.messenger = messenger;
    }

    openUsingEvent(event) {
        const data = EventTransformer.transformPointerEvent(event);

        if (!data || data.targets.length === 0) {
            return;
        }

        this.messenger.send('contextmenu.open', data);

        event.preventDefault();
    }

    hide() {
        this.messenger.send('contextmenu.hide');
    }
}
