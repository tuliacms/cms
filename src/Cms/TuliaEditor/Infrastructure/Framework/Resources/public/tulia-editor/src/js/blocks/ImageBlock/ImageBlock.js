const Editor = require('./Editor.vue').default;
const Render = require('./Render.vue').default;
const Manager = require('./Manager.vue').default;

export default {
    code: 'core-imageblock',
    name: 'Image',
    icon: 'data:image/png;base64,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',
    manager: Manager,
    editor: Editor,
    render: Render,
    defaults: {
        image: {
            id: null,
            filename: null
        }
    }
};
