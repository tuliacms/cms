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
        let parent = null;

        for (let sk in this.structure.sections) {
            parent = this.structure.sections[sk];

            let rows = this.structure.sections[sk].rows;

            for (let rk in rows) {
                if (rows[rk].id === childId) {
                    return parent;
                }

                parent = rows[rk];

                let columns = rows[rk].columns;

                for (let ck in columns) {
                    if (columns[ck].id === childId) {
                        return parent;
                    }

                    parent = columns[ck];

                    let blocks = columns[ck].blocks;

                    for (let bk in blocks) {
                        if (blocks[bk].id === childId) {
                            return parent;
                        }
                    }
                }
            }
        }

        return null;
    }

    newSection () {
        this.messenger.notify('structure.element.new-section', this.fixer.fixSection({}));
    }

    _listenToNewSection () {
        this.messenger.on('structure.element.new-section', (newSection) => {
            this._doNewSection(newSection);
        });
    }

    _doNewSection (newSection) {
        this.structure.sections.push(newSection);
    }

    newBlock (type, parent) {
        let block = {
            code: type
        };

        block = this.fixer.fixBlock(block);

        this.messenger.notify('structure.element.new-block', block, parent);
    }

    _listenToNewBlock () {
        this.messenger.on('structure.element.new-block', (block, parent) => {
            this._doNewBlock(block, parent);
        });
    }

    _doNewBlock (block, parent) {
        let column = this.find(parent);

        column.blocks.push(block);

        this.messenger.notify('structure.element.created', 'block', block.id);
    }

    removeElement (id) {
        this.messenger.notify('structure.element.remove', id);
    }

    _listenToRemoveElement () {
        this.messenger.on('structure.element.remove', (id) => {
            this._doRemoveElement(id);
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

        if (removed) {
            this.messenger.notify('structure.element.removed', id);
        }
    }

    updateElement (element) {
        this.messenger.notify('structure.element.update', element.id, toRaw(element));
    }

    _listenToUpdateElement () {
        this.messenger.on('structure.element.update', (id, newElement) => {
            this._doUpdateElement(id, newElement);
        });
    }

    _doUpdateElement (id, newElement) {
        let currentElement = this.find(id);

        if (!currentElement) {
            return;
        }

        // Implement basic comparison, only replace every key from newElement to currentElement.
        // @todo Try to detect which data changed, and update only the changed.
        for (let ni in newElement) {
            currentElement[ni] = newElement[ni];
        }

        this.messenger.notify('structure.element.updated', id);
    }

    moveElementUsingDelta (delta) {
        this.messenger.notify('structure.element.move', delta);
    }

    _listenToMoveElementUsingDelta () {
        this.messenger.on('structure.element.move', (delta) => {
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

            this.messenger.notify('structure.element.moved', delta);
            this.messenger.notify('structure.element.updated', delta.element.id);
        });
    }
};
