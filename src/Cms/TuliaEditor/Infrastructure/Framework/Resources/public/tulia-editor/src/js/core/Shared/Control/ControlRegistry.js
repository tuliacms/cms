export default class ControlRegistry {
    constructor(controls) {
        this.controls = controls;
    }

    manager(name) {
        return this.controls[name];
    }

    editor(name) {
        return this.controls[name];
    }
}
