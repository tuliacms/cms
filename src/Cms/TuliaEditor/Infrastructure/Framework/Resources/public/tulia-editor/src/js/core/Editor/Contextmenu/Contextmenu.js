export default class Contextmenu {
    register(elementId, type, data) {
        return JSON.stringify({
            type: type,
            elementId: elementId,
            data: data,
        });
    }
}
