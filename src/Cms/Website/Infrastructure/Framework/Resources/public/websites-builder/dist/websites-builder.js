var TuliaWebsiteBuilder;
/******/ (() => { // webpackBootstrap
/******/ 	"use strict";
/******/ 	var __webpack_modules__ = ({

/***/ "./node_modules/mini-css-extract-plugin/dist/loader.js!./node_modules/css-loader/dist/cjs.js!./node_modules/vue-loader/dist/stylePostLoader.js!./node_modules/sass-loader/dist/cjs.js!./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[4].use[0]!./src/js/components/Root.vue?vue&type=style&index=0&id=378f20ae&scoped=true&lang=scss":
/*!******************************************************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/mini-css-extract-plugin/dist/loader.js!./node_modules/css-loader/dist/cjs.js!./node_modules/vue-loader/dist/stylePostLoader.js!./node_modules/sass-loader/dist/cjs.js!./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[4].use[0]!./src/js/components/Root.vue?vue&type=style&index=0&id=378f20ae&scoped=true&lang=scss ***!
  \******************************************************************************************************************************************************************************************************************************************************************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
// extracted by mini-css-extract-plugin


/***/ }),

/***/ "./node_modules/mini-css-extract-plugin/dist/loader.js!./node_modules/css-loader/dist/cjs.js!./node_modules/vue-loader/dist/stylePostLoader.js!./node_modules/sass-loader/dist/cjs.js!./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[4].use[0]!./src/js/components/modals/Modal.vue?vue&type=style&index=0&id=89e68560&scoped=true&lang=scss":
/*!**************************************************************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/mini-css-extract-plugin/dist/loader.js!./node_modules/css-loader/dist/cjs.js!./node_modules/vue-loader/dist/stylePostLoader.js!./node_modules/sass-loader/dist/cjs.js!./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[4].use[0]!./src/js/components/modals/Modal.vue?vue&type=style&index=0&id=89e68560&scoped=true&lang=scss ***!
  \**************************************************************************************************************************************************************************************************************************************************************************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
// extracted by mini-css-extract-plugin


/***/ }),

/***/ "./node_modules/vue-loader/dist/exportHelper.js":
/*!******************************************************!*\
  !*** ./node_modules/vue-loader/dist/exportHelper.js ***!
  \******************************************************/
/***/ ((__unused_webpack_module, exports) => {


Object.defineProperty(exports, "__esModule", ({ value: true }));
// runtime helper for setting properties on components
// in a tree-shakable way
exports["default"] = (sfc, props) => {
    const target = sfc.__vccOpts || sfc;
    for (const [key, val] of props) {
        target[key] = val;
    }
    return target;
};


/***/ }),

/***/ "./src/js/components/ActionForm.vue":
/*!******************************************!*\
  !*** ./src/js/components/ActionForm.vue ***!
  \******************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _ActionForm_vue_vue_type_template_id_ea7c7e34__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./ActionForm.vue?vue&type=template&id=ea7c7e34 */ "./src/js/components/ActionForm.vue?vue&type=template&id=ea7c7e34");
/* harmony import */ var _ActionForm_vue_vue_type_script_setup_true_lang_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./ActionForm.vue?vue&type=script&setup=true&lang=js */ "./src/js/components/ActionForm.vue?vue&type=script&setup=true&lang=js");
/* harmony import */ var _home_adam_projects_tuliacms_development_tuliacms_core_src_Cms_Website_Infrastructure_Framework_Resources_public_websites_builder_node_modules_vue_loader_dist_exportHelper_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./node_modules/vue-loader/dist/exportHelper.js */ "./node_modules/vue-loader/dist/exportHelper.js");




;
const __exports__ = /*#__PURE__*/(0,_home_adam_projects_tuliacms_development_tuliacms_core_src_Cms_Website_Infrastructure_Framework_Resources_public_websites_builder_node_modules_vue_loader_dist_exportHelper_js__WEBPACK_IMPORTED_MODULE_2__["default"])(_ActionForm_vue_vue_type_script_setup_true_lang_js__WEBPACK_IMPORTED_MODULE_1__["default"], [['render',_ActionForm_vue_vue_type_template_id_ea7c7e34__WEBPACK_IMPORTED_MODULE_0__.render],['__file',"src/js/components/ActionForm.vue"]])
/* hot reload */
if (false) {}


/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (__exports__);

/***/ }),

/***/ "./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[4].use[0]!./src/js/components/ActionForm.vue?vue&type=script&setup=true&lang=js":
/*!*************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[4].use[0]!./src/js/components/ActionForm.vue?vue&type=script&setup=true&lang=js ***!
  \*************************************************************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = ({
  __name: 'ActionForm',
  props: ['endpoint'],
  setup(__props, { expose }) {

const props = __props

const Tulia = __webpack_require__(/*! Tulia */ "Tulia");
const { defineProps, defineExpose, ref, reactive } = __webpack_require__(/*! vue */ "vue");

const fields = reactive({
    list: [],
});

const form = ref(null);
const submit = (newFields) => {
    fields.list = newFields;
    Tulia.PageLoader.show();
    form.value.submit();
};

expose({ submit });

const __returned__ = { Tulia, defineProps, defineExpose, ref, reactive, props, fields, form, submit }
Object.defineProperty(__returned__, '__isScriptSetup', { enumerable: false, value: true })
return __returned__
}

});

/***/ }),

/***/ "./src/js/components/Root.vue":
/*!************************************!*\
  !*** ./src/js/components/Root.vue ***!
  \************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _Root_vue_vue_type_template_id_378f20ae_scoped_true__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./Root.vue?vue&type=template&id=378f20ae&scoped=true */ "./src/js/components/Root.vue?vue&type=template&id=378f20ae&scoped=true");
/* harmony import */ var _Root_vue_vue_type_script_setup_true_lang_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./Root.vue?vue&type=script&setup=true&lang=js */ "./src/js/components/Root.vue?vue&type=script&setup=true&lang=js");
/* harmony import */ var _Root_vue_vue_type_style_index_0_id_378f20ae_scoped_true_lang_scss__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./Root.vue?vue&type=style&index=0&id=378f20ae&scoped=true&lang=scss */ "./src/js/components/Root.vue?vue&type=style&index=0&id=378f20ae&scoped=true&lang=scss");
/* harmony import */ var _home_adam_projects_tuliacms_development_tuliacms_core_src_Cms_Website_Infrastructure_Framework_Resources_public_websites_builder_node_modules_vue_loader_dist_exportHelper_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./node_modules/vue-loader/dist/exportHelper.js */ "./node_modules/vue-loader/dist/exportHelper.js");




;


const __exports__ = /*#__PURE__*/(0,_home_adam_projects_tuliacms_development_tuliacms_core_src_Cms_Website_Infrastructure_Framework_Resources_public_websites_builder_node_modules_vue_loader_dist_exportHelper_js__WEBPACK_IMPORTED_MODULE_3__["default"])(_Root_vue_vue_type_script_setup_true_lang_js__WEBPACK_IMPORTED_MODULE_1__["default"], [['render',_Root_vue_vue_type_template_id_378f20ae_scoped_true__WEBPACK_IMPORTED_MODULE_0__.render],['__scopeId',"data-v-378f20ae"],['__file',"src/js/components/Root.vue"]])
/* hot reload */
if (false) {}


/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (__exports__);

/***/ }),

/***/ "./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[4].use[0]!./src/js/components/Root.vue?vue&type=script&setup=true&lang=js":
/*!*******************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[4].use[0]!./src/js/components/Root.vue?vue&type=script&setup=true&lang=js ***!
  \*******************************************************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = ({
  __name: 'Root',
  props: ['websites', 'translations', 'locales', 'endpoints'],
  setup(__props, { expose }) {
  expose();

const props = __props

const NewWebsiteModal = (__webpack_require__(/*! ./modals/NewWebsite.vue */ "./src/js/components/modals/NewWebsite.vue")["default"]);
const NewLocaleModal = (__webpack_require__(/*! ./modals/NewLocale.vue */ "./src/js/components/modals/NewLocale.vue")["default"]);
const ActionForm = (__webpack_require__(/*! ./ActionForm.vue */ "./src/js/components/ActionForm.vue")["default"]);
const { defineProps, computed, reactive, ref } = __webpack_require__(/*! vue */ "vue");
const Tulia = __webpack_require__(/*! Tulia */ "Tulia");


const newLocaleModal = ref(null);
const newWebsiteModal = ref(null);
const activateWebsiteForm = ref(null);
const deactivateWebsiteForm = ref(null);
const deleteWebsiteForm = ref(null);
const deleteLocaleForm = ref(null);
const modals = {
    newWebsite: {
        show: () => newWebsiteModal.value.show(),
    },
    newLocale: {
        show: (website) => newLocaleModal.value.show(website),
    }
};

const activateWebsite = (id) => {
    activateWebsiteForm.value.submit({ id: id });
};
const deactivateWebsite = (id) => {
    deactivateWebsiteForm.value.submit({ id: id });
};
const deleteLocale = (website, locale) => {
    Tulia.Confirmation.warning().then((v) => {
        if (v.value) {
            deleteLocaleForm.value.submit({website, locale});
        }
    });
};
const createWebsite = () => {
    modals.newWebsite.show();
};
const deleteWebsite = (id) => {
    Tulia.Confirmation.warning().then((v) => {
        if (v.value) {
            deleteWebsiteForm.value.submit(id);
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

const __returned__ = { NewWebsiteModal, NewLocaleModal, ActionForm, defineProps, computed, reactive, ref, Tulia, props, newLocaleModal, newWebsiteModal, activateWebsiteForm, deactivateWebsiteForm, deleteWebsiteForm, deleteLocaleForm, modals, activateWebsite, deactivateWebsite, deleteLocale, createWebsite, deleteWebsite, newLocale, localeProperty }
Object.defineProperty(__returned__, '__isScriptSetup', { enumerable: false, value: true })
return __returned__
}

});

/***/ }),

/***/ "./src/js/components/modals/Modal.vue":
/*!********************************************!*\
  !*** ./src/js/components/modals/Modal.vue ***!
  \********************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _Modal_vue_vue_type_template_id_89e68560_scoped_true__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./Modal.vue?vue&type=template&id=89e68560&scoped=true */ "./src/js/components/modals/Modal.vue?vue&type=template&id=89e68560&scoped=true");
/* harmony import */ var _Modal_vue_vue_type_script_setup_true_lang_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./Modal.vue?vue&type=script&setup=true&lang=js */ "./src/js/components/modals/Modal.vue?vue&type=script&setup=true&lang=js");
/* harmony import */ var _Modal_vue_vue_type_style_index_0_id_89e68560_scoped_true_lang_scss__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./Modal.vue?vue&type=style&index=0&id=89e68560&scoped=true&lang=scss */ "./src/js/components/modals/Modal.vue?vue&type=style&index=0&id=89e68560&scoped=true&lang=scss");
/* harmony import */ var _home_adam_projects_tuliacms_development_tuliacms_core_src_Cms_Website_Infrastructure_Framework_Resources_public_websites_builder_node_modules_vue_loader_dist_exportHelper_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./node_modules/vue-loader/dist/exportHelper.js */ "./node_modules/vue-loader/dist/exportHelper.js");




;


const __exports__ = /*#__PURE__*/(0,_home_adam_projects_tuliacms_development_tuliacms_core_src_Cms_Website_Infrastructure_Framework_Resources_public_websites_builder_node_modules_vue_loader_dist_exportHelper_js__WEBPACK_IMPORTED_MODULE_3__["default"])(_Modal_vue_vue_type_script_setup_true_lang_js__WEBPACK_IMPORTED_MODULE_1__["default"], [['render',_Modal_vue_vue_type_template_id_89e68560_scoped_true__WEBPACK_IMPORTED_MODULE_0__.render],['__scopeId',"data-v-89e68560"],['__file',"src/js/components/modals/Modal.vue"]])
/* hot reload */
if (false) {}


/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (__exports__);

/***/ }),

/***/ "./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[4].use[0]!./src/js/components/modals/Modal.vue?vue&type=script&setup=true&lang=js":
/*!***************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[4].use[0]!./src/js/components/modals/Modal.vue?vue&type=script&setup=true&lang=js ***!
  \***************************************************************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = ({
  __name: 'Modal',
  props: {
    title: {
        type: String,
        default: "<<Title goes here>>",
    },
    modificators: {
        type: String,
        default: 'modal-dialog-centered',
    },
},
  setup(__props, { expose }) {

const props = __props

const { onMounted, ref, defineProps, defineExpose, computed } = __webpack_require__(/*! vue */ "vue");
const { Modal } = __webpack_require__(/*! bootstrap */ "bootstrap");



let modalElement = ref(null);
let thisModalObj = null;

const modalClassname = computed(() => {
    return 'modal-dialog ' + props.modificators;
});

onMounted(() => {
    thisModalObj = new Modal(modalElement.value);
});
function show () {
    thisModalObj.show();
}
function hide () {
    thisModalObj.hide();
}
function showLoader () {
    modalElement.value.classList.add('modal-loading');
}
function hideLoader () {
    modalElement.value.classList.remove('modal-loading');
}
expose({ show, hide, showLoader, hideLoader });

const __returned__ = { onMounted, ref, defineProps, defineExpose, computed, Modal, props, modalElement, thisModalObj, modalClassname, show, hide, showLoader, hideLoader }
Object.defineProperty(__returned__, '__isScriptSetup', { enumerable: false, value: true })
return __returned__
}

});

/***/ }),

/***/ "./src/js/components/modals/NewLocale.vue":
/*!************************************************!*\
  !*** ./src/js/components/modals/NewLocale.vue ***!
  \************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _NewLocale_vue_vue_type_template_id_3d96f2bd__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./NewLocale.vue?vue&type=template&id=3d96f2bd */ "./src/js/components/modals/NewLocale.vue?vue&type=template&id=3d96f2bd");
/* harmony import */ var _NewLocale_vue_vue_type_script_setup_true_lang_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./NewLocale.vue?vue&type=script&setup=true&lang=js */ "./src/js/components/modals/NewLocale.vue?vue&type=script&setup=true&lang=js");
/* harmony import */ var _home_adam_projects_tuliacms_development_tuliacms_core_src_Cms_Website_Infrastructure_Framework_Resources_public_websites_builder_node_modules_vue_loader_dist_exportHelper_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./node_modules/vue-loader/dist/exportHelper.js */ "./node_modules/vue-loader/dist/exportHelper.js");




;
const __exports__ = /*#__PURE__*/(0,_home_adam_projects_tuliacms_development_tuliacms_core_src_Cms_Website_Infrastructure_Framework_Resources_public_websites_builder_node_modules_vue_loader_dist_exportHelper_js__WEBPACK_IMPORTED_MODULE_2__["default"])(_NewLocale_vue_vue_type_script_setup_true_lang_js__WEBPACK_IMPORTED_MODULE_1__["default"], [['render',_NewLocale_vue_vue_type_template_id_3d96f2bd__WEBPACK_IMPORTED_MODULE_0__.render],['__file',"src/js/components/modals/NewLocale.vue"]])
/* hot reload */
if (false) {}


/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (__exports__);

/***/ }),

/***/ "./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[4].use[0]!./src/js/components/modals/NewLocale.vue?vue&type=script&setup=true&lang=js":
/*!*******************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[4].use[0]!./src/js/components/modals/NewLocale.vue?vue&type=script&setup=true&lang=js ***!
  \*******************************************************************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = ({
  __name: 'NewLocale',
  props: ['translations', 'locales', 'endpoint'],
  setup(__props, { expose }) {

const props = __props

const FetchAPI = (__webpack_require__(/*! ./../../shared/FetchAPI.js */ "./src/js/shared/FetchAPI.js")["default"]);
const { defineProps, ref, defineExpose, reactive, toRaw } = __webpack_require__(/*! vue */ "vue");

const Modal = (__webpack_require__(/*! ./Modal.vue */ "./src/js/components/modals/Modal.vue")["default"]);

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

    resetFormErrors();

    FetchAPI.post(props.endpoint, { 'add_locale_form': toRaw(form.values) })
        .then((data) => {
            if (data.errors) {
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

       // if (!found) {
            availableLocales.list.push(allLocales[j]);
        //}
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
expose({ show, hide });

const __returned__ = { FetchAPI, defineProps, ref, defineExpose, reactive, toRaw, props, Modal, website, defaultLocale, availableLocales, modalInstance, form, resetForm, hasError, resetFormErrors, fillFormErrors, updateLocalePrefix, submit, collectAvailableLocales, show, hide }
Object.defineProperty(__returned__, '__isScriptSetup', { enumerable: false, value: true })
return __returned__
}

});

/***/ }),

/***/ "./src/js/components/modals/NewWebsite.vue":
/*!*************************************************!*\
  !*** ./src/js/components/modals/NewWebsite.vue ***!
  \*************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _NewWebsite_vue_vue_type_template_id_f0cc8f70__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./NewWebsite.vue?vue&type=template&id=f0cc8f70 */ "./src/js/components/modals/NewWebsite.vue?vue&type=template&id=f0cc8f70");
/* harmony import */ var _NewWebsite_vue_vue_type_script_setup_true_lang_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./NewWebsite.vue?vue&type=script&setup=true&lang=js */ "./src/js/components/modals/NewWebsite.vue?vue&type=script&setup=true&lang=js");
/* harmony import */ var _home_adam_projects_tuliacms_development_tuliacms_core_src_Cms_Website_Infrastructure_Framework_Resources_public_websites_builder_node_modules_vue_loader_dist_exportHelper_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./node_modules/vue-loader/dist/exportHelper.js */ "./node_modules/vue-loader/dist/exportHelper.js");




;
const __exports__ = /*#__PURE__*/(0,_home_adam_projects_tuliacms_development_tuliacms_core_src_Cms_Website_Infrastructure_Framework_Resources_public_websites_builder_node_modules_vue_loader_dist_exportHelper_js__WEBPACK_IMPORTED_MODULE_2__["default"])(_NewWebsite_vue_vue_type_script_setup_true_lang_js__WEBPACK_IMPORTED_MODULE_1__["default"], [['render',_NewWebsite_vue_vue_type_template_id_f0cc8f70__WEBPACK_IMPORTED_MODULE_0__.render],['__file',"src/js/components/modals/NewWebsite.vue"]])
/* hot reload */
if (false) {}


/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (__exports__);

/***/ }),

/***/ "./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[4].use[0]!./src/js/components/modals/NewWebsite.vue?vue&type=script&setup=true&lang=js":
/*!********************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[4].use[0]!./src/js/components/modals/NewWebsite.vue?vue&type=script&setup=true&lang=js ***!
  \********************************************************************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = ({
  __name: 'NewWebsite',
  props: ['translations', 'locales', 'endpoint'],
  setup(__props, { expose }) {

const props = __props

const FetchAPI = (__webpack_require__(/*! ./../../shared/FetchAPI.js */ "./src/js/shared/FetchAPI.js")["default"]);
const { defineProps, ref, defineExpose, reactive, toRaw } = __webpack_require__(/*! vue */ "vue");

const Modal = (__webpack_require__(/*! ./Modal.vue */ "./src/js/components/modals/Modal.vue")["default"]);

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
expose({ show, hide });

const __returned__ = { FetchAPI, defineProps, ref, defineExpose, reactive, toRaw, props, Modal, newWebsiteName, modalInstance, defaults, form, resetForm, hasError, resetFormErrors, fillFormErrors, submit, show, hide }
Object.defineProperty(__returned__, '__isScriptSetup', { enumerable: false, value: true })
return __returned__
}

});

/***/ }),

/***/ "./src/js/components/Root.vue?vue&type=style&index=0&id=378f20ae&scoped=true&lang=scss":
/*!*********************************************************************************************!*\
  !*** ./src/js/components/Root.vue?vue&type=style&index=0&id=378f20ae&scoped=true&lang=scss ***!
  \*********************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_mini_css_extract_plugin_dist_loader_js_node_modules_css_loader_dist_cjs_js_node_modules_vue_loader_dist_stylePostLoader_js_node_modules_sass_loader_dist_cjs_js_node_modules_vue_loader_dist_index_js_ruleSet_1_rules_4_use_0_Root_vue_vue_type_style_index_0_id_378f20ae_scoped_true_lang_scss__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../node_modules/mini-css-extract-plugin/dist/loader.js!../../../node_modules/css-loader/dist/cjs.js!../../../node_modules/vue-loader/dist/stylePostLoader.js!../../../node_modules/sass-loader/dist/cjs.js!../../../node_modules/vue-loader/dist/index.js??ruleSet[1].rules[4].use[0]!./Root.vue?vue&type=style&index=0&id=378f20ae&scoped=true&lang=scss */ "./node_modules/mini-css-extract-plugin/dist/loader.js!./node_modules/css-loader/dist/cjs.js!./node_modules/vue-loader/dist/stylePostLoader.js!./node_modules/sass-loader/dist/cjs.js!./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[4].use[0]!./src/js/components/Root.vue?vue&type=style&index=0&id=378f20ae&scoped=true&lang=scss");


/***/ }),

/***/ "./src/js/components/modals/Modal.vue?vue&type=style&index=0&id=89e68560&scoped=true&lang=scss":
/*!*****************************************************************************************************!*\
  !*** ./src/js/components/modals/Modal.vue?vue&type=style&index=0&id=89e68560&scoped=true&lang=scss ***!
  \*****************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_mini_css_extract_plugin_dist_loader_js_node_modules_css_loader_dist_cjs_js_node_modules_vue_loader_dist_stylePostLoader_js_node_modules_sass_loader_dist_cjs_js_node_modules_vue_loader_dist_index_js_ruleSet_1_rules_4_use_0_Modal_vue_vue_type_style_index_0_id_89e68560_scoped_true_lang_scss__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../node_modules/mini-css-extract-plugin/dist/loader.js!../../../../node_modules/css-loader/dist/cjs.js!../../../../node_modules/vue-loader/dist/stylePostLoader.js!../../../../node_modules/sass-loader/dist/cjs.js!../../../../node_modules/vue-loader/dist/index.js??ruleSet[1].rules[4].use[0]!./Modal.vue?vue&type=style&index=0&id=89e68560&scoped=true&lang=scss */ "./node_modules/mini-css-extract-plugin/dist/loader.js!./node_modules/css-loader/dist/cjs.js!./node_modules/vue-loader/dist/stylePostLoader.js!./node_modules/sass-loader/dist/cjs.js!./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[4].use[0]!./src/js/components/modals/Modal.vue?vue&type=style&index=0&id=89e68560&scoped=true&lang=scss");


/***/ }),

/***/ "./src/js/components/ActionForm.vue?vue&type=script&setup=true&lang=js":
/*!*****************************************************************************!*\
  !*** ./src/js/components/ActionForm.vue?vue&type=script&setup=true&lang=js ***!
  \*****************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* reexport safe */ _node_modules_vue_loader_dist_index_js_ruleSet_1_rules_4_use_0_ActionForm_vue_vue_type_script_setup_true_lang_js__WEBPACK_IMPORTED_MODULE_0__["default"])
/* harmony export */ });
/* harmony import */ var _node_modules_vue_loader_dist_index_js_ruleSet_1_rules_4_use_0_ActionForm_vue_vue_type_script_setup_true_lang_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../node_modules/vue-loader/dist/index.js??ruleSet[1].rules[4].use[0]!./ActionForm.vue?vue&type=script&setup=true&lang=js */ "./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[4].use[0]!./src/js/components/ActionForm.vue?vue&type=script&setup=true&lang=js");
 

/***/ }),

/***/ "./src/js/components/Root.vue?vue&type=script&setup=true&lang=js":
/*!***********************************************************************!*\
  !*** ./src/js/components/Root.vue?vue&type=script&setup=true&lang=js ***!
  \***********************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* reexport safe */ _node_modules_vue_loader_dist_index_js_ruleSet_1_rules_4_use_0_Root_vue_vue_type_script_setup_true_lang_js__WEBPACK_IMPORTED_MODULE_0__["default"])
/* harmony export */ });
/* harmony import */ var _node_modules_vue_loader_dist_index_js_ruleSet_1_rules_4_use_0_Root_vue_vue_type_script_setup_true_lang_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../node_modules/vue-loader/dist/index.js??ruleSet[1].rules[4].use[0]!./Root.vue?vue&type=script&setup=true&lang=js */ "./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[4].use[0]!./src/js/components/Root.vue?vue&type=script&setup=true&lang=js");
 

/***/ }),

/***/ "./src/js/components/modals/Modal.vue?vue&type=script&setup=true&lang=js":
/*!*******************************************************************************!*\
  !*** ./src/js/components/modals/Modal.vue?vue&type=script&setup=true&lang=js ***!
  \*******************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* reexport safe */ _node_modules_vue_loader_dist_index_js_ruleSet_1_rules_4_use_0_Modal_vue_vue_type_script_setup_true_lang_js__WEBPACK_IMPORTED_MODULE_0__["default"])
/* harmony export */ });
/* harmony import */ var _node_modules_vue_loader_dist_index_js_ruleSet_1_rules_4_use_0_Modal_vue_vue_type_script_setup_true_lang_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../node_modules/vue-loader/dist/index.js??ruleSet[1].rules[4].use[0]!./Modal.vue?vue&type=script&setup=true&lang=js */ "./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[4].use[0]!./src/js/components/modals/Modal.vue?vue&type=script&setup=true&lang=js");
 

/***/ }),

/***/ "./src/js/components/modals/NewLocale.vue?vue&type=script&setup=true&lang=js":
/*!***********************************************************************************!*\
  !*** ./src/js/components/modals/NewLocale.vue?vue&type=script&setup=true&lang=js ***!
  \***********************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* reexport safe */ _node_modules_vue_loader_dist_index_js_ruleSet_1_rules_4_use_0_NewLocale_vue_vue_type_script_setup_true_lang_js__WEBPACK_IMPORTED_MODULE_0__["default"])
/* harmony export */ });
/* harmony import */ var _node_modules_vue_loader_dist_index_js_ruleSet_1_rules_4_use_0_NewLocale_vue_vue_type_script_setup_true_lang_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../node_modules/vue-loader/dist/index.js??ruleSet[1].rules[4].use[0]!./NewLocale.vue?vue&type=script&setup=true&lang=js */ "./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[4].use[0]!./src/js/components/modals/NewLocale.vue?vue&type=script&setup=true&lang=js");
 

/***/ }),

/***/ "./src/js/components/modals/NewWebsite.vue?vue&type=script&setup=true&lang=js":
/*!************************************************************************************!*\
  !*** ./src/js/components/modals/NewWebsite.vue?vue&type=script&setup=true&lang=js ***!
  \************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* reexport safe */ _node_modules_vue_loader_dist_index_js_ruleSet_1_rules_4_use_0_NewWebsite_vue_vue_type_script_setup_true_lang_js__WEBPACK_IMPORTED_MODULE_0__["default"])
/* harmony export */ });
/* harmony import */ var _node_modules_vue_loader_dist_index_js_ruleSet_1_rules_4_use_0_NewWebsite_vue_vue_type_script_setup_true_lang_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../node_modules/vue-loader/dist/index.js??ruleSet[1].rules[4].use[0]!./NewWebsite.vue?vue&type=script&setup=true&lang=js */ "./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[4].use[0]!./src/js/components/modals/NewWebsite.vue?vue&type=script&setup=true&lang=js");
 

/***/ }),

/***/ "./src/js/components/ActionForm.vue?vue&type=template&id=ea7c7e34":
/*!************************************************************************!*\
  !*** ./src/js/components/ActionForm.vue?vue&type=template&id=ea7c7e34 ***!
  \************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "render": () => (/* reexport safe */ _node_modules_vue_loader_dist_templateLoader_js_ruleSet_1_rules_1_node_modules_vue_loader_dist_index_js_ruleSet_1_rules_4_use_0_ActionForm_vue_vue_type_template_id_ea7c7e34__WEBPACK_IMPORTED_MODULE_0__.render)
/* harmony export */ });
/* harmony import */ var _node_modules_vue_loader_dist_templateLoader_js_ruleSet_1_rules_1_node_modules_vue_loader_dist_index_js_ruleSet_1_rules_4_use_0_ActionForm_vue_vue_type_template_id_ea7c7e34__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[1]!../../../node_modules/vue-loader/dist/index.js??ruleSet[1].rules[4].use[0]!./ActionForm.vue?vue&type=template&id=ea7c7e34 */ "./node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[1]!./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[4].use[0]!./src/js/components/ActionForm.vue?vue&type=template&id=ea7c7e34");


/***/ }),

/***/ "./src/js/components/Root.vue?vue&type=template&id=378f20ae&scoped=true":
/*!******************************************************************************!*\
  !*** ./src/js/components/Root.vue?vue&type=template&id=378f20ae&scoped=true ***!
  \******************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "render": () => (/* reexport safe */ _node_modules_vue_loader_dist_templateLoader_js_ruleSet_1_rules_1_node_modules_vue_loader_dist_index_js_ruleSet_1_rules_4_use_0_Root_vue_vue_type_template_id_378f20ae_scoped_true__WEBPACK_IMPORTED_MODULE_0__.render)
/* harmony export */ });
/* harmony import */ var _node_modules_vue_loader_dist_templateLoader_js_ruleSet_1_rules_1_node_modules_vue_loader_dist_index_js_ruleSet_1_rules_4_use_0_Root_vue_vue_type_template_id_378f20ae_scoped_true__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[1]!../../../node_modules/vue-loader/dist/index.js??ruleSet[1].rules[4].use[0]!./Root.vue?vue&type=template&id=378f20ae&scoped=true */ "./node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[1]!./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[4].use[0]!./src/js/components/Root.vue?vue&type=template&id=378f20ae&scoped=true");


/***/ }),

/***/ "./src/js/components/modals/Modal.vue?vue&type=template&id=89e68560&scoped=true":
/*!**************************************************************************************!*\
  !*** ./src/js/components/modals/Modal.vue?vue&type=template&id=89e68560&scoped=true ***!
  \**************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "render": () => (/* reexport safe */ _node_modules_vue_loader_dist_templateLoader_js_ruleSet_1_rules_1_node_modules_vue_loader_dist_index_js_ruleSet_1_rules_4_use_0_Modal_vue_vue_type_template_id_89e68560_scoped_true__WEBPACK_IMPORTED_MODULE_0__.render)
/* harmony export */ });
/* harmony import */ var _node_modules_vue_loader_dist_templateLoader_js_ruleSet_1_rules_1_node_modules_vue_loader_dist_index_js_ruleSet_1_rules_4_use_0_Modal_vue_vue_type_template_id_89e68560_scoped_true__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[1]!../../../../node_modules/vue-loader/dist/index.js??ruleSet[1].rules[4].use[0]!./Modal.vue?vue&type=template&id=89e68560&scoped=true */ "./node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[1]!./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[4].use[0]!./src/js/components/modals/Modal.vue?vue&type=template&id=89e68560&scoped=true");


/***/ }),

/***/ "./src/js/components/modals/NewLocale.vue?vue&type=template&id=3d96f2bd":
/*!******************************************************************************!*\
  !*** ./src/js/components/modals/NewLocale.vue?vue&type=template&id=3d96f2bd ***!
  \******************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "render": () => (/* reexport safe */ _node_modules_vue_loader_dist_templateLoader_js_ruleSet_1_rules_1_node_modules_vue_loader_dist_index_js_ruleSet_1_rules_4_use_0_NewLocale_vue_vue_type_template_id_3d96f2bd__WEBPACK_IMPORTED_MODULE_0__.render)
/* harmony export */ });
/* harmony import */ var _node_modules_vue_loader_dist_templateLoader_js_ruleSet_1_rules_1_node_modules_vue_loader_dist_index_js_ruleSet_1_rules_4_use_0_NewLocale_vue_vue_type_template_id_3d96f2bd__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[1]!../../../../node_modules/vue-loader/dist/index.js??ruleSet[1].rules[4].use[0]!./NewLocale.vue?vue&type=template&id=3d96f2bd */ "./node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[1]!./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[4].use[0]!./src/js/components/modals/NewLocale.vue?vue&type=template&id=3d96f2bd");


/***/ }),

/***/ "./src/js/components/modals/NewWebsite.vue?vue&type=template&id=f0cc8f70":
/*!*******************************************************************************!*\
  !*** ./src/js/components/modals/NewWebsite.vue?vue&type=template&id=f0cc8f70 ***!
  \*******************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "render": () => (/* reexport safe */ _node_modules_vue_loader_dist_templateLoader_js_ruleSet_1_rules_1_node_modules_vue_loader_dist_index_js_ruleSet_1_rules_4_use_0_NewWebsite_vue_vue_type_template_id_f0cc8f70__WEBPACK_IMPORTED_MODULE_0__.render)
/* harmony export */ });
/* harmony import */ var _node_modules_vue_loader_dist_templateLoader_js_ruleSet_1_rules_1_node_modules_vue_loader_dist_index_js_ruleSet_1_rules_4_use_0_NewWebsite_vue_vue_type_template_id_f0cc8f70__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[1]!../../../../node_modules/vue-loader/dist/index.js??ruleSet[1].rules[4].use[0]!./NewWebsite.vue?vue&type=template&id=f0cc8f70 */ "./node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[1]!./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[4].use[0]!./src/js/components/modals/NewWebsite.vue?vue&type=template&id=f0cc8f70");


/***/ }),

/***/ "./node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[1]!./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[4].use[0]!./src/js/components/ActionForm.vue?vue&type=template&id=ea7c7e34":
/*!******************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[1]!./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[4].use[0]!./src/js/components/ActionForm.vue?vue&type=template&id=ea7c7e34 ***!
  \******************************************************************************************************************************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "render": () => (/* binding */ render)
/* harmony export */ });
/* harmony import */ var vue__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! vue */ "vue");
/* harmony import */ var vue__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(vue__WEBPACK_IMPORTED_MODULE_0__);


const _hoisted_1 = ["action"]
const _hoisted_2 = ["value"]
const _hoisted_3 = ["name", "value"]

function render(_ctx, _cache, $props, $setup, $data, $options) {
  return ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)("form", {
    action: $props.endpoint.url,
    method: "post",
    ref: "form"
  }, [
    (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("input", {
      type: "hidden",
      value: $props.endpoint.csrfToken,
      name: "_token"
    }, null, 8 /* PROPS */, _hoisted_2),
    ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(true), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)(vue__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, (0,vue__WEBPACK_IMPORTED_MODULE_0__.renderList)($setup.fields.list, (value, name) => {
      return ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)("input", {
        type: "hidden",
        name: name,
        value: value
      }, null, 8 /* PROPS */, _hoisted_3))
    }), 256 /* UNKEYED_FRAGMENT */))
  ], 8 /* PROPS */, _hoisted_1))
}

/***/ }),

/***/ "./node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[1]!./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[4].use[0]!./src/js/components/Root.vue?vue&type=template&id=378f20ae&scoped=true":
/*!************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[1]!./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[4].use[0]!./src/js/components/Root.vue?vue&type=template&id=378f20ae&scoped=true ***!
  \************************************************************************************************************************************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "render": () => (/* binding */ render)
/* harmony export */ });
/* harmony import */ var vue__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! vue */ "vue");
/* harmony import */ var vue__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(vue__WEBPACK_IMPORTED_MODULE_0__);


const _withScopeId = n => ((0,vue__WEBPACK_IMPORTED_MODULE_0__.pushScopeId)("data-v-378f20ae"),n=n(),(0,vue__WEBPACK_IMPORTED_MODULE_0__.popScopeId)(),n)
const _hoisted_1 = { class: "pane pane-lead" }
const _hoisted_2 = { class: "pane-header" }
const _hoisted_3 = { class: "pane-buttons" }
const _hoisted_4 = /*#__PURE__*/ _withScopeId(() => /*#__PURE__*/(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("i", { class: "btn-icon fas fa-plus" }, null, -1 /* HOISTED */))
const _hoisted_5 = /*#__PURE__*/ _withScopeId(() => /*#__PURE__*/(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("i", { class: "pane-header-icon fas fa-globe" }, null, -1 /* HOISTED */))
const _hoisted_6 = { class: "pane-title" }
const _hoisted_7 = { class: "pane-body" }
const _hoisted_8 = { class: "alert alert-info" }
const _hoisted_9 = { class: "row row-cols-1 row-cols-sm-1 row-cols-md-2 row-cols-lg-2 row-cols-xl-3 websites-list" }
const _hoisted_10 = { class: "col mb-4" }
const _hoisted_11 = { class: "card" }
const _hoisted_12 = { class: "card-body" }
const _hoisted_13 = ["title"]
const _hoisted_14 = ["onClick"]
const _hoisted_15 = { class: "card-title" }
const _hoisted_16 = { class: "text-muted" }
const _hoisted_17 = { class: "list-group list-group-flush" }
const _hoisted_18 = { class: "list-group-item d-flex justify-content-between align-items-center pe-1" }
const _hoisted_19 = ["src"]
const _hoisted_20 = { key: 0 }
const _hoisted_21 = { key: 1 }
const _hoisted_22 = {
  key: 2,
  class: "text-lowercase"
}
const _hoisted_23 = ["onClick", "title"]
const _hoisted_24 = /*#__PURE__*/ _withScopeId(() => /*#__PURE__*/(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("i", { class: "btn-icon fa fa-eye-slash" }, null, -1 /* HOISTED */))
const _hoisted_25 = [
  _hoisted_24
]
const _hoisted_26 = ["onClick", "title"]
const _hoisted_27 = /*#__PURE__*/ _withScopeId(() => /*#__PURE__*/(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("i", { class: "btn-icon fa fa-eye" }, null, -1 /* HOISTED */))
const _hoisted_28 = [
  _hoisted_27
]
const _hoisted_29 = ["onClick", "title"]
const _hoisted_30 = /*#__PURE__*/ _withScopeId(() => /*#__PURE__*/(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("i", { class: "btn-icon fa fa-trash" }, null, -1 /* HOISTED */))
const _hoisted_31 = [
  _hoisted_30
]
const _hoisted_32 = ["title", "onClick"]
const _hoisted_33 = /*#__PURE__*/ _withScopeId(() => /*#__PURE__*/(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("i", { class: "list-group-item-icon fa fa-plus" }, null, -1 /* HOISTED */))
const _hoisted_34 = { class: "card-footer px-2" }
const _hoisted_35 = ["onClick", "title"]
const _hoisted_36 = /*#__PURE__*/ _withScopeId(() => /*#__PURE__*/(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("i", { class: "btn-icon fa fa-eye-slash" }, null, -1 /* HOISTED */))
const _hoisted_37 = [
  _hoisted_36
]
const _hoisted_38 = ["onClick", "title"]
const _hoisted_39 = /*#__PURE__*/ _withScopeId(() => /*#__PURE__*/(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("i", { class: "btn-icon fa fa-eye" }, null, -1 /* HOISTED */))
const _hoisted_40 = [
  _hoisted_39
]
const _hoisted_41 = ["onClick", "title"]
const _hoisted_42 = /*#__PURE__*/ _withScopeId(() => /*#__PURE__*/(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("i", { class: "btn-icon fa fa-trash" }, null, -1 /* HOISTED */))
const _hoisted_43 = [
  _hoisted_42
]

function render(_ctx, _cache, $props, $setup, $data, $options) {
  return ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)(vue__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, [
    (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_1, [
      (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_2, [
        (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_3, [
          (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("a", {
            href: "#",
            class: "btn btn-success btn-icon-left",
            onClick: $setup.createWebsite
          }, [
            _hoisted_4,
            (0,vue__WEBPACK_IMPORTED_MODULE_0__.createTextVNode)(" " + (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)($props.translations.create), 1 /* TEXT */)
          ])
        ]),
        _hoisted_5,
        (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("h1", _hoisted_6, (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)($props.translations.websites), 1 /* TEXT */)
      ]),
      (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_7, [
        (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_8, (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)($props.translations.websitesLongDescription), 1 /* TEXT */),
        (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_9, [
          ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(true), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)(vue__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, (0,vue__WEBPACK_IMPORTED_MODULE_0__.renderList)($props.websites, (website) => {
            return ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)("div", _hoisted_10, [
              (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_11, [
                (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_12, [
                  (website.active === false)
                    ? ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)("span", {
                        key: 0,
                        "data-bs-toggle": "tooltip",
                        title: $props.translations.websiteInactiveHint,
                        class: "badge badge-secondary mb-3"
                      }, (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)($props.translations.websiteInactive), 9 /* TEXT, PROPS */, _hoisted_13))
                    : (0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)("v-if", true),
                  (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("a", {
                    href: "#",
                    onClick: $event => (_ctx.manageWebsite(website.id))
                  }, [
                    (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("h4", _hoisted_15, (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)(website.name), 1 /* TEXT */)
                  ], 8 /* PROPS */, _hoisted_14),
                  (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("small", _hoisted_16, "ID: " + (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)(website.id), 1 /* TEXT */)
                ]),
                (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_17, [
                  ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(true), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)(vue__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, (0,vue__WEBPACK_IMPORTED_MODULE_0__.renderList)(website.locales, (locale) => {
                    return ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)("div", _hoisted_18, [
                      (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("img", {
                        src: $setup.localeProperty(locale.code, 'flag'),
                        alt: "",
                        class: "website-locale-flag-icon"
                      }, null, 8 /* PROPS */, _hoisted_19),
                      (locale.is_default)
                        ? ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)("span", _hoisted_20, [
                            (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("b", null, (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)($setup.localeProperty(locale.code, 'name')), 1 /* TEXT */)
                          ]))
                        : ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)("span", _hoisted_21, (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)($setup.localeProperty(locale.code, 'name')), 1 /* TEXT */)),
                      (locale.is_default)
                        ? ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)("small", _hoisted_22, "(" + (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)($props.translations.defaultLocale) + ")", 1 /* TEXT */))
                        : (0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)("v-if", true),
                      (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", null, [
                        (website.active)
                          ? ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)("a", {
                              key: 0,
                              href: "#",
                              onClick: $event => ($setup.deactivateWebsite(website.id)),
                              title: $props.translations.deactivate,
                              "data-bs-toggle": "tooltip",
                              class: "btn btn-sm btn-icon-only btn-outline-primary me-2"
                            }, _hoisted_25, 8 /* PROPS */, _hoisted_23))
                          : (0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)("v-if", true),
                        (!website.active)
                          ? ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)("a", {
                              key: 1,
                              href: "#",
                              onClick: $event => ($setup.activateWebsite(website.id)),
                              title: $props.translations.activate,
                              "data-bs-toggle": "tooltip",
                              class: "btn btn-sm btn-icon-only btn-outline-primary me-2"
                            }, _hoisted_28, 8 /* PROPS */, _hoisted_26))
                          : (0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)("v-if", true),
                        (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("a", {
                          href: "#",
                          onClick: $event => ($setup.deleteLocale(website.id, locale.code)),
                          title: $props.translations.deleteLocale,
                          "data-bs-toggle": "tooltip",
                          class: "btn btn-sm btn-icon-only btn-outline-danger me-2"
                        }, _hoisted_31, 8 /* PROPS */, _hoisted_29)
                      ])
                    ]))
                  }), 256 /* UNKEYED_FRAGMENT */)),
                  (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("a", {
                    href: "#",
                    class: "list-group-item list-group-item-with-icon",
                    "data-bs-toggle": "tooltip",
                    "data-bs-placement": "right",
                    title: $props.translations.addLocale,
                    onClick: $event => ($setup.newLocale(website))
                  }, [
                    _hoisted_33,
                    (0,vue__WEBPACK_IMPORTED_MODULE_0__.createTextVNode)(" " + (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)($props.translations.addLocale), 1 /* TEXT */)
                  ], 8 /* PROPS */, _hoisted_32)
                ]),
                (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_34, [
                  (website.active)
                    ? ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)("a", {
                        key: 0,
                        href: "#",
                        onClick: $event => ($setup.deactivateWebsite(website.id)),
                        title: $props.translations.deactivate,
                        "data-bs-toggle": "tooltip",
                        class: "btn btn-sm btn-icon-only btn-outline-primary me-2"
                      }, _hoisted_37, 8 /* PROPS */, _hoisted_35))
                    : (0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)("v-if", true),
                  (!website.active)
                    ? ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)("a", {
                        key: 1,
                        href: "#",
                        onClick: $event => ($setup.activateWebsite(website.id)),
                        title: $props.translations.activate,
                        "data-bs-toggle": "tooltip",
                        class: "btn btn-sm btn-icon-only btn-outline-primary me-2"
                      }, _hoisted_40, 8 /* PROPS */, _hoisted_38))
                    : (0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)("v-if", true),
                  (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("a", {
                    href: "#",
                    onClick: $event => ($setup.deleteWebsite(website.id)),
                    title: $props.translations.deleteWebsite,
                    "data-bs-toggle": "tooltip",
                    class: "btn btn-sm btn-icon-only btn-outline-danger me-2"
                  }, _hoisted_43, 8 /* PROPS */, _hoisted_41)
                ])
              ])
            ]))
          }), 256 /* UNKEYED_FRAGMENT */))
        ])
      ])
    ]),
    (0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)($setup["NewWebsiteModal"], {
      translations: $props.translations,
      locales: $props.locales,
      endpoint: $props.endpoints.newWebsite,
      ref: "newWebsiteModal"
    }, null, 8 /* PROPS */, ["translations", "locales", "endpoint"]),
    (0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)($setup["NewLocaleModal"], {
      translations: $props.translations,
      locales: $props.locales,
      endpoint: $props.endpoints.newLocale,
      ref: "newLocaleModal"
    }, null, 8 /* PROPS */, ["translations", "locales", "endpoint"]),
    (0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)($setup["ActionForm"], {
      endpoint: $props.endpoints.activateWebsite,
      ref: "activateWebsiteForm"
    }, null, 8 /* PROPS */, ["endpoint"]),
    (0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)($setup["ActionForm"], {
      endpoint: $props.endpoints.deactivateWebsite,
      ref: "deactivateWebsiteForm"
    }, null, 8 /* PROPS */, ["endpoint"]),
    (0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)($setup["ActionForm"], {
      endpoint: $props.endpoints.deleteWebsite,
      ref: "deleteWebsiteForm"
    }, null, 8 /* PROPS */, ["endpoint"]),
    (0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)($setup["ActionForm"], {
      endpoint: $props.endpoints.deleteLocale,
      ref: "deleteLocaleForm"
    }, null, 8 /* PROPS */, ["endpoint"])
  ], 64 /* STABLE_FRAGMENT */))
}

/***/ }),

/***/ "./node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[1]!./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[4].use[0]!./src/js/components/modals/Modal.vue?vue&type=template&id=89e68560&scoped=true":
/*!********************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[1]!./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[4].use[0]!./src/js/components/modals/Modal.vue?vue&type=template&id=89e68560&scoped=true ***!
  \********************************************************************************************************************************************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "render": () => (/* binding */ render)
/* harmony export */ });
/* harmony import */ var vue__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! vue */ "vue");
/* harmony import */ var vue__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(vue__WEBPACK_IMPORTED_MODULE_0__);


const _withScopeId = n => ((0,vue__WEBPACK_IMPORTED_MODULE_0__.pushScopeId)("data-v-89e68560"),n=n(),(0,vue__WEBPACK_IMPORTED_MODULE_0__.popScopeId)(),n)
const _hoisted_1 = {
  class: "modal fade",
  "data-bs-backdrop": "static",
  tabindex: "-1",
  "aria-labelledby": "",
  "aria-hidden": "true",
  ref: "modalElement"
}
const _hoisted_2 = { class: "modal-content" }
const _hoisted_3 = { class: "modal-header" }
const _hoisted_4 = { class: "modal-title" }
const _hoisted_5 = /*#__PURE__*/ _withScopeId(() => /*#__PURE__*/(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("button", {
  type: "button",
  class: "btn-close",
  "data-bs-dismiss": "modal",
  "aria-label": "Close"
}, null, -1 /* HOISTED */))
const _hoisted_6 = { class: "modal-body" }
const _hoisted_7 = { class: "modal-footer" }
const _hoisted_8 = /*#__PURE__*/ _withScopeId(() => /*#__PURE__*/(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", { class: "loader" }, [
  /*#__PURE__*/(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", { class: "load-inner" }, [
    /*#__PURE__*/(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", {
      class: "spinner-border",
      role: "status",
      style: {"width":"3rem","height":"3rem"}
    }, [
      /*#__PURE__*/(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("span", { class: "visually-hidden" }, "Loading...")
    ])
  ])
], -1 /* HOISTED */))

function render(_ctx, _cache, $props, $setup, $data, $options) {
  return ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createBlock)(vue__WEBPACK_IMPORTED_MODULE_0__.Teleport, { to: "body" }, [
    (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_1, [
      (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", {
        class: (0,vue__WEBPACK_IMPORTED_MODULE_0__.normalizeClass)($setup.modalClassname)
      }, [
        (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_2, [
          (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_3, [
            (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("h5", _hoisted_4, (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)($props.title), 1 /* TEXT */),
            _hoisted_5
          ]),
          (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_6, [
            (0,vue__WEBPACK_IMPORTED_MODULE_0__.renderSlot)(_ctx.$slots, "body", {}, undefined, true)
          ]),
          (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_7, [
            (0,vue__WEBPACK_IMPORTED_MODULE_0__.renderSlot)(_ctx.$slots, "footer", {}, undefined, true)
          ]),
          _hoisted_8
        ])
      ], 2 /* CLASS */)
    ], 512 /* NEED_PATCH */)
  ]))
}

/***/ }),

/***/ "./node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[1]!./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[4].use[0]!./src/js/components/modals/NewLocale.vue?vue&type=template&id=3d96f2bd":
/*!************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[1]!./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[4].use[0]!./src/js/components/modals/NewLocale.vue?vue&type=template&id=3d96f2bd ***!
  \************************************************************************************************************************************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "render": () => (/* binding */ render)
/* harmony export */ });
/* harmony import */ var vue__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! vue */ "vue");
/* harmony import */ var vue__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(vue__WEBPACK_IMPORTED_MODULE_0__);


const _hoisted_1 = { class: "row" }
const _hoisted_2 = { class: "col-6" }
const _hoisted_3 = { class: "mb-3" }
const _hoisted_4 = {
  for: "new-locale-code",
  class: "form-label"
}
const _hoisted_5 = /*#__PURE__*/(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("option", { value: "" }, "---", -1 /* HOISTED */)
const _hoisted_6 = ["value"]
const _hoisted_7 = {
  key: 0,
  class: "invalid-feedback"
}
const _hoisted_8 = {
  class: "accordion",
  id: "accordion-new-locale"
}
const _hoisted_9 = { class: "accordion-item" }
const _hoisted_10 = { class: "accordion-header" }
const _hoisted_11 = {
  class: "accordion-button collapsed",
  type: "button",
  "data-bs-toggle": "collapse",
  "data-bs-target": "#collapseOne",
  "aria-expanded": "true",
  "aria-controls": "collapseOne"
}
const _hoisted_12 = {
  id: "collapseOne",
  class: "accordion-collapse collapse",
  "data-bs-parent": "#accordion-new-locale"
}
const _hoisted_13 = { class: "accordion-body" }
const _hoisted_14 = { class: "row" }
const _hoisted_15 = { class: "col-6" }
const _hoisted_16 = { class: "mb-3" }
const _hoisted_17 = {
  for: "new-locale-backend-prefix",
  class: "form-label"
}
const _hoisted_18 = /*#__PURE__*/(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("br", null, null, -1 /* HOISTED */)
const _hoisted_19 = {
  key: 0,
  class: "invalid-feedback"
}
const _hoisted_20 = { class: "mb-3" }
const _hoisted_21 = {
  for: "new-locale-domain-development",
  class: "form-label"
}
const _hoisted_22 = {
  key: 0,
  class: "invalid-feedback"
}
const _hoisted_23 = { class: "form-text mt-0 help-text" }
const _hoisted_24 = { class: "col-6" }
const _hoisted_25 = { class: "mb-3" }
const _hoisted_26 = {
  for: "new-locale-locale-prefix",
  class: "form-label"
}
const _hoisted_27 = {
  key: 0,
  class: "invalid-feedback"
}
const _hoisted_28 = ["innerHTML"]
const _hoisted_29 = { class: "mb-3" }
const _hoisted_30 = {
  for: "new-locale-path-prefix",
  class: "form-label"
}
const _hoisted_31 = {
  key: 0,
  class: "invalid-feedback"
}
const _hoisted_32 = ["innerHTML"]
const _hoisted_33 = { class: "mb-3" }
const _hoisted_34 = {
  for: "new-locale-ssl-mode",
  class: "form-label"
}
const _hoisted_35 = {
  class: "btn btn-outline-success",
  for: "new-locale-ssl-force-ssl"
}
const _hoisted_36 = {
  class: "btn btn-outline-secondary",
  for: "new-locale-ssl-non-ssl"
}
const _hoisted_37 = {
  class: "btn btn-outline-secondary",
  for: "new-locale-ssl-both"
}
const _hoisted_38 = {
  key: 0,
  class: "invalid-feedback"
}
const _hoisted_39 = { class: "form-text m-0 help-text" }
const _hoisted_40 = /*#__PURE__*/(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("i", { class: "btn-icon fa fa-times" }, null, -1 /* HOISTED */)
const _hoisted_41 = /*#__PURE__*/(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("i", { class: "btn-icon fa fa-save" }, null, -1 /* HOISTED */)

function render(_ctx, _cache, $props, $setup, $data, $options) {
  return ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)("div", null, [
    (0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)($setup["Modal"], {
      title: $props.translations.createWebsite,
      ref: "modalInstance",
      modificators: "modal-lg modal-dialog-centered"
    }, {
      body: (0,vue__WEBPACK_IMPORTED_MODULE_0__.withCtx)(() => [
        (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_1, [
          (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_2, [
            (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("fieldset", _hoisted_3, [
              (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("label", _hoisted_4, (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)($props.translations.locale), 1 /* TEXT */),
              (0,vue__WEBPACK_IMPORTED_MODULE_0__.withDirectives)((0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("select", {
                id: "new-locale-code",
                class: (0,vue__WEBPACK_IMPORTED_MODULE_0__.normalizeClass)({ 'form-select': 1, 'is-invalid': $setup.hasError('code') }),
                "onUpdate:modelValue": _cache[0] || (_cache[0] = $event => (($setup.form.values.code) = $event)),
                onChange: $setup.updateLocalePrefix
              }, [
                _hoisted_5,
                ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(true), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)(vue__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, (0,vue__WEBPACK_IMPORTED_MODULE_0__.renderList)($setup.availableLocales.list, (locale) => {
                  return ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)("option", {
                    value: locale.code
                  }, (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)(locale.name) + " [" + (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)(locale.code) + "]", 9 /* TEXT, PROPS */, _hoisted_6))
                }), 256 /* UNKEYED_FRAGMENT */))
              ], 34 /* CLASS, HYDRATE_EVENTS */), [
                [vue__WEBPACK_IMPORTED_MODULE_0__.vModelSelect, $setup.form.values.code]
              ]),
              ($setup.form.errors.code)
                ? ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)("div", _hoisted_7, (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)($setup.form.errors.code), 1 /* TEXT */))
                : (0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)("v-if", true)
            ])
          ])
        ]),
        (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_8, [
          (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_9, [
            (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("h2", _hoisted_10, [
              (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("button", _hoisted_11, (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)($props.translations.advancedOptions), 1 /* TEXT */)
            ]),
            (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_12, [
              (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_13, [
                (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_14, [
                  (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_15, [
                    (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("fieldset", _hoisted_16, [
                      (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("label", _hoisted_17, (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)($props.translations.domain), 1 /* TEXT */),
                      _hoisted_18,
                      (0,vue__WEBPACK_IMPORTED_MODULE_0__.withDirectives)((0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("input", {
                        type: "text",
                        id: "new-locale-backend-prefix",
                        class: (0,vue__WEBPACK_IMPORTED_MODULE_0__.normalizeClass)({ 'form-control mb-1': 1, 'is-invalid': $setup.hasError('domain') }),
                        "onUpdate:modelValue": _cache[1] || (_cache[1] = $event => (($setup.form.values.domain) = $event))
                      }, null, 2 /* CLASS */), [
                        [vue__WEBPACK_IMPORTED_MODULE_0__.vModelText, $setup.form.values.domain]
                      ]),
                      ($setup.form.errors.domain)
                        ? ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)("div", _hoisted_19, (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)($setup.form.errors.domain), 1 /* TEXT */))
                        : (0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)("v-if", true)
                    ]),
                    (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("fieldset", _hoisted_20, [
                      (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("label", _hoisted_21, (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)($props.translations.domainDevelopment), 1 /* TEXT */),
                      (0,vue__WEBPACK_IMPORTED_MODULE_0__.withDirectives)((0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("input", {
                        type: "text",
                        id: "new-locale-domain-development",
                        class: (0,vue__WEBPACK_IMPORTED_MODULE_0__.normalizeClass)({ 'form-control mb-1': 1, 'is-invalid': $setup.hasError('domainDevelopment') }),
                        "onUpdate:modelValue": _cache[2] || (_cache[2] = $event => (($setup.form.values.domainDevelopment) = $event))
                      }, null, 2 /* CLASS */), [
                        [vue__WEBPACK_IMPORTED_MODULE_0__.vModelText, $setup.form.values.domainDevelopment]
                      ]),
                      ($setup.form.errors.domainDevelopment)
                        ? ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)("div", _hoisted_22, (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)($setup.form.errors.domainDevelopment), 1 /* TEXT */))
                        : (0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)("v-if", true),
                      (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_23, (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)($props.translations.domainDevelopmentHelp), 1 /* TEXT */)
                    ])
                  ]),
                  (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_24, [
                    (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("fieldset", _hoisted_25, [
                      (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("label", _hoisted_26, (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)($props.translations.localePrefix), 1 /* TEXT */),
                      (0,vue__WEBPACK_IMPORTED_MODULE_0__.withDirectives)((0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("input", {
                        type: "text",
                        id: "new-locale-locale-prefix",
                        class: (0,vue__WEBPACK_IMPORTED_MODULE_0__.normalizeClass)({ 'form-control mb-1': 1, 'is-invalid': $setup.hasError('localePrefix') }),
                        "onUpdate:modelValue": _cache[3] || (_cache[3] = $event => (($setup.form.values.localePrefix) = $event))
                      }, null, 2 /* CLASS */), [
                        [vue__WEBPACK_IMPORTED_MODULE_0__.vModelText, $setup.form.values.localePrefix]
                      ]),
                      ($setup.form.errors.localePrefix)
                        ? ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)("div", _hoisted_27, (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)($setup.form.errors.localePrefix), 1 /* TEXT */))
                        : (0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)("v-if", true),
                      (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", {
                        class: "form-text mt-0 help-text",
                        innerHTML: $props.translations.localePrefixHelp
                      }, null, 8 /* PROPS */, _hoisted_28)
                    ]),
                    (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("fieldset", _hoisted_29, [
                      (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("label", _hoisted_30, (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)($props.translations.pathPrefix), 1 /* TEXT */),
                      (0,vue__WEBPACK_IMPORTED_MODULE_0__.withDirectives)((0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("input", {
                        type: "text",
                        id: "new-locale-path-prefix",
                        class: (0,vue__WEBPACK_IMPORTED_MODULE_0__.normalizeClass)({ 'form-control mb-1': 1, 'is-invalid': $setup.hasError('pathPrefix') }),
                        "onUpdate:modelValue": _cache[4] || (_cache[4] = $event => (($setup.form.values.pathPrefix) = $event))
                      }, null, 2 /* CLASS */), [
                        [vue__WEBPACK_IMPORTED_MODULE_0__.vModelText, $setup.form.values.pathPrefix]
                      ]),
                      ($setup.form.errors.pathPrefix)
                        ? ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)("div", _hoisted_31, (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)($setup.form.errors.pathPrefix), 1 /* TEXT */))
                        : (0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)("v-if", true),
                      (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", {
                        class: "form-text mt-0 help-text",
                        innerHTML: $props.translations.pathPrefixHelp
                      }, null, 8 /* PROPS */, _hoisted_32)
                    ]),
                    (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("fieldset", _hoisted_33, [
                      (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("label", _hoisted_34, (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)($props.translations.sslMode), 1 /* TEXT */),
                      (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", {
                        class: (0,vue__WEBPACK_IMPORTED_MODULE_0__.normalizeClass)({ 'btn-group mb-1': 1, 'is-invalid': $setup.hasError('sslMode') }),
                        role: "group"
                      }, [
                        (0,vue__WEBPACK_IMPORTED_MODULE_0__.withDirectives)((0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("input", {
                          type: "radio",
                          class: "btn-check",
                          name: "new-locale-ssl-mode",
                          id: "new-locale-ssl-force-ssl",
                          "onUpdate:modelValue": _cache[5] || (_cache[5] = $event => (($setup.form.values.sslMode) = $event)),
                          value: "FORCE_SSL",
                          autocomplete: "off",
                          checked: ""
                        }, null, 512 /* NEED_PATCH */), [
                          [vue__WEBPACK_IMPORTED_MODULE_0__.vModelRadio, $setup.form.values.sslMode]
                        ]),
                        (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("label", _hoisted_35, (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)($props.translations.forceSSL), 1 /* TEXT */),
                        (0,vue__WEBPACK_IMPORTED_MODULE_0__.withDirectives)((0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("input", {
                          type: "radio",
                          class: "btn-check",
                          name: "new-locale-ssl-mode",
                          id: "new-locale-ssl-non-ssl",
                          "onUpdate:modelValue": _cache[6] || (_cache[6] = $event => (($setup.form.values.sslMode) = $event)),
                          value: "FORCE_NON_SSL",
                          autocomplete: "off"
                        }, null, 512 /* NEED_PATCH */), [
                          [vue__WEBPACK_IMPORTED_MODULE_0__.vModelRadio, $setup.form.values.sslMode]
                        ]),
                        (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("label", _hoisted_36, (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)($props.translations.forceNonSSL), 1 /* TEXT */),
                        (0,vue__WEBPACK_IMPORTED_MODULE_0__.withDirectives)((0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("input", {
                          type: "radio",
                          class: "btn-check",
                          name: "new-locale-ssl-mode",
                          id: "new-locale-ssl-both",
                          "onUpdate:modelValue": _cache[7] || (_cache[7] = $event => (($setup.form.values.sslMode) = $event)),
                          value: "ALLOWED_BOTH",
                          autocomplete: "off"
                        }, null, 512 /* NEED_PATCH */), [
                          [vue__WEBPACK_IMPORTED_MODULE_0__.vModelRadio, $setup.form.values.sslMode]
                        ]),
                        (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("label", _hoisted_37, (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)($props.translations.allowedBothSSL), 1 /* TEXT */)
                      ], 2 /* CLASS */),
                      ($setup.form.errors.sslMode)
                        ? ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)("div", _hoisted_38, (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)($setup.form.errors.sslMode), 1 /* TEXT */))
                        : (0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)("v-if", true),
                      (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_39, (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)($props.translations.sslModeHelp), 1 /* TEXT */)
                    ])
                  ])
                ])
              ])
            ])
          ])
        ])
      ]),
      footer: (0,vue__WEBPACK_IMPORTED_MODULE_0__.withCtx)(() => [
        (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("button", {
          class: "btn btn-secondary btn-icon-left",
          onClick: $setup.hide
        }, [
          _hoisted_40,
          (0,vue__WEBPACK_IMPORTED_MODULE_0__.createTextVNode)(" " + (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)($props.translations.cancel), 1 /* TEXT */)
        ]),
        (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("button", {
          class: "btn btn-success btn-icon-left",
          onClick: $setup.submit
        }, [
          _hoisted_41,
          (0,vue__WEBPACK_IMPORTED_MODULE_0__.createTextVNode)(" " + (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)($props.translations.create), 1 /* TEXT */)
        ])
      ]),
      _: 1 /* STABLE */
    }, 8 /* PROPS */, ["title"])
  ]))
}

/***/ }),

/***/ "./node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[1]!./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[4].use[0]!./src/js/components/modals/NewWebsite.vue?vue&type=template&id=f0cc8f70":
/*!*************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[1]!./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[4].use[0]!./src/js/components/modals/NewWebsite.vue?vue&type=template&id=f0cc8f70 ***!
  \*************************************************************************************************************************************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "render": () => (/* binding */ render)
/* harmony export */ });
/* harmony import */ var vue__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! vue */ "vue");
/* harmony import */ var vue__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(vue__WEBPACK_IMPORTED_MODULE_0__);


const _hoisted_1 = { class: "row" }
const _hoisted_2 = { class: "col-6" }
const _hoisted_3 = { class: "mb-3" }
const _hoisted_4 = {
  for: "new-website-name",
  class: "form-label"
}
const _hoisted_5 = {
  key: 0,
  class: "invalid-feedback"
}
const _hoisted_6 = { class: "mb-3" }
const _hoisted_7 = {
  for: "new-website-domain",
  class: "form-label"
}
const _hoisted_8 = {
  key: 0,
  class: "invalid-feedback"
}
const _hoisted_9 = { class: "form-text mt-0 help-text" }
const _hoisted_10 = { class: "col-6" }
const _hoisted_11 = { class: "mb-3" }
const _hoisted_12 = {
  for: "new-website-locale",
  class: "form-label"
}
const _hoisted_13 = /*#__PURE__*/(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("br", null, null, -1 /* HOISTED */)
const _hoisted_14 = {
  class: "btn-group mb-1",
  role: "group"
}
const _hoisted_15 = {
  class: "btn btn-outline-success",
  for: "new-website-active"
}
const _hoisted_16 = {
  class: "btn btn-outline-secondary",
  for: "new-website-inactive"
}
const _hoisted_17 = {
  key: 0,
  class: "invalid-feedback"
}
const _hoisted_18 = {
  key: 1,
  class: "form-text mt-0 help-text"
}
const _hoisted_19 = { class: "mb-3" }
const _hoisted_20 = {
  for: "new-website-locale",
  class: "form-label"
}
const _hoisted_21 = /*#__PURE__*/(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("option", { value: "" }, "---", -1 /* HOISTED */)
const _hoisted_22 = ["value"]
const _hoisted_23 = {
  key: 0,
  class: "invalid-feedback"
}
const _hoisted_24 = { class: "form-text m-0 help-text bg-warning py-2 px-3 text-dark" }
const _hoisted_25 = {
  class: "accordion",
  id: "accordion-new-website"
}
const _hoisted_26 = { class: "accordion-item" }
const _hoisted_27 = { class: "accordion-header" }
const _hoisted_28 = {
  class: "accordion-button collapsed",
  type: "button",
  "data-bs-toggle": "collapse",
  "data-bs-target": "#collapseOne",
  "aria-expanded": "true",
  "aria-controls": "collapseOne"
}
const _hoisted_29 = {
  id: "collapseOne",
  class: "accordion-collapse collapse",
  "data-bs-parent": "#accordion-new-website"
}
const _hoisted_30 = { class: "accordion-body" }
const _hoisted_31 = { class: "row" }
const _hoisted_32 = { class: "col-6" }
const _hoisted_33 = { class: "mb-3" }
const _hoisted_34 = {
  for: "new-website-path-prefix",
  class: "form-label"
}
const _hoisted_35 = {
  key: 0,
  class: "invalid-feedback"
}
const _hoisted_36 = ["innerHTML"]
const _hoisted_37 = { class: "mb-3" }
const _hoisted_38 = {
  for: "new-website-domain-development",
  class: "form-label"
}
const _hoisted_39 = {
  key: 0,
  class: "invalid-feedback"
}
const _hoisted_40 = { class: "form-text mt-0 help-text" }
const _hoisted_41 = { class: "col-6" }
const _hoisted_42 = { class: "mb-3" }
const _hoisted_43 = {
  for: "new-website-backend-prefix",
  class: "form-label"
}
const _hoisted_44 = /*#__PURE__*/(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("br", null, null, -1 /* HOISTED */)
const _hoisted_45 = {
  key: 0,
  class: "invalid-feedback"
}
const _hoisted_46 = { class: "form-text mt-0 help-text" }
const _hoisted_47 = { class: "mb-3" }
const _hoisted_48 = {
  for: "new-website-ssl-mode",
  class: "form-label"
}
const _hoisted_49 = {
  class: "btn btn-outline-success",
  for: "new-website-ssl-force-ssl"
}
const _hoisted_50 = {
  class: "btn btn-outline-secondary",
  for: "new-website-ssl-non-ssl"
}
const _hoisted_51 = {
  class: "btn btn-outline-secondary",
  for: "new-website-ssl-both"
}
const _hoisted_52 = {
  key: 0,
  class: "invalid-feedback"
}
const _hoisted_53 = { class: "form-text m-0 help-text" }
const _hoisted_54 = /*#__PURE__*/(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("i", { class: "btn-icon fa fa-times" }, null, -1 /* HOISTED */)
const _hoisted_55 = /*#__PURE__*/(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("i", { class: "btn-icon fa fa-save" }, null, -1 /* HOISTED */)

function render(_ctx, _cache, $props, $setup, $data, $options) {
  return ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)("div", null, [
    (0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)($setup["Modal"], {
      title: $props.translations.createWebsite,
      ref: "modalInstance",
      modificators: "modal-lg modal-dialog-centered"
    }, {
      body: (0,vue__WEBPACK_IMPORTED_MODULE_0__.withCtx)(() => [
        (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_1, [
          (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_2, [
            (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("fieldset", _hoisted_3, [
              (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("label", _hoisted_4, (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)($props.translations.name), 1 /* TEXT */),
              (0,vue__WEBPACK_IMPORTED_MODULE_0__.withDirectives)((0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("input", {
                type: "text",
                id: "new-website-name",
                class: (0,vue__WEBPACK_IMPORTED_MODULE_0__.normalizeClass)({ 'form-control': 1, 'is-invalid': $setup.hasError('name') }),
                ref: "newWebsiteName",
                "onUpdate:modelValue": _cache[0] || (_cache[0] = $event => (($setup.form.values.name) = $event))
              }, null, 2 /* CLASS */), [
                [vue__WEBPACK_IMPORTED_MODULE_0__.vModelText, $setup.form.values.name]
              ]),
              ($setup.form.errors.name)
                ? ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)("div", _hoisted_5, (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)($setup.form.errors.name), 1 /* TEXT */))
                : (0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)("v-if", true)
            ]),
            (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("fieldset", _hoisted_6, [
              (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("label", _hoisted_7, (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)($props.translations.domain), 1 /* TEXT */),
              (0,vue__WEBPACK_IMPORTED_MODULE_0__.withDirectives)((0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("input", {
                type: "text",
                id: "new-website-domain",
                class: (0,vue__WEBPACK_IMPORTED_MODULE_0__.normalizeClass)({ 'form-control mb-1': 1, 'is-invalid': $setup.hasError('domain') }),
                "onUpdate:modelValue": _cache[1] || (_cache[1] = $event => (($setup.form.values.domain) = $event))
              }, null, 2 /* CLASS */), [
                [vue__WEBPACK_IMPORTED_MODULE_0__.vModelText, $setup.form.values.domain]
              ]),
              ($setup.form.errors.domain)
                ? ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)("div", _hoisted_8, (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)($setup.form.errors.domain), 1 /* TEXT */))
                : (0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)("v-if", true),
              (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_9, (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)($props.translations.domainHelp), 1 /* TEXT */)
            ])
          ]),
          (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_10, [
            (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("fieldset", _hoisted_11, [
              (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("label", _hoisted_12, (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)($props.translations.activity), 1 /* TEXT */),
              _hoisted_13,
              (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_14, [
                (0,vue__WEBPACK_IMPORTED_MODULE_0__.withDirectives)((0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("input", {
                  type: "radio",
                  class: "btn-check",
                  name: "new-website-activeness",
                  id: "new-website-active",
                  "onUpdate:modelValue": _cache[2] || (_cache[2] = $event => (($setup.form.values.activity) = $event)),
                  value: "1",
                  autocomplete: "off",
                  checked: ""
                }, null, 512 /* NEED_PATCH */), [
                  [vue__WEBPACK_IMPORTED_MODULE_0__.vModelRadio, $setup.form.values.activity]
                ]),
                (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("label", _hoisted_15, (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)($props.translations.active), 1 /* TEXT */),
                (0,vue__WEBPACK_IMPORTED_MODULE_0__.withDirectives)((0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("input", {
                  type: "radio",
                  class: "btn-check",
                  name: "new-website-activeness",
                  id: "new-website-inactive",
                  "onUpdate:modelValue": _cache[3] || (_cache[3] = $event => (($setup.form.values.activity) = $event)),
                  value: "0",
                  autocomplete: "off"
                }, null, 512 /* NEED_PATCH */), [
                  [vue__WEBPACK_IMPORTED_MODULE_0__.vModelRadio, $setup.form.values.activity]
                ]),
                (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("label", _hoisted_16, (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)($props.translations.inactive), 1 /* TEXT */)
              ]),
              ($setup.form.errors.activity)
                ? ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)("div", _hoisted_17, (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)($setup.form.errors.activity), 1 /* TEXT */))
                : (0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)("v-if", true),
              ($setup.form.values.activity === '0')
                ? ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)("div", _hoisted_18, (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)($props.translations.websiteInactiveHint), 1 /* TEXT */))
                : (0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)("v-if", true)
            ]),
            (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("fieldset", _hoisted_19, [
              (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("label", _hoisted_20, (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)($props.translations.defaultLocale), 1 /* TEXT */),
              (0,vue__WEBPACK_IMPORTED_MODULE_0__.withDirectives)((0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("select", {
                id: "new-website-locale",
                class: (0,vue__WEBPACK_IMPORTED_MODULE_0__.normalizeClass)({ 'form-select': 1, 'is-invalid': $setup.hasError('locale') }),
                "onUpdate:modelValue": _cache[4] || (_cache[4] = $event => (($setup.form.values.locale) = $event))
              }, [
                _hoisted_21,
                ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(true), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)(vue__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, (0,vue__WEBPACK_IMPORTED_MODULE_0__.renderList)($props.locales, (locale) => {
                  return ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)("option", {
                    value: locale.code
                  }, (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)(locale.name) + " [" + (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)(locale.code) + "]", 9 /* TEXT, PROPS */, _hoisted_22))
                }), 256 /* UNKEYED_FRAGMENT */))
              ], 2 /* CLASS */), [
                [vue__WEBPACK_IMPORTED_MODULE_0__.vModelSelect, $setup.form.values.locale]
              ]),
              ($setup.form.errors.locale)
                ? ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)("div", _hoisted_23, (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)($setup.form.errors.locale), 1 /* TEXT */))
                : (0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)("v-if", true),
              (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_24, (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)($props.translations.defaultLocaleCannotBeChangedAfterCreating), 1 /* TEXT */)
            ])
          ])
        ]),
        (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_25, [
          (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_26, [
            (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("h2", _hoisted_27, [
              (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("button", _hoisted_28, (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)($props.translations.advancedOptions), 1 /* TEXT */)
            ]),
            (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_29, [
              (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_30, [
                (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_31, [
                  (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_32, [
                    (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("fieldset", _hoisted_33, [
                      (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("label", _hoisted_34, (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)($props.translations.pathPrefix), 1 /* TEXT */),
                      (0,vue__WEBPACK_IMPORTED_MODULE_0__.withDirectives)((0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("input", {
                        type: "text",
                        id: "new-website-path-prefix",
                        class: (0,vue__WEBPACK_IMPORTED_MODULE_0__.normalizeClass)({ 'form-control mb-1': 1, 'is-invalid': $setup.hasError('pathPrefix') }),
                        "onUpdate:modelValue": _cache[5] || (_cache[5] = $event => (($setup.form.values.pathPrefix) = $event))
                      }, null, 2 /* CLASS */), [
                        [vue__WEBPACK_IMPORTED_MODULE_0__.vModelText, $setup.form.values.pathPrefix]
                      ]),
                      ($setup.form.errors.pathPrefix)
                        ? ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)("div", _hoisted_35, (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)($setup.form.errors.pathPrefix), 1 /* TEXT */))
                        : (0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)("v-if", true),
                      (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", {
                        class: "form-text mt-0 help-text",
                        innerHTML: $props.translations.pathPrefixHelp
                      }, null, 8 /* PROPS */, _hoisted_36)
                    ]),
                    (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("fieldset", _hoisted_37, [
                      (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("label", _hoisted_38, (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)($props.translations.domainDevelopment), 1 /* TEXT */),
                      (0,vue__WEBPACK_IMPORTED_MODULE_0__.withDirectives)((0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("input", {
                        type: "text",
                        id: "new-website-domain-development",
                        class: (0,vue__WEBPACK_IMPORTED_MODULE_0__.normalizeClass)({ 'form-control mb-1': 1, 'is-invalid': $setup.hasError('domainDevelopment') }),
                        "onUpdate:modelValue": _cache[6] || (_cache[6] = $event => (($setup.form.values.domainDevelopment) = $event))
                      }, null, 2 /* CLASS */), [
                        [vue__WEBPACK_IMPORTED_MODULE_0__.vModelText, $setup.form.values.domainDevelopment]
                      ]),
                      ($setup.form.errors.domainDevelopment)
                        ? ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)("div", _hoisted_39, (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)($setup.form.errors.domainDevelopment), 1 /* TEXT */))
                        : (0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)("v-if", true),
                      (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_40, (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)($props.translations.domainDevelopmentHelp), 1 /* TEXT */)
                    ])
                  ]),
                  (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_41, [
                    (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("fieldset", _hoisted_42, [
                      (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("label", _hoisted_43, (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)($props.translations.backendPrefix), 1 /* TEXT */),
                      _hoisted_44,
                      (0,vue__WEBPACK_IMPORTED_MODULE_0__.withDirectives)((0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("input", {
                        type: "text",
                        id: "new-website-backend-prefix",
                        class: (0,vue__WEBPACK_IMPORTED_MODULE_0__.normalizeClass)({ 'form-control mb-1': 1, 'is-invalid': $setup.hasError('backendPrefix') }),
                        "onUpdate:modelValue": _cache[7] || (_cache[7] = $event => (($setup.form.values.backendPrefix) = $event))
                      }, null, 2 /* CLASS */), [
                        [vue__WEBPACK_IMPORTED_MODULE_0__.vModelText, $setup.form.values.backendPrefix]
                      ]),
                      ($setup.form.errors.backendPrefix)
                        ? ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)("div", _hoisted_45, (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)($setup.form.errors.backendPrefix), 1 /* TEXT */))
                        : (0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)("v-if", true),
                      (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_46, (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)($props.translations.backendPrefixHelp), 1 /* TEXT */)
                    ]),
                    (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("fieldset", _hoisted_47, [
                      (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("label", _hoisted_48, (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)($props.translations.sslMode), 1 /* TEXT */),
                      (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", {
                        class: (0,vue__WEBPACK_IMPORTED_MODULE_0__.normalizeClass)({ 'btn-group mb-1': 1, 'is-invalid': $setup.hasError('sslMode') }),
                        role: "group"
                      }, [
                        (0,vue__WEBPACK_IMPORTED_MODULE_0__.withDirectives)((0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("input", {
                          type: "radio",
                          class: "btn-check",
                          name: "new-website-ssl-mode",
                          id: "new-website-ssl-force-ssl",
                          "onUpdate:modelValue": _cache[8] || (_cache[8] = $event => (($setup.form.values.sslMode) = $event)),
                          value: "FORCE_SSL",
                          autocomplete: "off",
                          checked: ""
                        }, null, 512 /* NEED_PATCH */), [
                          [vue__WEBPACK_IMPORTED_MODULE_0__.vModelRadio, $setup.form.values.sslMode]
                        ]),
                        (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("label", _hoisted_49, (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)($props.translations.forceSSL), 1 /* TEXT */),
                        (0,vue__WEBPACK_IMPORTED_MODULE_0__.withDirectives)((0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("input", {
                          type: "radio",
                          class: "btn-check",
                          name: "new-website-ssl-mode",
                          id: "new-website-ssl-non-ssl",
                          "onUpdate:modelValue": _cache[9] || (_cache[9] = $event => (($setup.form.values.sslMode) = $event)),
                          value: "FORCE_NON_SSL",
                          autocomplete: "off"
                        }, null, 512 /* NEED_PATCH */), [
                          [vue__WEBPACK_IMPORTED_MODULE_0__.vModelRadio, $setup.form.values.sslMode]
                        ]),
                        (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("label", _hoisted_50, (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)($props.translations.forceNonSSL), 1 /* TEXT */),
                        (0,vue__WEBPACK_IMPORTED_MODULE_0__.withDirectives)((0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("input", {
                          type: "radio",
                          class: "btn-check",
                          name: "new-website-ssl-mode",
                          id: "new-website-ssl-both",
                          "onUpdate:modelValue": _cache[10] || (_cache[10] = $event => (($setup.form.values.sslMode) = $event)),
                          value: "ALLOWED_BOTH",
                          autocomplete: "off"
                        }, null, 512 /* NEED_PATCH */), [
                          [vue__WEBPACK_IMPORTED_MODULE_0__.vModelRadio, $setup.form.values.sslMode]
                        ]),
                        (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("label", _hoisted_51, (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)($props.translations.allowedBothSSL), 1 /* TEXT */)
                      ], 2 /* CLASS */),
                      ($setup.form.errors.sslMode)
                        ? ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)("div", _hoisted_52, (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)($setup.form.errors.sslMode), 1 /* TEXT */))
                        : (0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)("v-if", true),
                      (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_53, (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)($props.translations.sslModeHelp), 1 /* TEXT */)
                    ])
                  ])
                ])
              ])
            ])
          ])
        ])
      ]),
      footer: (0,vue__WEBPACK_IMPORTED_MODULE_0__.withCtx)(() => [
        (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("button", {
          class: "btn btn-secondary btn-icon-left",
          onClick: $setup.hide
        }, [
          _hoisted_54,
          (0,vue__WEBPACK_IMPORTED_MODULE_0__.createTextVNode)(" " + (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)($props.translations.cancel), 1 /* TEXT */)
        ]),
        (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("button", {
          class: "btn btn-success btn-icon-left",
          onClick: $setup.submit
        }, [
          _hoisted_55,
          (0,vue__WEBPACK_IMPORTED_MODULE_0__.createTextVNode)(" " + (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)($props.translations.create), 1 /* TEXT */)
        ])
      ]),
      _: 1 /* STABLE */
    }, 8 /* PROPS */, ["title"])
  ]))
}

/***/ }),

/***/ "./src/js/shared/FetchAPI.js":
/*!***********************************!*\
  !*** ./src/js/shared/FetchAPI.js ***!
  \***********************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = ({
    post: async function (endpoint, body = {}) {
        body._token = endpoint.csrfToken;

        const response = await fetch(endpoint.url, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(body)
        });

        return response.json();
    }
});


/***/ }),

/***/ "Tulia":
/*!************************!*\
  !*** external "Tulia" ***!
  \************************/
/***/ ((module) => {

module.exports = Tulia;

/***/ }),

/***/ "vue":
/*!**********************!*\
  !*** external "Vue" ***!
  \**********************/
/***/ ((module) => {

module.exports = Vue;

/***/ }),

/***/ "bootstrap":
/*!****************************!*\
  !*** external "bootstrap" ***!
  \****************************/
/***/ ((module) => {

module.exports = bootstrap;

/***/ })

/******/ 	});
/************************************************************************/
/******/ 	// The module cache
/******/ 	var __webpack_module_cache__ = {};
/******/ 	
/******/ 	// The require function
/******/ 	function __webpack_require__(moduleId) {
/******/ 		// Check if module is in cache
/******/ 		var cachedModule = __webpack_module_cache__[moduleId];
/******/ 		if (cachedModule !== undefined) {
/******/ 			return cachedModule.exports;
/******/ 		}
/******/ 		// Create a new module (and put it into the cache)
/******/ 		var module = __webpack_module_cache__[moduleId] = {
/******/ 			// no module.id needed
/******/ 			// no module.loaded needed
/******/ 			exports: {}
/******/ 		};
/******/ 	
/******/ 		// Execute the module function
/******/ 		__webpack_modules__[moduleId](module, module.exports, __webpack_require__);
/******/ 	
/******/ 		// Return the exports of the module
/******/ 		return module.exports;
/******/ 	}
/******/ 	
/************************************************************************/
/******/ 	/* webpack/runtime/compat get default export */
/******/ 	(() => {
/******/ 		// getDefaultExport function for compatibility with non-harmony modules
/******/ 		__webpack_require__.n = (module) => {
/******/ 			var getter = module && module.__esModule ?
/******/ 				() => (module['default']) :
/******/ 				() => (module);
/******/ 			__webpack_require__.d(getter, { a: getter });
/******/ 			return getter;
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/define property getters */
/******/ 	(() => {
/******/ 		// define getter functions for harmony exports
/******/ 		__webpack_require__.d = (exports, definition) => {
/******/ 			for(var key in definition) {
/******/ 				if(__webpack_require__.o(definition, key) && !__webpack_require__.o(exports, key)) {
/******/ 					Object.defineProperty(exports, key, { enumerable: true, get: definition[key] });
/******/ 				}
/******/ 			}
/******/ 		};
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/hasOwnProperty shorthand */
/******/ 	(() => {
/******/ 		__webpack_require__.o = (obj, prop) => (Object.prototype.hasOwnProperty.call(obj, prop))
/******/ 	})();
/******/ 	
/******/ 	/* webpack/runtime/make namespace object */
/******/ 	(() => {
/******/ 		// define __esModule on exports
/******/ 		__webpack_require__.r = (exports) => {
/******/ 			if(typeof Symbol !== 'undefined' && Symbol.toStringTag) {
/******/ 				Object.defineProperty(exports, Symbol.toStringTag, { value: 'Module' });
/******/ 			}
/******/ 			Object.defineProperty(exports, '__esModule', { value: true });
/******/ 		};
/******/ 	})();
/******/ 	
/************************************************************************/
var __webpack_exports__ = {};
// This entry need to be wrapped in an IIFE because it need to be isolated against other modules in the chunk.
(() => {
/*!************************************!*\
  !*** ./src/js/websites-builder.js ***!
  \************************************/
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
const Root = (__webpack_require__(/*! ./components/Root.vue */ "./src/js/components/Root.vue")["default"]);
const Vue = __webpack_require__(/*! vue */ "vue");

/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (class {
    vue;

    constructor (selector, data) {
        this.vue = Vue.createApp(Root, data);
        this.vue.mount(selector);
    }
});

})();

TuliaWebsiteBuilder = __webpack_exports__["default"];
/******/ })()
;
//# sourceMappingURL=websites-builder.js.map