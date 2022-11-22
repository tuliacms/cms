const Popper = require('Popper');

const tooltips = [];

export default {
    /**
     * @todo Update Popper element when position of target element has changed
     * This happend when we move for example FAQ element up/down - the popper element
     * has not change his position until we scroll the page.
     * https://popper.js.org/docs/v2/lifecycle/#manual-update
     */
    mounted(el, binding, vnode, prevVnode) {
        const tooltip = document.createElement('div');
        tooltip.innerHTML = el.getAttribute('title');
        tooltip.classList.add('tued-tooltip');

        document.body.appendChild(tooltip);

        if (Popper.createPopper) {
            Popper.createPopper(el, tooltip, {
                placement: 'top',
                modifiers: [
                    {
                        name: 'offset',
                        options: {
                            offset: [0, 10],
                        },
                    },
                ],
            });
        } else {
            new Popper(el, tooltip, {
                placement: 'top',
                modifiers: [
                    {
                        name: 'offset',
                        options: {
                            offset: [0, 10],
                        },
                    },
                ],
            });
        }

        tooltips.push({
            el: el,
            tooltip: tooltip,
        });

        el.removeAttribute('title');
        el.addEventListener('mouseover', e => {
            tooltip.classList.add('tued-showed');
        });
        el.addEventListener('mouseleave', e => {
            tooltip.classList.remove('tued-showed');
        });
    },
    beforeUnmount(el, binding, vnode, prevVnode) {
        for (let i in tooltips) {
            if (tooltips[i].el === el) {
                tooltips[i].tooltip.parentNode.removeChild(tooltips[i].tooltip);
            }
        }
    }
};
