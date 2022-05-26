export default class EditField {
    view;
    model;
    eventDispatcher;
    translations;

    constructor (view, model, eventDispatcher, translations) {
        this.view = view;
        this.model = model;
        this.eventDispatcher = eventDispatcher;
        this.translations = translations;
    }

    open (fieldCode) {
        let field = this.model.findField(fieldCode);

        if (!field) {
            throw new Error('Cannot open edit modal, field not exists.');
        }

        this.view.form.field_editor.code = field.code;
        this.view.form.field_editor.type = field.type;
        this.view.form.field_editor.name = field.name;
        this.view.form.field_editor.multilingual = field.multilingual;
        this.view.form.field_editor.constraints = field.constraints;
        this.view.form.field_editor.configuration = field.configuration;
        this.view.modal.field_editor.show();

        this.eventDispatcher.emit('field:edit:modal:opened');
    }

    update (data) {
        let field = this.model.findField(this.view.form.field_editor.code.value, this.model);

        if (!field) {
            return;
        }

        field.metadata.has_errors = false;
        field.code.message = null;
        field.code.valid = true;
        field.name.value = data.name;
        field.name.message = null;
        field.name.valid = true;
        field.multilingual.value = data.multilingual;
        field.multilingual.message = null;
        field.multilingual.valid = true;
        field.configuration = data.configuration;
        field.constraints = data.constraints;

        this.view.modal.field_editor.hide();
        //this.$forceUpdate();
    }
}
