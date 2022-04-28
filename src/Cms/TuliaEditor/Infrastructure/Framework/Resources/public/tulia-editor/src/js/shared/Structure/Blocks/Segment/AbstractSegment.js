const Data = require('shared/Structure/Blocks/Data.js').default;
const ElementStyle = require('shared/Structure/Style/ElementStyle.js').default;

export default class AbstractSegment {
    segment;
    id;
    code;
    props;
    options;
    messenger;
    extensions;

    constructor (segment, props, options, messenger, extensions) {
        this.segment = segment;
        this.id = props.id;
        this.code = props.code;
        this.options = options;
        this.messenger = messenger;
        this.extensions = extensions;
        this.dataSynchronizer = new Data(props.id, segment, props.data, this.messenger);
        this.styleSynchronizer = new ElementStyle(props.style);

        this.init();
    }

    init () {}

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

    extension (name, ...params) {
        const extension = this.extensions.get(name);

        if (this.segment === 'render') {
            return extension.Render;
        }
        if (this.segment === 'manager') {
            return extension.Manager;
        }
        if (this.segment === 'editor') {
            return extension.Editor;
        }
    }
}
