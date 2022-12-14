export default class StateCalculator {
    canvas;
    blocksInstantiator;
    breakpoints;

    constructor (canvas, blocksInstantiator, options) {
        this.canvas = canvas;
        this.blocksInstantiator = blocksInstantiator;
        this.breakpoints = options.canvas.size.breakpoints;
    }

    calculateState (source, propertyPath) {
        const element = this.blocksInstantiator.manager({'block': source});
        const values = element.get(propertyPath);

        let calculatedValue = null;
        let breakpoint = this.canvas.getBreakpointName();
        let breakpointReached = false;

        for (let i = 0; i < this.breakpoints.length; i++) {
            if (this.breakpoints[i].name === breakpoint) {
                breakpointReached = true;
            }

            if (breakpointReached && values && values.hasOwnProperty(this.breakpoints[i].name)) {
                calculatedValue = values[this.breakpoints[i].name];
                break;
            }
        }

        return calculatedValue;
    }
}
