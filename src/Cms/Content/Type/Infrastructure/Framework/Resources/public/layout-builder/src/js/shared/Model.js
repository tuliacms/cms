export default class Model {
    model;

    constructor (model) {
        this.model = model;
    }

    removeField (fieldCode) {
        Tulia.Confirmation.warning().then((result) => {
            if (!result.value) {
                return;
            }

            this._removeField(fieldCode);
        });
    }

    _removeField (code) {
        for (let k in this.model.layout) {
            for (let s in this.model.layout[k].sections) {
                this._removeFieldFromList(this.model.layout[k].sections[s].fields, code);
            }
        }
    }

    _removeFieldFromList (fields, code) {
        for (let f in fields) {
            if (fields[f].code.value === code) {
                fields.splice(f, 1);
                return;
            }

            this._removeFieldFromList(fields[f].children, code);
        }
    }

    findField (code) {
        for (let k in this.model.layout) {
            for (let s in this.model.layout[k].sections) {
                let field = this._findInFields(this.model.layout[k].sections[s].fields, code);

                if (field) {
                    return field;
                }
            }
        }
    }

    _findInFields (fields, code) {
        for (let f in fields) {
            if (fields[f].code.value === code) {
                return fields[f];
            }

            let fieldFromChildren = this._findInFields(fields[f].children, code);

            if (fieldFromChildren) {
                return fieldFromChildren;
            }
        }
    }

    findSection (code) {
        for (let k in this.model.layout) {
            for (let s in this.model.layout[k].sections) {
                if (this.model.layout[k].sections[s].code === code) {
                    return this.model.layout[k].sections[s];
                }
            }
        }
    }
}
