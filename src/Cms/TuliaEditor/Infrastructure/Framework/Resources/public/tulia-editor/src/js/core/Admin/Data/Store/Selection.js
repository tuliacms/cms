import { defineStore } from 'pinia';
import ObjectCloner from "core/Shared/Utils/ObjectCloner";

export const useSelectionStore = defineStore('selection', {
    state: () => {
        return {
            selectionDisabled: false,
            selected: {
                id: null,
                type: null,
            },
            hoveringDisabled: false,
            hovered: {
                id: null,
                type: null,
            },
        };
    },
    actions: {
        select(id, type) {
            if (this.selectionDisabled) {
                return;
            }

            this.selected.type = type;
            this.selected.id = id;
        },
        deselect() {
            this.selected.type = null;
            this.selected.id = null;
        },
        hover(id, type) {
            if (this.hoveringDisabled) {
                return;
            }

            this.hovered.type = type;
            this.hovered.id = id;
        },
        dehover() {
            this.hovered.type = null;
            this.hovered.id = null;
        },
        disableHovering() {
            this.hoveringDisabled = true;
            this.hovered.type = null;
            this.hovered.id = null;
        },
        enableHovering() {
            this.hoveringDisabled = false;
        },
        disableSelection() {
            this.selectionDisabled = true;
            this.selected.type = null;
            this.selected.id = null;
        },
        enableSelection() {
            this.selectionDisabled = false;
        },
    },
    getters: {
        export(state) {
            return {
                hovered: ObjectCloner.deepClone(state.hovered),
                selected: ObjectCloner.deepClone(state.selected),
            };
        },
    },
});
