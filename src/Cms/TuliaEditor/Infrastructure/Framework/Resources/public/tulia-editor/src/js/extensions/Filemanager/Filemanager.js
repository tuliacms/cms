import Manager from "extensions/Filemanager/Manager.vue";
import Editor from "extensions/Filemanager/Editor";

export default {
    Manager,
    Editor: 'extension.filemanager.editor',
    services (container) {
        container.register('extension.filemanager.editor', Editor, ['%options']);
    }
}
