import { useCanvasStore } from "core/Admin/Data/Store/Canvas";

export default class Canvas {
    constructor(options) {
        this.store = useCanvasStore();
        this.store.initializeBreakpoints(
            options.canvas.size.breakpoints,
            options.canvas.size.default,
        );
    }

    changeSizeTo(key) {
        this.store.changeSizeTo(key);
    }

    get breakpoints() {
        return this.store.breakpoints;
    }

    get currentBreakpoint() {
        return this.store.currentBreakpoint;
    }
}
