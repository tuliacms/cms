<template>
    <div class="pane pane-lead">
        <div class="pane-header">
            <div class="pane-buttons">
                <a href="#" class="btn btn-success btn-icon-left" @click="createWebsite"><i class="btn-icon fas fa-plus"></i> {{ translations.create }}</a>
            </div>
            <i class="pane-header-icon fas fa-globe"></i>
            <h1 class="pane-title">{{ translations.websites }}</h1>
        </div>
        <div class="pane-body">
            <div class="alert alert-info">{{ translations.websitesLongDescription }}</div>
            <div class="row row-cols-1 row-cols-sm-1 row-cols-md-2 row-cols-lg-3 row-cols-xl-4 websites-list">
                <div class="col mb-4" v-for="website in websites">
                    <div class="card">
                        <div class="card-body">
                            <span
                                v-if="website.active === false"
                                data-bs-toggle="tooltip"
                                :title="translations.websiteInactiveHint"
                                class="badge badge-secondary"
                            >{{ translations.websiteInactive }}</span>
                            <a href="#" @click="manageWebsite(website.id)">
                                <h4 class="card-title">{{ website.name }}</h4>
                            </a>
                            <small class="text-muted">ID: {{ website.id }}</small>
                        </div>
                        <div class="list-group list-group-flush">
                            <a
                                v-for="locale in website.locales"
                                class="list-group-item d-flex justify-content-between align-items-center"
                                data-bs-toggle="tooltip"
                                data-bs-placement="right"
                                :title="translations.manageLocale"
                                href="#"
                                @click="manageLocale(website.id, locale.code)"
                            >
                                <img :src="localeProperty(locale.code, 'flag')" alt="" class="website-locale-flag-icon" />
                                <span v-if="locale.is_default">
                                    <b>{{ localeProperty(locale.code, 'name') }}</b>
                                </span>
                                <span v-else>
                                    {{ localeProperty(locale.code, 'name') }}
                                </span>
                                <small v-if="locale.is_default" class="text-lowercase">({{ translations.defaultLocale }})</small>
                            </a>
                            <a
                                href="#"
                                class="list-group-item list-group-item-with-icon"
                                data-bs-toggle="tooltip"
                                data-bs-placement="right"
                                :title="translations.addLocale"
                                @click="newLocale(website)"
                            >
                                <i class="list-group-item-icon fa fa-plus"></i>
                                {{ translations.addLocale }}
                            </a>
                        </div>
                        <div class="card-footer">
                            <!--<a href="#" @click="manageWebsite(website.id)" class="card-link py-3 d-inline-block" :title="translations.manageWebsite"><i class="fa fa-pen"></i> &nbsp; {{ translations.manageWebsite }}</a>-->
                            <a v-if="website.active" href="#" @click="toggleWebsiteVisibility(website.id)" title="Deactivate website" data-bs-toggle="tooltip" class="btn btn-sm btn-icon-only btn-outline-primary me-2"><i class="btn-icon fa fa-eye-slash"></i></a>
                            <a v-if="!website.active" href="#" @click="toggleWebsiteVisibility(website.id)" title="Activate website" data-bs-toggle="tooltip" class="btn btn-sm btn-icon-only btn-outline-primary me-2"><i class="btn-icon fa fa-eye"></i></a>
                            <a href="#" title="Remove website" data-bs-toggle="tooltip" class="btn btn-sm btn-icon-only btn-outline-danger me-2"><i class="btn-icon fa fa-trash"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <NewWebsiteModal
        :translations="translations"
        :locales="locales"
        :endpoint="endpoints.newWebsite"
        ref="newWebsiteModal"
    ></NewWebsiteModal>
    <NewLocaleModal
        :translations="translations"
        :locales="locales"
        :endpoint="endpoints.newLocale"
        ref="newLocaleModal"
    ></NewLocaleModal>
</template>

<script setup>
const NewWebsiteModal = require('./modals/NewWebsite.vue').default;
const NewLocaleModal = require('./modals/NewLocale.vue').default;
const { defineProps, computed, reactive, ref } = require('vue');
const Tulia = require('Tulia');
const props = defineProps(['websites', 'translations', 'locales', 'endpoints']);

const newLocaleModal = ref(null);
const newWebsiteModal = ref(null);
const modals = {
    newWebsite: {
        show: () => newWebsiteModal.value.show(),
    },
    newLocale: {
        show: (website) => newLocaleModal.value.show(website),
    }
};

const toggleWebsiteVisibility = (id) => {
    Tulia.PageLoader.show();
    alert(`Manage ${id}`);
};
const createWebsite = () => {
    modals.newWebsite.show();
};

const newLocale = (website) => {
    modals.newLocale.show(website);
};
const manageLocale = (websiteId, code) => {
    alert(`Manage locale ${code} of website ${websiteId}`);
};
/*const manageWebsite = (websiteId) => {
    alert(`Manage website ${websiteId}`);
};*/

const localeProperty = (code, property) => {
    for (let i = 0; i < props.locales.length; i++) {
        if (props.locales[i].code === code) {
            return props.locales[i][property];
        }
    }

    return '';
};
</script>

<style scoped lang="scss">
.websites-list {
    .list-group-item {
        position: relative;
        padding-left: 36px;

        .website-locale-flag-icon {
            position: absolute;
            left: 11px;
            top: 50%;
            transform: translateY(-50%);
            max-width: 16px;
        }
    }
}
</style>
