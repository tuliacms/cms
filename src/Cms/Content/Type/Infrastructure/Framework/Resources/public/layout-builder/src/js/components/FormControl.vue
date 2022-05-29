<template>
    <div>
        <label class="form-label" :for="id">{{ field.label }}</label>
        <template v-if="field.type === 'integer'">
            <input type="text" :id="id" :placeholder="field.placeholder" @change="$emit('change')" :class="{ 'form-control': true, 'is-invalid': field.valid === false }" v-model="field.value" />
        </template>
        <template v-if="field.type === 'string'">
            <input type="text" :id="id" :placeholder="field.placeholder" @change="$emit('change')" :class="{ 'form-control': true, 'is-invalid': field.valid === false }" v-model="field.value" />
        </template>
        <template v-if="field.type === 'textarea'">
            <textarea :id="id" :placeholder="field.placeholder" @change="$emit('change')" :class="{ 'form-control': true, 'is-invalid': field.valid === false }" v-model="field.value"></textarea>
        </template>
        <template v-if="field.type === 'choice'">
            <ChosenSelect :id="id" v-model="field.value" :class="{ 'is-invalid': field.valid === false }">
                <option value="" disabled>{{ field.placeholder ? field.placeholder : translations.pleaseSelectValue }}</option>
                <option v-for="option in field.choices" :key="option.value" :value="option.value">{{ option.label }}</option>
            </ChosenSelect>
        </template>
        <template v-if="field.type === 'yes_no'">
            <ChosenSelect :id="id" v-model="field.value" :class="{ 'is-invalid': field.valid === false }">
                <option value="" disabled>{{ field.placeholder ? field.placeholder : translations.pleaseSelectValue }}</option>
                <option value="1">{{ translations.yes }}</option>
                <option value="0">{{ translations.no }}</option>
            </ChosenSelect>
        </template>
        <div v-if="field.valid === false" class="invalid-feedback">{{ field.message }}</div>
        <div v-if="field.help_text" class="form-text">{{ field.help_text }}</div>
    </div>
</template>

<script>
const ChosenSelect = require('./ChosenSelect.vue').default;

export default {
    props: ['id', 'field', 'translations'],
    components: {ChosenSelect}
}
</script>
