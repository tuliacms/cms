export default class Block {
    id;
    code;
    options;
    dataSynchronizer;
    messenger;

    constructor (id, code, dataSynchronizer, options, messenger) {
        this.id = id;
        this.code = code;
        this.options = options;
        this.dataSynchronizer = dataSynchronizer;
        this.messenger = messenger;

        this.messenger.on('structure.element.created', (type, id) => {
            if (type === 'block' && id === this.id) {
                this.notify('created');
            }
        });
    }

    on (event, listener) {
        this.messenger.on(this.generateBlockPrefix(event), listener);
    }

    execute (operation, body) {
        return this.messenger.execute(this.generateBlockPrefix(operation), body);
    }

    notify (notification, ...body) {
        this.messenger.notify(this.generateBlockPrefix(notification), ...body);
    }

    operation (operation, listener) {
        this.messenger.operation(this.generateBlockPrefix(operation), listener);
    }

    get data () {
        return this.dataSynchronizer.reactiveData;
    }

    generateBlockPrefix (operation) {
        return `structure.block.instance.${this.code}.${this.id}.${operation}`;
    }
}
