export default class CreateBlockSubscriber {
    constructor(messenger, blockPocker) {
        this.messenger = messenger;
        this.blockPocker = blockPocker;
    }

    static getSubscribedEvents() {
        return {
            'admin.ready': 'registerReceivers',
        };
    }

    registerReceivers() {
        const self = this;

        this.messenger.receive('structure.create.block', () => self.blockPocker.new());
    }
}
