const { reactive, toRaw } = require('vue');

export default class BlockEditor {
    id;
    options;
    data;
    hooks;

    constructor (code, props, data, options, hooks) {
        this.hooks = hooks;
        this.options = options;

        this.data = reactive(Object.assign({}, data, toRaw(props.data)));
        this.id = props.id;

        this.hooks = hooks.forBlock(this.id);
    }

    on (event, listener) {
        this.hooks.on(event, listener);
    }
}
