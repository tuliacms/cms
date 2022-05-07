const Data = require('shared/Structure/Element/Data/Data.js').default;
const ElementStyle = require('shared/Structure/Element/Style/ElementStyle.js').default;

export default class AbstractSegment {
    segment;
    id;
    code;
    props;
    options;
    messenger;
    extensions;
    childrenManager;
    children = [];

    constructor (segment, type, element, options, messenger, extensions, childrenManager) {
        this.segment = segment;
        this.type = type;
        this.id = element.id;
        this.code = element.code;
        this.options = options;
        this.messenger = messenger;
        this.extensions = extensions;
        this.childrenManager = childrenManager;
        this.dataSynchronizer = new Data(element.id, this.type, segment, element.data, this.messenger);
        this.styleSynchronizer = new ElementStyle(element.style);

        let children = [];
        if (this.type === 'section') {
            for (let i in element.rows) {
                children.push({row: element.rows[i]});
            }
        } else if (this.type === 'row') {
            for (let i in element.columns) {
                children.push({column: element.columns[i]});
            }
        } else if (this.type === 'column') {
            for (let i in element.blocks) {
                children.push({block: element.blocks[i]});
            }
        }

        this.children = this.getChildren(children);

        this.init();
    }

    init () {}

    getChildren (sourceChildren) {}

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
        return `structure.${this.type}.instance.${this.id}.${operation}`;
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
