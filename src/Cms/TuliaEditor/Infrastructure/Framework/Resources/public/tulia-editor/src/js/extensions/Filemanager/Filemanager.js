import Manager from "extensions/Filemanager/Manager.vue";
import Editor from "extensions/Filemanager/Editor";
import Render from "extensions/Filemanager/Render";

export default {
    Manager,
    Render: 'extension.filemanager.render',
    Editor: 'extension.filemanager.editor',
    services (container) {
        container.register('extension.filemanager.render', Render, ['%options']);
        container.register('extension.filemanager.editor', Editor, ['%options']);
    }
}
