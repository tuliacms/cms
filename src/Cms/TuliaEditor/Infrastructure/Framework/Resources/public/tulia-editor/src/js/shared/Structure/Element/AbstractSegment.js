const Data = require('shared/Structure/Element/Data/Data.js').default;
const ElementStyle = require('shared/Structure/Element/Style/ElementStyle.js').default;

export default class AbstractSegment {
    segment;
    id;
    code;
    options;
    messenger;
    extensions;
    controls;
    structureTraversator;
    contextmenuService;
    assets;

    constructor (
        type,
        element,
        options,
        messenger,
        extensions,
        controls,
        structureTraversator,
        contextmenu,
        assets,
    ) {
        this.segment = this.getSegment();
        this.type = type;
        this.id = element.id;
        this.code = element.code;
        this.options = options;
        this.messenger = messenger;
        this.extensions = extensions;
        this.controls = controls;
        this.structureTraversator = structureTraversator;
        this.contextmenuService = contextmenu;
        this.assets = assets;
        this.dataSynchronizer = new Data(element.id, this.type, this.segment, element.data, this.messenger);
        this.styleSynchronizer = new ElementStyle(element.style);

        this.init();
    }

    init () {}

    getSegment () {}

    getParent () {
        return this.structureTraversator.getParent();
    }

    expectsFullWidthSection () {
        this.getParent().getParent().getParent().data.containerWidth = 'full-width-no-padding';
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
        return `structure.${this.type}.instance.${this.id}.${operation}`;
    }

    extension (name) {
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

    control (name) {
        return this.controls.get(name);
    }
}
