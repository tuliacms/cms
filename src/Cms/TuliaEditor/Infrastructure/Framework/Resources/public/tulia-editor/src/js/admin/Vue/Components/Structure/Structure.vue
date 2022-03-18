<template>
    <div class="tued-structure">
        <Section
            v-for="section in structure.sections"
            :key="section.id"
            :section="section"
            :availableBlocks="availableBlocks"
        ></Section>
    </div>
</template>

<script>
import Section from "./Section.vue";

export default {
    props: ['structure', 'availableBlocks'],
    components: { Section }
};
</script>
