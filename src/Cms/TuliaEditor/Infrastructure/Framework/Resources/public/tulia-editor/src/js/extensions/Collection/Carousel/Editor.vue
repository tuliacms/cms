<template>
    <div class="tued-carousel">
        <div class="tued-carousel-slides">
            <div v-for="(slide, key) in modelValue.collection" :class="{ 'tued-carousel-slide': true, 'd-block': state.active === key }">
                <slot name="slide" :slide="slide"></slot>
                <div class="tued-carousel-actions">
                    <div v-if="actions.slide">
                        Slide {{state.active + 1}}/{{modelValue.collection.length}}: &nbsp; <Actions :actions="actions.slide" :collection="modelValue" :item="slide" @movedTo="movedTo"></Actions>
                    </div>
                    <div v-if="actions.collection">
                        Collection: &nbsp; <Actions :actions="actions.collection" :collection="modelValue" @added="added"></Actions>
                    </div>
                </div>
            </div>
        </div>
        <div class="tued-carousel-navigation">
            <span class="tued-carousel-next" @click="nextSlide" v-if="modelValue.collection.length > 1" :title="translator.trans('nextSlide')" v-tooltip><i class="tued-chevron tued-chevron-right"></i></span>
            <span class="tued-carousel-prev" @click="prevSlide" v-if="modelValue.collection.length > 1" :title="translator.trans('prevSlide')" v-tooltip><i class="tued-chevron tued-chevron-left"></i></span>
        </div>
    </div>
</template>

<script setup>
const Collection = require('./../Editor.js').default;
const Actions = require('./../Actions/Editor.vue').default;
const { onUnmounted, onMounted, reactive, defineProps, defineEmits, inject } = require('vue');
const props = defineProps(['modelValue', 'actions']);
const emit = defineEmits(['update:modelValue']);
const extension = inject('extension.instance').editor('Collection.Carousel');
const translator = inject('translator');

const state = reactive({
    active: 0,
});

const nextSlide = () => {
    state.active++;

    if ((props.modelValue.collection.length - 1) < state.active) {
        state.active = 0;
    }
};
const prevSlide = () => {
    state.active--;

    if (state.active < 0) {
        state.active = props.modelValue.collection.length - 1;
    }
};

const validateCollection = () => {
    if (!props.modelValue instanceof Collection) {
        throw new Error('v-model must be instance of Collection extension.');
    }
};

const movedTo = (newIndex) => {
    state.active = newIndex;
};
const added = (index) => {
    state.active = index;
};

onUnmounted(() => extension.unmount());
onMounted(() => validateCollection());
</script>
<script>
export default {name: 'Extension.Collection.Carousel.Editor'}
</script>

<style scoped lang="scss">
.tued-carousel {
    position: relative;

    .tued-carousel-navigation {
        .tued-carousel-next,
        .tued-carousel-prev {
            width: 30px;
            height: 40px;
            position: absolute;
            top: 50%;
            transform: translateY(-50%);
            z-index: 10;

            &:hover {
                cursor: pointer;
            }
        }
        .tued-carousel-next {
            right: 20px;
        }
        .tued-carousel-prev {
            left: 20px;
        }
    }

    .tued-carousel-actions {
        background-color: #fff;
        padding: 7px;
        border: 1px solid #ddd;
        text-align: center;

        > div {
            display: inline-block;
            padding: 0 20px 0 7px;
        }
    }

    .tued-carousel-slide {
        display: none;
    }

    .tued-chevron {
        &:after {
            border-style: solid;
            border-width: 0.35em 0.35em 0 0;
            content: '';
            display: inline-block;
            height: 1.55em;
            position: relative;
            top: 0.15em;
            vertical-align: top;
            width: 1.55em;
            left: 0.25em;
            transform: rotate(-135deg);
        }

        &.tued-chevron-right:after {
            left: 0.15em;
            transform: rotate(45deg);
        }
    }
}
</style>
