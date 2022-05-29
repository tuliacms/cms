<template><select :multiple="multiple" class="form-control"><slot></slot></select></template>

<script>
export default {
    props: {
        modelValue: [String, Array],
        multiple: Boolean,
    },
    mounted () {
        $(this.$el)
            .val(this.value)
            .chosen()
            .on('change', e => this.$emit('update:modelValue', $(this.$el).val()))
    },
    watch: {
        value (val) {
            $(this.$el).val(val).trigger('chosen:updated');
        }
    },
    destroyed () {
        $(this.$el).chosen('destroy');
    }
};
</script>
