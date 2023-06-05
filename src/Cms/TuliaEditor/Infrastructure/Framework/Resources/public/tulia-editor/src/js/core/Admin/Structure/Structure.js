import SourceToUnifiedFormat from "core/Admin/Structure/Transformer/SourceToUnifiedFormat";
import StoreToUnifiedFormat from "core/Admin/Structure/Transformer/StoreToUnifiedFormat";
import ObjectCloner from "core/Shared/Utils/ObjectCloner";
import UnifiedToSource from "core/Admin/Structure/Transformer/UnifiedToSource";

export default class Structure {
    _current = null;
    _previous = null;

    constructor(structureStore, configRegistry, dataRegistry, messenger, options) {
        this.structureStore = structureStore;
        this.configRegistry = configRegistry;
        this.dataRegistry = dataRegistry;
        this.messenger = messenger;
        this.options = options;

        const structure = this.options.structure;

        this._current = SourceToUnifiedFormat.transform(structure);
        this._previous = ObjectCloner.deepClone(this._current);

        messenger.receive('init.editor', () => {
            messenger.send('init.options', {
                options: this.options,
                structure: structure,
            });
        });
    }

    revert() {
        this.replaceWith(this._previous);
        this._current = ObjectCloner.deepClone(this._previous);
        this.update();
    }

    current() {
        this.replaceWith(this._current);
        this.update();
    }

    currentAsNew() {
        this._current = StoreToUnifiedFormat.transform(this.structureStore, this.configRegistry, this.dataRegistry);
        this._previous = ObjectCloner.deepClone(this._current);
        this.update();

        return UnifiedToSource.transform(this._current);
    }

    update() {
        this.messenger.send('admin.structure.changed', {
            structure: this.structureStore.export,
        });
    }

    replaceWith(unified) {
        this.structureStore.replace(
            unified.structure.sections,
            unified.structure.rows,
            unified.structure.columns,
            unified.structure.blocks,
        );

        for (let b in unified.data.blocks) {
            this.messenger.send('element.data.replace', { id: b, type: 'block', data: unified.data.blocks[b] });
        }

        /**
         * @todo Remove those loops and implements initial values in:
         * TuliaEditor/Infrastructure/Framework/Resources/public/tulia-editor/src/js/core/Shared/Structure/Element/Config/ElementConfigStoreRegistry.js:30
         * TuliaEditor/Infrastructure/Framework/Resources/public/tulia-editor/src/js/core/Shared/Structure/Element/Data/ElementDataStoreRegistry.js:24
         */
        for (let b in unified.config.blocks) {
            this.configRegistry.get(b, 'block').replace(unified.config.blocks[b]);
        }
        for (let s in unified.config.sections) {
            this.configRegistry.get(s, 'section').replace(unified.config.sections[s]);
        }
    }
}
