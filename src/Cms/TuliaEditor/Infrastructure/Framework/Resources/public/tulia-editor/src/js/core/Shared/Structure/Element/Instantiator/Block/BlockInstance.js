import AbstractInstance from "core/Shared/Structure/Element/Instantiator/AbstractInstance";

export default class BlockInstance extends AbstractInstance {
    details = null;

    setDetails(details) {
        this.details = details;
    }
}
