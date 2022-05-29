export default class CreateField {
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

    open (sectionCode, parentField) {
        this.view.form.field_creator_section_code = sectionCode;
        this.view.form.field_creator_parent_field = parentField;
        this.view.modal.field_creator.show();

        this.eventDispatcher.emit('field:create:modal:opened');
    }

    create (data) {
        let section = this.model.findSection(this.view.form.field_creator_section_code);

        if (this.model.findField(data.code)) {
            Tulia.Info.info({
                title: this.translations.youCannotCreateTwoFieldsWithTheSameCode,
                type: 'warning'
            });
            return;
        }

        let newField = {
            metadata: {has_errors: false},
            code: {value: data.code, valid: true, message: null},
            name: {value: data.name, valid: true, message: null},
            type: {value: data.type, valid: true, message: null},
            multilingual: {value: data.multilingual, valid: true, message: null},
            configuration: data.configuration,
            constraints: [],
            children: [],
        };

        if (this.view.form.field_creator_parent_field) {
            let parent = this.model.findField(this.view.form.field_creator_parent_field);

            if (!parent) {
                alert('ERROR: Parent field not exists. Cannot create this field.');
                return;
            }

            parent.children.push(newField);
        } else {
            section.fields.push(newField);
        }

        this.view.modal.field_creator.hide();
        //this.openEditFieldModel(data.code);
        //this.$forceUpdate();
    }
}
