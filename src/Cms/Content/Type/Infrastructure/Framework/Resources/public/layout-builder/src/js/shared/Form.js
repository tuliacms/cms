export default class Form {
    model;
    view;
    translations;

    constructor (model, view, translations) {
        this.model = model;
        this.view = view;
        this.translations = translations;
    }

    save () {
        if (this.validate() === false) {
            return;
        }

        $('#ctb-form-field-node-type').val(JSON.stringify({
            layout: this.model.layout,
            type: this.model.type
        }));
        $('#ctb-form').submit();
    }

    validate () {
        let status = true;

        this.view.form.type_validation.name.valid = true;
        this.view.form.type_validation.name.message = null;
        this.view.form.type_validation.code.valid = true;
        this.view.form.type_validation.code.message = null;

        if (!this.model.type.name) {
            status = false;
            this.view.form.type_validation.name.valid = false;
            this.view.form.type_validation.name.message = this.translations.pleaseFillThisField;
        }

        if (!this.model.type.code) {
            status = false;
            this.view.form.type_validation.code.valid = false;
            this.view.form.type_validation.code.message = this.translations.pleaseFillThisField;
        } else if (!/^[0-9a-z_]+$/g.test(this.model.type.code)) {
            status = false;
            this.view.form.type_validation.code.valid = false;
            this.view.form.type_validation.code.message = this.translations.fieldCodeMustContainOnlyAlphanumsAndUnderline;
        }

        return status;
    }
}
