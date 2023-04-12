import { useCanvasStore } from "core/Admin/Data/Store/Canvas";

export default class Canvas {
    constructor(options, eventBus) {
        this.store = useCanvasStore();
        this.store.initializeBreakpoints(
            options.canvas.size.breakpoints,
            options.canvas.size.default,
        );
        this.eventBus = eventBus;
    }

    changeSizeTo(key) {
        this.store.changeSizeTo(key);
        this.eventBus.dispatch('canvas.breakpoint.changed', key);
    }

    get breakpoints() {
        return this.store.breakpoints;
    }

    get currentBreakpoint() {
        return this.store.currentBreakpoint;
    }
}
