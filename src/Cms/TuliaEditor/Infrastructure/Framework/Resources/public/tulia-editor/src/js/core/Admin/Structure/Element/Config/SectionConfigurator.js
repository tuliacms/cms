class Configurator {
    constructor(section) {
        this.section = section;
    }

    defaultWidth() {
        this.section.config.containerWidth = 'default';
        return this;
    }

    fullWidth() {
        this.section.config.containerWidth = 'full-width';
        return this;
    }

    fullWidthNoPadding() {
        this.section.config.containerWidth = 'full-width-no-padding';
        return this;
    }
}

export default class SectionConfigurator {
    ofBlock(block) {
        return new Configurator(block.parent.parent.parent);
    }
}
