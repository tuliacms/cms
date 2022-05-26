<template><select :multiple="multiple" class="form-control"><slot></slot></select></template>

<script>
export default {
    props: {
        value: [String, Array],
        multiple: Boolean,
    },
    mounted () {
        $(this.$el)
            .val(this.value)
            .chosen()
            .on('change', e => this.$emit('input', $(this.$el).val()))
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
