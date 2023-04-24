export default class ContentRendering {
    constructor(messenger) {
        this.messenger = messenger;
    }

    setNodeReference(node) {
        this.node = node;
    }

    render() {
        /**
         * I dont know why, but without timeout here, content does not contain last change.
         * It looks like I get the changes from Store, but Vue needs some time to rerender
         * the DOM.
         */
        setTimeout(() => {
            this.messenger.send('editor.content.rendered', this.node.value.innerHTML);
        }, 30);
    }
}
