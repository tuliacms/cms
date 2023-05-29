const Manager = require('./Manager.vue').default;
const Editor = require('./Editor.vue').default;
const Render = require('./Render.js').default;

export default {
    Manager,
    Editor,
    Render: 'extension.backgroundImage.render',
    services (container) {
        container.register('extension.backgroundImage.render', Render, ['@extension.filemanager.render']);
    }
}
