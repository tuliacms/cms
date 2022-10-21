<template>
    <div>
        <Modal :title="translations.createWebsite" ref="modalInstance" modificators="modal-lg modal-dialog-centered">
            <template #body>
                <div class="row">
                    <div class="col-6">
                        <fieldset class="mb-3">
                            <label for="new-website-name" class="form-label">{{ translations.name }}</label>
                            <input type="text" id="new-website-name" :class="{ 'form-control': 1, 'is-invalid': hasError('name') }" ref="newWebsiteName" v-model="form.values.name" />
                            <div v-if="form.errors.name" class="invalid-feedback">{{ form.errors.name }}</div>
                        </fieldset>
                        <fieldset class="mb-3">
                            <label for="new-website-domain" class="form-label">{{ translations.domain }}</label>
                            <input type="text" id="new-website-domain" :class="{ 'form-control mb-1': 1, 'is-invalid': hasError('domain') }" v-model="form.values.domain" />
                            <div v-if="form.errors.domain" class="invalid-feedback">{{ form.errors.domain }}</div>
                            <div class="form-text mt-0 help-text">{{ translations.domainHelp }}</div>
                        </fieldset>
                    </div>
                    <div class="col-6">
                        <fieldset class="mb-3">
                            <label for="new-website-locale" class="form-label">{{ translations.activity }}</label><br />
                            <div class="btn-group mb-1" role="group">
                                <input type="radio" class="btn-check" name="new-website-activeness" id="new-website-active" v-model="form.values.activity" value="1" autocomplete="off" checked>
                                <label class="btn btn-outline-success" for="new-website-active">{{ translations.active }}</label>
                                <input type="radio" class="btn-check" name="new-website-activeness" id="new-website-inactive" v-model="form.values.activity" value="0" autocomplete="off">
                                <label class="btn btn-outline-secondary" for="new-website-inactive">{{ translations.inactive }}</label>
                            </div>
                            <div v-if="form.errors.activity" class="invalid-feedback">{{ form.errors.activity }}</div>
                            <div class="form-text mt-0 help-text" v-if="form.values.activity === '0'">{{ translations.websiteInactiveHint }}</div>
                        </fieldset>
                        <fieldset class="mb-3">
                            <label for="new-website-locale" class="form-label">{{ translations.defaultLocale }}</label>
                            <select id="new-website-locale" :class="{ 'form-select': 1, 'is-invalid': hasError('locale') }" v-model="form.values.locale">
                                <option value="">---</option>
                                <option v-for="locale in locales" :value="locale.code">{{ locale.name }} [{{ locale.code }}]</option>
                            </select>
                            <div v-if="form.errors.locale" class="invalid-feedback">{{ form.errors.locale }}</div>
                            <div class="form-text m-0 help-text bg-warning py-2 px-3 text-dark">{{ translations.defaultLocaleCannotBeChangedAfterCreating }}</div>
                        </fieldset>
                    </div>
                </div>
                <div class="accordion" id="accordion-new-website">
                    <div class="accordion-item">
                        <h2 class="accordion-header">
                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                {{ translations.advancedOptions }}
                            </button>
                        </h2>
                        <div id="collapseOne" class="accordion-collapse collapse" data-bs-parent="#accordion-new-website">
                            <div class="accordion-body">
                                <div class="row">
                                    <div class="col-6">
                                        <fieldset class="mb-3">
                                            <label for="new-website-path-prefix" class="form-label">{{ translations.pathPrefix }}</label>
                                            <input type="text" id="new-website-path-prefix" :class="{ 'form-control mb-1': 1, 'is-invalid': hasError('pathPrefix') }" v-model="form.values.pathPrefix" />
                                            <div v-if="form.errors.pathPrefix" class="invalid-feedback">{{ form.errors.pathPrefix }}</div>
                                            <div class="form-text mt-0 help-text" v-html="translations.pathPrefixHelp"></div>
                                        </fieldset>
                                        <fieldset class="mb-3">
                                            <label for="new-website-domain-development" class="form-label">{{ translations.domainDevelopment }}</label>
                                            <input type="text" id="new-website-domain-development" :class="{ 'form-control mb-1': 1, 'is-invalid': hasError('domainDevelopment') }" v-model="form.values.domainDevelopment" />
                                            <div v-if="form.errors.domainDevelopment" class="invalid-feedback">{{ form.errors.domainDevelopment }}</div>
                                            <div class="form-text mt-0 help-text">{{ translations.domainDevelopmentHelp }}</div>
                                        </fieldset>
                                    </div>
                                    <div class="col-6">
                                        <fieldset class="mb-3">
                                            <label for="new-website-backend-prefix" class="form-label">{{ translations.backendPrefix }}</label><br />
                                            <input type="text" id="new-website-backend-prefix" :class="{ 'form-control mb-1': 1, 'is-invalid': hasError('backendPrefix') }" v-model="form.values.backendPrefix" />
                                            <div v-if="form.errors.backendPrefix" class="invalid-feedback">{{ form.errors.backendPrefix }}</div>
                                            <div class="form-text mt-0 help-text">{{ translations.backendPrefixHelp }}</div>
                                        </fieldset>
                                        <fieldset class="mb-3">
                                            <label for="new-website-ssl-mode" class="form-label">{{ translations.sslMode }}</label>
                                            <div :class="{ 'btn-group mb-1': 1, 'is-invalid': hasError('sslMode') }" role="group">
                                                <input type="radio" class="btn-check" name="new-website-ssl-mode" id="new-website-ssl-force-ssl" v-model="form.values.sslMode" value="FORCE_SSL" autocomplete="off" checked>
                                                <label class="btn btn-outline-success" for="new-website-ssl-force-ssl">{{ translations.forceSSL }}</label>
                                                <input type="radio" class="btn-check" name="new-website-ssl-mode" id="new-website-ssl-non-ssl" v-model="form.values.sslMode" value="FORCE_NON_SSL" autocomplete="off">
                                                <label class="btn btn-outline-secondary" for="new-website-ssl-non-ssl">{{ translations.forceNonSSL }}</label>
                                                <input type="radio" class="btn-check" name="new-website-ssl-mode" id="new-website-ssl-both" v-model="form.values.sslMode" value="ALLOWED_BOTH" autocomplete="off">
                                                <label class="btn btn-outline-secondary" for="new-website-ssl-both">{{ translations.allowedBothSSL }}</label>
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

const newWebsiteName = ref(null);
const modalInstance = ref(null);
const defaults = {
    locale: '',
    activity: '1',
    name: '',
    domain: '',
    pathPrefix: '',
    domainDevelopment: 'localhost',
    backendPrefix: '/administrator',
    sslMode: 'ALLOWED_BOTH',
};
const form = reactive({
    values: {
        locale: defaults.locale,
        activity: defaults.activity,
        name: defaults.name,
        domain: defaults.domain,
        pathPrefix: defaults.pathPrefix,
        domainDevelopment: defaults.domainDevelopment,
        backendPrefix: defaults.backendPrefix,
        sslMode: defaults.sslMode,
    },
    errors: {
        locale: null,
        activity: null,
        name: null,
        domain: null,
        pathPrefix: null,
        domainDevelopment: null,
        backendPrefix: null,
        sslMode: null,
    }
});

const resetForm = () => {
    resetFormErrors();
    form.values.locale = defaults.locale;
    form.values.activity = defaults.activity;
    form.values.name = defaults.name;
    form.values.domain = defaults.domain;
    form.values.pathPrefix = defaults.pathPrefix;
    form.values.domainDevelopment = defaults.domainDevelopment;
    form.values.backendPrefix = defaults.backendPrefix;
    form.values.sslMode = defaults.sslMode;
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

const submit = () => {
    modalInstance.value.showLoader();

    resetFormErrors();

    FetchAPI.post(props.endpoint, { 'new_website_form': toRaw(form.values) })
        .then((data) => {
            if (data.errors) {
                modalInstance.value.hideLoader();
                fillFormErrors(data.errors);
            } else {
                document.location.reload();
            }
        });
};

function show () {
    resetForm();
    modalInstance.value.show();
    setTimeout(() => newWebsiteName.value.focus(), 500);
}
function hide () {
    modalInstance.value.hide();
}
defineExpose({ show, hide });
</script>
