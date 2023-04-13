import AbstractManager from "core/Shared/Structure/Element/Instantiator/AbstractManager";

export default class Manager extends AbstractManager {
    details = null;

    setDetails(details) {
        this.details = details;
    }
}
