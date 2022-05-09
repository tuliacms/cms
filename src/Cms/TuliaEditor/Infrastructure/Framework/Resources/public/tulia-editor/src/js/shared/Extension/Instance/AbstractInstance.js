export default class AbstractInstance {
    code;
    instance;
    messenger;

    constructor (code, instance, messenger) {
        this.code = code;
        this.instance = instance;
        this.messenger = messenger;
    }

    operation (operation, callback) {
        return this.messenger.operation(this.generatePrefix(operation), callback);
    }

    execute (operation, data) {
        return this.messenger.execute(this.generatePrefix(operation), data);
    }

    generatePrefix (operation) {
        return `ext.operation.${this.instance}.${operation}`;
    }
}
