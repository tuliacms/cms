import { defineStore } from 'pinia';

export const useContextmenuStore = defineStore('contextmenu', {
    state: () => {
        return {
            items: [],
            opened: false,
            position: {
                x: 0,
                y: 0,
            },
        };
    },
    actions: {
        open(collection, position) {
            if (collection.total === 0) {
                return;
            }

            this.items = collection.collection;
            this.opened = true;
            this.position.x = position.x;
            this.position.y = position.y;
        },
        hide() {
            this.opened = false;
        }
    },
});
