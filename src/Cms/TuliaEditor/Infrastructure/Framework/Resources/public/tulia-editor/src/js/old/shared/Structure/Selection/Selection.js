export default class Selection {
    structure;
    messenger;
    selected = null;
    hovered = null;
    hoveringDisabled = false;
    selectingDisabled = false;

    constructor (structure, messenger) {
        this.messenger = messenger;
        this.structure = structure;

        this.build();
    }

    disableHovering () {
        this.messenger.notify('structure.selection.hovering.disable');
    }

    enableHovering () {
        this.messenger.notify('structure.selection.hovering.enable');
    }

    disableSelecting () {
        this.messenger.notify('structure.selection.selecting.disable');
    }

    enableSelecting () {
        this.messenger.notify('structure.selection.selecting.enable');
    }

    select (type, id, trigger) {
        if (this.selectingDisabled) {
            return;
        }

        if (this.selected && this.selected.type === type && this.selected.id === id) {
            return;
        }

        this.messenger.notify('structure.selection.deselect');
        this.messenger.notify('structure.selection.select', type, id, trigger);
    }

    hover (type, id, trigger) {
        if (this.hoveringDisabled) {
            return;
        }

        if (this.hovered && this.hovered.type === type && this.hovered.id === id) {
            return;
        }

        this.messenger.notify('structure.selection.dehover');
        this.messenger.notify('structure.selection.hover', type, id, trigger);
    }

    resetSelection () {
        if (this.selectingDisabled) {
            return;
        }

        this.messenger.notify('structure.selection.deselect');
    }

    resetHovered () {
        if (this.hoveringDisabled) {
            return;
        }

        this.messenger.notify('structure.selection.dehover');
    }

    reset () {
        this.resetSelection();
        this.resetHovered();
    }

    getSelected () {
        return this.selected;
    }

    getHovered () {
        return this.hovered;
    }

    get (type, id) {
        for (let s in this.structure.sections) {
            if (type === 'section' && this.structure.sections[s].id === id) {
                return this.structure.sections[s];
            }

            for (let r in this.structure.sections[s].rows) {
                if (type === 'row' && this.structure.sections[s].rows[r].id === id) {
                    return this.structure.sections[s].rows[r];
                }

                for (let c in this.structure.sections[s].rows[r].columns) {
                    if (type === 'column' && this.structure.sections[s].rows[r].columns[c].id === id) {
                        return this.structure.sections[s].rows[r].columns[c];
                    }

                    for (let b in this.structure.sections[s].rows[r].columns[c].blocks) {
                        if (type === 'block' && this.structure.sections[s].rows[r].columns[c].blocks[b].id === id) {
                            return this.structure.sections[s].rows[r].columns[c].blocks[b];
                        }
                    }
                }
            }
        }

        return null;
    }

    forEach (callable) {
        for (let s in this.structure.sections) {
            callable(this.structure.sections[s]);

            for (let r in this.structure.sections[s].rows) {
                callable(this.structure.sections[s].rows[r]);

                for (let c in this.structure.sections[s].rows[r].columns) {
                    callable(this.structure.sections[s].rows[r].columns[c]);

                    for (let b in this.structure.sections[s].rows[r].columns[c].blocks) {
                        callable(this.structure.sections[s].rows[r].columns[c].blocks[b]);
                    }
                }
            }
        }
    }

    build () {
        this.messenger.on('structure.selection.select', (type, id, trigger) => {
            let element = this.get(type, id);

            if (!element) {
                return;
            }

            element.metadata.selected = true;
            this.selected = { type: type, id: id };
            this.messenger.notify('structure.selection.selected', type, id, trigger);
        });
        this.messenger.on('structure.selection.hover', (type, id, trigger) => {
            let element = this.get(type, id);

            if (!element) {
                return;
            }

            element.metadata.hovered = true;
            this.hovered = { type: type, id: id };
            this.messenger.notify('structure.selection.hovered', type, id, trigger);
        });
        this.messenger.on('structure.selection.deselect', () => {
            this.forEach((element) => {
                if (element.metadata.selected) {
                    element.metadata.selected = false;
                }
            });
            this.selected = null;
            this.messenger.notify('structure.selection.deselected');
        });
        this.messenger.on('structure.selection.dehover', () => {
            this.forEach((element) => {
                if (element.metadata.hovered) {
                    element.metadata.hovered = false;
                }
            });
            this.hovered = null;
            this.messenger.notify('structure.selection.dehovered');
        });
        /*this.messenger.on('structure.hovering.clear', () => {
            this.selection.resetHovered();
        });*/

        this.messenger.on('structure.selection.hovering.disable', () => {
            this.hoveringDisabled = true;
        });
        this.messenger.on('structure.selection.hovering.enable', () => {
            this.hoveringDisabled = false;
        });
        this.messenger.on('structure.selection.selecting.disable', () => {
            this.selectingDisabled = true;
        });
        this.messenger.on('structure.selection.selecting.enable', () => {
            this.selectingDisabled = false;
        });
    }
}
