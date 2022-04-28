export default class Compiler {
    structure;

    constructor (structure) {
        this.structure = structure;
    }

    compile () {
        let styles = [];

        for (let s in this.structure.sections) {
            let section = this.structure.sections[s];
            styles.push(this.compileElementStyle(section.id, section.style));

            for (let r in section.rows) {
                let row = section.rows[r];
                styles.push(this.compileElementStyle(row.id, row.style));

                for (let c in row.columns) {
                    let column = row.columns[c];
                    styles.push(this.compileElementStyle(column.id, column.style));

                    for (let b in column.blocks) {
                        let block = column.blocks[b];
                        styles.push(this.compileElementStyle(block.id, block.style));
                    }
                }
            }
        }

        return styles.join('');
    }

    compileElementStyle (id, styles) {
        let compiled = [];

        for (let elementId in styles) {
            for (let style in styles[elementId]) {
                compiled.push(`#tued-block-${id} #${elementId} {${style}:${styles[elementId][style]()}}`);
            }
        }

        return compiled.join('');
    }
}
