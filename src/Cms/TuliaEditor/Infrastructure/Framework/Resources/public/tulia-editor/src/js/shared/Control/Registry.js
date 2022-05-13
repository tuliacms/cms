export default class Registry {
    controls;

    constructor (controls) {
        this.controls = controls;
    }

    get (name) {
        return this.controls[name];
    }
}
