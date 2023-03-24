import { defineStore } from 'pinia';
import ObjectCloner from "core/Shared/Utils/ObjectCloner";

export const useSelectionStore = defineStore('selection', {
    state: () => {
        return {
            disableSelection: false,
            selected: {
                id: null,
                type: null,
            },
            disableHovering: false,
            hovered: {
                id: null,
                type: null,
            },
        };
    },
    actions: {
        select(id, type) {
            this.selected.type = type;
            this.selected.id = id;
        },
        deselect() {
            this.selected.type = null;
            this.selected.id = null;
        },
    },
    getters: {
        export() {
            return {
                hovered: ObjectCloner.deepClone(this.hovered),
                selected: ObjectCloner.deepClone(this.selected),
            };
        },
    },
});
