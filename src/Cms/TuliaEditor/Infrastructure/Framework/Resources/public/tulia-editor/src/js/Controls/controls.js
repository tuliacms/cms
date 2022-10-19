const Select = require('./Select.vue').default;
const TaxonomySelect = require('./TaxonomySelect.vue').default;
const Text = require('./Input.Text.vue').default;
const FormSelect = require('./FormSelect.vue').default;
const Range = require('./Input.Range.vue').default;

export default {
    Select,
    TaxonomySelect,
    'Input.Text': Text,
    FormSelect,
    'Input.Range': Range,
}
