const Editor = require('./Editor.vue').default;
const Render = require('./Render.vue').default;
const Manager = require('./Manager.vue').default;

export default {
    theme: '*',
    framework: '*',
    code: 'core-mapblock',
    name: 'Map',
    icon: 'data:image/png;base64,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',
    editor: Editor,
    render: Render,
    manager: Manager,
    defaults: {
        position: {
            lat: 51.505,
            lng: -0.09,
        },
        zoom: 10,
        height: 200,
    }
};
