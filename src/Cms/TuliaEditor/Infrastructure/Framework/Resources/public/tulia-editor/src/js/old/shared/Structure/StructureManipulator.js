const { toRaw } = require('vue');
const Fixer = require('shared/Structure/Fixer.js').default;

export default class StructureManipulator {
    structure;
    messenger;
    fixer;

    constructor (structure, messenger) {
        this.messenger = messenger;
        this.structure = structure;
        this.fixer = new Fixer();

        this._listenToUpdateElement();
        this._listenToRemoveElement();
        this._listenToMoveElementUsingDelta();
        this._listenToNewSection();
        this._listenToNewRow();
        this._listenToNewColumn();
        this._listenToNewBlock();
    }

    update (newStructure) {
        this.structure.sections = newStructure.sections;
        this.messenger.notify('structure.updated');
    }

    find (id) {
        for (let sk in this.structure.sections) {
            if (this.structure.sections[sk].id === id) {
                return this.structure.sections[sk];
            }

            let rows = this.structure.sections[sk].rows;

            for (let rk in rows) {
                if (rows[rk].id === id) {
                    return rows[rk];
                }

                let columns = rows[rk].columns;

                for (let ck in columns) {
                    if (columns[ck].id === id) {
                        return columns[ck];
                    }

                    let blocks = columns[ck].blocks;

                    for (let bk in blocks) {
                        if (blocks[bk].id === id) {
                            return blocks[bk];
                        }
                    }
                }
            }
        }

        return null;
    }

    findParent (childId) {
        for (let sk in this.structure.sections) {
            let parentSection = this.structure.sections[sk];

            let rows = this.structure.sections[sk].rows;

            for (let rk in rows) {
                if (rows[rk].id === childId) {
                    return parentSection;
                }

                let parentRow = rows[rk];

                let columns = rows[rk].columns;

                for (let ck in columns) {
                    if (columns[ck].id === childId) {
                        return parentRow;
                    }

                    let parentColumn = columns[ck];

                    let blocks = columns[ck].blocks;

                    for (let bk in blocks) {
                        if (blocks[bk].id === childId) {
                            return parentColumn;
                        }
                    }
                }
            }
        }

        return null;
    }

    newSection (after) {
        this.messenger.execute('structure.element.create-section', { section: this.fixer.fixSection({}), after: after }).then((section) => {
            this.messenger.notify('structure.element.created', 'section', section.id);
        });
    }

    _listenToNewSection () {
        this.messenger.operation('structure.element.create-section', (data, success, fail) => {
            let after;

            if (data.after) {
                after = this.__findElementInCollection(data.after, this.structure.sections) + 1;
            } else {
                after = this.structure.sections.length;
            }

            this.structure.sections.splice(after, 0, data.section);

            success(data.section);
        });
    }

    newRow (inside) {
        this.messenger.execute('structure.element.create-row', { row: this.fixer.fixRow({}), inside: inside }).then((row) => {
            this.messenger.notify('structure.element.created', 'row', row.id);
        });
    }

    _listenToNewRow () {
        this.messenger.operation('structure.element.create-row', (data, success, fail) => {
            this.find(data.inside).rows.push(data.row);

            success(data.row);
        });
    }

    newColumn (inside) {
        this.messenger.execute('structure.element.create-column', { column: this.fixer.fixColumn({}), inside: inside }).then((column) => {
            this.messenger.notify('structure.element.created', 'column', column.id);
        });
    }

    newColumnBefore (before) {
        this.messenger.execute('structure.element.create-column', { column: this.fixer.fixColumn({}), before: before }).then((column) => {
            this.messenger.notify('structure.element.created', 'column', column.id);
        });
    }

    newColumnAfter (after) {
        this.messenger.execute('structure.element.create-column', { column: this.fixer.fixColumn({}), after: after }).then((column) => {
            this.messenger.notify('structure.element.created', 'column', column.id);
        });
    }

    _listenToNewColumn () {
        this.messenger.operation('structure.element.create-column', (data, success, fail) => {
            let after;
            let parent;

            if (data.before) {
                parent = this.findParent(data.before);
                after = this.__findElementInCollection(data.before, parent.columns) - 1;
            } else if (data.after) {
                parent = this.findParent(data.after);
                after = this.__findElementInCollection(data.after, parent.columns) + 1;
            } else {
                parent = this.find(data.inside);
            }

            if (!after) {
                after = parent.columns.length;
            }

            parent.columns.splice(after, 0, data.column);

            success(data.column);
        });
    }

    newBlock (type, parent, store) {
        let createBlock = (parent) => {
            let block = this.fixer.fixBlock({
                code: type,
                data: defaults,
            });

            this.messenger.execute('structure.element.create-block', {block, parent}).then(() => {
                this.messenger.notify('structure.element.created', 'block', block.id);
            });
        };

        if (!parent) {
            let column = this.fixer.fixColumn({});
            let row = this.fixer.fixRow({columns:[column]});
            let section = this.fixer.fixSection({rows:[row]});

            this.messenger.execute('structure.element.create-section', { section: section }).then((section) => {
                createBlock(column.id);
            });
        } else {
            createBlock(parent);
        }
    }

    _listenToNewBlock () {
        this.messenger.operation('structure.element.create-block', (data, success, fail) => {
            let column = this.find(data.parent);
            column.blocks.push(data.block);
            success();
        });
    }

    removeElement (id) {
        this.messenger.execute('structure.element.remove', {id}).then(() => {
            this.messenger.notify('structure.element.removed', id);
        });
    }

    _doRemoveElement (id) {
        let removed = false;

        loop:
        for (let sk in this.structure.sections) {
            if (this.structure.sections[sk].id === id) {
                this.structure.sections.splice(sk, 1);
                removed = true;
                break;
            }

            let rows = this.structure.sections[sk].rows;

            for (let rk in rows) {
                if (this.structure.sections[sk].rows[rk].id === id) {
                    this.structure.sections[sk].rows.splice(rk, 1);
                    removed = true;
                    break loop;
                }

                let columns = rows[rk].columns;

                for (let ck in columns) {
                    if (this.structure.sections[sk].rows[rk].columns[ck].id === id) {
                        this.structure.sections[sk].rows[rk].columns.splice(ck, 1);
                        removed = true;
                        break loop;
                    }

                    let blocks = columns[ck].blocks;

                    for (let bk in blocks) {
                        if (this.structure.sections[sk].rows[rk].columns[ck].blocks[bk].id === id) {
                            this.structure.sections[sk].rows[rk].columns[ck].blocks.splice(bk, 1);
                            removed = true;
                            break loop;
                        }
                    }
                }
            }
        }

        return removed;
    }

    _listenToRemoveElement () {
        this.messenger.operation('structure.element.remove', (data, success, fail) => {
            if (this._doRemoveElement(data.id)) {
                success();
            } else {
                fail();
            }
        });
    }

    updateElement (element) {
        this.messenger.execute('structure.element.update', { id: element.id, element: toRaw(element) }).then(() => {
            this.messenger.notify('structure.element.updated', element.id);
        });
    }

    _listenToUpdateElement () {
        this.messenger.operation('structure.element.update', (data, success, fail) => {
            let currentElement = this.find(data.id);

            if (!currentElement) {
                return;
            }

            // Implement basic comparison, only replace every key from newElement to currentElement.
            // @todo Try to detect which data changed, and update only the changed.
            for (let ni in data.element.data) {
                currentElement.data[ni] = data.element.data[ni];
            }
            for (let ni in data.element.sizes) {
                currentElement.sizes[ni] = data.element.sizes[ni];
            }

            success();
        });
    }

    moveElementUsingDelta (delta) {
        this.messenger.execute('structure.element.move', {delta}).then(() => {
            this.messenger.notify('structure.element.moved', delta);
            this.messenger.notify('structure.element.updated', delta.element.id);
        });
    }

    _listenToMoveElementUsingDelta () {
        this.messenger.operation('structure.element.move', (data, success, fail) => {
            let delta = data.delta;
            let element = toRaw(this.find(delta.element.id));

            if (delta.from.parent.type === 'structure' && delta.to.parent.type === 'structure') {
                this._doRemoveElement(delta.element.id);
                this.structure.sections.splice(delta.to.index, 0, element);
            } else {
                let newParent = this.find(delta.to.parent.id);

                this._doRemoveElement(delta.element.id);

                if (newParent.type === 'column') {
                    newParent.blocks.splice(delta.to.index, 0, element);
                } else if (newParent.type === 'row') {
                    newParent.columns.splice(delta.to.index, 0, element);
                } else if (newParent.type === 'section') {
                    newParent.rows.splice(delta.to.index, 0, element);
                }
            }

            success();
        });
    }

    __findElementInCollection (id, collection) {
        for (let i in collection) {
            if (collection[i].id === id) {
                return parseInt(i);
            }
        }

        return null;
    }
};
