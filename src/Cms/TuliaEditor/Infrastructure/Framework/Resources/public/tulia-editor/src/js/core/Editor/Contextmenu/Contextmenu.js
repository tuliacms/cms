export default class Contextmenu {
    register(type, elementId, data) {
        return JSON.stringify({
            type: type,
            elementId: elementId,
            data: data,
        });
    }
}
