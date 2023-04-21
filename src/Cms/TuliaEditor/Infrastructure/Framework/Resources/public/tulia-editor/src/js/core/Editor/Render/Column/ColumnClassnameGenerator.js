export default class ColumnClassnameGenerator {
    static generate(column, defaultClasslist) {
        let classList = defaultClasslist;
        let anySizingAdded = false;

        classList.push('col');

        for (let i in column.config.sizes) {
            if (column.config.sizes[i]) {
                let prefix = `${i}-`;

                if (i === 'xs') {
                    prefix = '';
                }

                classList.push(`col-${prefix}${column.config.sizes[i]}`);
                anySizingAdded = true;
            }
        }

        return classList;
    }
}
