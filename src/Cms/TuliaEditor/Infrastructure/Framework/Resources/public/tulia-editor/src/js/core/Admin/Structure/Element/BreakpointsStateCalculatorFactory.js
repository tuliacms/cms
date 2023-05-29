class StateCalculator {
    constructor(data, breakpoints, breakpointNameGetter) {
        this.data = data;
        this.breakpoints = breakpoints;
        this.breakpointNameGetter = breakpointNameGetter;
    }

    calculateState() {
        let calculatedValue = null;
        let breakpoint = this.breakpointNameGetter();
        let breakpointReached = false;

        for (let i = 0; i < this.breakpoints.length; i++) {
            if (this.breakpoints[i].name === breakpoint) {
                breakpointReached = true;
            }

            if (breakpointReached && this.data[this.breakpoints[i].name]) {
                calculatedValue = this.data[this.breakpoints[i].name];
                break;
            }
        }

        return calculatedValue;
    }
}

export default class BreakpointsStateCalculatorFactory {
    constructor (options, eventBus) {
        this.breakpoints = options.canvas.size.breakpoints;
        this.eventBus = eventBus;
    }

    fromStorage(breakpointsAwareDataStorage) {
        return new StateCalculator(
            breakpointsAwareDataStorage.source,
            this.breakpoints,
            () => breakpointsAwareDataStorage.breakpointName,
        );
    }
}
