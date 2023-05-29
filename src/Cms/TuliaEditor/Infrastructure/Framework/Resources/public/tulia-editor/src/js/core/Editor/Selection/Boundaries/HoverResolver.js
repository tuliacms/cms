export default class HoverResolver {
    constructor (selection) {
        this.selection = selection;
        this.hoveredElement = null;
        this.hoveredStack = [];
    }

    enter (id, type) {
        this.hoveredElement = { id, type };
        this.hoveredStack.push(this.hoveredElement);
        this.selection.hover(this.hoveredElement.id, this.hoveredElement.type);
    }

    leave () {
        this.hoveredStack.pop();

        if (this.hoveredStack[this.hoveredStack.length - 1]) {
            this.hoveredElement = this.hoveredStack[this.hoveredStack.length - 1];
            this.selection.hover(this.hoveredElement.id, this.hoveredElement.type);
        } else {
            this.selection.dehover();
        }
    }
}
