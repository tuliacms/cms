<template>
    <div>
        <Modal :title="translations.addLocale" ref="modalInstance" modificators="modal-lg modal-dialog-centered">
            <template #body>
                <div class="row">
                    <div class="col">
                        <fieldset class="mb-3">
                            <label for="new-locale-code" class="form-label">{{ translations.locale }}</label>
                            <select id="new-locale-code" :class="{ 'form-select': 1, 'is-invalid': hasError('code') }" v-model="form.values.code" @change="updateLocalePrefix">
                                <option value="">---</option>
                                <option v-for="locale in availableLocales.list" :value="locale.code">{{ locale.name }} [{{ locale.code }}]</option>
                            </select>
                            <div v-if="form.errors.code" class="invalid-feedback">{{ form.errors.code }}</div>
                        </fieldset>
                    </div>
                </div>
                <div class="accordion" id="accordion-new-locale">
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                {{ translations.advancedOptions }}
                            </button>
                        </h2>
                        <div id="collapseOne" class="accordion-collapse collapse" data-bs-parent="#accordion-new-locale">
                            <div class="accordion-body">
                                <div class="row">
                                    <div class="col-6">
                                        <fieldset class="mb-3">
                                            <label for="new-locale-backend-prefix" class="form-label">{{ translations.domain }}</label><br />
                                            <input type="text" id="new-locale-backend-prefix" :class="{ 'form-control mb-1': 1, 'is-invalid': hasError('domain') }" v-model="form.values.domain" />
                                            <div v-if="form.errors.domain" class="invalid-feedback">{{ form.errors.domain }}</div>
                                        </fieldset>
                                        <fieldset class="mb-3">
                                            <label for="new-locale-domain-development" class="form-label">{{ translations.domainDevelopment }}</label>
                                            <input type="text" id="new-locale-domain-development" :class="{ 'form-control mb-1': 1, 'is-invalid': hasError('domainDevelopment') }" v-model="form.values.domainDevelopment" />
                                            <div v-if="form.errors.domainDevelopment" class="invalid-feedback">{{ form.errors.domainDevelopment }}</div>
                                            <div class="form-text mt-0 help-text">{{ translations.domainDevelopmentHelp }}</div>
                                        </fieldset>
                                    </div>
                                    <div class="col-6">
                                        <fieldset class="mb-3">
                                            <label for="new-locale-locale-prefix" class="form-label">{{ translations.localePrefix }}</label>
                                            <input type="text" id="new-locale-locale-prefix" :class="{ 'form-control mb-1': 1, 'is-invalid': hasError('localePrefix') }" v-model="form.values.localePrefix" />
                                            <div v-if="form.errors.localePrefix" class="invalid-feedback">{{ form.errors.localePrefix }}</div>
                                            <div class="form-text mt-0 help-text" v-html="translations.localePrefixHelp"></div>
                                        </fieldset>
                                        <fieldset class="mb-3">
                                            <label for="new-locale-path-prefix" class="form-label">{{ translations.pathPrefix }}</label>
                                            <input type="text" id="new-locale-path-prefix" :class="{ 'form-control mb-1': 1, 'is-invalid': hasError('pathPrefix') }" v-model="form.values.pathPrefix" />
                                            <div v-if="form.errors.pathPrefix" class="invalid-feedback">{{ form.errors.pathPrefix }}</div>
                                            <div class="form-text mt-0 help-text" v-html="translations.pathPrefixHelp"></div>
                                        </fieldset>
                                    </div>
                                </div>
                                <div class="row">
                                    <div class="col">
                                        <fieldset class="mb-3">
                                            <label for="new-locale-ssl-mode" class="form-label d-block">{{ translations.sslMode }}</label>
                                            <div :class="{ 'btn-group mb-1': 1, 'is-invalid': hasError('sslMode') }" role="group">
                                                <input type="radio" class="btn-check" name="new-locale-ssl-mode" id="new-locale-ssl-force-ssl" v-model="form.values.sslMode" value="FORCE_SSL" autocomplete="off" checked>
                                                <label class="btn btn-outline-success" for="new-locale-ssl-force-ssl">{{ translations.forceSSL }}</label>
                                                <input type="radio" class="btn-check" name="new-locale-ssl-mode" id="new-locale-ssl-non-ssl" v-model="form.values.sslMode" value="FORCE_NON_SSL" autocomplete="off">
                                                <label class="btn btn-outline-secondary" for="new-locale-ssl-non-ssl">{{ translations.forceNonSSL }}</label>
                                                <input type="radio" class="btn-check" name="new-locale-ssl-mode" id="new-locale-ssl-both" v-model="form.values.sslMode" value="ALLOWED_BOTH" autocomplete="off">
                                                <label class="btn btn-outline-secondary" for="new-locale-ssl-both">{{ translations.allowedBothSSL }}</label>
                                            </div>
                                            <div v-if="form.errors.sslMode" class="invalid-feedback">{{ form.errors.sslMode }}</div>
                                            <div class="form-text m-0 help-text">{{ translations.sslModeHelp }}</div>
                                        </fieldset>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </template>
            <template #footer>
                <button class="btn btn-secondary btn-icon-left" @click="hide"><i class="btn-icon fa fa-times"></i> {{ translations.cancel }}</button>
                <button class="btn btn-success btn-icon-left" @click="submit"><i class="btn-icon fa fa-save"></i> {{ translations.create }}</button>
            </template>
        </Modal>
    </div>
</template>

<script setup>
const FetchAPI = require('./../../shared/FetchAPI.js').default;
const { defineProps, ref, defineExpose, reactive, toRaw } = require('vue');
const props = defineProps(['translations', 'locales', 'endpoint']);
const Modal = require('./Modal.vue').default;
const Tulia = require('Tulia');

let website = null;
let defaultLocale = null;
let availableLocales = reactive({
    list: [],
});
const modalInstance = ref(null);

const form = reactive({
    values: {
        website: '',
        code: '',
        domain: '',
        domainDevelopment: '',
        localePrefix: '',
        pathPrefix: '',
        sslMode: '',
    },
    errors: {
        code: null,
        domain: null,
        domainDevelopment: null,
        localePrefix: null,
        pathPrefix: null,
        sslMode: null,
    }
});

const resetForm = () => {
    resetFormErrors();

    form.values.website = website.id;
    form.values.code = '';
    form.values.domain = defaultLocale.domain;
    form.values.domainDevelopment = defaultLocale.domain_development;
    form.values.localePrefix = '';
    form.values.pathPrefix = defaultLocale.path_prefix;
    form.values.sslMode = defaultLocale.ssl_mode;
};
const hasError = (field) => {
    return !!form.errors[field];
};

function resetFormErrors() {
    for (let i in form.errors) {
        form.errors[i] = null;
    }
}
function fillFormErrors(errors) {
    for (let i in errors) {
        form.errors[i] = errors[i][0];
    }
}

const updateLocalePrefix = () => {
    for (let i in props.locales) {
        if (props.locales[i].code === form.values.code) {
            form.values.localePrefix = props.locales[i].path_prefix;
        }
    }
};

const submit = () => {
    modalInstance.value.showLoader();
    Tulia.PageLoader.show();

    resetFormErrors();

    FetchAPI.post(props.endpoint, { 'add_locale_form': toRaw(form.values) })
        .then((data) => {
            if (data.errors) {
                Tulia.PageLoader.hide();
                modalInstance.value.hideLoader();
                fillFormErrors(data.errors);
            } else {
                document.location.reload();
            }
        });
};
const collectAvailableLocales = (currentLocales) => {
    const allLocales = toRaw(props.locales);
    availableLocales.list = [];

    for (let j in allLocales) {
        let found = false;

        for (let i in currentLocales) {
            if (allLocales[j].code === currentLocales[i].code) {
                found = true;
                break;
            }
        }

        if (!found) {
            availableLocales.list.push(allLocales[j]);
        }
    }
};

function show (newWebsite) {
    website = newWebsite;

    for (let i in website.locales) {
        if (website.locales[i].is_default) {
            defaultLocale = website.locales[i];
        }
    }

    if (!defaultLocale) {
        throw new Error(`Unable to find default locale for website ${website.id}`);
    }

    collectAvailableLocales(website.locales);
    resetForm();
    modalInstance.value.show();
}
function hide () {
    modalInstance.value.hide();
}
defineExpose({ show, hide });
</script>
