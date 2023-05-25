import { v4 } from "uuid";

export default class Blocks {
    constructor(
        blocksRegistry,
        structureStore,
        selectionUseCase,
        structureUseCase,
        columnsUseCase,
        rowsUseCase,
        sectionsUseCase,
        inspector,
    ) {
        this.blocksRegistry = blocksRegistry;
        this.structureStore = structureStore;
        this.selectionUseCase = selectionUseCase;
        this.structureUseCase = structureUseCase;
        this.columnsUseCase = columnsUseCase;
        this.rowsUseCase = rowsUseCase;
        this.sectionsUseCase = sectionsUseCase;
        this.inspector = inspector;
    }

    newBlock(code, columnId) {
        const block = this.blocksRegistry;
        const id = v4();

        if (!block) {
            return;
        }

        if (!columnId) {
            columnId = this.columnsUseCase.newOne(
                this.rowsUseCase.newOne(
                    this.sectionsUseCase.newOne()
                )
            );
        }

        this.structureStore.appendBlock({ id, code }, columnId);
        this.selectionUseCase.select(id, 'block', true);
        this.structureUseCase.update();
        this.inspector.inspect(id, 'block');

        return id;
    }

    remove(id) {
        this.structureStore.removeBlock(id);
        this.structureUseCase.update();
    }
}
