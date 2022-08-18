/******/ (() => { // webpackBootstrap
/******/ 	var __webpack_modules__ = ({

/***/ "./node_modules/vue-loader/dist/exportHelper.js":
/*!******************************************************!*\
  !*** ./node_modules/vue-loader/dist/exportHelper.js ***!
  \******************************************************/
/***/ ((__unused_webpack_module, exports) => {

"use strict";

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

/***/ "./src/js/App.vue":
/*!************************!*\
  !*** ./src/js/App.vue ***!
  \************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _App_vue_vue_type_template_id_3ea74058__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./App.vue?vue&type=template&id=3ea74058 */ "./src/js/App.vue?vue&type=template&id=3ea74058");
/* harmony import */ var _App_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./App.vue?vue&type=script&lang=js */ "./src/js/App.vue?vue&type=script&lang=js");
/* harmony import */ var _home_adam_projects_tuliacms_development_tuliacms_core_src_Cms_ContactForm_Infrastructure_Framework_Resources_public_node_modules_vue_loader_dist_exportHelper_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./node_modules/vue-loader/dist/exportHelper.js */ "./node_modules/vue-loader/dist/exportHelper.js");




;
const __exports__ = /*#__PURE__*/(0,_home_adam_projects_tuliacms_development_tuliacms_core_src_Cms_ContactForm_Infrastructure_Framework_Resources_public_node_modules_vue_loader_dist_exportHelper_js__WEBPACK_IMPORTED_MODULE_2__["default"])(_App_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_1__["default"], [['render',_App_vue_vue_type_template_id_3ea74058__WEBPACK_IMPORTED_MODULE_0__.render],['__file',"src/js/App.vue"]])
/* hot reload */
if (false) {}


/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (__exports__);

/***/ }),

/***/ "./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[2].use[0]!./src/js/App.vue?vue&type=script&lang=js":
/*!********************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[2].use[0]!./src/js/App.vue?vue&type=script&lang=js ***!
  \********************************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });

/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = ({
    name: "App",
    data() {
        let availableFields = window.ContactFormBuilder.availableFields;

        for (let t in availableFields) {
            for (let o in availableFields[t].options) {
                availableFields[t].options[o].focused = false;
            }
        }

        return {
            fields: window.ContactFormBuilder.fields,
            availableFields: availableFields,
            translations: window.ContactFormBuilder.translations,
            fieldsTemplate: window.ContactFormBuilder.fieldsTemplate,
        }
    },
    methods: {
        addInput: function (alias) {
            let field = {
                alias: alias,
                options: {}
            };

            for (let i in this.availableFields[alias].options) {
                field.options[i] = {
                    name: i,
                    value: '',
                    error: null,
                };
            }

            this.fields.push(field);
        },
        removeField: function (key) {
            this.fields.splice(key, 1);
        },
        resizeInput: function (event) {
            event.target.style.width = ((event.target.value.length + 0.2) * 8) + 'px';
        },
        showLegend: function (alias, option) {
            this.availableFields[alias].options[option].focused = true;
        },
        hideLegend: function (alias, option) {
            this.availableFields[alias].options[option].focused = false;
        },
        addFieldToTemplate: function (key) {
            let textarea = $('#form_form_template');
            let cursorPos = textarea.prop('selectionStart');
            let v = textarea.val();
            let textBefore = v.substring(0,  cursorPos );
            let textAfter  = v.substring( cursorPos, v.length );
            textarea.val(textBefore + '[' + this.fields[key].options.name.value + ']' + textAfter);
        }
    },
    mounted: function () {
        // Set default width form existing fields.
        for (let input of this.$el.querySelectorAll('input.form-control')) {
            this.resizeInput({
                target: input
            });
        }
    }
});


/***/ }),

/***/ "./src/js/App.vue?vue&type=script&lang=js":
/*!************************************************!*\
  !*** ./src/js/App.vue?vue&type=script&lang=js ***!
  \************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* reexport safe */ _node_modules_vue_loader_dist_index_js_ruleSet_1_rules_2_use_0_App_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_0__["default"])
/* harmony export */ });
/* harmony import */ var _node_modules_vue_loader_dist_index_js_ruleSet_1_rules_2_use_0_App_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../node_modules/vue-loader/dist/index.js??ruleSet[1].rules[2].use[0]!./App.vue?vue&type=script&lang=js */ "./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[2].use[0]!./src/js/App.vue?vue&type=script&lang=js");
 

/***/ }),

/***/ "./src/js/App.vue?vue&type=template&id=3ea74058":
/*!******************************************************!*\
  !*** ./src/js/App.vue?vue&type=template&id=3ea74058 ***!
  \******************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "render": () => (/* reexport safe */ _node_modules_vue_loader_dist_templateLoader_js_ruleSet_1_rules_1_node_modules_vue_loader_dist_index_js_ruleSet_1_rules_2_use_0_App_vue_vue_type_template_id_3ea74058__WEBPACK_IMPORTED_MODULE_0__.render)
/* harmony export */ });
/* harmony import */ var _node_modules_vue_loader_dist_templateLoader_js_ruleSet_1_rules_1_node_modules_vue_loader_dist_index_js_ruleSet_1_rules_2_use_0_App_vue_vue_type_template_id_3ea74058__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[1]!../../node_modules/vue-loader/dist/index.js??ruleSet[1].rules[2].use[0]!./App.vue?vue&type=template&id=3ea74058 */ "./node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[1]!./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[2].use[0]!./src/js/App.vue?vue&type=template&id=3ea74058");


/***/ }),

/***/ "./node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[1]!./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[2].use[0]!./src/js/App.vue?vue&type=template&id=3ea74058":
/*!************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[1]!./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[2].use[0]!./src/js/App.vue?vue&type=template&id=3ea74058 ***!
  \************************************************************************************************************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "render": () => (/* binding */ render)
/* harmony export */ });
/* harmony import */ var vue__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! vue */ "vue");
/* harmony import */ var vue__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(vue__WEBPACK_IMPORTED_MODULE_0__);


const _hoisted_1 = { class: "app" }
const _hoisted_2 = { class: "text-muted" }
const _hoisted_3 = ["onClick"]
const _hoisted_4 = /*#__PURE__*/(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("hr", null, null, -1 /* HOISTED */)
const _hoisted_5 = { class: "text-muted" }
const _hoisted_6 = { class: "contact-form-fields-builder" }
const _hoisted_7 = ["data-field-name"]
const _hoisted_8 = ["name", "onUpdate:modelValue"]
const _hoisted_9 = ["onClick", "title"]
const _hoisted_10 = ["title"]
const _hoisted_11 = /*#__PURE__*/(0,vue__WEBPACK_IMPORTED_MODULE_0__.createTextVNode)(" ")
const _hoisted_12 = ["data-option-name", "data-option-key", "name", "onUpdate:modelValue", "onFocus", "onBlur"]
const _hoisted_13 = /*#__PURE__*/(0,vue__WEBPACK_IMPORTED_MODULE_0__.createTextVNode)("\"")
const _hoisted_14 = /*#__PURE__*/(0,vue__WEBPACK_IMPORTED_MODULE_0__.createTextVNode)("] ")
const _hoisted_15 = ["onClick", "title"]
const _hoisted_16 = {
  key: 0,
  class: "card"
}
const _hoisted_17 = { class: "card-body" }
const _hoisted_18 = { class: "form-field-option-legends" }
const _hoisted_19 = ["data-option-legend-name"]
const _hoisted_20 = { class: "card" }
const _hoisted_21 = { class: "card-header" }
const _hoisted_22 = { class: "card-body" }
const _hoisted_23 = { style: {"font-size":"17px"} }
const _hoisted_24 = /*#__PURE__*/(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("br", null, null, -1 /* HOISTED */)
const _hoisted_25 = /*#__PURE__*/(0,vue__WEBPACK_IMPORTED_MODULE_0__.createTextVNode)()
const _hoisted_26 = /*#__PURE__*/(0,vue__WEBPACK_IMPORTED_MODULE_0__.createTextVNode)(" | ")
const _hoisted_27 = { key: 0 }
const _hoisted_28 = /*#__PURE__*/(0,vue__WEBPACK_IMPORTED_MODULE_0__.createTextVNode)()
const _hoisted_29 = /*#__PURE__*/(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("code", null, "yes", -1 /* HOISTED */)
const _hoisted_30 = /*#__PURE__*/(0,vue__WEBPACK_IMPORTED_MODULE_0__.createTextVNode)(" | ")
const _hoisted_31 = /*#__PURE__*/(0,vue__WEBPACK_IMPORTED_MODULE_0__.createTextVNode)()
const _hoisted_32 = { key: 1 }
const _hoisted_33 = /*#__PURE__*/(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("br", null, null, -1 /* HOISTED */)
const _hoisted_34 = /*#__PURE__*/(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("br", null, null, -1 /* HOISTED */)
const _hoisted_35 = { class: "pl-4" }
const _hoisted_36 = /*#__PURE__*/(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("br", null, null, -1 /* HOISTED */)
const _hoisted_37 = /*#__PURE__*/(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("hr", null, null, -1 /* HOISTED */)
const _hoisted_38 = { class: "text-muted" }
const _hoisted_39 = {
  key: 0,
  class: "card"
}
const _hoisted_40 = { class: "card-body" }
const _hoisted_41 = {
  key: 1,
  class: "form-group"
}
const _hoisted_42 = {
  key: 0,
  class: "invalid-feedback d-block"
}
const _hoisted_43 = { class: "d-block" }
const _hoisted_44 = /*#__PURE__*/(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("span", { class: "form-error-icon badge badge-danger text-uppercase" }, "Błąd", -1 /* HOISTED */)
const _hoisted_45 = { class: "form-error-message" }

function render(_ctx, _cache, $props, $setup, $data, $options) {
  return ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)("div", _hoisted_1, [
    (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("h3", null, (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)($data.translations.availableFields), 1 /* TEXT */),
    (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("p", _hoisted_2, (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)($data.translations.availableFieldsInfo), 1 /* TEXT */),
    ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(true), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)(vue__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, (0,vue__WEBPACK_IMPORTED_MODULE_0__.renderList)($data.availableFields, (item) => {
      return ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)("button", {
        type: "button",
        class: "btn btn-success mr-2",
        onClick: $event => ($options.addInput(item.alias))
      }, (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)(item.label), 9 /* TEXT, PROPS */, _hoisted_3))
    }), 256 /* UNKEYED_FRAGMENT */)),
    _hoisted_4,
    (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("h3", null, (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)($data.translations.fieldsBuilder), 1 /* TEXT */),
    (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("p", _hoisted_5, (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)($data.translations.fieldsBuilderInfo), 1 /* TEXT */),
    (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_6, [
      ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(true), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)(vue__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, (0,vue__WEBPACK_IMPORTED_MODULE_0__.renderList)($data.fields, (field, key) => {
        return ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)("div", {
          class: "form-field-prototype",
          "data-field-name": field.alias
        }, [
          (0,vue__WEBPACK_IMPORTED_MODULE_0__.withDirectives)((0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("input", {
            type: "hidden",
            name: 'form[fields][' + key + '][alias]',
            "onUpdate:modelValue": $event => ((field.alias) = $event),
            autocomplete: "off"
          }, null, 8 /* PROPS */, _hoisted_8), [
            [vue__WEBPACK_IMPORTED_MODULE_0__.vModelText, field.alias]
          ]),
          (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("span", {
            onClick: $event => ($options.removeField(key)),
            class: "field-remove fas fa-window-close",
            title: $data.translations.removeField,
            "data-toggle": "tooltip"
          }, null, 8 /* PROPS */, _hoisted_9),
          (0,vue__WEBPACK_IMPORTED_MODULE_0__.createTextVNode)(" [" + (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)(field.alias), 1 /* TEXT */),
          (0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)("\n                    "),
          ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(true), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)(vue__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, (0,vue__WEBPACK_IMPORTED_MODULE_0__.renderList)($data.availableFields[field.alias].options, (option, name) => {
            return ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)("span", {
              class: (0,vue__WEBPACK_IMPORTED_MODULE_0__.normalizeClass)({ 'text-danger': $data.fields[key]['options'][name].error !== null }),
              title: $data.fields[key]['options'][name].error,
              "data-toggle": "tooltip"
            }, [
              (0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)("\n                    "),
              _hoisted_11,
              (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("label", {
                class: (0,vue__WEBPACK_IMPORTED_MODULE_0__.normalizeClass)({ 'field-optional': !option.required })
              }, [
                (0,vue__WEBPACK_IMPORTED_MODULE_0__.createTextVNode)((0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)(name) + "=\"", 1 /* TEXT */),
                (0,vue__WEBPACK_IMPORTED_MODULE_0__.withDirectives)((0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("input", {
                  type: "text",
                  autocomplete: "off",
                  "data-option-name": name,
                  "data-option-key": key,
                  class: "form-control",
                  name: 'form[fields][' + key + '][' + name + ']',
                  "onUpdate:modelValue": $event => (($data.fields[key]['options'][name].value) = $event),
                  onChange: _cache[0] || (_cache[0] = (...args) => ($options.resizeInput && $options.resizeInput(...args))),
                  onInput: _cache[1] || (_cache[1] = (...args) => ($options.resizeInput && $options.resizeInput(...args))),
                  onFocus: $event => ($options.showLegend(field.alias, name)),
                  onBlur: $event => ($options.hideLegend(field.alias, name))
                }, null, 40 /* PROPS, HYDRATE_EVENTS */, _hoisted_12), [
                  [vue__WEBPACK_IMPORTED_MODULE_0__.vModelText, $data.fields[key]['options'][name].value]
                ]),
                _hoisted_13
              ], 2 /* CLASS */),
              (0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)("\n                ")
            ], 10 /* CLASS, PROPS */, _hoisted_10))
          }), 256 /* UNKEYED_FRAGMENT */)),
          _hoisted_14,
          (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("span", {
            onClick: $event => ($options.addFieldToTemplate(key)),
            class: "field-add-to-template fas fa-plus-square",
            title: $data.translations.addFieldToTemplate,
            "data-toggle": "tooltip"
          }, null, 8 /* PROPS */, _hoisted_15)
        ], 8 /* PROPS */, _hoisted_7))
      }), 256 /* UNKEYED_FRAGMENT */)),
      ($data.fields.length === 0)
        ? ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)("div", _hoisted_16, [
            (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_17, (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)($data.translations.addAnyFieldsToCreateForm), 1 /* TEXT */)
          ]))
        : (0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)("v-if", true)
    ]),
    (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_18, [
      ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(true), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)(vue__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, (0,vue__WEBPACK_IMPORTED_MODULE_0__.renderList)($data.availableFields, (field) => {
        return ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)("div", null, [
          ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(true), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)(vue__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, (0,vue__WEBPACK_IMPORTED_MODULE_0__.renderList)(field.options, (option, optionName) => {
            return ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)("div", null, [
              (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", {
                class: (0,vue__WEBPACK_IMPORTED_MODULE_0__.normalizeClass)(["form-field-option-legend", { 'd-block': option.focused }]),
                "data-option-legend-name": field.name + '_' + optionName
              }, [
                (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_20, [
                  (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_21, (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)($data.translations.controlOptionLabel), 1 /* TEXT */),
                  (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_22, [
                    (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("span", _hoisted_23, (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)(option.name), 1 /* TEXT */),
                    _hoisted_24,
                    (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("i", null, (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)($data.translations.name) + ":", 1 /* TEXT */),
                    _hoisted_25,
                    (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("code", null, (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)(optionName), 1 /* TEXT */),
                    _hoisted_26,
                    (option.required)
                      ? ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)("span", _hoisted_27, [
                          (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("i", null, (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)($data.translations.required) + ":", 1 /* TEXT */),
                          _hoisted_28,
                          _hoisted_29,
                          _hoisted_30
                        ]))
                      : (0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)("v-if", true),
                    (0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)("                                <span v-if=\"option.multilingual\"><i>{{ translations.multilingual }}:</i> <code>{{ translations.yes }}</code> | </span>"),
                    (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("i", null, (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)($data.translations.type) + ":", 1 /* TEXT */),
                    _hoisted_31,
                    (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("code", null, (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)(option.type), 1 /* TEXT */),
                    (option.type === 'collection')
                      ? ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)("div", _hoisted_32, [
                          _hoisted_33,
                          (0,vue__WEBPACK_IMPORTED_MODULE_0__.createTextVNode)((0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)($data.translations.valuesSeparatedByPipeAllowedFollowing), 1 /* TEXT */),
                          _hoisted_34,
                          ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(true), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)(vue__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, (0,vue__WEBPACK_IMPORTED_MODULE_0__.renderList)(option.collection, (val, key) => {
                            return ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)("span", _hoisted_35, [
                              (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("b", null, (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)(key), 1 /* TEXT */),
                              (0,vue__WEBPACK_IMPORTED_MODULE_0__.createTextVNode)(" - " + (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)(val), 1 /* TEXT */),
                              _hoisted_36
                            ]))
                          }), 256 /* UNKEYED_FRAGMENT */))
                        ]))
                      : (0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)("v-if", true)
                  ])
                ])
              ], 10 /* CLASS, PROPS */, _hoisted_19)
            ]))
          }), 256 /* UNKEYED_FRAGMENT */))
        ]))
      }), 256 /* UNKEYED_FRAGMENT */))
    ]),
    _hoisted_37,
    (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("h3", null, (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)($data.translations.fieldsTemplate), 1 /* TEXT */),
    (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("p", _hoisted_38, (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)($data.translations.fieldsTemplateInfo), 1 /* TEXT */),
    ($data.fields.length === 0)
      ? ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)("div", _hoisted_39, [
          (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_40, (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)($data.translations.addAnyFieldsToCreateForm), 1 /* TEXT */)
        ]))
      : ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)("div", _hoisted_41, [
          ($data.fieldsTemplate.error !== null)
            ? ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)("span", _hoisted_42, [
                (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("span", _hoisted_43, [
                  _hoisted_44,
                  (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("span", _hoisted_45, (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)($data.fieldsTemplate.error), 1 /* TEXT */)
                ])
              ]))
            : (0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)("v-if", true),
          (0,vue__WEBPACK_IMPORTED_MODULE_0__.withDirectives)((0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("textarea", {
            id: "form_form_template",
            name: "form[fields_template]",
            class: (0,vue__WEBPACK_IMPORTED_MODULE_0__.normalizeClass)([{ 'is-invalid': $data.fieldsTemplate.error !== null }, "form-control"]),
            style: {"height":"150px","font-family":"monospace","font-size":"15px"},
            autocomplete: "off",
            "onUpdate:modelValue": _cache[2] || (_cache[2] = $event => (($data.fieldsTemplate.value) = $event))
          }, null, 2 /* CLASS */), [
            [vue__WEBPACK_IMPORTED_MODULE_0__.vModelText, $data.fieldsTemplate.value]
          ])
        ]))
  ]))
}

/***/ }),

/***/ "vue":
/*!**********************!*\
  !*** external "Vue" ***!
  \**********************/
/***/ ((module) => {

"use strict";
module.exports = Vue;

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
/*!************************!*\
  !*** ./src/js/main.js ***!
  \************************/
const App = (__webpack_require__(/*! ./App.vue */ "./src/js/App.vue")["default"]);
const Vue = __webpack_require__(/*! vue */ "vue");

vue = Vue.createApp(App);
vue.config.devtools = true;
vue.config.performance = true;
vue.mount('#contact-form-builder');

})();

/******/ })()
;
//# sourceMappingURL=build.js.map