const _ = require('lodash');

export default class Block {
    id;
    code;
    options;
    dataSynchronizer;
    styleSynchronizer;
    messenger;

    constructor (id, code, dataSynchronizer, styleSynchronizer, options, messenger) {
        this.id = id;
        this.code = code;
        this.options = options;
        this.dataSynchronizer = dataSynchronizer;
        this.styleSynchronizer = styleSynchronizer;
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

    style (prefix, styles) {
        let id = _.uniqueId(`tued-element-style-${prefix}-`);

        this.styleSynchronizer.reactiveStyle[id] = styles;

        return id;
    }

    generateBlockPrefix (operation) {
        return `structure.block.instance.${this.id}.${operation}`;
    }
}
