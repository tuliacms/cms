import { defineStore } from 'pinia';

export const useSelectionStore = defineStore('selection', {
    state: () => {
        return {
            selected: {
                id: null,
                type: null,
                tagName: 'div',
                boundaries: {
                    left: -100,
                    top: -100,
                    width: 0,
                    height: 0,
                    scrollTop: null,
                },
            },
            hovered: {
                id: null,
                type: null,
                boundaries: {
                    left: -100,
                    top: -100,
                    width: 0,
                    height: 0,
                    scrollTop: null,
                },
            },
        };
    },
    actions: {
        update(selection) {
            this.selected.type = selection?.selected?.type;
            this.selected.id = selection?.selected?.id;
            this.hovered.type = selection?.hovered?.type;
            this.hovered.id = selection?.hovered?.id;
        },
        export() {
            return {
                hovered: { id: this.hovered.id, type: this.hovered.type },
                selected: { id: this.selected.id, type: this.selected.type },
            };
        },
    },
});
