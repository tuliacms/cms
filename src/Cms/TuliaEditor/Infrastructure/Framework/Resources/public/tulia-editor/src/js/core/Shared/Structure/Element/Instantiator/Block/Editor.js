import AbstractEditor from "core/Shared/Structure/Element/Instantiator/AbstractEditor";

export default class Editor extends AbstractEditor {
    details = null;

    setDetails(details) {
        this.details = details;
    }
}
