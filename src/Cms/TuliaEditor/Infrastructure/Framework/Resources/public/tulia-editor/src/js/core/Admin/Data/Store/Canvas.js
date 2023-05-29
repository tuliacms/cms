import { defineStore } from 'pinia';
import ObjectCloner from "core/Shared/Utils/ObjectCloner";

export const useCanvasStore = defineStore('canvas', {
    state: () => {
        return {
            currentBreakpoint: {
                name: 'xl',
                width: 1200,
            },
            breakpoints: [],
        };
    },
    actions: {
        initializeBreakpoints(breakpoints, defaultBreakpoint) {
            this.breakpoints = ObjectCloner.deepClone(breakpoints);

            for (let i in breakpoints) {
                if (breakpoints[i].name === defaultBreakpoint) {
                    this.currentBreakpoint.name = breakpoints[i].name;
                    this.currentBreakpoint.width = breakpoints[i].width;
                }
            }
        },
        changeSizeTo(name) {
            for (let i in this.breakpoints) {
                if (this.breakpoints[i].name === name) {
                    this.currentBreakpoint.name = this.breakpoints[i].name;
                    this.currentBreakpoint.width = this.breakpoints[i].width;
                }
            }
        },
    },
});
