export default class HoverResolver {
    hoveredElement;
    hoveredStack = [];
    selection;

    constructor (selection) {
        this.selection = selection;
    }

    enter (element, type, id) {
        this.hoveredElement = {
            element: element,
            type: type,
            id: id
        };
        this.hoveredStack.push(this.hoveredElement);
        this.selection.hover(this.hoveredElement.type, this.hoveredElement.id);
    }

    leave () {
        this.hoveredStack.pop();

        if (this.hoveredStack[this.hoveredStack.length - 1]) {
            this.hoveredElement = this.hoveredStack[this.hoveredStack.length - 1];
            this.selection.hover(this.hoveredElement.type, this.hoveredElement.id);
        } else {
            this.selection.resetHovered();
        }
    }
}
