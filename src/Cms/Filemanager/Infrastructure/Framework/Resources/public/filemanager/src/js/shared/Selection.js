export default class {
    eventDispatcher;
    multiple;
    selected = [];

    constructor(eventDispatcher, multiple) {
        this.eventDispatcher = eventDispatcher;
        this.multiple = multiple;
    }

    /*init () {
        let self = this;

        this.eventDispatcher.on('view.file.click', function (file) {
            self.toggle(file.id);
        });

        this.eventDispatcher.on('upload.complete.partial', function (files) {
            for (let i = 0; i < files.length; i++) {
                self.select(files[i].id);
            }
        });

        this.eventDispatcher.on('view.file.dblclick', function (file) {
            self.selectOnly(file.id);
        }, 1000);

        this.eventDispatcher.on('selected.change', function () {
            self.commandBus.cmd('view.files.refresh-selection');
        });
    };*/

    getSelected () {
        return this.selected;
    };

    toggle (id) {
        if (this.isSelected(id)) {
            this.deselect(id);
        } else {
            this.select(id);
        }
    };

    select (id) {
        if (this.multiple) {
            this.selected.push(id);
        } else {
            this.selected = [id];
        }

        this.eventDispatcher.dispatch('selection.change');
    };

    selectOnly (id) {
        this.selected = [id];
        this.eventDispatcher.dispatch('selection.change');
    };

    deselect (id) {
        for (let i = 0; i < this.selected.length; i++) {
            if (this.selected[i] === id) {
                this.selected.splice(i, 1);
            }
        }

        this.eventDispatcher.dispatch('selection.change');
    };

    isSelected (id) {
        return 0 <= this.selected.indexOf(id);
    };

    clear () {
        this.selected = [];
        this.eventDispatcher.dispatch('selection.change');
    };
}
