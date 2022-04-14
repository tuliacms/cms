const { toRaw } = require('vue');

export default class Canvas {
    messenger;
    breakpoints;
    breakpoint;

    constructor (messenger, breakpoints, breakpoint) {
        this.breakpoints = breakpoints;
        this.breakpoint = breakpoint;
        this.messenger = messenger;
    }

    changeSizeTo (name) {
        for (let i in this.breakpoints) {
            if (this.breakpoints[i].name === name) {
                this.breakpoint.name = name;
                this.breakpoint.width = this.breakpoints[i].width;
                this.messenger.send('device.size.changed', toRaw(this.breakpoints[i]));
            }
        }
    }

    getBreakpointName () {
        return this.breakpoint.name;
    }
};
