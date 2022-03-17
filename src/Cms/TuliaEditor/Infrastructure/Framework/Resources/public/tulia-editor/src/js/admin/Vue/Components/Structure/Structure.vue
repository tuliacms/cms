<template>
    <div>
        <Section
            v-for="section in structure.sections"
            :key="section.id"
            :section="section"
        ></Section>
    </div>
</template>

<script>
import Section from "./Section";

export default {
    props: ['structure'],
    components: { Section },
};
</script>
