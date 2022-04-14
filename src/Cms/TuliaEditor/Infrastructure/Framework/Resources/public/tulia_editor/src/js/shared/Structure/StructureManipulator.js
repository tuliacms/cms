const { toRaw } = require('vue');

export default class StructureManipulator {
    structure;
    messenger;

    constructor (structure, messenger) {
        this.messenger = messenger;
        this.structure = structure;

        this._listenToUpdateElement();
        this._listenToRemoveElement();
        this._listenToMoveElementUsingDelta();
    }

    update (newStructure) {
        this.structure.sections = newStructure.sections;
        this.messenger.send('structure.updated');
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

    removeElement (id) {
        this.messenger.send('structure.element.delete', id);
    }

    _listenToRemoveElement () {
        this.messenger.listen('structure.element.delete', (id) => {
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
            this.messenger.send('structure.element.removed', id);
        }
    }

    updateElement (element) {
        this.messenger.send('structure.element.update', element.id, toRaw(element));
    }

    _listenToUpdateElement () {
        this.messenger.listen('structure.element.update', (id, newElement) => {
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

        this.messenger.send('structure.element.updated', id);
    }

    moveElementUsingDelta (delta) {
        this.messenger.send('structure.element.move', delta);
    }

    _listenToMoveElementUsingDelta () {
        this.messenger.listen('structure.element.move', (delta) => {
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

            this.messenger.send('structure.element.moved', delta);
            this.messenger.send('structure.element.updated', delta.element.id);
        });
    }
};
