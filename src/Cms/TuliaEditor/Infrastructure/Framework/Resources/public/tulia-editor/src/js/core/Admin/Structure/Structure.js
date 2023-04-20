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

        const structure = this.options.structure.structure ?? this.getStructure();

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

        for (let b in unified.config.blocks) {
            this.configRegistry.get(b, 'block').replace(unified.config.blocks[b]);
        }
    }

    getStructure() {
        return {
            sections: [
                {
                    id: "f348288f-b7b9-4622-af30-cf9cec2654c8",
                    rows: [
                        {
                            id: "5373a20b-0f3b-4bba-9411-e6073db15857",
                            columns: [
                                {
                                    id: "5f4f01c4-e49e-429b-ae82-a1cda361fe47",
                                    blocks: [
                                        {
                                            id: "59d9a354-070e-4e80-82ec-6dc71f4aa573",
                                            code: "core-mapblock",
                                            store: {
                                                data: {}
                                            }
                                        }
                                    ],
                                },
                                {
                                    id: "4ce4e48a-8dc1-49d3-841f-b639570a8aa0",
                                    blocks: [
                                        {
                                            id: "0e0bd94a-6e8f-43f9-9ba0-05d7fbf48113",
                                            code: "core-galleryblock",
                                            store: {
                                                data: {}
                                            }
                                        }
                                    ],
                                },
                                {
                                    id: "8846e2c3-d0b0-4c8a-a5e0-5a358ade7868",
                                    blocks: [

                                    ],
                                },
                            ],
                        },
                    ],
                },
                {
                    id: "3aaab68f-73a8-4871-bf3e-6e8698eed744",
                    rows: [
                        {
                            id: "7ac6f37f-cc0c-4c9d-9c2b-bed9ef4e1dd0",
                            columns: [
                                {
                                    id: "6fa6ab47-8624-4175-bf9b-5105be8c859a",
                                    blocks: [
                                        {
                                            id: "f410f23b-dc66-4b7d-9070-7d7be79ed6d9",
                                            code: "core-textblock",
                                            store: {
                                                data: {
                                                    text: 'My sample text. In store.'
                                                }
                                            }
                                        }
                                    ],
                                },
                                {
                                    id: "92dd5110-3815-4eb4-a32c-14215fac4c2b",
                                    blocks: [
                                        {
                                            id: "20687444-337b-4ccf-8be6-3ac2288e1dd2",
                                            code: "core-videoblock",
                                            store: {
                                                data: {},
                                                config: {
                                                    url: 'https://www.youtube.com/watch?v=bzBT9mEXOV8&list=RDbzBT9mEXOV8&start_radio=1&ab_channel=OU7SIDE',
                                                    ratio: '16x9',
                                                }
                                            }
                                        }
                                    ],
                                },
                                {
                                    id: "a2180b3a-a2b7-4f32-ad88-8c1fad3c0edd",
                                    blocks: [
                                        {
                                            id: "8a946960-9e95-4b51-ab40-aac6cac7f538",
                                            code: "core-imageblock",
                                            store: {
                                                data: {},
                                                config: {
                                                    url: 'https://www.youtube.com/watch?v=bzBT9mEXOV8&list=RDbzBT9mEXOV8&start_radio=1&ab_channel=OU7SIDE',
                                                    ratio: '16x9',
                                                }
                                            }
                                        }
                                    ],
                                },
                            ],
                        },
                    ],
                },
                {
                    id: "70d5581b-d415-4e00-a509-650eefc50b52",
                    rows: [
                        {
                            id: "392f96b7-ebcd-451c-a0c6-9a55233f2aad",
                            columns: [],
                        },
                    ],
                },
                {
                    id: "08023e72-0934-402d-8dcd-2f1128bdb1dc",
                    rows: [
                        {
                            id: "fcc99a1f-5b98-49cc-b69b-9c686e4c133d",
                            columns: [],
                        },
                    ],
                },
                {
                    id: "79322024-843c-48f9-8bb0-e6a21f1f5d89",
                    rows: [],
                },
                {
                    id: "427d2344-79d5-4b1d-a098-916514619d2f",
                    rows: [],
                },
            ]
        };
    }
}
