export default class ElementOffset {
    static get(el) {
        const rect = el.getBoundingClientRect(),
            scrollLeft = document.documentElement.scrollLeft,
            scrollTop = document.documentElement.scrollTop;

        return {
            top: rect.top + scrollTop,
            left: rect.left + scrollLeft
        };
    }
}
