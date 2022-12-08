export default class SizingClassnameGenerator {
    block;

    constructor (block) {
        this.block = block;
    }

    generate () {
        if (!this.block.data._internal?.sizing) {
            return '';
        }

        let classlist = [];

        for (let type in this.block.data._internal.sizing) {
            const prefixType = type.substring(0, 1);

            for (let side in this.block.data._internal.sizing[type]) {
                const prefixSide = side.substring(0, 1);

                for (let breakpoint in this.block.data._internal.sizing[type][side]) {
                    let prefix = `${prefixType}${prefixSide}-`;

                    if (breakpoint !== 'xs') {
                        prefix += `${breakpoint}-`
                    }

                    classlist.push(`${prefix}${this.block.data._internal.sizing[type][side][breakpoint]}`);
                }
            }
        }

        return classlist.join(' ');
    }
}
