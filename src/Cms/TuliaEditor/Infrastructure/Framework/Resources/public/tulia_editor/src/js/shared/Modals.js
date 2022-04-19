export default class Modals {
    modals;

    constructor (modals) {
        this.modals = modals;
    }

    open (name) {
        this.modals.instances.push(name);
    }

    close (name) {
        let index = this.modals.instances.indexOf(name);

        if (index >= 0) {
            this.modals.instances.splice(index, 1);
        }
    }

    isOpened (name) {
        return this.modals.instances.indexOf(name) >= 0;
    }
}
