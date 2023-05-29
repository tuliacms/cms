export default class EventTransformer {
    static transformPointerEvent (event) {
        if (event.target.tagName === 'HTML') {
            return null;
        }

        let targets = [];
        let target = event.target;

        while (target.tagName !== 'BODY') {
            if (target.hasAttribute('tued-contextmenu')) {
                targets.push(JSON.parse(target.getAttribute('tued-contextmenu')));
            }

            target = target.parentNode;
        }

        return {
            position: {
                x: event.x,
                y: event.y,
            },
            targets: targets,
        };
    }
}
