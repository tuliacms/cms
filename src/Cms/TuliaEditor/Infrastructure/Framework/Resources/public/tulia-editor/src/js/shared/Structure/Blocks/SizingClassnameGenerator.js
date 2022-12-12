export default class SizingClassnameGenerator {
    block;

    constructor (block) {
        this.block = block;
    }

    generate () {
        let classlist = [];

        if (this.block.data._internal?.sizing) {
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
        }

        if (this.block.data._internal.hasOwnProperty('visibility')) {
            for (let breakpoint in this.block.data._internal.visibility) {
                let prefix = 'd-';

                if (breakpoint !== 'xs') {
                    prefix += `${breakpoint}-`
                }

                if (this.block.data._internal.visibility[breakpoint] === '1') {
                    classlist.push(`${prefix}block`);
                } else if (this.block.data._internal.visibility[breakpoint] === '0') {
                    classlist.push(`${prefix}none`);
                }
            }
        }

        return classlist.join(' ');
    }
}
