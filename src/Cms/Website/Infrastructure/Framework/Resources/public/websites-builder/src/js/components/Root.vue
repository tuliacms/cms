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
            <div class="row row-cols-1 row-cols-sm-1 row-cols-md-2 row-cols-lg-2 row-cols-xl-3 websites-list">
                <div class="col mb-4" v-for="website in websites">
                    <div class="card">
                        <div class="card-body">
                            <span
                                v-if="website.enabled === false"
                                data-bs-toggle="tooltip"
                                :title="translations.websiteInactiveHint"
                                class="badge badge-secondary mb-3"
                            >{{ translations.websiteInactive }}</span>
                            <h4 class="card-title mb-1">{{ website.name }}</h4>
                            <a :href="website.address">{{ website.address }}</a>
                            <small class="text-muted d-block mt-3">ID: {{ website.id }}</small>
                        </div>
                        <div class="list-group list-group-flush">
                            <div
                                v-for="locale in website.locales"
                                class="list-group-item pe-1"
                            >
                                <div class="locale-info d-flex justify-content-between align-items-center">
                                    <div>
                                        <img :src="localeProperty(locale.code, 'flag')" alt="" class="website-locale-flag-icon" />
                                        <span v-if="locale.is_default">
                                            <b>{{ localeProperty(locale.code, 'name') }}</b>
                                        </span>
                                        <span v-else>
                                            {{ localeProperty(locale.code, 'name') }}
                                        </span>
                                        <small v-if="locale.is_default" class="text-lowercase">&nbsp;({{ translations.defaultLocale }})</small>
                                    </div>
                                    <div>
                                        <div v-if="locale.enabled" class="form-check form-switch d-inline">
                                            <input class="form-check-input" type="checkbox" role="switch" @change="(e) => e.target.setAttribute('disabled', 'disabled') || deactivateLocale(website.id, locale.code)" :title="translations.deactivate" data-bs-toggle="tooltip" checked>
                                        </div>
                                        <div v-if="!locale.enabled" class="form-check form-switch d-inline">
                                            <input class="form-check-input" type="checkbox" role="switch" @change="(e) => e.target.setAttribute('disabled', 'disabled') || activateLocale(website.id, locale.code)" :title="translations.activate" data-bs-toggle="tooltip">
                                        </div>
                                        <a v-if="!locale.is_default && !locale.is_current" href="#" @click="deleteLocale(website.id, locale.code)" :title="translations.deleteLocale" data-bs-toggle="tooltip" class="btn btn-sm btn-icon-only btn-outline-danger me-2"><i class="btn-icon fa fa-trash"></i></a>
                                        <span v-if="locale.is_default || locale.is_current" class="d-inline-block">
                                            <a href="#" disabled="disabled" class="btn btn-sm btn-icon-only btn-outline-danger disabled me-2"><i class="btn-icon fa fa-trash"></i></a>
                                        </span>
                                    </div>
                                </div>
                                <a :href="locale.address" target="_blank" class="locale-address">{{ locale.address }}</a>
                            </div>
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
                        <div class="card-footer d-flex align-items-center pe-1">
                            <span class="me-2">{{ translations.activity }}</span>
                            <div v-if="website.enabled" class="form-check form-switch d-inline">
                                <input class="form-check-input" type="checkbox" role="switch" @change="(e) => e.target.setAttribute('disabled', 'disabled') || deactivateWebsite(website.id)" :title="translations.deactivate" data-bs-toggle="tooltip" checked>
                            </div>
                            <div v-if="!website.enabled" class="form-check form-switch d-inline">
                                <input class="form-check-input" type="checkbox" role="switch" @change="(e) => e.target.setAttribute('disabled', 'disabled') || activateWebsite(website.id)" :title="translations.activate" data-bs-toggle="tooltip">
                            </div>
                            <a v-if="!website.is_current" href="#" @click="deleteWebsite(website.id)" :title="translations.deleteWebsite" data-bs-toggle="tooltip" class="btn btn-sm btn-icon-only btn-outline-danger me-2 mx-auto"><i class="btn-icon fa fa-trash"></i></a>
                            <span v-if="website.is_current" class="d-inline-block me-2 mx-auto">
                                <a href="#" disabled="disabled" class="btn btn-sm btn-icon-only btn-outline-danger disabled"><i class="btn-icon fa fa-trash"></i></a>
                            </span>
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
    <ActionForm :endpoint="endpoints.activateWebsite" ref="activateWebsiteForm"></ActionForm>
    <ActionForm :endpoint="endpoints.deactivateWebsite" ref="deactivateWebsiteForm"></ActionForm>
    <ActionForm :endpoint="endpoints.activateLocale" ref="activateLocaleForm"></ActionForm>
    <ActionForm :endpoint="endpoints.deactivateLocale" ref="deactivateLocaleForm"></ActionForm>
    <ActionForm :endpoint="endpoints.deleteWebsite" ref="deleteWebsiteForm"></ActionForm>
    <ActionForm :endpoint="endpoints.deleteLocale" ref="deleteLocaleForm"></ActionForm>
</template>

<script setup>
const NewWebsiteModal = require('./modals/NewWebsite.vue').default;
const NewLocaleModal = require('./modals/NewLocale.vue').default;
const ActionForm = require('./ActionForm.vue').default;
const { defineProps, computed, reactive, ref } = require('vue');
const Tulia = require('Tulia');
const props = defineProps(['websites', 'translations', 'locales', 'endpoints']);

const newLocaleModal = ref(null);
const newWebsiteModal = ref(null);
const activateWebsiteForm = ref(null);
const deactivateWebsiteForm = ref(null);
const deleteWebsiteForm = ref(null);
const deleteLocaleForm = ref(null);
const activateLocaleForm = ref(null);
const deactivateLocaleForm = ref(null);
const modals = {
    newWebsite: {
        show: () => newWebsiteModal.value.show(),
    },
    newLocale: {
        show: (website) => newLocaleModal.value.show(website),
    }
};

const activateWebsite = (id) => {
    activateWebsiteForm.value.submit({ id });
};
const deactivateWebsite = (id) => {
    deactivateWebsiteForm.value.submit({ id });
};
const deactivateLocale = (website, code) => {
    deactivateLocaleForm.value.submit({ website, code });
};
const activateLocale = (website, code) => {
    activateLocaleForm.value.submit({ website, code });
};
const deleteLocale = (website, locale) => {
    Tulia.Confirmation.critical().then((v) => {
        if (v.value) {
            deleteLocaleForm.value.submit({ website: website, locale: locale, password: v.password });
        }
    });
};
const createWebsite = () => {
    modals.newWebsite.show();
};
const deleteWebsite = (id) => {
    Tulia.Confirmation.critical().then((v) => {
        if (v.value) {
            deleteWebsiteForm.value.submit({ id: id, password: v.password });
        }
    });
};

const newLocale = (website) => {
    modals.newLocale.show(website);
};

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
            top: 17px;
            max-width: 16px;
        }
    }
}
.form-check-input {
    height: 1.5em;
}
.form-switch {
    padding-left: 0;
    margin-right: 10px;

    .form-check-input {
        width: 3em;
        margin-left: 0;
        float: none;

        &:checked {
            background-color: var(--bs-success);
        }
    }
}
</style>
