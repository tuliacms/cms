export default class BlockClassnameGenerator {
    static generate (block) {
        let classlist = [];

        for (let type of ['padding', 'margin']) {
            const prefixType = type.substring(0, 1);

            for (let side of ['left', 'top', 'right', 'bottom']) {
                const prefixSide = side.substring(0, 1);

                for (let breakpoint in block.config[type][side]) {
                    let prefix = `${prefixType}${prefixSide}-`;

                    if (breakpoint !== 'xs') {
                        prefix += `${breakpoint}-`
                    }

                    if (!block.config[type][side][breakpoint]) {
                        continue;
                    }

                    classlist.push(`${prefix}${block.config[type][side][breakpoint]}`);
                }
            }
        }

        for (let breakpoint in block.config.visibility) {
            let prefix = 'd-';

            if (breakpoint !== 'xs') {
                prefix += `${breakpoint}-`
            }

            if (block.config.visibility[breakpoint] === '1') {
                classlist.push(`${prefix}block`);
            } else if (block.config.visibility[breakpoint] === '0') {
                classlist.push(`${prefix}none`);
            }
        }

        /*if (block.data._internal.hasOwnProperty('alignment')) {
            classlist.push(block.data._internal.alignment);
        }*/

        return classlist.join(' ');
    }
}
