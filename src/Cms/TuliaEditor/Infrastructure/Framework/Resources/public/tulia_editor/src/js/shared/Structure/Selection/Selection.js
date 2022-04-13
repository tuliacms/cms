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
        this.messenger.send('structure.selection.hovering.disable');
    }

    enableHovering () {
        this.messenger.send('structure.selection.hovering.enable');
    }

    disableSelecting () {
        this.messenger.send('structure.selection.selecting.disable');
    }

    enableSelecting () {
        this.messenger.send('structure.selection.selecting.enable');
    }

    update () {
        if (this.selected) {
            this.select(this.selected.type, this.selected.id);
        }
        if (this.hovered) {
            this.hover(this.hovered.type, this.hovered.id);
        }
    }

    select (type, id) {
        if (this.selectingDisabled) {
            return;
        }

        this.messenger.send('structure.selection.deselect');
        this.messenger.send('structure.selection.select', type, id);
    }

    hover (type, id) {
        if (this.hoveringDisabled) {
            return;
        }

        this.messenger.send('structure.selection.dehover');
        this.messenger.send('structure.selection.hover', type, id);
    }

    resetSelection () {
        if (this.selectingDisabled) {
            return;
        }

        this.messenger.send('structure.selection.deselect');
    }

    resetHovered () {
        if (this.hoveringDisabled) {
            return;
        }

        this.messenger.send('structure.selection.dehover');
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
        this.messenger.listen('structure.selection.select', (type, id) => {
            let element = this.get(type, id);

            if (!element) {
                return;
            }

            element.metadata.selected = true;
            this.selected = { type: type, id: id };
            this.messenger.send('structure.selection.selected', type, id);
        });
        this.messenger.listen('structure.selection.hover', (type, id) => {
            let element = this.get(type, id);

            if (!element) {
                return;
            }

            element.metadata.hovered = true;
            this.hovered = { type: type, id: id };
            this.messenger.send('structure.selection.hovered', type, id);
        });
        this.messenger.listen('structure.selection.deselect', () => {
            this.forEach((element) => {
                if (element.metadata.selected) {
                    element.metadata.selected = false;
                }
            });
            this.selected = null;
            this.messenger.send('structure.selection.deselected');
        });
        this.messenger.listen('structure.selection.dehover', () => {
            this.forEach((element) => {
                if (element.metadata.hovered) {
                    element.metadata.hovered = false;
                }
            });
            this.hovered = null;
            this.messenger.send('structure.selection.dehovered');
        });
        /*this.messenger.listen('structure.hovering.clear', () => {
            this.selection.resetHovered();
        });*/

        this.messenger.listen('structure.selection.hovering.disable', () => {
            this.hoveringDisabled = true;
        });
        this.messenger.listen('structure.selection.hovering.enable', () => {
            this.hoveringDisabled = false;
        });
        this.messenger.listen('structure.selection.selecting.disable', () => {
            this.selectingDisabled = true;
        });
        this.messenger.listen('structure.selection.selecting.enable', () => {
            this.selectingDisabled = false;
        });
    }
}
