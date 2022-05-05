export default class ColumnSize {
    structureManipulator;

    constructor (structureManipulator) {
        this.structureManipulator = structureManipulator;
    }

    changeTo (column, breakpoint, size) {
        size = parseInt(size);

        // Reset if value is empty or 'zero'
        if (!size) {
            size = null;
        } else {
            // Max 12 columns
            if (size >= 12) {
                size = 12;
            }

            if (size <= 0) {
                size = null;
            }
        }

        column.sizes[breakpoint].size = size;

        this.structureManipulator.updateElement(column);

        return size;
    }

    increment (column, breakpoint) {
        let size = column.sizes[breakpoint].size;

        if (!size) {
            size = 0;
        }

        return this.changeTo(column, breakpoint, size + 1);
    }

    decrement (column, breakpoint) {
        let size = column.sizes[breakpoint].size;

        if (!size) {
            return size;
        }

        return this.changeTo(column, breakpoint, size - 1);
    }
}
