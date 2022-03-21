module.exports = class VueComponents {
    static registerBlocksAsComponents (blocks) {
        for (let i in blocks) {
            let data = blocks[i].data();
            let dataBinds = [];
            let props = [];
            let watches = {};

            for (let property in data) {
                dataBinds.push(` :${property}="blockData.${property}"`);
                props.push(property);
                watches[property] = function (value) {
                    this.$emit('value-changed', property, value);
                }
            }

            this.registerBlockEditorComponent(
                blocks[i],
                props,
                data,
                dataBinds,
                watches
            );

            this.registerBlockRenderingComponent(
                blocks[i],
                props,
                data,
                dataBinds
            );
        }
    }

    static registerBlockRenderingComponent (block, props, data, dataBinds) {
        Vue.component(block.name + '-rendering-component-frame', {
            props: ['blockData'],
            template: `<div class="tued-block-outer">
                <component
                    :is="'${block.name}-rendering'"
                    ${dataBinds.join()}
                ></component>
            </div>`
        });

        Vue.component(block.name + '-rendering', {
            props: props,
            data () {
                return data;
            },
            template: `<div class="tued-block-inner">${block.render()}</div>`
        });
    }

    static registerBlockEditorComponent (block, props, data, dataBinds, watches) {
        Vue.component(block.name + '-component-frame', {
            props: ['blockData'],
            template: `<div>
            <component
                :is="'${block.name}'"
                ${dataBinds.join()}
                @value-changed="updateBlockData"
            ></component>
            </div>`,
            methods: {
                updateBlockData (property, value) {
                    this.blockData[property] = value;
                }
            }
        });

        Vue.component(block.name, {
            props: props,
            data () {
                return data;
            },
            watch: watches,
            template: `<div
                    class="tued-structure-element-selectable"
                    @mouseenter="$root.$emit('structure.hoverable.enter', $el, 'block')"
                    @mouseleave="$root.$emit('structure.hoverable.leave', $el, 'block')"
                    @mousedown="$root.$emit('structure.selectable.select', $el, 'block')"
                    data-tagname="Block"
                >
                    ${block.template()}
                </div>`
        });
    }
};
