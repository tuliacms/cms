export default class ColumnSize {
    changeTo(store, size) {
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

        store.data.value = size;
    }

    increment(store) {
        let size = store.data.value;

        if (!size) {
            size = 0;
        }

        this.changeTo(store, size + 1);
    }

    decrement(store) {
        let size = store.data.value;

        if (!size) {
            size = 12;
        }

        this.changeTo(store, size - 1);
    }
}
