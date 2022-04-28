const { reactive } = require('vue');

export default class ElementStyle {
    style;

    constructor (styleProperty) {
        this.style = reactive(styleProperty);
    }

    get reactiveStyle () {
        return this.style;
    }
}
