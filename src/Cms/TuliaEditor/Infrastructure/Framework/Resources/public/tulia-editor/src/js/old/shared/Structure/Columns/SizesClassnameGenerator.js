export default class SizesClassnameGenerator {
    column;
    defaultClasslist;

    constructor (column, defaultClasslist) {
        this.column = column;
        this.defaultClasslist = defaultClasslist;
    }

    generate () {
        let classList = this.defaultClasslist;
        let anySizingAdded = false;

        for (let i in this.column.sizes) {
            if (this.column.sizes[i].size) {
                let prefix = `${i}-`;

                if (i === 'xs') {
                    prefix = '';
                }

                classList.push(`col-${prefix}${this.column.sizes[i].size}`);
                anySizingAdded = true;
            }
        }

        if (anySizingAdded === false) {
            classList.push('col');
        }

        return classList;
    }
}
