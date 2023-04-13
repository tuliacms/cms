import AbstractRender from "core/Shared/Structure/Element/Instantiator/AbstractRender";

export default class Render extends AbstractRender {
    details = null;

    setDetails(details) {
        this.details = details;
    }
}
