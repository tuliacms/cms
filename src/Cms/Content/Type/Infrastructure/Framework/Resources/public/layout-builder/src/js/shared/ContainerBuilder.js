const EventDispatcher = require('./EventDispatcher.js').default;
const Form = require('./Form.js').default;
const Model = require('./Model.js').default;
const CreateField = require('./Modals/CreateField.js').default;
const EditField = require('./Modals/EditField.js').default;

export default class ContainerBuilder {
    static build (view, model, provider) {
        const s = {};
        s.translations = window.ContentBuilderLayoutBuilder.translations;
        s.options = window.ContentBuilderLayoutBuilder;
        s.eventDispatcher = new EventDispatcher();
        s.form = new Form(model, view, s.translations);
        s.model = new Model(model);
        s.createFieldModal = new CreateField(view, s.model, s.eventDispatcher, s.translations);
        s.editFieldModal = new EditField(view, s.model, s.eventDispatcher, s.translations);

        provider('options', s.options);
        provider('translations', s.translations);
        provider('eventDispatcher', s.eventDispatcher);
        provider('form', s.form);
        provider('model', s.model);
        provider('createFieldModal', s.createFieldModal);
        provider('editFieldModal', s.editFieldModal);

        return s;
    }
}
