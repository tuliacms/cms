const Editor = require('./Editor.vue').default;
const Render = require('./Render.vue').default;
const Manager = require('./Manager.vue').default;

export default {
    theme: '{{ theme.name }}',
    framework: 'bootstrap-5',
    code: '{{ block.name }}',
    name: '{{ block.name }}',
    icon: '{{ block.thumbnail }}',
    manager: Manager,
    editor: Editor,
    render: Render,
    defaults: {
        content: 'Lorem ipsum dolor sit amet...'
    }
};
