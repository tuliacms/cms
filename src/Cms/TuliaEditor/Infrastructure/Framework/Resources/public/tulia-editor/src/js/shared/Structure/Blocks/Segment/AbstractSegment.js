const Data = require('shared/Structure/Blocks/Data.js').default;
const ElementStyle = require('shared/Structure/Style/ElementStyle.js').default;

export default class AbstractSegment {
    id;
    code;
    props;
    options;
    messenger;

    constructor (segment, props, options, messenger) {
        this.id = props.id;
        this.code = props.code;
        this.options = options;
        this.messenger = messenger;
        this.dataSynchronizer = new Data(props.id, segment, props.data, this.messenger);
        this.styleSynchronizer = new ElementStyle(props.style);

        if (segment === 'manager') {
            this.messenger.on('structure.element.created', (type, id) => {
                if (type === 'block' && id === this.id) {
                    this.notify('created');
                }
            });
        }
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
        return `structure.block.instance.${this.id}.${operation}`;
    }
}
