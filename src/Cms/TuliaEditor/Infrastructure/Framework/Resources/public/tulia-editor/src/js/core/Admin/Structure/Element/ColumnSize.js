export default class ColumnSize {
    changeTo(column, breakpoint, size) {
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

        column.config.sizes[breakpoint].size = size;
    }

    increment(column, breakpoint) {
        let size = column.config.sizes[breakpoint].size;

        if (!size) {
            size = 0;
        }

        this.changeTo(column, breakpoint, size + 1);
    }

    decrement(column, breakpoint) {
        let size = column.config.sizes[breakpoint].size;

        if (!size) {
            size = 12;
        }

        this.changeTo(column, breakpoint, size - 1);
    }
}
