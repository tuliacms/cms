export default class ColumnClassnameGenerator {
    column;
    defaultClasslist;

    constructor(column, defaultClasslist) {
        this.column = column;
        this.defaultClasslist = defaultClasslist;
    }

    generate() {
        let classList = this.defaultClasslist;
        let anySizingAdded = false;

        classList.push('col');

        for (let i in this.column.config.sizes) {
            if (this.column.config.sizes[i].size) {
                let prefix = `${i}-`;

                if (i === 'xs') {
                    prefix = '';
                }

                classList.push(`col-${prefix}${this.column.config.sizes[i].size}`);
                anySizingAdded = true;
            }
        }

        return classList;
    }
}
