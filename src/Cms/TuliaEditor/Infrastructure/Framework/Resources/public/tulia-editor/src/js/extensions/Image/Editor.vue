<template>
    <div class="tued-block_editable-image">
        <img :src="imageLink" @load="onLoad()" ref="image" :class="{ 'opacity-25': !loaded }" />
        <button class="tued-btn" type="button" v-tooltip :title="translator.trans('selectImage')" @click="extension.send('chose-image')"><i class="fa-solid fa-pen"></i></button>
        <button class="tued-btn" v-if="props.modelValue.id" type="button" v-tooltip :title="translator.trans('clearImage')" @click="extension.send('remove-image')" style="left: initial; right: 5px;"><i class="fa-solid fa-close"></i></button>
    </div>
</template>

<style scoped lang="scss">
    .tued-block_editable-image {
        outline: none;

        &:hover {
            outline: 1px dotted #555 !important;
        }

        img {
            max-width: 100%;
            transition: .1s all;
        }
    }
</style>

<script setup>
import { defineProps, defineEmits, computed, inject, onMounted, onUnmounted, ref, watch } from "vue";
const props = defineProps({
    modelValue: {
        required: true,
        type: Object,
        validator (value) {
            return value.hasOwnProperty('id')
                && value.hasOwnProperty('filename');
        }
    },
    size: {
        required: true,
        type: String,
    },
    holder: {
        required: true,
        type: String,
    },
});
const options = inject('options');
const translator = inject('translator');
const filemanager = inject('extensions.registry').editor('Filemanager');
const extension = inject('instance.extensions').editor('Image');
const emit = defineEmits(['update:modelValue', 'updated']);
const image = ref(null);
const loaded = ref(true);
const imageSize = ref(null);

const imageLink = computed(() => {
    if (props.modelValue.id) {
        return filemanager.generatePreviewImagePath(props.modelValue, imageSize.value);
    }

    return 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAlgAAADIBAMAAADGsYKFAAAAG1BMVEXMzMyWlpbFxcWcnJyjo6Oqqqq+vr6xsbG3t7ecUE7+AAAERUlEQVR42uzSsUoDQRSG0YuQ2HodJe2ChXXUwlILSWtIYynoAyj4AII+uDtmV9NYTGVgz2n+Zm7zMQEAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAADsucyjQKwQq4FYDcRqIFYDsRqI1UCsBmI1EOsf7cY6eCuruvNNWXXxh8/l1UtU483y8iGmYifWU2Ze9LvudxGD882y3L3H6CMzy2OM5rfZu46J6GMNM89e6WKW1X18e86qjJ/pMKvjn8SvOTyeht9YZ1ndxDqrxRBn66SLnTilG65muXUa0/DFvtn0tA0EYXiycWIfeWNjcrRJSznWVUt7NaCKI676cU2kol6JqqIeA/343WU9s95AwIkabuPnEGV2ncuj8Xh21/GyWFI6BJM5OeLQ4ubO5VfXEBakgkbWSHJo4oQ0cnzu9MGk/Cs//ZJU0MjaRXz1B0CJ5O+3gocD3A5mB9+XEi25Mb+RNNM4yw4qYI9U0MgCLq0NvqcmwJiIZsCxna3s5C0FsOAQO3wXHtkEKxCTCkSWlPQ+2JLhcC4Z1eeiFUkKBSKrQFxP58CUNCCypO6Erg8okdhPlkMRzw6k0BuWZYCULKGW5sHLWkjJzvg5F9vUERtDvqrnHntVHYfijkhLhfeyJGcSssxE1shfZQdBMmvjfnP3VRiRBvzTUMIxWfLay+kpp46Rgi4XUb+Oe1LX7URKGmhkjSUceVmMvw0r1yIM6njm3FGupHdoZKV3ZPUaWV4OlS6Bwjq+bhT1lCx4fAfvwlVZQy7ovoRFdVxhTEwfCWlgrayDHyfAPVlDkdWgpCv1C+kHZb0owezIZI1ZkQXSQLusEHhcVtnJWpblfOx5WcyqrIwU0CorgCX5mUnN6mS1yJoDeHPDw77Ae1lj0kWrrAL4sDRciixX4JW0opvJiuByx3XwaSfrMVkD4Hz5qrmzE3EHr6Rv30yWbCusLqRDWRvGxJj9fdLAGlnZ8kI6d3aCOvZdfq4kx1pk8YfPJJ9pudvPWsgWjZLnYrssyawJDzfbx/N7O6Wlts2/VVk+dSp3TihSirt78JG1poE2WYFICMHDcoxBA4lLOd2ZaDmSbpMV8tGXKSEpNQPO3bkhh8c6zw0fkGUAvM8OK8QF9oycUN+YXxBZAwBX2WEJJfW9VRZVYEb2y5TIFBzH9jIOVb1z1CprAmY6Z1k0E3lymcwr2VReI2vIqXNEuciSgYVcZkpVidUui57hlt2MQpYlA6lxR9AhVL0muYaLgl/G/Vq8zcjy/CQ+o0YWRR+L15+po4Whli70KYj0lKntCYFL6mhl8OkLfwm0vL22BX2Av+RKTnO2IXAL5rmaNvT/iaRjMGoWg1tg5JW3CZRs9m1FBbybmgt0D8MNyHW9YbQdIYRX1LGWShKraxw2ICqArmJtSmT/2Np17x0dHf/ag0MCAAAAAEH/X7vBDgAAAAAAAAAAAAAAAAAA8AT99p+0ltRNJAAAAABJRU5ErkJggg==';
});

extension.receive('image-chosen', (data) => {
    loaded.value = false;

    emit('update:modelValue', {
        id: data.id,
        filename: data.filename,
    });
});

watch(() => props.size, async (newSize) => {
    props.modelValue.id && (loaded.value = false);
    imageSize.value = newSize;
});

const onLoad = () => {
    loaded.value = true;
    emit('updated');
};

onMounted(() => {
    imageSize.value = props.size;
});
onUnmounted(() => {
    extension.unmount();
});
</script>
<script>
export default {name: 'Extension.Image.Editor'}
</script>
