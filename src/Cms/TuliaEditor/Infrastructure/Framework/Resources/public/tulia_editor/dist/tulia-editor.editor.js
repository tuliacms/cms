/*!
 * Tulia Editor
 * @author	Adam Banaszkiewicz <adam@codevia.com>
 * @license MIT only with Tulia CMS package. Usage outside the Tulia CMS package is prohibited.
 *
 */
/******/ (() => { // webpackBootstrap
/******/ 	var __webpack_modules__ = ({

/***/ "./src/css/tulia-editor.editor.scss":
/*!******************************************!*\
  !*** ./src/css/tulia-editor.editor.scss ***!
  \******************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
// extracted by mini-css-extract-plugin


/***/ }),

/***/ "./node_modules/mini-css-extract-plugin/dist/loader.js!./node_modules/css-loader/dist/cjs.js!./node_modules/vue-loader/dist/stylePostLoader.js!./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[4].use[0]!./src/js/blocks/ImageBlock/Editor.vue?vue&type=style&index=0&id=3505231e&scoped=true&lang=css":
/*!***********************************************************************************************************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/mini-css-extract-plugin/dist/loader.js!./node_modules/css-loader/dist/cjs.js!./node_modules/vue-loader/dist/stylePostLoader.js!./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[4].use[0]!./src/js/blocks/ImageBlock/Editor.vue?vue&type=style&index=0&id=3505231e&scoped=true&lang=css ***!
  \***********************************************************************************************************************************************************************************************************************************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
// extracted by mini-css-extract-plugin


/***/ }),

/***/ "./node_modules/uuid/index.js":
/*!************************************!*\
  !*** ./node_modules/uuid/index.js ***!
  \************************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

var v1 = __webpack_require__(/*! ./v1 */ "./node_modules/uuid/v1.js");
var v4 = __webpack_require__(/*! ./v4 */ "./node_modules/uuid/v4.js");

var uuid = v4;
uuid.v1 = v1;
uuid.v4 = v4;

module.exports = uuid;


/***/ }),

/***/ "./node_modules/uuid/lib/bytesToUuid.js":
/*!**********************************************!*\
  !*** ./node_modules/uuid/lib/bytesToUuid.js ***!
  \**********************************************/
/***/ ((module) => {

/**
 * Convert array of 16 byte values to UUID string format of the form:
 * XXXXXXXX-XXXX-XXXX-XXXX-XXXXXXXXXXXX
 */
var byteToHex = [];
for (var i = 0; i < 256; ++i) {
  byteToHex[i] = (i + 0x100).toString(16).substr(1);
}

function bytesToUuid(buf, offset) {
  var i = offset || 0;
  var bth = byteToHex;
  // join used to fix memory issue caused by concatenation: https://bugs.chromium.org/p/v8/issues/detail?id=3175#c4
  return ([
    bth[buf[i++]], bth[buf[i++]],
    bth[buf[i++]], bth[buf[i++]], '-',
    bth[buf[i++]], bth[buf[i++]], '-',
    bth[buf[i++]], bth[buf[i++]], '-',
    bth[buf[i++]], bth[buf[i++]], '-',
    bth[buf[i++]], bth[buf[i++]],
    bth[buf[i++]], bth[buf[i++]],
    bth[buf[i++]], bth[buf[i++]]
  ]).join('');
}

module.exports = bytesToUuid;


/***/ }),

/***/ "./node_modules/uuid/lib/rng-browser.js":
/*!**********************************************!*\
  !*** ./node_modules/uuid/lib/rng-browser.js ***!
  \**********************************************/
/***/ ((module) => {

// Unique ID creation requires a high quality random # generator.  In the
// browser this is a little complicated due to unknown quality of Math.random()
// and inconsistent support for the `crypto` API.  We do the best we can via
// feature-detection

// getRandomValues needs to be invoked in a context where "this" is a Crypto
// implementation. Also, find the complete implementation of crypto on IE11.
var getRandomValues = (typeof(crypto) != 'undefined' && crypto.getRandomValues && crypto.getRandomValues.bind(crypto)) ||
                      (typeof(msCrypto) != 'undefined' && typeof window.msCrypto.getRandomValues == 'function' && msCrypto.getRandomValues.bind(msCrypto));

if (getRandomValues) {
  // WHATWG crypto RNG - http://wiki.whatwg.org/wiki/Crypto
  var rnds8 = new Uint8Array(16); // eslint-disable-line no-undef

  module.exports = function whatwgRNG() {
    getRandomValues(rnds8);
    return rnds8;
  };
} else {
  // Math.random()-based (RNG)
  //
  // If all else fails, use Math.random().  It's fast, but is of unspecified
  // quality.
  var rnds = new Array(16);

  module.exports = function mathRNG() {
    for (var i = 0, r; i < 16; i++) {
      if ((i & 0x03) === 0) r = Math.random() * 0x100000000;
      rnds[i] = r >>> ((i & 0x03) << 3) & 0xff;
    }

    return rnds;
  };
}


/***/ }),

/***/ "./node_modules/uuid/v1.js":
/*!*********************************!*\
  !*** ./node_modules/uuid/v1.js ***!
  \*********************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

var rng = __webpack_require__(/*! ./lib/rng */ "./node_modules/uuid/lib/rng-browser.js");
var bytesToUuid = __webpack_require__(/*! ./lib/bytesToUuid */ "./node_modules/uuid/lib/bytesToUuid.js");

// **`v1()` - Generate time-based UUID**
//
// Inspired by https://github.com/LiosK/UUID.js
// and http://docs.python.org/library/uuid.html

var _nodeId;
var _clockseq;

// Previous uuid creation time
var _lastMSecs = 0;
var _lastNSecs = 0;

// See https://github.com/uuidjs/uuid for API details
function v1(options, buf, offset) {
  var i = buf && offset || 0;
  var b = buf || [];

  options = options || {};
  var node = options.node || _nodeId;
  var clockseq = options.clockseq !== undefined ? options.clockseq : _clockseq;

  // node and clockseq need to be initialized to random values if they're not
  // specified.  We do this lazily to minimize issues related to insufficient
  // system entropy.  See #189
  if (node == null || clockseq == null) {
    var seedBytes = rng();
    if (node == null) {
      // Per 4.5, create and 48-bit node id, (47 random bits + multicast bit = 1)
      node = _nodeId = [
        seedBytes[0] | 0x01,
        seedBytes[1], seedBytes[2], seedBytes[3], seedBytes[4], seedBytes[5]
      ];
    }
    if (clockseq == null) {
      // Per 4.2.2, randomize (14 bit) clockseq
      clockseq = _clockseq = (seedBytes[6] << 8 | seedBytes[7]) & 0x3fff;
    }
  }

  // UUID timestamps are 100 nano-second units since the Gregorian epoch,
  // (1582-10-15 00:00).  JSNumbers aren't precise enough for this, so
  // time is handled internally as 'msecs' (integer milliseconds) and 'nsecs'
  // (100-nanoseconds offset from msecs) since unix epoch, 1970-01-01 00:00.
  var msecs = options.msecs !== undefined ? options.msecs : new Date().getTime();

  // Per 4.2.1.2, use count of uuid's generated during the current clock
  // cycle to simulate higher resolution clock
  var nsecs = options.nsecs !== undefined ? options.nsecs : _lastNSecs + 1;

  // Time since last uuid creation (in msecs)
  var dt = (msecs - _lastMSecs) + (nsecs - _lastNSecs)/10000;

  // Per 4.2.1.2, Bump clockseq on clock regression
  if (dt < 0 && options.clockseq === undefined) {
    clockseq = clockseq + 1 & 0x3fff;
  }

  // Reset nsecs if clock regresses (new clockseq) or we've moved onto a new
  // time interval
  if ((dt < 0 || msecs > _lastMSecs) && options.nsecs === undefined) {
    nsecs = 0;
  }

  // Per 4.2.1.2 Throw error if too many uuids are requested
  if (nsecs >= 10000) {
    throw new Error('uuid.v1(): Can\'t create more than 10M uuids/sec');
  }

  _lastMSecs = msecs;
  _lastNSecs = nsecs;
  _clockseq = clockseq;

  // Per 4.1.4 - Convert from unix epoch to Gregorian epoch
  msecs += 12219292800000;

  // `time_low`
  var tl = ((msecs & 0xfffffff) * 10000 + nsecs) % 0x100000000;
  b[i++] = tl >>> 24 & 0xff;
  b[i++] = tl >>> 16 & 0xff;
  b[i++] = tl >>> 8 & 0xff;
  b[i++] = tl & 0xff;

  // `time_mid`
  var tmh = (msecs / 0x100000000 * 10000) & 0xfffffff;
  b[i++] = tmh >>> 8 & 0xff;
  b[i++] = tmh & 0xff;

  // `time_high_and_version`
  b[i++] = tmh >>> 24 & 0xf | 0x10; // include version
  b[i++] = tmh >>> 16 & 0xff;

  // `clock_seq_hi_and_reserved` (Per 4.2.2 - include variant)
  b[i++] = clockseq >>> 8 | 0x80;

  // `clock_seq_low`
  b[i++] = clockseq & 0xff;

  // `node`
  for (var n = 0; n < 6; ++n) {
    b[i + n] = node[n];
  }

  return buf ? buf : bytesToUuid(b);
}

module.exports = v1;


/***/ }),

/***/ "./node_modules/uuid/v4.js":
/*!*********************************!*\
  !*** ./node_modules/uuid/v4.js ***!
  \*********************************/
/***/ ((module, __unused_webpack_exports, __webpack_require__) => {

var rng = __webpack_require__(/*! ./lib/rng */ "./node_modules/uuid/lib/rng-browser.js");
var bytesToUuid = __webpack_require__(/*! ./lib/bytesToUuid */ "./node_modules/uuid/lib/bytesToUuid.js");

function v4(options, buf, offset) {
  var i = buf && offset || 0;

  if (typeof(options) == 'string') {
    buf = options === 'binary' ? new Array(16) : null;
    options = null;
  }
  options = options || {};

  var rnds = options.random || (options.rng || rng)();

  // Per 4.4, set bits for version and `clock_seq_hi_and_reserved`
  rnds[6] = (rnds[6] & 0x0f) | 0x40;
  rnds[8] = (rnds[8] & 0x3f) | 0x80;

  // Copy bytes to buffer, if provided
  if (buf) {
    for (var ii = 0; ii < 16; ++ii) {
      buf[i + ii] = rnds[ii];
    }
  }

  return buf || bytesToUuid(rnds);
}

module.exports = v4;


/***/ }),

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

/***/ "./src/js/Components/Editor/Rendering/Canvas.vue":
/*!*******************************************************!*\
  !*** ./src/js/Components/Editor/Rendering/Canvas.vue ***!
  \*******************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _Canvas_vue_vue_type_template_id_27d57861__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./Canvas.vue?vue&type=template&id=27d57861 */ "./src/js/Components/Editor/Rendering/Canvas.vue?vue&type=template&id=27d57861");
/* harmony import */ var _Canvas_vue_vue_type_script_setup_true_lang_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./Canvas.vue?vue&type=script&setup=true&lang=js */ "./src/js/Components/Editor/Rendering/Canvas.vue?vue&type=script&setup=true&lang=js");
/* harmony import */ var _home_adam_projects_tuliacms_core_src_Cms_TuliaEditor_Infrastructure_Framework_Resources_public_tulia_editor_node_modules_vue_loader_dist_exportHelper_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./node_modules/vue-loader/dist/exportHelper.js */ "./node_modules/vue-loader/dist/exportHelper.js");




;
const __exports__ = /*#__PURE__*/(0,_home_adam_projects_tuliacms_core_src_Cms_TuliaEditor_Infrastructure_Framework_Resources_public_tulia_editor_node_modules_vue_loader_dist_exportHelper_js__WEBPACK_IMPORTED_MODULE_2__["default"])(_Canvas_vue_vue_type_script_setup_true_lang_js__WEBPACK_IMPORTED_MODULE_1__["default"], [['render',_Canvas_vue_vue_type_template_id_27d57861__WEBPACK_IMPORTED_MODULE_0__.render],['__file',"src/js/Components/Editor/Rendering/Canvas.vue"]])
/* hot reload */
if (false) {}


/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (__exports__);

/***/ }),

/***/ "./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[4].use[0]!./src/js/Components/Editor/Rendering/Canvas.vue?vue&type=script&setup=true&lang=js":
/*!**************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[4].use[0]!./src/js/Components/Editor/Rendering/Canvas.vue?vue&type=script&setup=true&lang=js ***!
  \**************************************************************************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = ({
  props: ['structure'],
  setup(__props, { expose }) {
  expose();

const props = __props

const { ref, defineProps, inject } = __webpack_require__(/*! vue */ "vue");

const messenger = inject('messenger');

/**********************
 * Render area preview
 **********************/
const renderPreview = ref(false);
messenger.operation('editor.canvas.preview.toggle', (params, success, fail) => {
    renderPreview.value = !renderPreview.value;

    success();
});

const __returned__ = { ref, defineProps, inject, props, messenger, renderPreview }
Object.defineProperty(__returned__, '__isScriptSetup', { enumerable: false, value: true })
return __returned__
}

});

/***/ }),

/***/ "./src/js/Components/Editor/Root.vue":
/*!*******************************************!*\
  !*** ./src/js/Components/Editor/Root.vue ***!
  \*******************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _Root_vue_vue_type_template_id_1386ac68__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./Root.vue?vue&type=template&id=1386ac68 */ "./src/js/Components/Editor/Root.vue?vue&type=template&id=1386ac68");
/* harmony import */ var _Root_vue_vue_type_script_setup_true_lang_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./Root.vue?vue&type=script&setup=true&lang=js */ "./src/js/Components/Editor/Root.vue?vue&type=script&setup=true&lang=js");
/* harmony import */ var _home_adam_projects_tuliacms_core_src_Cms_TuliaEditor_Infrastructure_Framework_Resources_public_tulia_editor_node_modules_vue_loader_dist_exportHelper_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./node_modules/vue-loader/dist/exportHelper.js */ "./node_modules/vue-loader/dist/exportHelper.js");




;
const __exports__ = /*#__PURE__*/(0,_home_adam_projects_tuliacms_core_src_Cms_TuliaEditor_Infrastructure_Framework_Resources_public_tulia_editor_node_modules_vue_loader_dist_exportHelper_js__WEBPACK_IMPORTED_MODULE_2__["default"])(_Root_vue_vue_type_script_setup_true_lang_js__WEBPACK_IMPORTED_MODULE_1__["default"], [['render',_Root_vue_vue_type_template_id_1386ac68__WEBPACK_IMPORTED_MODULE_0__.render],['__file',"src/js/Components/Editor/Root.vue"]])
/* hot reload */
if (false) {}


/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (__exports__);

/***/ }),

/***/ "./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[4].use[0]!./src/js/Components/Editor/Root.vue?vue&type=script&setup=true&lang=js":
/*!**************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[4].use[0]!./src/js/Components/Editor/Root.vue?vue&type=script&setup=true&lang=js ***!
  \**************************************************************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = ({
  props: [
    'container',
    'instanceId',
    'options',
    'availableBlocks',
    'structure'
],
  setup(__props, { expose }) {
  expose();

const props = __props

const StructureComponent = (__webpack_require__(/*! components/Editor/Structure/Structure.vue */ "./src/js/Components/Editor/Structure/Structure.vue")["default"]);
const RenderingCanvasComponent = (__webpack_require__(/*! components/Editor/Rendering/Canvas.vue */ "./src/js/Components/Editor/Rendering/Canvas.vue")["default"]);
const ObjectCloner = (__webpack_require__(/*! shared/Utils/ObjectCloner.js */ "./src/js/shared/Utils/ObjectCloner.js")["default"]);
const Selection = (__webpack_require__(/*! shared/Structure/Selection/Selection.js */ "./src/js/shared/Structure/Selection/Selection.js")["default"]);
const StructureManipulator = (__webpack_require__(/*! shared/Structure/StructureManipulator.js */ "./src/js/shared/Structure/StructureManipulator.js")["default"]);
const { defineProps, provide, reactive, onMounted, toRaw, ref } = __webpack_require__(/*! vue */ "vue");



const structure = reactive(ObjectCloner.deepClone(props.structure));
const selection = new Selection(structure, props.container.messenger);
const structureManipulator = new StructureManipulator(structure, props.container.messenger);

provide('selection', selection);
provide('messenger', props.container.messenger);
provide('eventDispatcher', props.container.eventDispatcher);
provide('translator', props.container.translator);
provide('structureManipulator', structureManipulator);
provide('options', props.options);

const renderedContent = ref(null);

onMounted(() => {
    props.container.eventDispatcher.on('block.inner.updated', () => {
        //props.container.messenger.notify('structure.synchronize.from.editor', ObjectCloner.deepClone(toRaw(structure)));
    });

    props.container.messenger.operation('structure.fetch', (params, success, fail) => {
        success({
            content: renderedContent.value.$el.innerHTML,
            structure: ObjectCloner.deepClone(toRaw(structure))
        });
    });
    props.container.messenger.on('structure.synchronize.from.admin', (newStructure) => {
        structure.sections = newStructure.sections;
        props.container.messenger.notify('structure.updated');
    });

    props.container.messenger.on('structure.move-element', (delta) => {
        structureManipulator.moveElementUsingDelta(delta);

        // @todo We need mechanism of wating for all windows confirms the message was handled and operation of moving was done in structure.
        // Only with that we can select element in document.
        // Right now we have to hack system, and select with timeout.
        setTimeout(() => {
            selection.resetHovered();
            selection.select(delta.element.type, delta.element.id);
        }, 60);
    });

    props.container.messenger.on('editor.click.outside', () => {
        selection.resetSelection();
    });

    document.addEventListener('click', (event) => {
        if (event.target.tagName === 'HTML') {
            props.container.messenger.notify('editor.click.outside');
        }
    });
});





/*********
 * Blocks
 *********/
const Blocks = (__webpack_require__(/*! shared/Structure/Blocks/Blocks.js */ "./src/js/shared/Structure/Blocks/Blocks.js")["default"]);
const BlockHooks = (__webpack_require__(/*! shared/Structure/Blocks/BlockHooks.js */ "./src/js/shared/Structure/Blocks/BlockHooks.js")["default"]);
const BlocksRegistry = (__webpack_require__(/*! shared/Structure/Blocks/Registry.js */ "./src/js/shared/Structure/Blocks/Registry.js")["default"]);

const blockHooks = new BlockHooks(props.container.messenger);
const blocksRegistry = new BlocksRegistry(props.availableBlocks);

provide('blocksRegistry', blocksRegistry);
provide('blocks', new Blocks(blockHooks, props.options.blocks, props.container.messenger));

const __returned__ = { StructureComponent, RenderingCanvasComponent, ObjectCloner, Selection, StructureManipulator, defineProps, provide, reactive, onMounted, toRaw, ref, props, structure, selection, structureManipulator, renderedContent, Blocks, BlockHooks, BlocksRegistry, blockHooks, blocksRegistry }
Object.defineProperty(__returned__, '__isScriptSetup', { enumerable: false, value: true })
return __returned__
}

});

/***/ }),

/***/ "./src/js/Components/Editor/Structure/Block.vue":
/*!******************************************************!*\
  !*** ./src/js/Components/Editor/Structure/Block.vue ***!
  \******************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _Block_vue_vue_type_template_id_0de2ec3b__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./Block.vue?vue&type=template&id=0de2ec3b */ "./src/js/Components/Editor/Structure/Block.vue?vue&type=template&id=0de2ec3b");
/* harmony import */ var _Block_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./Block.vue?vue&type=script&lang=js */ "./src/js/Components/Editor/Structure/Block.vue?vue&type=script&lang=js");
/* harmony import */ var _home_adam_projects_tuliacms_core_src_Cms_TuliaEditor_Infrastructure_Framework_Resources_public_tulia_editor_node_modules_vue_loader_dist_exportHelper_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./node_modules/vue-loader/dist/exportHelper.js */ "./node_modules/vue-loader/dist/exportHelper.js");




;
const __exports__ = /*#__PURE__*/(0,_home_adam_projects_tuliacms_core_src_Cms_TuliaEditor_Infrastructure_Framework_Resources_public_tulia_editor_node_modules_vue_loader_dist_exportHelper_js__WEBPACK_IMPORTED_MODULE_2__["default"])(_Block_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_1__["default"], [['render',_Block_vue_vue_type_template_id_0de2ec3b__WEBPACK_IMPORTED_MODULE_0__.render],['__file',"src/js/Components/Editor/Structure/Block.vue"]])
/* hot reload */
if (false) {}


/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (__exports__);

/***/ }),

/***/ "./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[4].use[0]!./src/js/Components/Editor/Structure/Block.vue?vue&type=script&lang=js":
/*!**************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[4].use[0]!./src/js/Components/Editor/Structure/Block.vue?vue&type=script&lang=js ***!
  \**************************************************************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });

/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = ({
    props: ['block', 'parent'],
    inject: ['selection', 'eventDispatcher'],
});


/***/ }),

/***/ "./src/js/Components/Editor/Structure/Column.vue":
/*!*******************************************************!*\
  !*** ./src/js/Components/Editor/Structure/Column.vue ***!
  \*******************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _Column_vue_vue_type_template_id_32264bd0__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./Column.vue?vue&type=template&id=32264bd0 */ "./src/js/Components/Editor/Structure/Column.vue?vue&type=template&id=32264bd0");
/* harmony import */ var _Column_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./Column.vue?vue&type=script&lang=js */ "./src/js/Components/Editor/Structure/Column.vue?vue&type=script&lang=js");
/* harmony import */ var _home_adam_projects_tuliacms_core_src_Cms_TuliaEditor_Infrastructure_Framework_Resources_public_tulia_editor_node_modules_vue_loader_dist_exportHelper_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./node_modules/vue-loader/dist/exportHelper.js */ "./node_modules/vue-loader/dist/exportHelper.js");




;
const __exports__ = /*#__PURE__*/(0,_home_adam_projects_tuliacms_core_src_Cms_TuliaEditor_Infrastructure_Framework_Resources_public_tulia_editor_node_modules_vue_loader_dist_exportHelper_js__WEBPACK_IMPORTED_MODULE_2__["default"])(_Column_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_1__["default"], [['render',_Column_vue_vue_type_template_id_32264bd0__WEBPACK_IMPORTED_MODULE_0__.render],['__file',"src/js/Components/Editor/Structure/Column.vue"]])
/* hot reload */
if (false) {}


/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (__exports__);

/***/ }),

/***/ "./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[4].use[0]!./src/js/Components/Editor/Structure/Column.vue?vue&type=script&lang=js":
/*!***************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[4].use[0]!./src/js/Components/Editor/Structure/Column.vue?vue&type=script&lang=js ***!
  \***************************************************************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });

const Block = (__webpack_require__(/*! ./Block.vue */ "./src/js/Components/Editor/Structure/Block.vue")["default"]);

/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = ({
    props: ['column', 'parent'],
    inject: ['selection'],
    components: {Block},
    computed: {
        columnClass: function () {
            let classList = ['tued-structure-column', 'tued-structure-element-selectable'];
            let anySizingAdded = false;

            for (let i in this.column.sizes) {
                if (this.column.sizes[i].size) {
                    let prefix = `${i}-`;

                    if (i === 'xs') {
                        prefix = '';
                    }

                    classList.push(`col-${prefix}${this.column.sizes[i].size}`);
                    anySizingAdded = true;
                }
            }

            if (anySizingAdded === false) {
                classList.push('col');
            }

            return classList;
        }
    }
});


/***/ }),

/***/ "./src/js/Components/Editor/Structure/Row.vue":
/*!****************************************************!*\
  !*** ./src/js/Components/Editor/Structure/Row.vue ***!
  \****************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _Row_vue_vue_type_template_id_7cc13ef0__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./Row.vue?vue&type=template&id=7cc13ef0 */ "./src/js/Components/Editor/Structure/Row.vue?vue&type=template&id=7cc13ef0");
/* harmony import */ var _Row_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./Row.vue?vue&type=script&lang=js */ "./src/js/Components/Editor/Structure/Row.vue?vue&type=script&lang=js");
/* harmony import */ var _home_adam_projects_tuliacms_core_src_Cms_TuliaEditor_Infrastructure_Framework_Resources_public_tulia_editor_node_modules_vue_loader_dist_exportHelper_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./node_modules/vue-loader/dist/exportHelper.js */ "./node_modules/vue-loader/dist/exportHelper.js");




;
const __exports__ = /*#__PURE__*/(0,_home_adam_projects_tuliacms_core_src_Cms_TuliaEditor_Infrastructure_Framework_Resources_public_tulia_editor_node_modules_vue_loader_dist_exportHelper_js__WEBPACK_IMPORTED_MODULE_2__["default"])(_Row_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_1__["default"], [['render',_Row_vue_vue_type_template_id_7cc13ef0__WEBPACK_IMPORTED_MODULE_0__.render],['__file',"src/js/Components/Editor/Structure/Row.vue"]])
/* hot reload */
if (false) {}


/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (__exports__);

/***/ }),

/***/ "./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[4].use[0]!./src/js/Components/Editor/Structure/Row.vue?vue&type=script&lang=js":
/*!************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[4].use[0]!./src/js/Components/Editor/Structure/Row.vue?vue&type=script&lang=js ***!
  \************************************************************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });

const Column = (__webpack_require__(/*! ./Column.vue */ "./src/js/Components/Editor/Structure/Column.vue")["default"]);

/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = ({
    props: ['row', 'parent'],
    inject: ['selection'],
    components: { Column },
});


/***/ }),

/***/ "./src/js/Components/Editor/Structure/Section.vue":
/*!********************************************************!*\
  !*** ./src/js/Components/Editor/Structure/Section.vue ***!
  \********************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _Section_vue_vue_type_template_id_4d532d13__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./Section.vue?vue&type=template&id=4d532d13 */ "./src/js/Components/Editor/Structure/Section.vue?vue&type=template&id=4d532d13");
/* harmony import */ var _Section_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./Section.vue?vue&type=script&lang=js */ "./src/js/Components/Editor/Structure/Section.vue?vue&type=script&lang=js");
/* harmony import */ var _home_adam_projects_tuliacms_core_src_Cms_TuliaEditor_Infrastructure_Framework_Resources_public_tulia_editor_node_modules_vue_loader_dist_exportHelper_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./node_modules/vue-loader/dist/exportHelper.js */ "./node_modules/vue-loader/dist/exportHelper.js");




;
const __exports__ = /*#__PURE__*/(0,_home_adam_projects_tuliacms_core_src_Cms_TuliaEditor_Infrastructure_Framework_Resources_public_tulia_editor_node_modules_vue_loader_dist_exportHelper_js__WEBPACK_IMPORTED_MODULE_2__["default"])(_Section_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_1__["default"], [['render',_Section_vue_vue_type_template_id_4d532d13__WEBPACK_IMPORTED_MODULE_0__.render],['__file',"src/js/Components/Editor/Structure/Section.vue"]])
/* hot reload */
if (false) {}


/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (__exports__);

/***/ }),

/***/ "./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[4].use[0]!./src/js/Components/Editor/Structure/Section.vue?vue&type=script&lang=js":
/*!****************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[4].use[0]!./src/js/Components/Editor/Structure/Section.vue?vue&type=script&lang=js ***!
  \****************************************************************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });

const Row = (__webpack_require__(/*! ./Row.vue */ "./src/js/Components/Editor/Structure/Row.vue")["default"]);

/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = ({
    props: ['section'],
    inject: ['selection'],
    components: { Row }
});


/***/ }),

/***/ "./src/js/Components/Editor/Structure/Structure.vue":
/*!**********************************************************!*\
  !*** ./src/js/Components/Editor/Structure/Structure.vue ***!
  \**********************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _Structure_vue_vue_type_template_id_4d3491be__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./Structure.vue?vue&type=template&id=4d3491be */ "./src/js/Components/Editor/Structure/Structure.vue?vue&type=template&id=4d3491be");
/* harmony import */ var _Structure_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./Structure.vue?vue&type=script&lang=js */ "./src/js/Components/Editor/Structure/Structure.vue?vue&type=script&lang=js");
/* harmony import */ var _home_adam_projects_tuliacms_core_src_Cms_TuliaEditor_Infrastructure_Framework_Resources_public_tulia_editor_node_modules_vue_loader_dist_exportHelper_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./node_modules/vue-loader/dist/exportHelper.js */ "./node_modules/vue-loader/dist/exportHelper.js");




;
const __exports__ = /*#__PURE__*/(0,_home_adam_projects_tuliacms_core_src_Cms_TuliaEditor_Infrastructure_Framework_Resources_public_tulia_editor_node_modules_vue_loader_dist_exportHelper_js__WEBPACK_IMPORTED_MODULE_2__["default"])(_Structure_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_1__["default"], [['render',_Structure_vue_vue_type_template_id_4d3491be__WEBPACK_IMPORTED_MODULE_0__.render],['__file',"src/js/Components/Editor/Structure/Structure.vue"]])
/* hot reload */
if (false) {}


/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (__exports__);

/***/ }),

/***/ "./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[4].use[0]!./src/js/Components/Editor/Structure/Structure.vue?vue&type=script&lang=js":
/*!******************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[4].use[0]!./src/js/Components/Editor/Structure/Structure.vue?vue&type=script&lang=js ***!
  \******************************************************************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });

const Section = (__webpack_require__(/*! components/Editor/Structure/Section.vue */ "./src/js/Components/Editor/Structure/Section.vue")["default"]);
const ObjectCloner = (__webpack_require__(/*! shared/Utils/ObjectCloner.js */ "./src/js/shared/Utils/ObjectCloner.js")["default"]);
const SelectedBoundaries = (__webpack_require__(/*! shared/Structure/Selection/Boundaries/Selected.js */ "./src/js/shared/Structure/Selection/Boundaries/Selected.js")["default"]);
const HoveredBoundaries = (__webpack_require__(/*! shared/Structure/Selection/Boundaries/Hovered.js */ "./src/js/shared/Structure/Selection/Boundaries/Hovered.js")["default"]);
const HoverResolver = (__webpack_require__(/*! shared/Structure/Selection/HoverResolver.js */ "./src/js/shared/Structure/Selection/HoverResolver.js")["default"]);
const { toRaw } = __webpack_require__(/*! vue */ "vue");

/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = ({
    props: ['structure'],
    inject: ['messenger', 'selection', 'eventDispatcher', 'structureManipulator', 'translator'],
    components: { Section },
    data () {
        return {
            width: '100%',
            hoverable: {
                resolver: new HoverResolver(this.selection),
                boundaries: new HoveredBoundaries(this.updateHoverableStyle),
                style: {
                    left: -100,
                    top: -100,
                    width: 0,
                    height: 0,
                }
            },
            selectable: {
                boundaries: new SelectedBoundaries(this.updateSelectableStyle),
                style: {
                    left: -100,
                    top: -100,
                    width: 0,
                    height: 0,
                    tagName: 'div',
                }
            },
            actions: {
                style: {
                    left: -100,
                    top: -100,
                    width: 0,
                },
                activeness: {
                    selectParent: false,
                    duplicate: true,
                    delete: true,
                }
            }
        }
    },
    methods: {
        selectionEnter: function (type, id) {
            let el = this.getElement(type, id);
            this.hoverable.resolver.enter(el, type, id);
        },
        selectionLeave: function () {
            this.hoverable.resolver.leave();
        },
        updateHoverableStyle: function (style) {
            this.hoverable.style.left = style.left;
            this.hoverable.style.top = style.top;
            this.hoverable.style.width = style.width;
            this.hoverable.style.height = style.height;
        },
        updateSelectableStyle: function (style) {
            this.selectable.style.left = style.left;
            this.selectable.style.top = style.top;
            this.selectable.style.width = style.width;
            this.selectable.style.height = style.height;
            this.selectable.style.tagName = style.tagName;

            let elm = this.$refs['element-actions'];

            this.actions.style.top = style.top - elm.offsetHeight;
            this.actions.style.left = style.left;
            this.actions.style.width = style.width;
        },
        selectParentSelectable: function () {
            let selected = this.selection.getSelected();

            if (!selected) {
                return;
            }

            let parent = this.structureManipulator.findParent(selected.id);

            if (!parent) {
                return;
            }

            this.selection.select(parent.type, parent.id);
        },
        deleteSelectedElement: function () {
            let selected = this.selection.getSelected();

            if (!selected) {
                console.error('Cannot remove selected element. None of elements were selected.');
                return;
            }

            this.structureManipulator.removeElement(selected.id);
        },
        hideElementActions: function () {
            this.actions.activeness.selectParent = false;
        },
        updateElementActions: function (el, type, element) {
            let parent = this.structureManipulator.findParent(element.id);

            if (!parent) {
                this.hideElementActions();
                return;
            }

            this.actions.activeness.selectParent = true;
        },
        getElement: function (type, id) {
            return this.$refs['structure'].querySelector(`#tued-structure-${type}-${id}`);
        }
    },
    mounted () {
        // @todo Is this event `block.inner.updated` needed?
        // Maybe we can use `structure.element.updated`?
        this.eventDispatcher.on('block.inner.updated', () => {
            this.hoverable.boundaries.update();
            this.selectable.boundaries.update();
        });
        this.messenger.on('structure.element.updated', () => {
            this.hoverable.boundaries.update();
            this.selectable.boundaries.update();
        });
        this.messenger.on('structure.element.removed', () => {
            this.selection.resetSelection();
            this.selection.resetHovered();
        });

        /**
         * Selection
         */
        this.messenger.on('structure.selection.select', (type, id) => {
            let node = this.getElement(type, id);
            let element = this.structureManipulator.find(id);

            this.selectable.boundaries.highlightSelected(node);
            this.updateElementActions(node, type, element);
        });
        this.messenger.on('structure.selection.deselect', () => {
            this.selectable.boundaries.clearSelectionHighlight();
        });
        this.messenger.on('structure.selection.hover', (type, id) => {
            let node = this.getElement(type, id);

            this.hoverable.boundaries.highlight(node, type, id);
        });
        this.messenger.on('structure.selection.dehover', () => {
            this.hoverable.boundaries.clear();
        });
        this.messenger.on('device.size.changed', () => {
            let animationTime = 300;
            this.selectable.boundaries.keepUpdatePositionFor(animationTime);
        });
    }
});


/***/ }),

/***/ "./src/js/blocks/ImageBlock/Editor.vue":
/*!*********************************************!*\
  !*** ./src/js/blocks/ImageBlock/Editor.vue ***!
  \*********************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _Editor_vue_vue_type_template_id_3505231e_scoped_true__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./Editor.vue?vue&type=template&id=3505231e&scoped=true */ "./src/js/blocks/ImageBlock/Editor.vue?vue&type=template&id=3505231e&scoped=true");
/* harmony import */ var _Editor_vue_vue_type_script_setup_true_lang_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./Editor.vue?vue&type=script&setup=true&lang=js */ "./src/js/blocks/ImageBlock/Editor.vue?vue&type=script&setup=true&lang=js");
/* harmony import */ var _Editor_vue_vue_type_style_index_0_id_3505231e_scoped_true_lang_css__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./Editor.vue?vue&type=style&index=0&id=3505231e&scoped=true&lang=css */ "./src/js/blocks/ImageBlock/Editor.vue?vue&type=style&index=0&id=3505231e&scoped=true&lang=css");
/* harmony import */ var _home_adam_projects_tuliacms_core_src_Cms_TuliaEditor_Infrastructure_Framework_Resources_public_tulia_editor_node_modules_vue_loader_dist_exportHelper_js__WEBPACK_IMPORTED_MODULE_3__ = __webpack_require__(/*! ./node_modules/vue-loader/dist/exportHelper.js */ "./node_modules/vue-loader/dist/exportHelper.js");




;


const __exports__ = /*#__PURE__*/(0,_home_adam_projects_tuliacms_core_src_Cms_TuliaEditor_Infrastructure_Framework_Resources_public_tulia_editor_node_modules_vue_loader_dist_exportHelper_js__WEBPACK_IMPORTED_MODULE_3__["default"])(_Editor_vue_vue_type_script_setup_true_lang_js__WEBPACK_IMPORTED_MODULE_1__["default"], [['render',_Editor_vue_vue_type_template_id_3505231e_scoped_true__WEBPACK_IMPORTED_MODULE_0__.render],['__scopeId',"data-v-3505231e"],['__file',"src/js/blocks/ImageBlock/Editor.vue"]])
/* hot reload */
if (false) {}


/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (__exports__);

/***/ }),

/***/ "./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[4].use[0]!./src/js/blocks/ImageBlock/Editor.vue?vue&type=script&setup=true&lang=js":
/*!****************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[4].use[0]!./src/js/blocks/ImageBlock/Editor.vue?vue&type=script&setup=true&lang=js ***!
  \****************************************************************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = ({
  props: (__webpack_require__(/*! ./props.js */ "./src/js/blocks/ImageBlock/props.js")["default"]),
  setup(__props, { expose }) {
  expose();

const props = __props

const WysiwygEditor = (__webpack_require__(/*! extensions/WysiwygEditor.vue */ "./src/js/extensions/WysiwygEditor.vue")["default"]);
const { defineProps, inject, computed } = __webpack_require__(/*! vue */ "vue");

/****************
 * Prepare block
 ****************/

const blocks = inject('blocks');
const block = blocks.editor('core-imageblock', props);

/**************
 * Block logic
 **************/
const imageResolvePath =  decodeURIComponent(block.options.image_resolve_path);
const imagePathFromId = function () {
    return imageResolvePath
        .replace('{size}', 'original')
        .replace('{id}', block.data.imageId)
        .replace('{filename}', block.data.imageFilename);
};
const imageLink = computed(() => {
    if (!block.data.imageId) {
        return 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAlgAAADIBAMAAADGsYKFAAAAG1BMVEXMzMyWlpbFxcWcnJyjo6Oqqqq+vr6xsbG3t7ecUE7+AAAERUlEQVR42uzSsUoDQRSG0YuQ2HodJe2ChXXUwlILSWtIYynoAyj4AII+uDtmV9NYTGVgz2n+Zm7zMQEAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAADsucyjQKwQq4FYDcRqIFYDsRqI1UCsBmI1EOsf7cY6eCuruvNNWXXxh8/l1UtU483y8iGmYifWU2Ze9LvudxGD882y3L3H6CMzy2OM5rfZu46J6GMNM89e6WKW1X18e86qjJ/pMKvjn8SvOTyeht9YZ1ndxDqrxRBn66SLnTilG65muXUa0/DFvtn0tA0EYXiycWIfeWNjcrRJSznWVUt7NaCKI676cU2kol6JqqIeA/343WU9s95AwIkabuPnEGV2ncuj8Xh21/GyWFI6BJM5OeLQ4ubO5VfXEBakgkbWSHJo4oQ0cnzu9MGk/Cs//ZJU0MjaRXz1B0CJ5O+3gocD3A5mB9+XEi25Mb+RNNM4yw4qYI9U0MgCLq0NvqcmwJiIZsCxna3s5C0FsOAQO3wXHtkEKxCTCkSWlPQ+2JLhcC4Z1eeiFUkKBSKrQFxP58CUNCCypO6Erg8okdhPlkMRzw6k0BuWZYCULKGW5sHLWkjJzvg5F9vUERtDvqrnHntVHYfijkhLhfeyJGcSssxE1shfZQdBMmvjfnP3VRiRBvzTUMIxWfLay+kpp46Rgi4XUb+Oe1LX7URKGmhkjSUceVmMvw0r1yIM6njm3FGupHdoZKV3ZPUaWV4OlS6Bwjq+bhT1lCx4fAfvwlVZQy7ovoRFdVxhTEwfCWlgrayDHyfAPVlDkdWgpCv1C+kHZb0owezIZI1ZkQXSQLusEHhcVtnJWpblfOx5WcyqrIwU0CorgCX5mUnN6mS1yJoDeHPDw77Ae1lj0kWrrAL4sDRciixX4JW0opvJiuByx3XwaSfrMVkD4Hz5qrmzE3EHr6Rv30yWbCusLqRDWRvGxJj9fdLAGlnZ8kI6d3aCOvZdfq4kx1pk8YfPJJ9pudvPWsgWjZLnYrssyawJDzfbx/N7O6Wlts2/VVk+dSp3TihSirt78JG1poE2WYFICMHDcoxBA4lLOd2ZaDmSbpMV8tGXKSEpNQPO3bkhh8c6zw0fkGUAvM8OK8QF9oycUN+YXxBZAwBX2WEJJfW9VRZVYEb2y5TIFBzH9jIOVb1z1CprAmY6Z1k0E3lymcwr2VReI2vIqXNEuciSgYVcZkpVidUui57hlt2MQpYlA6lxR9AhVL0muYaLgl/G/Vq8zcjy/CQ+o0YWRR+L15+po4Whli70KYj0lKntCYFL6mhl8OkLfwm0vL22BX2Av+RKTnO2IXAL5rmaNvT/iaRjMGoWg1tg5JW3CZRs9m1FBbybmgt0D8MNyHW9YbQdIYRX1LGWShKraxw2ICqArmJtSmT/2Np17x0dHf/ag0MCAAAAAEH/X7vBDgAAAAAAAAAAAAAAAAAA8AT99p+0ltRNJAAAAABJRU5ErkJggg==';
    }

    return imagePathFromId();
});

const __returned__ = { WysiwygEditor, defineProps, inject, computed, props, blocks, block, imageResolvePath, imagePathFromId, imageLink }
Object.defineProperty(__returned__, '__isScriptSetup', { enumerable: false, value: true })
return __returned__
}

});

/***/ }),

/***/ "./src/js/blocks/ImageBlock/Manager.vue":
/*!**********************************************!*\
  !*** ./src/js/blocks/ImageBlock/Manager.vue ***!
  \**********************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _Manager_vue_vue_type_template_id_a875ed68__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./Manager.vue?vue&type=template&id=a875ed68 */ "./src/js/blocks/ImageBlock/Manager.vue?vue&type=template&id=a875ed68");
/* harmony import */ var _Manager_vue_vue_type_script_setup_true_lang_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./Manager.vue?vue&type=script&setup=true&lang=js */ "./src/js/blocks/ImageBlock/Manager.vue?vue&type=script&setup=true&lang=js");
/* harmony import */ var _home_adam_projects_tuliacms_core_src_Cms_TuliaEditor_Infrastructure_Framework_Resources_public_tulia_editor_node_modules_vue_loader_dist_exportHelper_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./node_modules/vue-loader/dist/exportHelper.js */ "./node_modules/vue-loader/dist/exportHelper.js");




;
const __exports__ = /*#__PURE__*/(0,_home_adam_projects_tuliacms_core_src_Cms_TuliaEditor_Infrastructure_Framework_Resources_public_tulia_editor_node_modules_vue_loader_dist_exportHelper_js__WEBPACK_IMPORTED_MODULE_2__["default"])(_Manager_vue_vue_type_script_setup_true_lang_js__WEBPACK_IMPORTED_MODULE_1__["default"], [['render',_Manager_vue_vue_type_template_id_a875ed68__WEBPACK_IMPORTED_MODULE_0__.render],['__file',"src/js/blocks/ImageBlock/Manager.vue"]])
/* hot reload */
if (false) {}


/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (__exports__);

/***/ }),

/***/ "./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[4].use[0]!./src/js/blocks/ImageBlock/Manager.vue?vue&type=script&setup=true&lang=js":
/*!*****************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[4].use[0]!./src/js/blocks/ImageBlock/Manager.vue?vue&type=script&setup=true&lang=js ***!
  \*****************************************************************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = ({
  props: (__webpack_require__(/*! ./props.js */ "./src/js/blocks/ImageBlock/props.js")["default"]),
  setup(__props, { expose }) {
  expose();

const props = __props

const { computed, onMounted, defineProps, inject } = __webpack_require__(/*! vue */ "vue");

/****************
 * Prepare block
 ****************/

const blocks = inject('blocks');
const block = blocks.manager('core-imageblock', props);

/**************
 * Block logic
 **************/
const Tulia = __webpack_require__(/*! Tulia */ "Tulia");

const filemanager = {
    instance: null
};

const imageLink = computed(() => {
    if (!block.data.imageId) {
        return 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAlgAAADIBAMAAADGsYKFAAAAG1BMVEXMzMyWlpbFxcWcnJyjo6Oqqqq+vr6xsbG3t7ecUE7+AAAERUlEQVR42uzSsUoDQRSG0YuQ2HodJe2ChXXUwlILSWtIYynoAyj4AII+uDtmV9NYTGVgz2n+Zm7zMQEAAAAAAAAAAAAAAAAAAAAAAAAAAAAAAADsucyjQKwQq4FYDcRqIFYDsRqI1UCsBmI1EOsf7cY6eCuruvNNWXXxh8/l1UtU483y8iGmYifWU2Ze9LvudxGD882y3L3H6CMzy2OM5rfZu46J6GMNM89e6WKW1X18e86qjJ/pMKvjn8SvOTyeht9YZ1ndxDqrxRBn66SLnTilG65muXUa0/DFvtn0tA0EYXiycWIfeWNjcrRJSznWVUt7NaCKI676cU2kol6JqqIeA/343WU9s95AwIkabuPnEGV2ncuj8Xh21/GyWFI6BJM5OeLQ4ubO5VfXEBakgkbWSHJo4oQ0cnzu9MGk/Cs//ZJU0MjaRXz1B0CJ5O+3gocD3A5mB9+XEi25Mb+RNNM4yw4qYI9U0MgCLq0NvqcmwJiIZsCxna3s5C0FsOAQO3wXHtkEKxCTCkSWlPQ+2JLhcC4Z1eeiFUkKBSKrQFxP58CUNCCypO6Erg8okdhPlkMRzw6k0BuWZYCULKGW5sHLWkjJzvg5F9vUERtDvqrnHntVHYfijkhLhfeyJGcSssxE1shfZQdBMmvjfnP3VRiRBvzTUMIxWfLay+kpp46Rgi4XUb+Oe1LX7URKGmhkjSUceVmMvw0r1yIM6njm3FGupHdoZKV3ZPUaWV4OlS6Bwjq+bhT1lCx4fAfvwlVZQy7ovoRFdVxhTEwfCWlgrayDHyfAPVlDkdWgpCv1C+kHZb0owezIZI1ZkQXSQLusEHhcVtnJWpblfOx5WcyqrIwU0CorgCX5mUnN6mS1yJoDeHPDw77Ae1lj0kWrrAL4sDRciixX4JW0opvJiuByx3XwaSfrMVkD4Hz5qrmzE3EHr6Rv30yWbCusLqRDWRvGxJj9fdLAGlnZ8kI6d3aCOvZdfq4kx1pk8YfPJJ9pudvPWsgWjZLnYrssyawJDzfbx/N7O6Wlts2/VVk+dSp3TihSirt78JG1poE2WYFICMHDcoxBA4lLOd2ZaDmSbpMV8tGXKSEpNQPO3bkhh8c6zw0fkGUAvM8OK8QF9oycUN+YXxBZAwBX2WEJJfW9VRZVYEb2y5TIFBzH9jIOVb1z1CprAmY6Z1k0E3lymcwr2VReI2vIqXNEuciSgYVcZkpVidUui57hlt2MQpYlA6lxR9AhVL0muYaLgl/G/Vq8zcjy/CQ+o0YWRR+L15+po4Whli70KYj0lKntCYFL6mhl8OkLfwm0vL22BX2Av+RKTnO2IXAL5rmaNvT/iaRjMGoWg1tg5JW3CZRs9m1FBbybmgt0D8MNyHW9YbQdIYRX1LGWShKraxw2ICqArmJtSmT/2Np17x0dHf/ag0MCAAAAAEH/X7vBDgAAAAAAAAAAAAAAAAAA8AT99p+0ltRNJAAAAABJRU5ErkJggg==';
    }

    return '#';
});

const getFilemanager = () => {
    if (filemanager.instance) {
        return filemanager.instance;
    }

    return filemanager.instance = Tulia.Filemanager.create({
        showOnInit: false,
        endpoint: block.options.filemanager_endpoint,
        filter: { type: 'image' },
        multiple: false,
        closeOnSelect: true,
        onSelect: function (files) {
            if (!files.length) {
                return;
            }

            block.data.imageId = files[0].id;
            block.data.imageFilename = files[0].name;
        }
    });
};

onMounted(() => {
    block.on('created', () => {
        getFilemanager().show();
    });

    block.operation('chose-image', (data, success, fail) => {
        getFilemanager().show();
        success();
    });
});


const __returned__ = { computed, onMounted, defineProps, inject, props, blocks, block, Tulia, filemanager, imageLink, getFilemanager }
Object.defineProperty(__returned__, '__isScriptSetup', { enumerable: false, value: true })
return __returned__
}

});

/***/ }),

/***/ "./src/js/blocks/ImageBlock/Render.vue":
/*!*********************************************!*\
  !*** ./src/js/blocks/ImageBlock/Render.vue ***!
  \*********************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _Render_vue_vue_type_template_id_4e13f272__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./Render.vue?vue&type=template&id=4e13f272 */ "./src/js/blocks/ImageBlock/Render.vue?vue&type=template&id=4e13f272");
/* harmony import */ var _Render_vue_vue_type_script_setup_true_lang_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./Render.vue?vue&type=script&setup=true&lang=js */ "./src/js/blocks/ImageBlock/Render.vue?vue&type=script&setup=true&lang=js");
/* harmony import */ var _home_adam_projects_tuliacms_core_src_Cms_TuliaEditor_Infrastructure_Framework_Resources_public_tulia_editor_node_modules_vue_loader_dist_exportHelper_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./node_modules/vue-loader/dist/exportHelper.js */ "./node_modules/vue-loader/dist/exportHelper.js");




;
const __exports__ = /*#__PURE__*/(0,_home_adam_projects_tuliacms_core_src_Cms_TuliaEditor_Infrastructure_Framework_Resources_public_tulia_editor_node_modules_vue_loader_dist_exportHelper_js__WEBPACK_IMPORTED_MODULE_2__["default"])(_Render_vue_vue_type_script_setup_true_lang_js__WEBPACK_IMPORTED_MODULE_1__["default"], [['render',_Render_vue_vue_type_template_id_4e13f272__WEBPACK_IMPORTED_MODULE_0__.render],['__file',"src/js/blocks/ImageBlock/Render.vue"]])
/* hot reload */
if (false) {}


/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (__exports__);

/***/ }),

/***/ "./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[4].use[0]!./src/js/blocks/ImageBlock/Render.vue?vue&type=script&setup=true&lang=js":
/*!****************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[4].use[0]!./src/js/blocks/ImageBlock/Render.vue?vue&type=script&setup=true&lang=js ***!
  \****************************************************************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = ({
  props: (__webpack_require__(/*! ./props.js */ "./src/js/blocks/ImageBlock/props.js")["default"]),
  setup(__props, { expose }) {
  expose();

const props = __props

const { defineProps, inject } = __webpack_require__(/*! vue */ "vue");

/****************
 * Prepare block
 ****************/

const blocks = inject('blocks');
const block = blocks.render('core-imageblock', props);


const __returned__ = { defineProps, inject, props, blocks, block }
Object.defineProperty(__returned__, '__isScriptSetup', { enumerable: false, value: true })
return __returned__
}

});

/***/ }),

/***/ "./src/js/blocks/TextBlock/Editor.vue":
/*!********************************************!*\
  !*** ./src/js/blocks/TextBlock/Editor.vue ***!
  \********************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _Editor_vue_vue_type_template_id_45cbd610__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./Editor.vue?vue&type=template&id=45cbd610 */ "./src/js/blocks/TextBlock/Editor.vue?vue&type=template&id=45cbd610");
/* harmony import */ var _Editor_vue_vue_type_script_setup_true_lang_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./Editor.vue?vue&type=script&setup=true&lang=js */ "./src/js/blocks/TextBlock/Editor.vue?vue&type=script&setup=true&lang=js");
/* harmony import */ var _home_adam_projects_tuliacms_core_src_Cms_TuliaEditor_Infrastructure_Framework_Resources_public_tulia_editor_node_modules_vue_loader_dist_exportHelper_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./node_modules/vue-loader/dist/exportHelper.js */ "./node_modules/vue-loader/dist/exportHelper.js");




;
const __exports__ = /*#__PURE__*/(0,_home_adam_projects_tuliacms_core_src_Cms_TuliaEditor_Infrastructure_Framework_Resources_public_tulia_editor_node_modules_vue_loader_dist_exportHelper_js__WEBPACK_IMPORTED_MODULE_2__["default"])(_Editor_vue_vue_type_script_setup_true_lang_js__WEBPACK_IMPORTED_MODULE_1__["default"], [['render',_Editor_vue_vue_type_template_id_45cbd610__WEBPACK_IMPORTED_MODULE_0__.render],['__file',"src/js/blocks/TextBlock/Editor.vue"]])
/* hot reload */
if (false) {}


/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (__exports__);

/***/ }),

/***/ "./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[4].use[0]!./src/js/blocks/TextBlock/Editor.vue?vue&type=script&setup=true&lang=js":
/*!***************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[4].use[0]!./src/js/blocks/TextBlock/Editor.vue?vue&type=script&setup=true&lang=js ***!
  \***************************************************************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = ({
  props: (__webpack_require__(/*! ./props.js */ "./src/js/blocks/TextBlock/props.js")["default"]),
  setup(__props, { expose }) {
  expose();

const props = __props

const WysiwygEditor = (__webpack_require__(/*! extensions/WysiwygEditor.vue */ "./src/js/extensions/WysiwygEditor.vue")["default"]);
const { defineProps, inject } = __webpack_require__(/*! vue */ "vue");

/****************
 * Prepare block
 ****************/

const blocks = inject('blocks');
const block = blocks.editor('core-textblock', props);

const __returned__ = { WysiwygEditor, defineProps, inject, props, blocks, block }
Object.defineProperty(__returned__, '__isScriptSetup', { enumerable: false, value: true })
return __returned__
}

});

/***/ }),

/***/ "./src/js/blocks/TextBlock/Manager.vue":
/*!*********************************************!*\
  !*** ./src/js/blocks/TextBlock/Manager.vue ***!
  \*********************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _Manager_vue_vue_type_template_id_064e52b2__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./Manager.vue?vue&type=template&id=064e52b2 */ "./src/js/blocks/TextBlock/Manager.vue?vue&type=template&id=064e52b2");
/* harmony import */ var _Manager_vue_vue_type_script_setup_true_lang_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./Manager.vue?vue&type=script&setup=true&lang=js */ "./src/js/blocks/TextBlock/Manager.vue?vue&type=script&setup=true&lang=js");
/* harmony import */ var _home_adam_projects_tuliacms_core_src_Cms_TuliaEditor_Infrastructure_Framework_Resources_public_tulia_editor_node_modules_vue_loader_dist_exportHelper_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./node_modules/vue-loader/dist/exportHelper.js */ "./node_modules/vue-loader/dist/exportHelper.js");




;
const __exports__ = /*#__PURE__*/(0,_home_adam_projects_tuliacms_core_src_Cms_TuliaEditor_Infrastructure_Framework_Resources_public_tulia_editor_node_modules_vue_loader_dist_exportHelper_js__WEBPACK_IMPORTED_MODULE_2__["default"])(_Manager_vue_vue_type_script_setup_true_lang_js__WEBPACK_IMPORTED_MODULE_1__["default"], [['render',_Manager_vue_vue_type_template_id_064e52b2__WEBPACK_IMPORTED_MODULE_0__.render],['__file',"src/js/blocks/TextBlock/Manager.vue"]])
/* hot reload */
if (false) {}


/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (__exports__);

/***/ }),

/***/ "./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[4].use[0]!./src/js/blocks/TextBlock/Manager.vue?vue&type=script&setup=true&lang=js":
/*!****************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[4].use[0]!./src/js/blocks/TextBlock/Manager.vue?vue&type=script&setup=true&lang=js ***!
  \****************************************************************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = ({
  props: (__webpack_require__(/*! ./props.js */ "./src/js/blocks/TextBlock/props.js")["default"]),
  setup(__props, { expose }) {
  expose();

const props = __props

const { computed, onMounted, defineProps, inject } = __webpack_require__(/*! vue */ "vue");


/****************
 * Prepare block
 ****************/

const blocks = inject('blocks');
const block = blocks.manager('core-textblock', props);



/**************
 * Block logic
 **************/

const __returned__ = { computed, onMounted, defineProps, inject, props, blocks, block }
Object.defineProperty(__returned__, '__isScriptSetup', { enumerable: false, value: true })
return __returned__
}

});

/***/ }),

/***/ "./src/js/blocks/TextBlock/Render.vue":
/*!********************************************!*\
  !*** ./src/js/blocks/TextBlock/Render.vue ***!
  \********************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _Render_vue_vue_type_template_id_fdea0ebe__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./Render.vue?vue&type=template&id=fdea0ebe */ "./src/js/blocks/TextBlock/Render.vue?vue&type=template&id=fdea0ebe");
/* harmony import */ var _Render_vue_vue_type_script_setup_true_lang_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./Render.vue?vue&type=script&setup=true&lang=js */ "./src/js/blocks/TextBlock/Render.vue?vue&type=script&setup=true&lang=js");
/* harmony import */ var _home_adam_projects_tuliacms_core_src_Cms_TuliaEditor_Infrastructure_Framework_Resources_public_tulia_editor_node_modules_vue_loader_dist_exportHelper_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./node_modules/vue-loader/dist/exportHelper.js */ "./node_modules/vue-loader/dist/exportHelper.js");




;
const __exports__ = /*#__PURE__*/(0,_home_adam_projects_tuliacms_core_src_Cms_TuliaEditor_Infrastructure_Framework_Resources_public_tulia_editor_node_modules_vue_loader_dist_exportHelper_js__WEBPACK_IMPORTED_MODULE_2__["default"])(_Render_vue_vue_type_script_setup_true_lang_js__WEBPACK_IMPORTED_MODULE_1__["default"], [['render',_Render_vue_vue_type_template_id_fdea0ebe__WEBPACK_IMPORTED_MODULE_0__.render],['__file',"src/js/blocks/TextBlock/Render.vue"]])
/* hot reload */
if (false) {}


/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (__exports__);

/***/ }),

/***/ "./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[4].use[0]!./src/js/blocks/TextBlock/Render.vue?vue&type=script&setup=true&lang=js":
/*!***************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[4].use[0]!./src/js/blocks/TextBlock/Render.vue?vue&type=script&setup=true&lang=js ***!
  \***************************************************************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = ({
  props: (__webpack_require__(/*! ./props.js */ "./src/js/blocks/TextBlock/props.js")["default"]),
  setup(__props, { expose }) {
  expose();

const props = __props

const { defineProps, inject } = __webpack_require__(/*! vue */ "vue");

/****************
 * Prepare block
 ****************/

const blocks = inject('blocks');
const block = blocks.render('core-textblock', props);

const __returned__ = { defineProps, inject, props, blocks, block }
Object.defineProperty(__returned__, '__isScriptSetup', { enumerable: false, value: true })
return __returned__
}

});

/***/ }),

/***/ "./src/js/extensions/WysiwygEditor.vue":
/*!*********************************************!*\
  !*** ./src/js/extensions/WysiwygEditor.vue ***!
  \*********************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
/* harmony import */ var _WysiwygEditor_vue_vue_type_template_id_4a713d3c__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./WysiwygEditor.vue?vue&type=template&id=4a713d3c */ "./src/js/extensions/WysiwygEditor.vue?vue&type=template&id=4a713d3c");
/* harmony import */ var _WysiwygEditor_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_1__ = __webpack_require__(/*! ./WysiwygEditor.vue?vue&type=script&lang=js */ "./src/js/extensions/WysiwygEditor.vue?vue&type=script&lang=js");
/* harmony import */ var _home_adam_projects_tuliacms_core_src_Cms_TuliaEditor_Infrastructure_Framework_Resources_public_tulia_editor_node_modules_vue_loader_dist_exportHelper_js__WEBPACK_IMPORTED_MODULE_2__ = __webpack_require__(/*! ./node_modules/vue-loader/dist/exportHelper.js */ "./node_modules/vue-loader/dist/exportHelper.js");




;
const __exports__ = /*#__PURE__*/(0,_home_adam_projects_tuliacms_core_src_Cms_TuliaEditor_Infrastructure_Framework_Resources_public_tulia_editor_node_modules_vue_loader_dist_exportHelper_js__WEBPACK_IMPORTED_MODULE_2__["default"])(_WysiwygEditor_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_1__["default"], [['render',_WysiwygEditor_vue_vue_type_template_id_4a713d3c__WEBPACK_IMPORTED_MODULE_0__.render],['__file',"src/js/extensions/WysiwygEditor.vue"]])
/* hot reload */
if (false) {}


/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (__exports__);

/***/ }),

/***/ "./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[4].use[0]!./src/js/extensions/WysiwygEditor.vue?vue&type=script&lang=js":
/*!*****************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[4].use[0]!./src/js/extensions/WysiwygEditor.vue?vue&type=script&lang=js ***!
  \*****************************************************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });

const ClassObserver = (__webpack_require__(/*! shared/Utils/ClassObserver.js */ "./src/js/shared/Utils/ClassObserver.js")["default"]);

/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = ({
    props: {
        modelValue: {
            required: true,
            default: ''
        },
    },
    inject: ['eventDispatcher', 'messenger'],
    data () {
        return {
            quill: null,
        };
    },
    mounted () {
        let editorContent = this.$el.getElementsByClassName('editor-container')[0];
        let editorToolbar = this.$el.getElementsByClassName('editor-toolbar')[0];
        editorContent.innerHTML = this.modelValue ?? null;

        let quill = new Quill(editorContent, {
            theme: 'bubble',
            placeholder: 'Start typing...',
            modules: {
                toolbar: editorToolbar,
            }
        });
        quill.on('text-change', () => {
            this.$emit('update:modelValue', quill.root.innerHTML);
            this.eventDispatcher.emit('block.inner.updated');
        });

        this.quill = quill;

        /*new ClassObserver(quill.theme.tooltip.root, 'ql-hidden', (currentClass) => {
            if(currentClass) {
                this.$eventDispatcher.emit('structure.selection.show');
            } else {
                this.$eventDispatcher.emit('structure.selection.hide');
            }
        });*/

        this.messenger.on('editor.click.outside', () => {
            quill.theme.tooltip.root.classList.add('ql-hidden');
        });
    },
    watch: {
        modelValue (val) {
            if (this.quill.root.innerHTML === val) {
                return;
            }

            this.quill.root.innerHTML = val ? val : '';
        }
    },
    /*destroyed () {
        $(this.$el).chosen('destroy');
    }*/
});


/***/ }),

/***/ "./src/js/blocks/ImageBlock/Editor.vue?vue&type=style&index=0&id=3505231e&scoped=true&lang=css":
/*!*****************************************************************************************************!*\
  !*** ./src/js/blocks/ImageBlock/Editor.vue?vue&type=style&index=0&id=3505231e&scoped=true&lang=css ***!
  \*****************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony import */ var _node_modules_mini_css_extract_plugin_dist_loader_js_node_modules_css_loader_dist_cjs_js_node_modules_vue_loader_dist_stylePostLoader_js_node_modules_vue_loader_dist_index_js_ruleSet_1_rules_4_use_0_Editor_vue_vue_type_style_index_0_id_3505231e_scoped_true_lang_css__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../node_modules/mini-css-extract-plugin/dist/loader.js!../../../../node_modules/css-loader/dist/cjs.js!../../../../node_modules/vue-loader/dist/stylePostLoader.js!../../../../node_modules/vue-loader/dist/index.js??ruleSet[1].rules[4].use[0]!./Editor.vue?vue&type=style&index=0&id=3505231e&scoped=true&lang=css */ "./node_modules/mini-css-extract-plugin/dist/loader.js!./node_modules/css-loader/dist/cjs.js!./node_modules/vue-loader/dist/stylePostLoader.js!./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[4].use[0]!./src/js/blocks/ImageBlock/Editor.vue?vue&type=style&index=0&id=3505231e&scoped=true&lang=css");


/***/ }),

/***/ "./src/js/Components/Editor/Rendering/Canvas.vue?vue&type=script&setup=true&lang=js":
/*!******************************************************************************************!*\
  !*** ./src/js/Components/Editor/Rendering/Canvas.vue?vue&type=script&setup=true&lang=js ***!
  \******************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* reexport safe */ _node_modules_vue_loader_dist_index_js_ruleSet_1_rules_4_use_0_Canvas_vue_vue_type_script_setup_true_lang_js__WEBPACK_IMPORTED_MODULE_0__["default"])
/* harmony export */ });
/* harmony import */ var _node_modules_vue_loader_dist_index_js_ruleSet_1_rules_4_use_0_Canvas_vue_vue_type_script_setup_true_lang_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/vue-loader/dist/index.js??ruleSet[1].rules[4].use[0]!./Canvas.vue?vue&type=script&setup=true&lang=js */ "./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[4].use[0]!./src/js/Components/Editor/Rendering/Canvas.vue?vue&type=script&setup=true&lang=js");
 

/***/ }),

/***/ "./src/js/Components/Editor/Root.vue?vue&type=script&setup=true&lang=js":
/*!******************************************************************************!*\
  !*** ./src/js/Components/Editor/Root.vue?vue&type=script&setup=true&lang=js ***!
  \******************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* reexport safe */ _node_modules_vue_loader_dist_index_js_ruleSet_1_rules_4_use_0_Root_vue_vue_type_script_setup_true_lang_js__WEBPACK_IMPORTED_MODULE_0__["default"])
/* harmony export */ });
/* harmony import */ var _node_modules_vue_loader_dist_index_js_ruleSet_1_rules_4_use_0_Root_vue_vue_type_script_setup_true_lang_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../node_modules/vue-loader/dist/index.js??ruleSet[1].rules[4].use[0]!./Root.vue?vue&type=script&setup=true&lang=js */ "./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[4].use[0]!./src/js/Components/Editor/Root.vue?vue&type=script&setup=true&lang=js");
 

/***/ }),

/***/ "./src/js/Components/Editor/Structure/Block.vue?vue&type=script&lang=js":
/*!******************************************************************************!*\
  !*** ./src/js/Components/Editor/Structure/Block.vue?vue&type=script&lang=js ***!
  \******************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* reexport safe */ _node_modules_vue_loader_dist_index_js_ruleSet_1_rules_4_use_0_Block_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_0__["default"])
/* harmony export */ });
/* harmony import */ var _node_modules_vue_loader_dist_index_js_ruleSet_1_rules_4_use_0_Block_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/vue-loader/dist/index.js??ruleSet[1].rules[4].use[0]!./Block.vue?vue&type=script&lang=js */ "./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[4].use[0]!./src/js/Components/Editor/Structure/Block.vue?vue&type=script&lang=js");
 

/***/ }),

/***/ "./src/js/Components/Editor/Structure/Column.vue?vue&type=script&lang=js":
/*!*******************************************************************************!*\
  !*** ./src/js/Components/Editor/Structure/Column.vue?vue&type=script&lang=js ***!
  \*******************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* reexport safe */ _node_modules_vue_loader_dist_index_js_ruleSet_1_rules_4_use_0_Column_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_0__["default"])
/* harmony export */ });
/* harmony import */ var _node_modules_vue_loader_dist_index_js_ruleSet_1_rules_4_use_0_Column_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/vue-loader/dist/index.js??ruleSet[1].rules[4].use[0]!./Column.vue?vue&type=script&lang=js */ "./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[4].use[0]!./src/js/Components/Editor/Structure/Column.vue?vue&type=script&lang=js");
 

/***/ }),

/***/ "./src/js/Components/Editor/Structure/Row.vue?vue&type=script&lang=js":
/*!****************************************************************************!*\
  !*** ./src/js/Components/Editor/Structure/Row.vue?vue&type=script&lang=js ***!
  \****************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* reexport safe */ _node_modules_vue_loader_dist_index_js_ruleSet_1_rules_4_use_0_Row_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_0__["default"])
/* harmony export */ });
/* harmony import */ var _node_modules_vue_loader_dist_index_js_ruleSet_1_rules_4_use_0_Row_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/vue-loader/dist/index.js??ruleSet[1].rules[4].use[0]!./Row.vue?vue&type=script&lang=js */ "./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[4].use[0]!./src/js/Components/Editor/Structure/Row.vue?vue&type=script&lang=js");
 

/***/ }),

/***/ "./src/js/Components/Editor/Structure/Section.vue?vue&type=script&lang=js":
/*!********************************************************************************!*\
  !*** ./src/js/Components/Editor/Structure/Section.vue?vue&type=script&lang=js ***!
  \********************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* reexport safe */ _node_modules_vue_loader_dist_index_js_ruleSet_1_rules_4_use_0_Section_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_0__["default"])
/* harmony export */ });
/* harmony import */ var _node_modules_vue_loader_dist_index_js_ruleSet_1_rules_4_use_0_Section_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/vue-loader/dist/index.js??ruleSet[1].rules[4].use[0]!./Section.vue?vue&type=script&lang=js */ "./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[4].use[0]!./src/js/Components/Editor/Structure/Section.vue?vue&type=script&lang=js");
 

/***/ }),

/***/ "./src/js/Components/Editor/Structure/Structure.vue?vue&type=script&lang=js":
/*!**********************************************************************************!*\
  !*** ./src/js/Components/Editor/Structure/Structure.vue?vue&type=script&lang=js ***!
  \**********************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* reexport safe */ _node_modules_vue_loader_dist_index_js_ruleSet_1_rules_4_use_0_Structure_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_0__["default"])
/* harmony export */ });
/* harmony import */ var _node_modules_vue_loader_dist_index_js_ruleSet_1_rules_4_use_0_Structure_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/vue-loader/dist/index.js??ruleSet[1].rules[4].use[0]!./Structure.vue?vue&type=script&lang=js */ "./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[4].use[0]!./src/js/Components/Editor/Structure/Structure.vue?vue&type=script&lang=js");
 

/***/ }),

/***/ "./src/js/blocks/ImageBlock/Editor.vue?vue&type=script&setup=true&lang=js":
/*!********************************************************************************!*\
  !*** ./src/js/blocks/ImageBlock/Editor.vue?vue&type=script&setup=true&lang=js ***!
  \********************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* reexport safe */ _node_modules_vue_loader_dist_index_js_ruleSet_1_rules_4_use_0_Editor_vue_vue_type_script_setup_true_lang_js__WEBPACK_IMPORTED_MODULE_0__["default"])
/* harmony export */ });
/* harmony import */ var _node_modules_vue_loader_dist_index_js_ruleSet_1_rules_4_use_0_Editor_vue_vue_type_script_setup_true_lang_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../node_modules/vue-loader/dist/index.js??ruleSet[1].rules[4].use[0]!./Editor.vue?vue&type=script&setup=true&lang=js */ "./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[4].use[0]!./src/js/blocks/ImageBlock/Editor.vue?vue&type=script&setup=true&lang=js");
 

/***/ }),

/***/ "./src/js/blocks/ImageBlock/Manager.vue?vue&type=script&setup=true&lang=js":
/*!*********************************************************************************!*\
  !*** ./src/js/blocks/ImageBlock/Manager.vue?vue&type=script&setup=true&lang=js ***!
  \*********************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* reexport safe */ _node_modules_vue_loader_dist_index_js_ruleSet_1_rules_4_use_0_Manager_vue_vue_type_script_setup_true_lang_js__WEBPACK_IMPORTED_MODULE_0__["default"])
/* harmony export */ });
/* harmony import */ var _node_modules_vue_loader_dist_index_js_ruleSet_1_rules_4_use_0_Manager_vue_vue_type_script_setup_true_lang_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../node_modules/vue-loader/dist/index.js??ruleSet[1].rules[4].use[0]!./Manager.vue?vue&type=script&setup=true&lang=js */ "./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[4].use[0]!./src/js/blocks/ImageBlock/Manager.vue?vue&type=script&setup=true&lang=js");
 

/***/ }),

/***/ "./src/js/blocks/ImageBlock/Render.vue?vue&type=script&setup=true&lang=js":
/*!********************************************************************************!*\
  !*** ./src/js/blocks/ImageBlock/Render.vue?vue&type=script&setup=true&lang=js ***!
  \********************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* reexport safe */ _node_modules_vue_loader_dist_index_js_ruleSet_1_rules_4_use_0_Render_vue_vue_type_script_setup_true_lang_js__WEBPACK_IMPORTED_MODULE_0__["default"])
/* harmony export */ });
/* harmony import */ var _node_modules_vue_loader_dist_index_js_ruleSet_1_rules_4_use_0_Render_vue_vue_type_script_setup_true_lang_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../node_modules/vue-loader/dist/index.js??ruleSet[1].rules[4].use[0]!./Render.vue?vue&type=script&setup=true&lang=js */ "./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[4].use[0]!./src/js/blocks/ImageBlock/Render.vue?vue&type=script&setup=true&lang=js");
 

/***/ }),

/***/ "./src/js/blocks/TextBlock/Editor.vue?vue&type=script&setup=true&lang=js":
/*!*******************************************************************************!*\
  !*** ./src/js/blocks/TextBlock/Editor.vue?vue&type=script&setup=true&lang=js ***!
  \*******************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* reexport safe */ _node_modules_vue_loader_dist_index_js_ruleSet_1_rules_4_use_0_Editor_vue_vue_type_script_setup_true_lang_js__WEBPACK_IMPORTED_MODULE_0__["default"])
/* harmony export */ });
/* harmony import */ var _node_modules_vue_loader_dist_index_js_ruleSet_1_rules_4_use_0_Editor_vue_vue_type_script_setup_true_lang_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../node_modules/vue-loader/dist/index.js??ruleSet[1].rules[4].use[0]!./Editor.vue?vue&type=script&setup=true&lang=js */ "./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[4].use[0]!./src/js/blocks/TextBlock/Editor.vue?vue&type=script&setup=true&lang=js");
 

/***/ }),

/***/ "./src/js/blocks/TextBlock/Manager.vue?vue&type=script&setup=true&lang=js":
/*!********************************************************************************!*\
  !*** ./src/js/blocks/TextBlock/Manager.vue?vue&type=script&setup=true&lang=js ***!
  \********************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* reexport safe */ _node_modules_vue_loader_dist_index_js_ruleSet_1_rules_4_use_0_Manager_vue_vue_type_script_setup_true_lang_js__WEBPACK_IMPORTED_MODULE_0__["default"])
/* harmony export */ });
/* harmony import */ var _node_modules_vue_loader_dist_index_js_ruleSet_1_rules_4_use_0_Manager_vue_vue_type_script_setup_true_lang_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../node_modules/vue-loader/dist/index.js??ruleSet[1].rules[4].use[0]!./Manager.vue?vue&type=script&setup=true&lang=js */ "./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[4].use[0]!./src/js/blocks/TextBlock/Manager.vue?vue&type=script&setup=true&lang=js");
 

/***/ }),

/***/ "./src/js/blocks/TextBlock/Render.vue?vue&type=script&setup=true&lang=js":
/*!*******************************************************************************!*\
  !*** ./src/js/blocks/TextBlock/Render.vue?vue&type=script&setup=true&lang=js ***!
  \*******************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* reexport safe */ _node_modules_vue_loader_dist_index_js_ruleSet_1_rules_4_use_0_Render_vue_vue_type_script_setup_true_lang_js__WEBPACK_IMPORTED_MODULE_0__["default"])
/* harmony export */ });
/* harmony import */ var _node_modules_vue_loader_dist_index_js_ruleSet_1_rules_4_use_0_Render_vue_vue_type_script_setup_true_lang_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../node_modules/vue-loader/dist/index.js??ruleSet[1].rules[4].use[0]!./Render.vue?vue&type=script&setup=true&lang=js */ "./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[4].use[0]!./src/js/blocks/TextBlock/Render.vue?vue&type=script&setup=true&lang=js");
 

/***/ }),

/***/ "./src/js/extensions/WysiwygEditor.vue?vue&type=script&lang=js":
/*!*********************************************************************!*\
  !*** ./src/js/extensions/WysiwygEditor.vue?vue&type=script&lang=js ***!
  \*********************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* reexport safe */ _node_modules_vue_loader_dist_index_js_ruleSet_1_rules_4_use_0_WysiwygEditor_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_0__["default"])
/* harmony export */ });
/* harmony import */ var _node_modules_vue_loader_dist_index_js_ruleSet_1_rules_4_use_0_WysiwygEditor_vue_vue_type_script_lang_js__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../node_modules/vue-loader/dist/index.js??ruleSet[1].rules[4].use[0]!./WysiwygEditor.vue?vue&type=script&lang=js */ "./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[4].use[0]!./src/js/extensions/WysiwygEditor.vue?vue&type=script&lang=js");
 

/***/ }),

/***/ "./src/js/Components/Editor/Rendering/Canvas.vue?vue&type=template&id=27d57861":
/*!*************************************************************************************!*\
  !*** ./src/js/Components/Editor/Rendering/Canvas.vue?vue&type=template&id=27d57861 ***!
  \*************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "render": () => (/* reexport safe */ _node_modules_vue_loader_dist_templateLoader_js_ruleSet_1_rules_1_node_modules_vue_loader_dist_index_js_ruleSet_1_rules_4_use_0_Canvas_vue_vue_type_template_id_27d57861__WEBPACK_IMPORTED_MODULE_0__.render)
/* harmony export */ });
/* harmony import */ var _node_modules_vue_loader_dist_templateLoader_js_ruleSet_1_rules_1_node_modules_vue_loader_dist_index_js_ruleSet_1_rules_4_use_0_Canvas_vue_vue_type_template_id_27d57861__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[1]!../../../../../node_modules/vue-loader/dist/index.js??ruleSet[1].rules[4].use[0]!./Canvas.vue?vue&type=template&id=27d57861 */ "./node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[1]!./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[4].use[0]!./src/js/Components/Editor/Rendering/Canvas.vue?vue&type=template&id=27d57861");


/***/ }),

/***/ "./src/js/Components/Editor/Root.vue?vue&type=template&id=1386ac68":
/*!*************************************************************************!*\
  !*** ./src/js/Components/Editor/Root.vue?vue&type=template&id=1386ac68 ***!
  \*************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "render": () => (/* reexport safe */ _node_modules_vue_loader_dist_templateLoader_js_ruleSet_1_rules_1_node_modules_vue_loader_dist_index_js_ruleSet_1_rules_4_use_0_Root_vue_vue_type_template_id_1386ac68__WEBPACK_IMPORTED_MODULE_0__.render)
/* harmony export */ });
/* harmony import */ var _node_modules_vue_loader_dist_templateLoader_js_ruleSet_1_rules_1_node_modules_vue_loader_dist_index_js_ruleSet_1_rules_4_use_0_Root_vue_vue_type_template_id_1386ac68__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[1]!../../../../node_modules/vue-loader/dist/index.js??ruleSet[1].rules[4].use[0]!./Root.vue?vue&type=template&id=1386ac68 */ "./node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[1]!./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[4].use[0]!./src/js/Components/Editor/Root.vue?vue&type=template&id=1386ac68");


/***/ }),

/***/ "./src/js/Components/Editor/Structure/Block.vue?vue&type=template&id=0de2ec3b":
/*!************************************************************************************!*\
  !*** ./src/js/Components/Editor/Structure/Block.vue?vue&type=template&id=0de2ec3b ***!
  \************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "render": () => (/* reexport safe */ _node_modules_vue_loader_dist_templateLoader_js_ruleSet_1_rules_1_node_modules_vue_loader_dist_index_js_ruleSet_1_rules_4_use_0_Block_vue_vue_type_template_id_0de2ec3b__WEBPACK_IMPORTED_MODULE_0__.render)
/* harmony export */ });
/* harmony import */ var _node_modules_vue_loader_dist_templateLoader_js_ruleSet_1_rules_1_node_modules_vue_loader_dist_index_js_ruleSet_1_rules_4_use_0_Block_vue_vue_type_template_id_0de2ec3b__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[1]!../../../../../node_modules/vue-loader/dist/index.js??ruleSet[1].rules[4].use[0]!./Block.vue?vue&type=template&id=0de2ec3b */ "./node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[1]!./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[4].use[0]!./src/js/Components/Editor/Structure/Block.vue?vue&type=template&id=0de2ec3b");


/***/ }),

/***/ "./src/js/Components/Editor/Structure/Column.vue?vue&type=template&id=32264bd0":
/*!*************************************************************************************!*\
  !*** ./src/js/Components/Editor/Structure/Column.vue?vue&type=template&id=32264bd0 ***!
  \*************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "render": () => (/* reexport safe */ _node_modules_vue_loader_dist_templateLoader_js_ruleSet_1_rules_1_node_modules_vue_loader_dist_index_js_ruleSet_1_rules_4_use_0_Column_vue_vue_type_template_id_32264bd0__WEBPACK_IMPORTED_MODULE_0__.render)
/* harmony export */ });
/* harmony import */ var _node_modules_vue_loader_dist_templateLoader_js_ruleSet_1_rules_1_node_modules_vue_loader_dist_index_js_ruleSet_1_rules_4_use_0_Column_vue_vue_type_template_id_32264bd0__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[1]!../../../../../node_modules/vue-loader/dist/index.js??ruleSet[1].rules[4].use[0]!./Column.vue?vue&type=template&id=32264bd0 */ "./node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[1]!./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[4].use[0]!./src/js/Components/Editor/Structure/Column.vue?vue&type=template&id=32264bd0");


/***/ }),

/***/ "./src/js/Components/Editor/Structure/Row.vue?vue&type=template&id=7cc13ef0":
/*!**********************************************************************************!*\
  !*** ./src/js/Components/Editor/Structure/Row.vue?vue&type=template&id=7cc13ef0 ***!
  \**********************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "render": () => (/* reexport safe */ _node_modules_vue_loader_dist_templateLoader_js_ruleSet_1_rules_1_node_modules_vue_loader_dist_index_js_ruleSet_1_rules_4_use_0_Row_vue_vue_type_template_id_7cc13ef0__WEBPACK_IMPORTED_MODULE_0__.render)
/* harmony export */ });
/* harmony import */ var _node_modules_vue_loader_dist_templateLoader_js_ruleSet_1_rules_1_node_modules_vue_loader_dist_index_js_ruleSet_1_rules_4_use_0_Row_vue_vue_type_template_id_7cc13ef0__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[1]!../../../../../node_modules/vue-loader/dist/index.js??ruleSet[1].rules[4].use[0]!./Row.vue?vue&type=template&id=7cc13ef0 */ "./node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[1]!./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[4].use[0]!./src/js/Components/Editor/Structure/Row.vue?vue&type=template&id=7cc13ef0");


/***/ }),

/***/ "./src/js/Components/Editor/Structure/Section.vue?vue&type=template&id=4d532d13":
/*!**************************************************************************************!*\
  !*** ./src/js/Components/Editor/Structure/Section.vue?vue&type=template&id=4d532d13 ***!
  \**************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "render": () => (/* reexport safe */ _node_modules_vue_loader_dist_templateLoader_js_ruleSet_1_rules_1_node_modules_vue_loader_dist_index_js_ruleSet_1_rules_4_use_0_Section_vue_vue_type_template_id_4d532d13__WEBPACK_IMPORTED_MODULE_0__.render)
/* harmony export */ });
/* harmony import */ var _node_modules_vue_loader_dist_templateLoader_js_ruleSet_1_rules_1_node_modules_vue_loader_dist_index_js_ruleSet_1_rules_4_use_0_Section_vue_vue_type_template_id_4d532d13__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[1]!../../../../../node_modules/vue-loader/dist/index.js??ruleSet[1].rules[4].use[0]!./Section.vue?vue&type=template&id=4d532d13 */ "./node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[1]!./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[4].use[0]!./src/js/Components/Editor/Structure/Section.vue?vue&type=template&id=4d532d13");


/***/ }),

/***/ "./src/js/Components/Editor/Structure/Structure.vue?vue&type=template&id=4d3491be":
/*!****************************************************************************************!*\
  !*** ./src/js/Components/Editor/Structure/Structure.vue?vue&type=template&id=4d3491be ***!
  \****************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "render": () => (/* reexport safe */ _node_modules_vue_loader_dist_templateLoader_js_ruleSet_1_rules_1_node_modules_vue_loader_dist_index_js_ruleSet_1_rules_4_use_0_Structure_vue_vue_type_template_id_4d3491be__WEBPACK_IMPORTED_MODULE_0__.render)
/* harmony export */ });
/* harmony import */ var _node_modules_vue_loader_dist_templateLoader_js_ruleSet_1_rules_1_node_modules_vue_loader_dist_index_js_ruleSet_1_rules_4_use_0_Structure_vue_vue_type_template_id_4d3491be__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../../node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[1]!../../../../../node_modules/vue-loader/dist/index.js??ruleSet[1].rules[4].use[0]!./Structure.vue?vue&type=template&id=4d3491be */ "./node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[1]!./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[4].use[0]!./src/js/Components/Editor/Structure/Structure.vue?vue&type=template&id=4d3491be");


/***/ }),

/***/ "./src/js/blocks/ImageBlock/Editor.vue?vue&type=template&id=3505231e&scoped=true":
/*!***************************************************************************************!*\
  !*** ./src/js/blocks/ImageBlock/Editor.vue?vue&type=template&id=3505231e&scoped=true ***!
  \***************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "render": () => (/* reexport safe */ _node_modules_vue_loader_dist_templateLoader_js_ruleSet_1_rules_1_node_modules_vue_loader_dist_index_js_ruleSet_1_rules_4_use_0_Editor_vue_vue_type_template_id_3505231e_scoped_true__WEBPACK_IMPORTED_MODULE_0__.render)
/* harmony export */ });
/* harmony import */ var _node_modules_vue_loader_dist_templateLoader_js_ruleSet_1_rules_1_node_modules_vue_loader_dist_index_js_ruleSet_1_rules_4_use_0_Editor_vue_vue_type_template_id_3505231e_scoped_true__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[1]!../../../../node_modules/vue-loader/dist/index.js??ruleSet[1].rules[4].use[0]!./Editor.vue?vue&type=template&id=3505231e&scoped=true */ "./node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[1]!./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[4].use[0]!./src/js/blocks/ImageBlock/Editor.vue?vue&type=template&id=3505231e&scoped=true");


/***/ }),

/***/ "./src/js/blocks/ImageBlock/Manager.vue?vue&type=template&id=a875ed68":
/*!****************************************************************************!*\
  !*** ./src/js/blocks/ImageBlock/Manager.vue?vue&type=template&id=a875ed68 ***!
  \****************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "render": () => (/* reexport safe */ _node_modules_vue_loader_dist_templateLoader_js_ruleSet_1_rules_1_node_modules_vue_loader_dist_index_js_ruleSet_1_rules_4_use_0_Manager_vue_vue_type_template_id_a875ed68__WEBPACK_IMPORTED_MODULE_0__.render)
/* harmony export */ });
/* harmony import */ var _node_modules_vue_loader_dist_templateLoader_js_ruleSet_1_rules_1_node_modules_vue_loader_dist_index_js_ruleSet_1_rules_4_use_0_Manager_vue_vue_type_template_id_a875ed68__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[1]!../../../../node_modules/vue-loader/dist/index.js??ruleSet[1].rules[4].use[0]!./Manager.vue?vue&type=template&id=a875ed68 */ "./node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[1]!./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[4].use[0]!./src/js/blocks/ImageBlock/Manager.vue?vue&type=template&id=a875ed68");


/***/ }),

/***/ "./src/js/blocks/ImageBlock/Render.vue?vue&type=template&id=4e13f272":
/*!***************************************************************************!*\
  !*** ./src/js/blocks/ImageBlock/Render.vue?vue&type=template&id=4e13f272 ***!
  \***************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "render": () => (/* reexport safe */ _node_modules_vue_loader_dist_templateLoader_js_ruleSet_1_rules_1_node_modules_vue_loader_dist_index_js_ruleSet_1_rules_4_use_0_Render_vue_vue_type_template_id_4e13f272__WEBPACK_IMPORTED_MODULE_0__.render)
/* harmony export */ });
/* harmony import */ var _node_modules_vue_loader_dist_templateLoader_js_ruleSet_1_rules_1_node_modules_vue_loader_dist_index_js_ruleSet_1_rules_4_use_0_Render_vue_vue_type_template_id_4e13f272__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[1]!../../../../node_modules/vue-loader/dist/index.js??ruleSet[1].rules[4].use[0]!./Render.vue?vue&type=template&id=4e13f272 */ "./node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[1]!./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[4].use[0]!./src/js/blocks/ImageBlock/Render.vue?vue&type=template&id=4e13f272");


/***/ }),

/***/ "./src/js/blocks/TextBlock/Editor.vue?vue&type=template&id=45cbd610":
/*!**************************************************************************!*\
  !*** ./src/js/blocks/TextBlock/Editor.vue?vue&type=template&id=45cbd610 ***!
  \**************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "render": () => (/* reexport safe */ _node_modules_vue_loader_dist_templateLoader_js_ruleSet_1_rules_1_node_modules_vue_loader_dist_index_js_ruleSet_1_rules_4_use_0_Editor_vue_vue_type_template_id_45cbd610__WEBPACK_IMPORTED_MODULE_0__.render)
/* harmony export */ });
/* harmony import */ var _node_modules_vue_loader_dist_templateLoader_js_ruleSet_1_rules_1_node_modules_vue_loader_dist_index_js_ruleSet_1_rules_4_use_0_Editor_vue_vue_type_template_id_45cbd610__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[1]!../../../../node_modules/vue-loader/dist/index.js??ruleSet[1].rules[4].use[0]!./Editor.vue?vue&type=template&id=45cbd610 */ "./node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[1]!./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[4].use[0]!./src/js/blocks/TextBlock/Editor.vue?vue&type=template&id=45cbd610");


/***/ }),

/***/ "./src/js/blocks/TextBlock/Manager.vue?vue&type=template&id=064e52b2":
/*!***************************************************************************!*\
  !*** ./src/js/blocks/TextBlock/Manager.vue?vue&type=template&id=064e52b2 ***!
  \***************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "render": () => (/* reexport safe */ _node_modules_vue_loader_dist_templateLoader_js_ruleSet_1_rules_1_node_modules_vue_loader_dist_index_js_ruleSet_1_rules_4_use_0_Manager_vue_vue_type_template_id_064e52b2__WEBPACK_IMPORTED_MODULE_0__.render)
/* harmony export */ });
/* harmony import */ var _node_modules_vue_loader_dist_templateLoader_js_ruleSet_1_rules_1_node_modules_vue_loader_dist_index_js_ruleSet_1_rules_4_use_0_Manager_vue_vue_type_template_id_064e52b2__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[1]!../../../../node_modules/vue-loader/dist/index.js??ruleSet[1].rules[4].use[0]!./Manager.vue?vue&type=template&id=064e52b2 */ "./node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[1]!./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[4].use[0]!./src/js/blocks/TextBlock/Manager.vue?vue&type=template&id=064e52b2");


/***/ }),

/***/ "./src/js/blocks/TextBlock/Render.vue?vue&type=template&id=fdea0ebe":
/*!**************************************************************************!*\
  !*** ./src/js/blocks/TextBlock/Render.vue?vue&type=template&id=fdea0ebe ***!
  \**************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "render": () => (/* reexport safe */ _node_modules_vue_loader_dist_templateLoader_js_ruleSet_1_rules_1_node_modules_vue_loader_dist_index_js_ruleSet_1_rules_4_use_0_Render_vue_vue_type_template_id_fdea0ebe__WEBPACK_IMPORTED_MODULE_0__.render)
/* harmony export */ });
/* harmony import */ var _node_modules_vue_loader_dist_templateLoader_js_ruleSet_1_rules_1_node_modules_vue_loader_dist_index_js_ruleSet_1_rules_4_use_0_Render_vue_vue_type_template_id_fdea0ebe__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../../node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[1]!../../../../node_modules/vue-loader/dist/index.js??ruleSet[1].rules[4].use[0]!./Render.vue?vue&type=template&id=fdea0ebe */ "./node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[1]!./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[4].use[0]!./src/js/blocks/TextBlock/Render.vue?vue&type=template&id=fdea0ebe");


/***/ }),

/***/ "./src/js/extensions/WysiwygEditor.vue?vue&type=template&id=4a713d3c":
/*!***************************************************************************!*\
  !*** ./src/js/extensions/WysiwygEditor.vue?vue&type=template&id=4a713d3c ***!
  \***************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "render": () => (/* reexport safe */ _node_modules_vue_loader_dist_templateLoader_js_ruleSet_1_rules_1_node_modules_vue_loader_dist_index_js_ruleSet_1_rules_4_use_0_WysiwygEditor_vue_vue_type_template_id_4a713d3c__WEBPACK_IMPORTED_MODULE_0__.render)
/* harmony export */ });
/* harmony import */ var _node_modules_vue_loader_dist_templateLoader_js_ruleSet_1_rules_1_node_modules_vue_loader_dist_index_js_ruleSet_1_rules_4_use_0_WysiwygEditor_vue_vue_type_template_id_4a713d3c__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! -!../../../node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[1]!../../../node_modules/vue-loader/dist/index.js??ruleSet[1].rules[4].use[0]!./WysiwygEditor.vue?vue&type=template&id=4a713d3c */ "./node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[1]!./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[4].use[0]!./src/js/extensions/WysiwygEditor.vue?vue&type=template&id=4a713d3c");


/***/ }),

/***/ "./node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[1]!./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[4].use[0]!./src/js/Components/Editor/Rendering/Canvas.vue?vue&type=template&id=27d57861":
/*!*******************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[1]!./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[4].use[0]!./src/js/Components/Editor/Rendering/Canvas.vue?vue&type=template&id=27d57861 ***!
  \*******************************************************************************************************************************************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "render": () => (/* binding */ render)
/* harmony export */ });
/* harmony import */ var vue__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! vue */ "vue");
/* harmony import */ var vue__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(vue__WEBPACK_IMPORTED_MODULE_0__);


const _hoisted_1 = { class: "tued-container container-xxl" }

function render(_ctx, _cache, $props, $setup, $data, $options) {
  return ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)("div", {
    class: (0,vue__WEBPACK_IMPORTED_MODULE_0__.normalizeClass)({ 'tued-rendering-canvas': true, 'tued-rendering-canvas-showed': $setup.renderPreview })
  }, [
    ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(true), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)(vue__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, (0,vue__WEBPACK_IMPORTED_MODULE_0__.renderList)($props.structure.sections, (section, key) => {
      return ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)("section", {
        key: 'section-' + key,
        class: "tued-section"
      }, [
        (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_1, [
          ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(true), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)(vue__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, (0,vue__WEBPACK_IMPORTED_MODULE_0__.renderList)(section.rows, (row, key) => {
            return ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)("div", {
              key: 'row-' + key,
              class: "tued-row row"
            }, [
              ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(true), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)(vue__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, (0,vue__WEBPACK_IMPORTED_MODULE_0__.renderList)(row.columns, (column, key) => {
                return ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)("div", {
                  key: 'column-' + key,
                  class: "tued-column col"
                }, [
                  ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(true), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)(vue__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, (0,vue__WEBPACK_IMPORTED_MODULE_0__.renderList)(column.blocks, (block, key) => {
                    return ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createBlock)((0,vue__WEBPACK_IMPORTED_MODULE_0__.resolveDynamicComponent)('block-' + block.code + '-render'), {
                      key: 'block-' + key,
                      data: block.data,
                      id: block.id
                    }, null, 8 /* PROPS */, ["data", "id"]))
                  }), 128 /* KEYED_FRAGMENT */))
                ]))
              }), 128 /* KEYED_FRAGMENT */))
            ]))
          }), 128 /* KEYED_FRAGMENT */))
        ])
      ]))
    }), 128 /* KEYED_FRAGMENT */))
  ], 2 /* CLASS */))
}

/***/ }),

/***/ "./node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[1]!./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[4].use[0]!./src/js/Components/Editor/Root.vue?vue&type=template&id=1386ac68":
/*!*******************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[1]!./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[4].use[0]!./src/js/Components/Editor/Root.vue?vue&type=template&id=1386ac68 ***!
  \*******************************************************************************************************************************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "render": () => (/* binding */ render)
/* harmony export */ });
/* harmony import */ var vue__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! vue */ "vue");
/* harmony import */ var vue__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(vue__WEBPACK_IMPORTED_MODULE_0__);


const _hoisted_1 = { class: "tued-container" }

function render(_ctx, _cache, $props, $setup, $data, $options) {
  return ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)("div", _hoisted_1, [
    (0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)($setup["StructureComponent"], { structure: $setup.structure }, null, 8 /* PROPS */, ["structure"]),
    (0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)($setup["RenderingCanvasComponent"], {
      ref: "renderedContent",
      structure: $setup.structure
    }, null, 8 /* PROPS */, ["structure"])
  ]))
}

/***/ }),

/***/ "./node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[1]!./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[4].use[0]!./src/js/Components/Editor/Structure/Block.vue?vue&type=template&id=0de2ec3b":
/*!******************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[1]!./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[4].use[0]!./src/js/Components/Editor/Structure/Block.vue?vue&type=template&id=0de2ec3b ***!
  \******************************************************************************************************************************************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "render": () => (/* binding */ render)
/* harmony export */ });
/* harmony import */ var vue__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! vue */ "vue");
/* harmony import */ var vue__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(vue__WEBPACK_IMPORTED_MODULE_0__);


function render(_ctx, _cache, $props, $setup, $data, $options) {
  return ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)("div", {
    class: "tued-structure-element-selectable",
    onMouseenter: _cache[1] || (_cache[1] = $event => (_ctx.$emit('selection-enter', 'block', $props.block.id))),
    onMouseleave: _cache[2] || (_cache[2] = $event => (_ctx.$emit('selection-leave', 'block', $props.block.id))),
    onMousedown: _cache[3] || (_cache[3] = (0,vue__WEBPACK_IMPORTED_MODULE_0__.withModifiers)($event => ($options.selection.select('block', $props.block.id)), ["stop"])),
    "data-tagname": "Block"
  }, [
    ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createBlock)((0,vue__WEBPACK_IMPORTED_MODULE_0__.resolveDynamicComponent)('block-' + $props.block.code + '-editor'), {
      data: $props.block.data,
      id: $props.block.id,
      onUpdated: _cache[0] || (_cache[0] = $event => ($options.eventDispatcher.emit('block.inner.updated')))
    }, null, 8 /* PROPS */, ["data", "id"]))
  ], 32 /* HYDRATE_EVENTS */))
}

/***/ }),

/***/ "./node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[1]!./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[4].use[0]!./src/js/Components/Editor/Structure/Column.vue?vue&type=template&id=32264bd0":
/*!*******************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[1]!./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[4].use[0]!./src/js/Components/Editor/Structure/Column.vue?vue&type=template&id=32264bd0 ***!
  \*******************************************************************************************************************************************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "render": () => (/* binding */ render)
/* harmony export */ });
/* harmony import */ var vue__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! vue */ "vue");
/* harmony import */ var vue__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(vue__WEBPACK_IMPORTED_MODULE_0__);


const _hoisted_1 = ["id"]
const _hoisted_2 = { key: 0 }

function render(_ctx, _cache, $props, $setup, $data, $options) {
  const _component_Block = (0,vue__WEBPACK_IMPORTED_MODULE_0__.resolveComponent)("Block")

  return ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)("div", {
    class: (0,vue__WEBPACK_IMPORTED_MODULE_0__.normalizeClass)($options.columnClass),
    id: $props.column.id,
    onMouseenter: _cache[2] || (_cache[2] = $event => (_ctx.$emit('selection-enter', 'column', $props.column.id))),
    onMouseleave: _cache[3] || (_cache[3] = $event => (_ctx.$emit('selection-leave', 'column', $props.column.id))),
    onMousedown: _cache[4] || (_cache[4] = (0,vue__WEBPACK_IMPORTED_MODULE_0__.withModifiers)($event => ($options.selection.select('column', $props.column.id)), ["stop"])),
    "data-tagname": "Column"
  }, [
    ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(true), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)(vue__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, (0,vue__WEBPACK_IMPORTED_MODULE_0__.renderList)($props.column.blocks, (block) => {
      return ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createBlock)(_component_Block, {
        id: 'tued-structure-block-' + block.id,
        key: block.id,
        block: block,
        parent: $props.column,
        onSelectionEnter: _cache[0] || (_cache[0] = (type, id) => _ctx.$emit('selection-enter', type, id)),
        onSelectionLeave: _cache[1] || (_cache[1] = (type, id) => _ctx.$emit('selection-leave', type, id))
      }, null, 8 /* PROPS */, ["id", "block", "parent"]))
    }), 128 /* KEYED_FRAGMENT */)),
    ($props.column.blocks.length === 0)
      ? ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)("div", _hoisted_2, " Empty Column "))
      : (0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)("v-if", true)
  ], 42 /* CLASS, PROPS, HYDRATE_EVENTS */, _hoisted_1))
}

/***/ }),

/***/ "./node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[1]!./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[4].use[0]!./src/js/Components/Editor/Structure/Row.vue?vue&type=template&id=7cc13ef0":
/*!****************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[1]!./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[4].use[0]!./src/js/Components/Editor/Structure/Row.vue?vue&type=template&id=7cc13ef0 ***!
  \****************************************************************************************************************************************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "render": () => (/* binding */ render)
/* harmony export */ });
/* harmony import */ var vue__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! vue */ "vue");
/* harmony import */ var vue__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(vue__WEBPACK_IMPORTED_MODULE_0__);


const _hoisted_1 = ["id"]
const _hoisted_2 = { key: 0 }

function render(_ctx, _cache, $props, $setup, $data, $options) {
  const _component_Column = (0,vue__WEBPACK_IMPORTED_MODULE_0__.resolveComponent)("Column")

  return ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)("div", {
    class: "tued-structure-row tued-structure-element-selectable row",
    id: $props.row.id,
    onMouseenter: _cache[2] || (_cache[2] = $event => (_ctx.$emit('selection-enter', 'row', $props.row.id))),
    onMouseleave: _cache[3] || (_cache[3] = $event => (_ctx.$emit('selection-leave', 'row', $props.row.id))),
    onMousedown: _cache[4] || (_cache[4] = (0,vue__WEBPACK_IMPORTED_MODULE_0__.withModifiers)($event => ($options.selection.select('row', $props.row.id)), ["stop"])),
    "data-tagname": "Row"
  }, [
    ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(true), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)(vue__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, (0,vue__WEBPACK_IMPORTED_MODULE_0__.renderList)($props.row.columns, (column) => {
      return ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createBlock)(_component_Column, {
        id: 'tued-structure-column-' + column.id,
        key: column.id,
        column: column,
        parent: $props.row,
        onSelectionEnter: _cache[0] || (_cache[0] = (type, id) => _ctx.$emit('selection-enter', type, id)),
        onSelectionLeave: _cache[1] || (_cache[1] = (type, id) => _ctx.$emit('selection-leave', type, id))
      }, null, 8 /* PROPS */, ["id", "column", "parent"]))
    }), 128 /* KEYED_FRAGMENT */)),
    ($props.row.columns.length === 0)
      ? ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)("div", _hoisted_2, " Empty Row "))
      : (0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)("v-if", true)
  ], 40 /* PROPS, HYDRATE_EVENTS */, _hoisted_1))
}

/***/ }),

/***/ "./node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[1]!./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[4].use[0]!./src/js/Components/Editor/Structure/Section.vue?vue&type=template&id=4d532d13":
/*!********************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[1]!./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[4].use[0]!./src/js/Components/Editor/Structure/Section.vue?vue&type=template&id=4d532d13 ***!
  \********************************************************************************************************************************************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "render": () => (/* binding */ render)
/* harmony export */ });
/* harmony import */ var vue__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! vue */ "vue");
/* harmony import */ var vue__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(vue__WEBPACK_IMPORTED_MODULE_0__);


const _hoisted_1 = ["id"]
const _hoisted_2 = { class: "container-xxl" }
const _hoisted_3 = { key: 0 }

function render(_ctx, _cache, $props, $setup, $data, $options) {
  const _component_Row = (0,vue__WEBPACK_IMPORTED_MODULE_0__.resolveComponent)("Row")

  return ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)("section", {
    class: "tued-structure-section tued-structure-element-selectable",
    id: $props.section.id,
    onMouseenter: _cache[2] || (_cache[2] = $event => (_ctx.$emit('selection-enter', 'section', $props.section.id))),
    onMouseleave: _cache[3] || (_cache[3] = $event => (_ctx.$emit('selection-leave', 'section', $props.section.id))),
    onMousedown: _cache[4] || (_cache[4] = (0,vue__WEBPACK_IMPORTED_MODULE_0__.withModifiers)($event => ($options.selection.select('section', $props.section.id)), ["stop"])),
    "data-tagname": "Section"
  }, [
    (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_2, [
      ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(true), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)(vue__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, (0,vue__WEBPACK_IMPORTED_MODULE_0__.renderList)($props.section.rows, (row) => {
        return ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createBlock)(_component_Row, {
          id: 'tued-structure-row-' + row.id,
          key: row.id,
          row: row,
          parent: $props.section,
          onSelectionEnter: _cache[0] || (_cache[0] = (type, id) => _ctx.$emit('selection-enter', type, id)),
          onSelectionLeave: _cache[1] || (_cache[1] = (type, id) => _ctx.$emit('selection-leave', type, id))
        }, null, 8 /* PROPS */, ["id", "row", "parent"]))
      }), 128 /* KEYED_FRAGMENT */)),
      ($props.section.rows.length === 0)
        ? ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)("div", _hoisted_3, " Empty Section "))
        : (0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)("v-if", true)
    ])
  ], 40 /* PROPS, HYDRATE_EVENTS */, _hoisted_1))
}

/***/ }),

/***/ "./node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[1]!./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[4].use[0]!./src/js/Components/Editor/Structure/Structure.vue?vue&type=template&id=4d3491be":
/*!**********************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[1]!./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[4].use[0]!./src/js/Components/Editor/Structure/Structure.vue?vue&type=template&id=4d3491be ***!
  \**********************************************************************************************************************************************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "render": () => (/* binding */ render)
/* harmony export */ });
/* harmony import */ var vue__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! vue */ "vue");
/* harmony import */ var vue__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(vue__WEBPACK_IMPORTED_MODULE_0__);


const _hoisted_1 = {
  class: "tued-structure",
  ref: "structure"
}
const _hoisted_2 = { class: "tued-node-name" }
const _hoisted_3 = /*#__PURE__*/(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("i", { class: "fas fa-long-arrow-alt-up" }, null, -1 /* HOISTED */)
const _hoisted_4 = [
  _hoisted_3
]
const _hoisted_5 = {
  key: 1,
  class: "tued-element-action",
  title: "Duplikuj"
}
const _hoisted_6 = /*#__PURE__*/(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("i", { class: "fas fa-copy" }, null, -1 /* HOISTED */)
const _hoisted_7 = [
  _hoisted_6
]
const _hoisted_8 = /*#__PURE__*/(0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("i", { class: "fas fa-trash" }, null, -1 /* HOISTED */)
const _hoisted_9 = [
  _hoisted_8
]

function render(_ctx, _cache, $props, $setup, $data, $options) {
  const _component_Section = (0,vue__WEBPACK_IMPORTED_MODULE_0__.resolveComponent)("Section")

  return ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)("div", _hoisted_1, [
    ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(true), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)(vue__WEBPACK_IMPORTED_MODULE_0__.Fragment, null, (0,vue__WEBPACK_IMPORTED_MODULE_0__.renderList)($props.structure.sections, (section) => {
      return ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createBlock)(_component_Section, {
        id: 'tued-structure-section-' + section.id,
        key: section.id,
        section: section,
        onSelectionEnter: _cache[0] || (_cache[0] = (type, id) => $options.selectionEnter(type, id)),
        onSelectionLeave: _cache[1] || (_cache[1] = (type, id) => $options.selectionLeave(type, id))
      }, null, 8 /* PROPS */, ["id", "section"]))
    }), 128 /* KEYED_FRAGMENT */)),
    (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", {
      class: "tued-structure-new-element",
      onClick: _cache[2] || (_cache[2] = $event => ($options.structureManipulator.newSection()))
    }, (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)($options.translator.trans('newSection')), 1 /* TEXT */),
    (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", {
      class: "tued-element-boundaries tued-element-selected-boundaries",
      style: (0,vue__WEBPACK_IMPORTED_MODULE_0__.normalizeStyle)({
                left: $data.selectable.style.left + 'px',
                top: $data.selectable.style.top + 'px',
                width: $data.selectable.style.width + 'px',
                height: $data.selectable.style.height + 'px',
            })
    }, [
      (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_2, (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)($data.selectable.style.tagName), 1 /* TEXT */)
    ], 4 /* STYLE */),
    (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", {
      class: "tued-element-boundaries tued-element-hovered-boundaries",
      style: (0,vue__WEBPACK_IMPORTED_MODULE_0__.normalizeStyle)({
                left: $data.hoverable.style.left + 'px',
                top: $data.hoverable.style.top + 'px',
                width: $data.hoverable.style.width + 'px',
                height: $data.hoverable.style.height + 'px',
            })
    }, null, 4 /* STYLE */),
    (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", {
      class: "tued-element-actions",
      ref: "element-actions",
      style: (0,vue__WEBPACK_IMPORTED_MODULE_0__.normalizeStyle)({
                width: $data.actions.style.width + 'px',
                left: $data.actions.style.left + 'px',
                top: $data.actions.style.top + 'px',
            })
    }, [
      ($data.actions.activeness.selectParent)
        ? ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)("div", {
            key: 0,
            class: "tued-element-action",
            title: "Zaznacz blok wyżej",
            onClick: _cache[3] || (_cache[3] = $event => ($options.selectParentSelectable()))
          }, _hoisted_4))
        : (0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)("v-if", true),
      ($data.actions.activeness.duplicate)
        ? ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)("div", _hoisted_5, _hoisted_7))
        : (0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)("v-if", true),
      ($data.actions.activeness.delete)
        ? ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)("div", {
            key: 2,
            class: "tued-element-action",
            title: "Usuń",
            onClick: _cache[4] || (_cache[4] = $event => ($options.deleteSelectedElement()))
          }, _hoisted_9))
        : (0,vue__WEBPACK_IMPORTED_MODULE_0__.createCommentVNode)("v-if", true)
    ], 4 /* STYLE */)
  ], 512 /* NEED_PATCH */))
}

/***/ }),

/***/ "./node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[1]!./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[4].use[0]!./src/js/blocks/ImageBlock/Editor.vue?vue&type=template&id=3505231e&scoped=true":
/*!*********************************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[1]!./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[4].use[0]!./src/js/blocks/ImageBlock/Editor.vue?vue&type=template&id=3505231e&scoped=true ***!
  \*********************************************************************************************************************************************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "render": () => (/* binding */ render)
/* harmony export */ });
/* harmony import */ var vue__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! vue */ "vue");
/* harmony import */ var vue__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(vue__WEBPACK_IMPORTED_MODULE_0__);


const _withScopeId = n => ((0,vue__WEBPACK_IMPORTED_MODULE_0__.pushScopeId)("data-v-3505231e"),n=n(),(0,vue__WEBPACK_IMPORTED_MODULE_0__.popScopeId)(),n)
const _hoisted_1 = ["src"]

function render(_ctx, _cache, $props, $setup, $data, $options) {
  return ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)("div", null, [
    (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("img", {
      class: "tued-block-image_image",
      src: $setup.imageLink,
      onClick: _cache[0] || (_cache[0] = $event => ($setup.block.execute('chose-image'))),
      onLoad: _cache[1] || (_cache[1] = $event => (_ctx.$emit('updated')))
    }, null, 40 /* PROPS, HYDRATE_EVENTS */, _hoisted_1)
  ]))
}

/***/ }),

/***/ "./node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[1]!./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[4].use[0]!./src/js/blocks/ImageBlock/Manager.vue?vue&type=template&id=a875ed68":
/*!**********************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[1]!./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[4].use[0]!./src/js/blocks/ImageBlock/Manager.vue?vue&type=template&id=a875ed68 ***!
  \**********************************************************************************************************************************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "render": () => (/* binding */ render)
/* harmony export */ });

function render(_ctx, _cache, $props, $setup, $data, $options) {
  return null
}

/***/ }),

/***/ "./node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[1]!./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[4].use[0]!./src/js/blocks/ImageBlock/Render.vue?vue&type=template&id=4e13f272":
/*!*********************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[1]!./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[4].use[0]!./src/js/blocks/ImageBlock/Render.vue?vue&type=template&id=4e13f272 ***!
  \*********************************************************************************************************************************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "render": () => (/* binding */ render)
/* harmony export */ });
/* harmony import */ var vue__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! vue */ "vue");
/* harmony import */ var vue__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(vue__WEBPACK_IMPORTED_MODULE_0__);


function render(_ctx, _cache, $props, $setup, $data, $options) {
  return ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)("div", null, "[image id=\"" + (0,vue__WEBPACK_IMPORTED_MODULE_0__.toDisplayString)($setup.block.data.imageId) + "\" size=\"thumbnail\"]", 1 /* TEXT */))
}

/***/ }),

/***/ "./node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[1]!./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[4].use[0]!./src/js/blocks/TextBlock/Editor.vue?vue&type=template&id=45cbd610":
/*!********************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[1]!./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[4].use[0]!./src/js/blocks/TextBlock/Editor.vue?vue&type=template&id=45cbd610 ***!
  \********************************************************************************************************************************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "render": () => (/* binding */ render)
/* harmony export */ });
/* harmony import */ var vue__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! vue */ "vue");
/* harmony import */ var vue__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(vue__WEBPACK_IMPORTED_MODULE_0__);


function render(_ctx, _cache, $props, $setup, $data, $options) {
  return ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)("div", null, [
    (0,vue__WEBPACK_IMPORTED_MODULE_0__.createVNode)($setup["WysiwygEditor"], {
      modelValue: $setup.block.data.text,
      "onUpdate:modelValue": _cache[0] || (_cache[0] = $event => (($setup.block.data.text) = $event))
    }, null, 8 /* PROPS */, ["modelValue"])
  ]))
}

/***/ }),

/***/ "./node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[1]!./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[4].use[0]!./src/js/blocks/TextBlock/Manager.vue?vue&type=template&id=064e52b2":
/*!*********************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[1]!./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[4].use[0]!./src/js/blocks/TextBlock/Manager.vue?vue&type=template&id=064e52b2 ***!
  \*********************************************************************************************************************************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "render": () => (/* binding */ render)
/* harmony export */ });

function render(_ctx, _cache, $props, $setup, $data, $options) {
  return null
}

/***/ }),

/***/ "./node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[1]!./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[4].use[0]!./src/js/blocks/TextBlock/Render.vue?vue&type=template&id=fdea0ebe":
/*!********************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[1]!./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[4].use[0]!./src/js/blocks/TextBlock/Render.vue?vue&type=template&id=fdea0ebe ***!
  \********************************************************************************************************************************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "render": () => (/* binding */ render)
/* harmony export */ });
/* harmony import */ var vue__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! vue */ "vue");
/* harmony import */ var vue__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(vue__WEBPACK_IMPORTED_MODULE_0__);


const _hoisted_1 = ["innerHTML"]

function render(_ctx, _cache, $props, $setup, $data, $options) {
  return ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)("div", {
    innerHTML: $setup.block.data.text
  }, null, 8 /* PROPS */, _hoisted_1))
}

/***/ }),

/***/ "./node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[1]!./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[4].use[0]!./src/js/extensions/WysiwygEditor.vue?vue&type=template&id=4a713d3c":
/*!*********************************************************************************************************************************************************************************************************************!*\
  !*** ./node_modules/vue-loader/dist/templateLoader.js??ruleSet[1].rules[1]!./node_modules/vue-loader/dist/index.js??ruleSet[1].rules[4].use[0]!./src/js/extensions/WysiwygEditor.vue?vue&type=template&id=4a713d3c ***!
  \*********************************************************************************************************************************************************************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "render": () => (/* binding */ render)
/* harmony export */ });
/* harmony import */ var vue__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! vue */ "vue");
/* harmony import */ var vue__WEBPACK_IMPORTED_MODULE_0___default = /*#__PURE__*/__webpack_require__.n(vue__WEBPACK_IMPORTED_MODULE_0__);


const _hoisted_1 = /*#__PURE__*/(0,vue__WEBPACK_IMPORTED_MODULE_0__.createStaticVNode)("<div class=\"editor-toolbar\"><span class=\"ql-formats\"><button class=\"ql-bold\" title=\"Bold &lt;ctrl+b&gt;\"></button><button class=\"ql-italic\" title=\"Italic &lt;ctrl+i&gt;\"></button><button class=\"ql-underline\" title=\"Underline &lt;ctrl+u&gt;\"></button><button class=\"ql-strike\" title=\"Strikethrough\"></button></span><span class=\"ql-formats\"><button class=\"ql-script\" value=\"sub\" title=\"Subscript\"></button><button class=\"ql-script\" value=\"super\" title=\"Superscript\"></button></span><span class=\"ql-formats\"><button class=\"ql-header\" value=\"1\"></button><button class=\"ql-header\" value=\"2\"></button><button class=\"ql-blockquote\" title=\"Blockquote\"></button><button class=\"ql-code-block\" title=\"Code block\"></button></span><span class=\"ql-formats\"><button class=\"ql-list\" value=\"ordered\"></button><button class=\"ql-list\" value=\"bullet\"></button></span><span class=\"ql-formats\"><select class=\"ql-align\" title=\"Align text\"></select><button class=\"ql-link\" title=\"Link text\"></button><button class=\"ql-clean\" title=\"Clean text formatting\"></button></span></div>", 1)
const _hoisted_2 = { class: "editor-container" }

function render(_ctx, _cache, $props, $setup, $data, $options) {
  return ((0,vue__WEBPACK_IMPORTED_MODULE_0__.openBlock)(), (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementBlock)("div", null, [
    _hoisted_1,
    (0,vue__WEBPACK_IMPORTED_MODULE_0__.createElementVNode)("div", _hoisted_2, [
      (0,vue__WEBPACK_IMPORTED_MODULE_0__.renderSlot)(_ctx.$slots, "default")
    ])
  ]))
}

/***/ }),

/***/ "./src/js/blocks/ImageBlock/ImageBlock.js":
/*!************************************************!*\
  !*** ./src/js/blocks/ImageBlock/ImageBlock.js ***!
  \************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
const Editor = (__webpack_require__(/*! ./Editor.vue */ "./src/js/blocks/ImageBlock/Editor.vue")["default"]);
const Render = (__webpack_require__(/*! ./Render.vue */ "./src/js/blocks/ImageBlock/Render.vue")["default"]);
const Manager = (__webpack_require__(/*! ./Manager.vue */ "./src/js/blocks/ImageBlock/Manager.vue")["default"]);

/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = ({
    code: 'core-imageblock',
    name: 'Image',
    icon: 'data:image/png;base64,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',
    manager: Manager,
    editor: Editor,
    render: Render
});


/***/ }),

/***/ "./src/js/blocks/ImageBlock/props.js":
/*!*******************************************!*\
  !*** ./src/js/blocks/ImageBlock/props.js ***!
  \*******************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
const defaults = {
    imageId: null,
    imageFilename: null,
};

/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = ({
    id: String,
    data: {
        type: Object,
        default (rawProps) {
            return Object.assign({}, defaults, rawProps);
        }
    }
});


/***/ }),

/***/ "./src/js/blocks/TextBlock/TextBlock.js":
/*!**********************************************!*\
  !*** ./src/js/blocks/TextBlock/TextBlock.js ***!
  \**********************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
const Editor = (__webpack_require__(/*! ./Editor.vue */ "./src/js/blocks/TextBlock/Editor.vue")["default"]);
const Render = (__webpack_require__(/*! ./Render.vue */ "./src/js/blocks/TextBlock/Render.vue")["default"]);
const Manager = (__webpack_require__(/*! ./Manager.vue */ "./src/js/blocks/TextBlock/Manager.vue")["default"]);

/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = ({
    code: 'core-textblock',
    name: 'Text',
    icon: 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAPoAAABkCAMAAACYXt08AAAAUVBMVEX///+/v79/f38/Pz8AAADg4ODv7+8vLy/Pz88fHx+wsLBvb29PT0+fn59fX1+Pj48PDw9paWkYGBiqqqrGxsZVVVUpKSm2trZ4eHhFRUXX19clbRB9AAAOQElEQVR42uyai25sNQxFt+3Ycd4BJAT8/4cyPpl2yqVwW95CszWPOHaSvRSptU6Lp5566qmnnnrqqaeeesiY8JCCHH7PGN6V478g2pKn4c9osuIhwQ9D5xmPinfV8WvlhX9YJAA3wKsCruFVSQGrhJDWl0DtGpK7qcI81lhU5GXwU+YA3dAR39WgGvVAzEbgZDCt5tAYnH2PvGfcFBOkZGezU+CXoevbcTZGDMMCHYeq1UD0eXQUDFlC2MwZLpzJZM0W6bxytbzaBMc8pLVNzLGudc5RUqZyH0VvZb0hBbqgzZXBbGV1BpAVXdutyqg0FTCBGNe+R61OAoRjh8kFeltYIVEgwjmY5TLQG4tp2ArrCfU2VI5NW5ufR08ohtqRAcGsoDkaEJZ7hdbVgOxcUdmj5KBbBjohYrhSpyhbL+h9wI1Zi1vYHssy3CgTNeAFvTgIRwV1Ik7dSIA4D6iYK+3Yn9c5p/Yw0EbtanrQs2IxM7wD+dPoVuI4FUi4ksk8mE9awhqvGFxuqb+iU2FmPTVD+JoFXtCNc0PUzVwBWB4LS7gQ8QPdZx64NLbINpaACHRq4QLSOF3ofM5xoY6IRhc/6JuZj6Gw81n0yRDFmAd9DkDrBDoQAfEtg2KXWy1AjpOqIBsUx5IoJtUJ6y/ocupowSWKejYUQ35BX6jsDZZwqQ+iObwDetAXA0oCe4NeMWYYmHVUVKYc2U4wY4ZnQD/7E74w4FnEDrplyY4p14WoXEHO435RnKWQbhHByNLtoK8S5bHmoF9JAbPJ2QejI9ZKPehaRDgWcCxHAIX5loUPunWRYVn6G3SRohg5N+jlMeeeLuce+ZWl4e+UAsXwr0gI/6qafPwX8P8M/al3Ve2DdQt3ueIdrYGHFD/V18ZYvyg0U/wNYnxYewItaVr4kL75HuPQ7fdWjO54qHn9Bv2M/QtPWevAH5eSA6fFdNcYnFaWEiES9+4VcK+mUaZEuPePEd80N5A7yEihBK0OIyU38oiN7u0nLAL75nvNWd3d6DSz97STRs2cem0BxEFuN3Q/Rkxj7mE7a8xUd4tvj6RBw5/TB8hl9IXWuSjtSdyXgK4GJTGA2ldWu5rK3VqeXECFe0OVVa7+EVGVXNNAoi6QXpNsplRKkpycEyhBSt7hOwL65vux95CdKLGVsif6zsXalkThSIjyyBXSOEPohi5xXlZiCDcJ2zlsn5m29r1baG0UcOGeW+v4mkx9CTYwmAQxaNQdlZEivdjUeMEEAhRDu04pyHb6x6PdVjIkGglpKFEWStVSRVoHvXQle6BDBFIMiVcCiSemNLqca2eGKjF7Bobd0VczNWJsAr3aDnTNgBz00ykz4/R5X5NmnhcWCQUcM6s05nFfu0SMT1OJePE5xdK9fzyauXcgkaWZrO5ehBIhXnzQvSf5El0Q6Y2YziJDZyp3dM88OfwAB/0YIYa2vO62DzrJA507l4+jn560GHhdt25QzAGzs7Y52lgM6AO9wQq6n/7xyFMaCNSeOiQjv0VXTiakiYKxjoOe7+gj+RBNbMt6xeZjiQcWXz2rHfTLyCK2Cey77YNuV2tNEy5h+VO3Ll1AWcRIEINueos8rF1xNnSRN7eeexnwW6GHyYVQ2bjQRxqoO+Xyiq63KKGl69aRk1zoNclBR09pYNyKdOzXW6ciMrGiZz3ocV6AosXcsZ1xzawim6zIFHCWB3pY+4tFjP+afrPPe6I/9dRTTz311FNPPfXUU/87je+Mf8InRIrfljne1yJ8QPa2iiJQ+4qJz4kSA/EO/dgtfYePigml4be1Nt7Xt4zf1Bg4atKKP87i+LjHRF/4+KPoW5FYCUZ20LX6lVG4RxCZqng7dHJ4muYKO0bUnczJzsPVSLsSbm8CXXuDDF71jq6VENvEevXHfzNozne+AtR+xyS60NVMnaDnka3C3C1yZAoQ3MjDXUgp0gD9Lrr0lydodKFfD1SBuNHdaOc0KOWy7c2Qt6TaUnFh3VImAL5lc94dUnIUeBqcILfCImmdZzYrll3ouvOeaLdPp7SlplvCcil97N3u6As3SWsN3LkwzhOiNhuJkMvKRHtq6/c/XCfI5C0sA8BJC5B+F90T/QJdibIAaNmT90I93zK3120470MnyhOJIeypOoWtjJYR29SayMqWCKQhN4gc9Fnc9aATzWRpIHdKjtKpy0p1JRfBkbUyzTuQsQ180IOmRDArqJ8I/IJOaAyawEl/HR2t/AL9eqAKwFMrkCIih/fNcOxZ5KBjlbTCloAPbWmJMBNfQZS8otvcxS9031mSR90tBaQi0jiJyEG/izsJM9vGW/QUgUzmEZG8RY9MTJ70B9BtXw9G+Y4uGVkiVVLDLEp+eN8MZVqgNwiPjrbfoGuqnsiTbPsF+qBEbaHwhc4bLWE3KzPQezbykUjXbQhbADwDVeJTURTtC/S1AI2oGCZRh/0CvY2Ttq+gYyS28nM7V6IrNwgDbRyucKW31P//0DKYJbu9t7eqjJ5egBiSEbt6UzPu5teubz4S5m+BrGxbUb7ubJbtOITqxmKc3x53PfeFioiL6Y46eSxfjm3teu+73u+cN/DcjjKytrQfon8ZDNwD6hfYe/OeOscCy8Loldgb5CU/UHeyrAcXLlx4xCtrXz1th333nuxsu4c4/h4NbL/xMHcGmxX7GxwEb17zG/oW8kd22BevQtJ22+lEk+T3b8pgnfpNTQvEZdj4dSpeHQTMk/o4yldZidZpluVph4UaZTfNBtypu5sd1o0xxYZQCnRaApixIOIsolR+Ti/AaWfoQLzVqXcOW89EN7sslDPvp7mhB9bmApZ2IfCwP3wX4CCoKcmgrpa14NvYM4HfwEnLPaI2GXZYo0ZXtb5unToLpQQ7LMPV2m4uZ4f5tCwBw41QcovO9GYhlZ9CFeYBg/EgBvzQOYxOVYdtwmA05E1KJLn0to+p1pu5oU9gqVa4xzPsD31p+j5sQz2IO6mXbO00gJoybKsO+mIMkR7fN0NUbtQzbD6GQ7TYR6ATrA7BagmwUFuHnmQbQ5yn/BSKmJoNBZwwM1hCtE7qy2GLDleIVhl3d5nLNaPLGSalLqQUvpc6e/gJTurDgDqpm2FbDbK+3Wp0NTt6k7pLPuHpkPh3X3cEL0sAmttys1qZ1C2muuYrqZ0Bay7qp8OWBI/V8XWGfC7349RtxDcL1NXFWpiKuVFX2yp2KSj1aBGSyNVJHYPeGt4b3gLAgs4TvEXDEmC4hx8k043AgudkUM8IyY6Ea1FrmB+iNZKLePjNYSvjah+oZyy3qNed0rPURSAGq1JvUSIHL+euU/K+wfFa9RUQIjtl8fukDsmJz5zzOkaE8LhT6r+nJaC3Cppi9V1LzJ51akW0OMzGQxFqKPsc1WErRZ+L1dI9dTU3aG+PgaOIUv+3zpzZ0BMw/94B/kX9P0P7Ql2Wa+ZTtRSsNfZde5RmX5KuwX7Ze/plg2uhn8MT6u4lVGP5VEVWzZQ9Jl4P83oL6S2dgPXv+Cxxn6TehX1iE/4ChJ7H8/MdB1C3NngmAGlYhomVRn7Uhv5jVyZ2pFmVOgSsrmBHSpeYLfXY3dlxQ3Nk5MOQnFgU1GdJGLTRrdwr9PtDNBNPSSvrzULvOwwxDa2qgpZugjjs9xqY3SxJY60M4/BV5t5k06kbU6IhQJBsgYmVCPlRc2xGjGZi9/57I5m77qM/QNEfmtyrhz+sOXo3HpEGiljsrm9ibTQV1KGMZ5HHrdxLqonV+ABdI4RAAblYsooYdpg7/9h6U6e2Hd7eJn35pYFllqT14H4VhHwZ+DJ7UMdTTur4PdvRkRhNTKF7Umdu0wHpBnVmhiW2h7h9I0Xxfh81RdBiIHxX0MWz3AsPrrdKslRGfdWywQ7qLa20I0+75BLElWXYrwmdxAYL3ecovwyTjTFfpS50Ujf31CVmpU6kux4TrgjB2ISN1g/JuSv1oYwX9ZB8UXrEEH2E0EndpzqpG7Ooq6Al5eXU0kuK0cFC30m9VCL7QD1Fhxyf/wz1HF1d1Hnbd1APW+NBeWv2kbrqKs+jHqvMyi4eyvjQuq+AwUUdAjjvrY+JvtkuJJYylzrCoFVV0BItQWxH0r0QaecJ6pS9L0o9eRrvhmwpTKyfUg/HFu92vcfhkXXzuutbPE7qFbcgYxMFUc9txvKqjBMUM2u516KuhVwOrl21wVY/yshY5+4HgiFoF3W19Ip4S6SdJ6g/wGb6KtxOOdJfgEXq47fCOfoqOCKR/ufhsvh/6J8k/wsa/Tz290udPoli6BFY5E/hJX0RzPR9qO+WOn0OnPmTobYe/huhQnYextuwU2CavRB4lF5ZtIktT7kJTJdAWGf4nXpwveksBh2pQyGEQLoQLTkbPpKk1bipQclZRAU0MX3c+k1YQlYLvJA5HRVW8+Q+pWpFZgJsGyf33k4lVCJa0KBaQ9+po2KloSqpD5LWax3YUSyUTzkrS5KmhJ2NCQYBodpHGwIiSWApPuz9Fv0uLCE7CrzYkIvrfy4AowPvOKlryqrR3G2QIsE39UbdZvKOVapZaxn0EKwLqZyFSj0laVTriJUx6qgiR8eCocB179ffhiVkNSPKZpCR+6P5R+poA0WMTOqcbtTJh0yTOqNeaypEvQ+dyapSHySp88RojjSmE0fFkKeGTH2//jYsIasFXos6pNj8HDdDLdH+SF31Ja6zSswN6s2XRR2f/gfqQ86qSs33kpRr/0FT8JETCFPOtKHTr78PS8haiM1FfeRHZ+IzFnuInNTxPkNfFlCv4h15nwd1d9CirvVad9RHoldV6h6XJNWvXMZqMm/FUAxlLGljoX/JHFAanZBfnUKx2O1/FZZ+lvolWS9cuHDhwoULFy5cuHDhwhfwAaJMhjV80ULwAAAAAElFTkSuQmCC',
    manager: Manager,
    editor: Editor,
    render: Render
});


/***/ }),

/***/ "./src/js/blocks/TextBlock/props.js":
/*!******************************************!*\
  !*** ./src/js/blocks/TextBlock/props.js ***!
  \******************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
const defaults = {
    text: ''
};

/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = ({
    id: String,
    data: {
        type: Object,
        default (rawProps) {
            return Object.assign({}, defaults, rawProps);
        }
    }
});


/***/ }),

/***/ "./src/js/blocks/blocks.js":
/*!*********************************!*\
  !*** ./src/js/blocks/blocks.js ***!
  \*********************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
const TextBlock = (__webpack_require__(/*! ./TextBlock/TextBlock.js */ "./src/js/blocks/TextBlock/TextBlock.js")["default"]);
const ImageBlock = (__webpack_require__(/*! ./ImageBlock/ImageBlock.js */ "./src/js/blocks/ImageBlock/ImageBlock.js")["default"]);

let blocks = {};

blocks[TextBlock.code] = TextBlock;
blocks[ImageBlock.code] = ImageBlock;

/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = (blocks);


/***/ }),

/***/ "./src/js/extensions/extensions.js":
/*!*****************************************!*\
  !*** ./src/js/extensions/extensions.js ***!
  \*****************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (__WEBPACK_DEFAULT_EXPORT__)
/* harmony export */ });
const WysiwygEditor = (__webpack_require__(/*! ./WysiwygEditor.vue */ "./src/js/extensions/WysiwygEditor.vue")["default"]);

/* harmony default export */ const __WEBPACK_DEFAULT_EXPORT__ = ({
    WysiwygEditor
});


/***/ }),

/***/ "./src/js/shared/EventDispatcher.js":
/*!******************************************!*\
  !*** ./src/js/shared/EventDispatcher.js ***!
  \******************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ EventDispatcher)
/* harmony export */ });
class EventDispatcher {
    events = [];

    on (events, listener, priority) {
        events = events.split(',');
        priority = priority || 100;

        for (let i = 0; i < events.length; i++) {
            let name = events[i].trim();

            if (this.events[name]) {
                this.events[name].push({
                    listener: listener,
                    priority: priority
                });
            } else {
                this.events[name] = [];
                this.events[name].push({
                    listener: listener,
                    priority: priority
                });
            }

            this.events[name].sort(function (a, b) {
                return b.priority - a.priority;
            });
        }

        return this;
    }

    emit (name, ...args) {
        if (! this.events[name]) {
            return this;
        }

        args = args || [];

        for (let i = 0; i < this.events[name].length; i++) {
            if (typeof(this.events[name][i].listener) !== 'function') {
                throw new Error('One of the listeners of the "' + name + '" event is not a function.');
            }

            this.events[name][i].listener(...args);
        }

        return this;
    }
};


/***/ }),

/***/ "./src/js/shared/I18n/Catalogue.js":
/*!*****************************************!*\
  !*** ./src/js/shared/I18n/Catalogue.js ***!
  \*****************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ Catalogue)
/* harmony export */ });
class Catalogue {
    translations = {};

    constructor (translations) {
        this.translations = translations;
    }

    get (name) {
        return this.translations[name] ?? null;
    }
};


/***/ }),

/***/ "./src/js/shared/I18n/Translator.js":
/*!******************************************!*\
  !*** ./src/js/shared/I18n/Translator.js ***!
  \******************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ Translator)
/* harmony export */ });
const Catalogue = (__webpack_require__(/*! ./Catalogue.js */ "./src/js/shared/I18n/Catalogue.js")["default"]);

class Translator {
    catalogues = {};
    locale;
    fallbackLocales = [];
    translations;

    constructor (locale, fallbackLocales, translations) {
        this.translations = translations;
        this.locale = locale;
        this.fallbackLocales = fallbackLocales;
    }

    trans (name) {
        let locales = [this.locale].concat(this.fallbackLocales);
        let translation = null;

        locales = this.resolveLocalesCodes(locales);

        for (let locale of locales) {
            translation = this.getCatalogue(locale).get(name);

            // Break if found in this locale.
            if (translation !== null) {
                break;
            }
        }

        if (translation === null) {
            translation = name;
        }

        return translation;
    }

    getCatalogue (locale) {
        if (this.catalogues[locale]) {
            return this.catalogues[locale];
        }

        this.catalogues[locale] = new Catalogue(this.translations[locale] ?? {});

        return this.catalogues[locale];
    }

    /**
     * Splits all ISO locales, and after every ISO locale,
     * adds simple locale code without region. Like for `en_US`, creates `en`.
     * @internal
     */
    resolveLocalesCodes (locales) {
        let resolved = [];

        for (let locale of locales) {
            if (locale.indexOf('_')) {
                let d = locale.split('_');

                resolved.push(locale);
                resolved.push(d[0]);
            }
        }

        return resolved;
    }
};


/***/ }),

/***/ "./src/js/shared/Messaging/Messenger.js":
/*!**********************************************!*\
  !*** ./src/js/shared/Messaging/Messenger.js ***!
  \**********************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ Messenger)
/* harmony export */ });
const OperationPromise = (__webpack_require__(/*! shared/Messaging/OperationPromise.js */ "./src/js/shared/Messaging/OperationPromise.js")["default"]);
const Executor = (__webpack_require__(/*! shared/Messaging/OperationExecutor.js */ "./src/js/shared/Messaging/OperationExecutor.js")["default"]);
const { v4 } = __webpack_require__(/*! uuid */ "./node_modules/uuid/index.js");

class Messenger {
    instanceId;
    windowName;
    windows;
    notificationListeners = [];
    operationListeners = [];
    promises = [];

    constructor (instanceId, windowName, windows) {
        this.instanceId = parseInt(instanceId);
        this.windowName = windowName;
        this.windows = windows;

        this.start();
    }

    start () {
        window.addEventListener('message', (event) => {
            if (event.origin !== location.protocol + '//' + location.host) {
                return;
            }

            if (!event.data.header) {
                return;
            }

            if (event.data.header.instanceId !== this.instanceId) {
                return;
            }

            if (event.data.header.type === 'notification') {
                this.callNotificationListeners(event.data);
            }
            if (event.data.header.type === 'operation') {
                this.callOperationListeners(event.data);
            }
            if (event.data.header.type === 'operation-confirmation') {
                this.confirmOperation(event.data);
            }

        }, false);
    }

    /**
     * Executes operation, and waits for all listeners in all windows to confirm
     * executed operation, and then call caller when end.
     */
    execute (operation, body) {
        const messageId = v4();
        const promise = new OperationPromise(operation, body, this.windows.length);

        this.promises.push({
            messageId: messageId,
            operation: operation,
            promise: promise,
        });

        this.sendToAllWindows(operation, 'operation', messageId, body);

        return promise.getNativePromise();
    }

    /**
     * Sends one way informational event, without delivering confirmation.
     */
    notify (notification, ...body) {
        this.sendToAllWindows(notification, 'notification', v4(), body);
    }

    /**
     * Bind listeners for notify() calls.
     */
    on (notification, listener) {
        if (!this.notificationListeners[notification]) {
            this.notificationListeners[notification] = [];
        }

        this.notificationListeners[notification].push({
            listener: listener,
        });
    }

    operation (operation, listener) {
        if (!this.operationListeners[operation]) {
            this.operationListeners[operation] = [];
        }

        this.operationListeners[operation].push({
            listener: listener
        });
    }

    callOperationListeners (event) {
        if (!this.operationListeners[event.header.name]) {
            // If no listeners registered, we can confirm operation immediately
            this.sendOperationConfirmation(event.header.name, event.header.messageId, {});
            return;
        }

        new Executor(
            event,
            this.operationListeners[event.header.name],
            // Success
            (event, data) => {
                this.sendOperationConfirmation(event.header.name, event.header.messageId, data);
            },
            // Fail
            () => {

            }
        );
    }

    callNotificationListeners (event) {
        for (let i in this.notificationListeners[event.header.name]) {
            let listener = this.notificationListeners[event.header.name][i];
            listener.listener.call(this, ...event.body);
        }
    }

    sendOperationConfirmation (operation, messageId, body) {
        this.sendToAllWindows(operation, 'operation-confirmation', messageId, body);
    }

    confirmOperation (event) {
        for (let i in this.promises) {
            if (
                this.promises[i].operation === event.header.name
                && this.promises[i].messageId === event.header.messageId
            ) {
                this.promises[i].promise.resolve(event.body);
            }
        }
    }

    addWindow (window) {
        this.windows.push(window);
    }

    sendToAllWindows (name, type, messageId, body) {
        for (let i in this.windows) {
            this.windows[i].postMessage(
                {
                    header: {
                        name: name,
                        instanceId: this.instanceId,
                        type: type,
                        messageId: messageId,
                        sender: this.windowName
                    },
                    body: body
                },
                location.protocol + '//' + location.host
            );
        }
    }
}


/***/ }),

/***/ "./src/js/shared/Messaging/OperationExecutor.js":
/*!******************************************************!*\
  !*** ./src/js/shared/Messaging/OperationExecutor.js ***!
  \******************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ OperationExecutor)
/* harmony export */ });
class OperationExecutor {
    event;
    listeners;
    success;
    fail;

    ended = false;
    listenersLeftToCall = 0;
    startedAt;
    // Time in miliseconds.
    waitingTime = 150;
    waitingInterval;

    constructor (event, listeners, success, fail) {
        this.event = event;
        this.listeners = listeners;
        this.success = success;
        this.fail = fail;

        this.listenersLeftToCall = this.listeners.length;
        this.startedAt = (new Date()).getTime();

        this.decideToSuccessOrFail();

        for (let i in listeners) {
            let operation = listeners[i];
            operation.listener.call(this, event.body, (data) => {
                this.listenersLeftToCall--;
                this.storeData(data);
                this.decideToSuccessOrFail();
            }, () => {
                this.decideToSuccessOrFail();
            });
        }

        this.waitingInterval = setInterval(() => {
            this.decideToSuccessOrFail();
        }, 10);
    }

    end () {
        clearInterval(this.waitingInterval);
        this.ended = true;
    }

    storeData (data) {
        if (typeof(data) === 'object') {
            this.data = {...data, ...this.data};
        }
    }

    decideToSuccessOrFail () {
        if (this.ended) {
            return;
        }

        if (this.listenersLeftToCall === 0) {
            this.success(this.event, this.data);
            this.end();
        }

        if (this.waitingTimeExceeded()) {
            console.error(`TuliaEditor: End operation with fail. Did You forget to call "success" or "fail" callback in some listener of "${this.event.header.name}" operation?`);
            this.fail(this.event);
            this.end();
        }
    }

    waitingTimeExceeded () {
        return (new Date()).getTime() - this.startedAt >= this.waitingTime;
    }
}


/***/ }),

/***/ "./src/js/shared/Messaging/OperationPromise.js":
/*!*****************************************************!*\
  !*** ./src/js/shared/Messaging/OperationPromise.js ***!
  \*****************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ OperationPromise)
/* harmony export */ });
class OperationPromise {
    nativePromise;
    operation;
    body;
    numberOfRequiredDeliveryConfirmations;

    resolveCallback;
    rejectCallback;

    confirmations = 0;
    rejections = 0;

    responseBody = {};

    constructor (operation, body, numberOfRequiredDeliveryConfirmations) {
        this.numberOfRequiredDeliveryConfirmations = numberOfRequiredDeliveryConfirmations;
        this.operation = operation;
        this.body = body;

        this.nativePromise = new Promise((resolve, reject) => {
            this.resolveCallback = resolve;
            this.rejectCallback = reject;
        });
    }

    resolve (body) {
        this.responseBody = {...body, ...this.responseBody};
        this.confirmations++;
        this.decideSuccessOrFail();
    }

    reject () {
        this.rejections++;
        this.decideSuccessOrFail();
    }

    decideSuccessOrFail() {
        if (this.confirmations + this.rejections !== this.numberOfRequiredDeliveryConfirmations) {
            return;
        }

        if (this.confirmations === this.numberOfRequiredDeliveryConfirmations) {
            this.resolveCallback(this.responseBody);
        } else {
            this.rejectCallback();
        }
    }

    getNativePromise () {
        return this.nativePromise;
    }
}


/***/ }),

/***/ "./src/js/shared/Structure/Blocks/BlockHooks.js":
/*!******************************************************!*\
  !*** ./src/js/shared/Structure/Blocks/BlockHooks.js ***!
  \******************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ BlockHooks)
/* harmony export */ });
class BlockHooks {
    messenger;
    eventsCollection = {};
    bindedBlocksHooks = [];

    constructor (messenger) {
        this.messenger = messenger;

        this.listen();
    }

    forBlock (blockId) {
        this.bindedBlocksHooks.push(blockId);

        let events = this.eventsCollection[blockId] ?? [];
        this.eventsCollection[blockId] = [];

        return new BlockHook(this.messenger, blockId, events);
    }

    listen () {
        this.messenger.on('structure.element.created', (type, id) => {
            if (type !== 'block') {
                return;
            }

            if (this.bindedBlocksHooks.indexOf(id) < 0) {
                if (!this.eventsCollection[id]) {
                    this.eventsCollection[id] = [];
                }

                this.eventsCollection[id].push({
                    event: 'created',
                });
            }
        });
    }
}

class BlockHook {
    messegner;
    blockId;
    occuredEvents;
    listeners = [];
    eventsCallsTimes = {
        created: 0
    };

    constructor (messegner, blockId, occuredEvents) {
        this.messegner = messegner;
        this.blockId = blockId;
        this.occuredEvents = occuredEvents;

        this.listen();
    }

    on (event, listener) {
        this.listeners.push({
            event: event,
            listener: listener
        });

        this.callListenersForOccuredEvents(event);
    }

    callListeners (event) {
        if (this.eventAlreadyDispatched(event) === false) {
            this.eventsCallsTimes[event] = 0;
        }

        if (this.isSingularEvent(event) && this.eventsCallsTimes[event] >= 1) {
            return;
        }

        this.eventsCallsTimes[event]++;

        for (let i in this.listeners) {
            if (this.listeners[i].event === event) {
                this.listeners[i].listener.call();
            }
        }
    }

    callListenersForOccuredEvents (event) {
        for (let i in this.occuredEvents) {
            if (this.occuredEvents[i].event === event) {
                this.callListeners(event);
            }
        }
    }

    listen () {

    }

    isSingularEvent (event) {
        switch (event) {
            case 'created':
                return true;
        }

        return false;
    }

    eventAlreadyDispatched (event) {
        return !!this.eventsCallsTimes[event];
    }
}


/***/ }),

/***/ "./src/js/shared/Structure/Blocks/Blocks.js":
/*!**************************************************!*\
  !*** ./src/js/shared/Structure/Blocks/Blocks.js ***!
  \**************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ Blocks)
/* harmony export */ });
const Block = (__webpack_require__(/*! shared/Structure/Blocks/Editor/Block.js */ "./src/js/shared/Structure/Blocks/Editor/Block.js")["default"]);
const Data = (__webpack_require__(/*! shared/Structure/Blocks/Data.js */ "./src/js/shared/Structure/Blocks/Data.js")["default"]);

class Blocks {
    hooks;
    blocksOptions;
    messenger;

    constructor (hooks, blocksOptions, messenger) {
        this.hooks = hooks;
        this.blocksOptions = blocksOptions;
        this.messenger = messenger;
    }

    editor (code, props) {
        return new Block(
            props.id,
            code,
            new Data(props.id, 'editor', props, this.messenger),
            this.blocksOptions[code] ?? {},
            this.messenger
        );
    }

    manager (code, props) {
        return new Block(
            props.id,
            code,
            new Data(props.id, 'manager', props, this.messenger),
            this.blocksOptions[code] ?? {},
            this.messenger
        );
    }

    render (code, props) {
        return new Block(
            props.id,
            code,
            new Data(props.id, 'render', props, this.messenger),
            this.blocksOptions[code] ?? {},
            this.messenger
        );
    }
}


/***/ }),

/***/ "./src/js/shared/Structure/Blocks/Data.js":
/*!************************************************!*\
  !*** ./src/js/shared/Structure/Blocks/Data.js ***!
  \************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ Data)
/* harmony export */ });
const { reactive, toRaw, watch } = __webpack_require__(/*! vue */ "vue");

class Data {
    data;
    blockId;
    messenger;
    owner;
    version = 0;
    lastUpdateFromOutside = false;

    constructor (blockId, owner, props, messenger) {
        this.blockId = blockId;
        this.owner = owner;
        this.messenger = messenger;

        this.data = reactive(props.data);

        this.propagateChangesInThisInstance();
        this.watchForChangesInOtherInstance();
    }

    get reactiveData () {
        return this.data;
    }

    propagateChangesInThisInstance () {
        watch(this.data, async (newData, oldData) => {
            if (this.lastUpdateFromOutside) {
                return;
            }

            this.version++;

            this.messenger.execute('structure.block.data.update', {
                owner: this.owner,
                data: toRaw(newData),
                blockId: this.blockId,
                version: this.version,
            });
        });
    }

    watchForChangesInOtherInstance () {
        this.messenger.operation('structure.block.data.update', (data, success, fail) => {
            // Catch only operations for the same block in all windows
            if (data.blockId === this.blockId && data.owner !== this.owner) {
                this.handleDataUpdate(data);
            }

            success();
        });
    }

    handleDataUpdate (newData) {
        if (newData.version > this.version) {
            for (let i in newData.data) {
                this.lastUpdateFromOutside = true;
                this.data[i] = newData.data[i];
            }
            this.lastUpdateFromOutside = false;
            this.version = newData.version;
        }
    }
}


/***/ }),

/***/ "./src/js/shared/Structure/Blocks/Editor/Block.js":
/*!********************************************************!*\
  !*** ./src/js/shared/Structure/Blocks/Editor/Block.js ***!
  \********************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ Block)
/* harmony export */ });
class Block {
    id;
    code;
    options;
    dataSynchronizer;
    messenger;

    constructor (id, code, dataSynchronizer, options, messenger) {
        this.id = id;
        this.code = code;
        this.options = options;
        this.dataSynchronizer = dataSynchronizer;
        this.messenger = messenger;

        this.messenger.on('structure.element.created', (type, id) => {
            if (type === 'block' && id === this.id) {
                this.notify('created');
            }
        });
    }

    on (event, listener) {
        this.messenger.on(this.generateBlockPrefix(event), listener);
    }

    execute (operation, body) {
        return this.messenger.execute(this.generateBlockPrefix(operation), body);
    }

    notify (notification, ...body) {
        this.messenger.notify(this.generateBlockPrefix(notification), ...body);
    }

    operation (operation, listener) {
        this.messenger.operation(this.generateBlockPrefix(operation), listener);
    }

    get data () {
        return this.dataSynchronizer.reactiveData;
    }

    generateBlockPrefix (operation) {
        return `structure.block.instance.${this.code}.${this.id}.${operation}`;
    }
}


/***/ }),

/***/ "./src/js/shared/Structure/Blocks/Registry.js":
/*!****************************************************!*\
  !*** ./src/js/shared/Structure/Blocks/Registry.js ***!
  \****************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ Registry)
/* harmony export */ });
class Registry {
    blocks;

    constructor (blocks) {
        this.blocks = blocks;
    }

    get (code) {
        return this.blocks[code];
    }
}


/***/ }),

/***/ "./src/js/shared/Structure/Fixer.js":
/*!******************************************!*\
  !*** ./src/js/shared/Structure/Fixer.js ***!
  \******************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ Fixer)
/* harmony export */ });
const ObjectCloner = (__webpack_require__(/*! shared/Utils/ObjectCloner.js */ "./src/js/shared/Utils/ObjectCloner.js")["default"]);
const { v4 } = __webpack_require__(/*! uuid */ "./node_modules/uuid/index.js");

class Fixer {
    usedIds = [];

    fixStructure (structure) {
        let workingCopy = ObjectCloner.deepClone(structure);

        for (let i in workingCopy.sections) {
            workingCopy.sections[i] = this.fixSection(workingCopy.sections[i]);
        }

        return workingCopy;
    }

    fixSection (section) {
        this.fixElementId(section);

        if (!section.type) {
            section.type = 'section';
        }

        if (!section.metadata) {
            section.metadata = {
                hovered: false,
                selected: false,
                parent: {
                    type: null,
                    id: null,
                }
            };
        }

        if (!section.rows || section.rows.length === 0) {
            section.rows = [{}];
        }

        for (let i in section.rows) {
            section.rows[i] = this.fixRow(section.rows[i]);
        }

        return section;
    }

    fixRow (row, sectionId) {
        this.fixElementId(row);

        if (!row.type) {
            row.type = 'row';
        }

        if (!row.metadata) {
            row.metadata = {
                hovered: false,
                selected: false,
                parent: {
                    type: 'section',
                    id: sectionId,
                }
            };
        }

        if (!row.columns || row.columns.length === 0) {
            row.columns = [{}];
        }

        for (let i in row.columns) {
            row.columns[i] = this.fixColumn(row.columns[i]);
        }

        return row;
    }

    fixColumn (column, rowId) {
        this.fixElementId(column);

        if (!column.type) {
            column.type = 'column';
        }

        if (!column.sizes) {
            column.sizes = {
                xxl: { size: null },
                xl: { size: null },
                lg: { size: null },
                md: { size: null },
                sm: { size: null },
                xs: { size: null },
            };
        }

        if (!column.metadata) {
            column.metadata = {
                hovered: false,
                selected: false,
                parent: {
                    type: 'row',
                    id: rowId,
                }
            };
        }

        if (!column.blocks) {
            column.blocks = [];
        }

        for (let i in column.blocks) {
            column.blocks[i] = this.fixBlock(column.blocks[i]);
        }

        return column;
    }

    fixBlock (block, columnId) {
        this.fixElementId(block);

        if (!block.type) {
            block.type = 'block';
        }

        if (!block.metadata) {
            block.metadata = {
                hovered: false,
                selected: false,
                parent: {
                    type: 'column',
                    id: columnId,
                }
            };
        }

        if (!block.data) {
            block.data = {};
        }

        return block;
    }

    fixElementId (element) {
        if (!element.id || this.usedIds.indexOf(element.id) >= 0) {
            element.id = v4();
        }

        this.usedIds.push(element.id);
    }
}


/***/ }),

/***/ "./src/js/shared/Structure/Selection/Boundaries/Hovered.js":
/*!*****************************************************************!*\
  !*** ./src/js/shared/Structure/Selection/Boundaries/Hovered.js ***!
  \*****************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ Hovered)
/* harmony export */ });
class Hovered {
    hoveredElement;
    viewUpdater;

    constructor (viewUpdater) {
        this.viewUpdater = viewUpdater;
    }

    highlight (element, type, id) {
        this.hoveredElement = {
            element: element,
            type: type,
            id: id,
        };
        this.updatePosition();
    }

    clear () {
        this.hoveredElement = null;
        this.resetPosition();
    }

    hide () {
        this.hoveredElement = null;
        this.resetPosition();
    }

    update () {
        this.updatePosition();
    }

    /*disable () {
        this.disabledStack++;

        if (this.isDisabled()) {
            this.resetPosition();
        }
    }

    isDisabled () {
        return this.disabledStack > 0;
    }

    enable () {
        this.disabledStack--;

        if (this.isDisabled() === false) {
            this.updatePosition();
        }
    }*/

    updatePosition () {
        /*if (this.isDisabled()) {
            return;
        }*/

        if (!this.hoveredElement) {
            return;
        }

        this.viewUpdater({
            top: this.hoveredElement.element.offsetTop,
            left: this.hoveredElement.element.offsetLeft,
            width: this.hoveredElement.element.offsetWidth,
            height: this.hoveredElement.element.offsetHeight,
        });
    }

    resetPosition () {
        this.viewUpdater({
            top: -100,
            left: -100,
            width: 0,
            height: 0,
        });
    }
}


/***/ }),

/***/ "./src/js/shared/Structure/Selection/Boundaries/Selected.js":
/*!******************************************************************!*\
  !*** ./src/js/shared/Structure/Selection/Boundaries/Selected.js ***!
  \******************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ Selected)
/* harmony export */ });
class Selected {
    selectedElement;
    positionUpdateAnimationFrameHandle;
    viewUpdater;
    hidden = false;

    constructor (viewUpdater) {
        this.viewUpdater = viewUpdater;
    }

    highlightSelected (element) {
        this.selectedElement = element;
        this.updatePosition();
    }

    clearSelectionHighlight () {
        this.selectedElement = null;
        this.resetPosition();
    }

    hide () {
        this.hidden = true;
        this.resetPosition();
    }

    show () {
        this.hidden = false;
        this.updatePosition();
    }

    update () {
        this.updatePosition();
    }

    keepUpdatePositionFor (microseconds) {
        let self = this;

        function doTheUdate() {
            self.updatePosition();
            self.positionUpdateAnimationFrameHandle = requestAnimationFrame(doTheUdate);
        }

        requestAnimationFrame(doTheUdate);

        setTimeout(() => {
            cancelAnimationFrame(self.positionUpdateAnimationFrameHandle);
        }, microseconds);
    }

    updatePosition () {
        if (!this.selectedElement || this.hidden) {
            return;
        }

        let doc = this.selectedElement.ownerDocument;

        this.viewUpdater({
            top: this.selectedElement.offsetTop,
            left: this.selectedElement.offsetLeft,
            width: this.selectedElement.offsetWidth,
            height: this.selectedElement.offsetHeight,
            tagName: this.selectedElement.dataset.tagname ?? this.selectedElement.tagName,
            // @todo Remove dependency to jQuery
            scrollTop : $(doc.defaultView || doc.parentWindow).scrollTop()
        });
    }

    resetPosition () {
        this.viewUpdater({
            top: -100,
            left: -100,
            width: 0,
            height: 0,
            tagName: null,
            scrollTop : 0
        });
    }
};


/***/ }),

/***/ "./src/js/shared/Structure/Selection/HoverResolver.js":
/*!************************************************************!*\
  !*** ./src/js/shared/Structure/Selection/HoverResolver.js ***!
  \************************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ HoverResolver)
/* harmony export */ });
class HoverResolver {
    hoveredElement;
    hoveredStack = [];
    selection;

    constructor (selection) {
        this.selection = selection;
    }

    enter (element, type, id) {
        this.hoveredElement = {
            element: element,
            type: type,
            id: id
        };
        this.hoveredStack.push(this.hoveredElement);
        this.selection.hover(this.hoveredElement.type, this.hoveredElement.id);
    }

    leave () {
        this.hoveredStack.pop();

        if (this.hoveredStack[this.hoveredStack.length - 1]) {
            this.hoveredElement = this.hoveredStack[this.hoveredStack.length - 1];
            this.selection.hover(this.hoveredElement.type, this.hoveredElement.id);
        } else {
            this.selection.resetHovered();
        }
    }
}


/***/ }),

/***/ "./src/js/shared/Structure/Selection/Selection.js":
/*!********************************************************!*\
  !*** ./src/js/shared/Structure/Selection/Selection.js ***!
  \********************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ Selection)
/* harmony export */ });
class Selection {
    structure;
    messenger;
    selected = null;
    hovered = null;
    hoveringDisabled = false;
    selectingDisabled = false;

    constructor (structure, messenger) {
        this.messenger = messenger;
        this.structure = structure;

        this.build();
    }

    disableHovering () {
        this.messenger.notify('structure.selection.hovering.disable');
    }

    enableHovering () {
        this.messenger.notify('structure.selection.hovering.enable');
    }

    disableSelecting () {
        this.messenger.notify('structure.selection.selecting.disable');
    }

    enableSelecting () {
        this.messenger.notify('structure.selection.selecting.enable');
    }

    update () {
        if (this.selected) {
            this.select(this.selected.type, this.selected.id);
        }
        if (this.hovered) {
            this.hover(this.hovered.type, this.hovered.id);
        }
    }

    select (type, id) {
        if (this.selectingDisabled) {
            return;
        }

        this.messenger.notify('structure.selection.deselect');
        this.messenger.notify('structure.selection.select', type, id);
    }

    hover (type, id) {
        if (this.hoveringDisabled) {
            return;
        }

        this.messenger.notify('structure.selection.dehover');
        this.messenger.notify('structure.selection.hover', type, id);
    }

    resetSelection () {
        if (this.selectingDisabled) {
            return;
        }

        this.messenger.notify('structure.selection.deselect');
    }

    resetHovered () {
        if (this.hoveringDisabled) {
            return;
        }

        this.messenger.notify('structure.selection.dehover');
    }

    reset () {
        this.resetSelection();
        this.resetHovered();
    }

    getSelected () {
        return this.selected;
    }

    getHovered () {
        return this.hovered;
    }

    get (type, id) {
        for (let s in this.structure.sections) {
            if (type === 'section' && this.structure.sections[s].id === id) {
                return this.structure.sections[s];
            }

            for (let r in this.structure.sections[s].rows) {
                if (type === 'row' && this.structure.sections[s].rows[r].id === id) {
                    return this.structure.sections[s].rows[r];
                }

                for (let c in this.structure.sections[s].rows[r].columns) {
                    if (type === 'column' && this.structure.sections[s].rows[r].columns[c].id === id) {
                        return this.structure.sections[s].rows[r].columns[c];
                    }

                    for (let b in this.structure.sections[s].rows[r].columns[c].blocks) {
                        if (type === 'block' && this.structure.sections[s].rows[r].columns[c].blocks[b].id === id) {
                            return this.structure.sections[s].rows[r].columns[c].blocks[b];
                        }
                    }
                }
            }
        }

        return null;
    }

    forEach (callable) {
        for (let s in this.structure.sections) {
            callable(this.structure.sections[s]);

            for (let r in this.structure.sections[s].rows) {
                callable(this.structure.sections[s].rows[r]);

                for (let c in this.structure.sections[s].rows[r].columns) {
                    callable(this.structure.sections[s].rows[r].columns[c]);

                    for (let b in this.structure.sections[s].rows[r].columns[c].blocks) {
                        callable(this.structure.sections[s].rows[r].columns[c].blocks[b]);
                    }
                }
            }
        }
    }

    build () {
        this.messenger.on('structure.selection.select', (type, id) => {
            let element = this.get(type, id);

            if (!element) {
                return;
            }

            element.metadata.selected = true;
            this.selected = { type: type, id: id };
            this.messenger.notify('structure.selection.selected', type, id);
        });
        this.messenger.on('structure.selection.hover', (type, id) => {
            let element = this.get(type, id);

            if (!element) {
                return;
            }

            element.metadata.hovered = true;
            this.hovered = { type: type, id: id };
            this.messenger.notify('structure.selection.hovered', type, id);
        });
        this.messenger.on('structure.selection.deselect', () => {
            this.forEach((element) => {
                if (element.metadata.selected) {
                    element.metadata.selected = false;
                }
            });
            this.selected = null;
            this.messenger.notify('structure.selection.deselected');
        });
        this.messenger.on('structure.selection.dehover', () => {
            this.forEach((element) => {
                if (element.metadata.hovered) {
                    element.metadata.hovered = false;
                }
            });
            this.hovered = null;
            this.messenger.notify('structure.selection.dehovered');
        });
        /*this.messenger.on('structure.hovering.clear', () => {
            this.selection.resetHovered();
        });*/

        this.messenger.on('structure.selection.hovering.disable', () => {
            this.hoveringDisabled = true;
        });
        this.messenger.on('structure.selection.hovering.enable', () => {
            this.hoveringDisabled = false;
        });
        this.messenger.on('structure.selection.selecting.disable', () => {
            this.selectingDisabled = true;
        });
        this.messenger.on('structure.selection.selecting.enable', () => {
            this.selectingDisabled = false;
        });
    }
}


/***/ }),

/***/ "./src/js/shared/Structure/StructureManipulator.js":
/*!*********************************************************!*\
  !*** ./src/js/shared/Structure/StructureManipulator.js ***!
  \*********************************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ StructureManipulator)
/* harmony export */ });
const { toRaw } = __webpack_require__(/*! vue */ "vue");
const Fixer = (__webpack_require__(/*! shared/Structure/Fixer.js */ "./src/js/shared/Structure/Fixer.js")["default"]);

class StructureManipulator {
    structure;
    messenger;
    fixer;

    constructor (structure, messenger) {
        this.messenger = messenger;
        this.structure = structure;
        this.fixer = new Fixer();

        this._listenToUpdateElement();
        this._listenToRemoveElement();
        this._listenToMoveElementUsingDelta();
        this._listenToNewSection();
        this._listenToNewBlock();
    }

    update (newStructure) {
        this.structure.sections = newStructure.sections;
        this.messenger.notify('structure.updated');
    }

    find (id) {
        for (let sk in this.structure.sections) {
            if (this.structure.sections[sk].id === id) {
                return this.structure.sections[sk];
            }

            let rows = this.structure.sections[sk].rows;

            for (let rk in rows) {
                if (rows[rk].id === id) {
                    return rows[rk];
                }

                let columns = rows[rk].columns;

                for (let ck in columns) {
                    if (columns[ck].id === id) {
                        return columns[ck];
                    }

                    let blocks = columns[ck].blocks;

                    for (let bk in blocks) {
                        if (blocks[bk].id === id) {
                            return blocks[bk];
                        }
                    }
                }
            }
        }

        return null;
    }

    findParent (childId) {
        let parent = null;

        for (let sk in this.structure.sections) {
            parent = this.structure.sections[sk];

            let rows = this.structure.sections[sk].rows;

            for (let rk in rows) {
                if (rows[rk].id === childId) {
                    return parent;
                }

                parent = rows[rk];

                let columns = rows[rk].columns;

                for (let ck in columns) {
                    if (columns[ck].id === childId) {
                        return parent;
                    }

                    parent = columns[ck];

                    let blocks = columns[ck].blocks;

                    for (let bk in blocks) {
                        if (blocks[bk].id === childId) {
                            return parent;
                        }
                    }
                }
            }
        }

        return null;
    }

    newSection () {
        this.messenger.notify('structure.element.new-section', this.fixer.fixSection({}));
    }

    _listenToNewSection () {
        this.messenger.on('structure.element.new-section', (newSection) => {
            this._doNewSection(newSection);
        });
    }

    _doNewSection (newSection) {
        this.structure.sections.push(newSection);
    }

    newBlock (type, parent) {
        let block = {
            code: type
        };

        block = this.fixer.fixBlock(block);

        this.messenger.notify('structure.element.new-block', block, parent);
    }

    _listenToNewBlock () {
        this.messenger.on('structure.element.new-block', (block, parent) => {
            this._doNewBlock(block, parent);
        });
    }

    _doNewBlock (block, parent) {
        let column = this.find(parent);

        column.blocks.push(block);

        this.messenger.notify('structure.element.created', 'block', block.id);
    }

    removeElement (id) {
        this.messenger.notify('structure.element.remove', id);
    }

    _listenToRemoveElement () {
        this.messenger.on('structure.element.remove', (id) => {
            this._doRemoveElement(id);
        });
    }

    _doRemoveElement (id) {
        let removed = false;

        loop:
        for (let sk in this.structure.sections) {
            if (this.structure.sections[sk].id === id) {
                this.structure.sections.splice(sk, 1);
                removed = true;
                break;
            }

            let rows = this.structure.sections[sk].rows;

            for (let rk in rows) {
                if (this.structure.sections[sk].rows[rk].id === id) {
                    this.structure.sections[sk].rows.splice(rk, 1);
                    removed = true;
                    break loop;
                }

                let columns = rows[rk].columns;

                for (let ck in columns) {
                    if (this.structure.sections[sk].rows[rk].columns[ck].id === id) {
                        this.structure.sections[sk].rows[rk].columns.splice(ck, 1);
                        removed = true;
                        break loop;
                    }

                    let blocks = columns[ck].blocks;

                    for (let bk in blocks) {
                        if (this.structure.sections[sk].rows[rk].columns[ck].blocks[bk].id === id) {
                            this.structure.sections[sk].rows[rk].columns[ck].blocks.splice(bk, 1);
                            removed = true;
                            break loop;
                        }
                    }
                }
            }
        }

        if (removed) {
            this.messenger.notify('structure.element.removed', id);
        }
    }

    updateElement (element) {
        this.messenger.notify('structure.element.update', element.id, toRaw(element));
    }

    _listenToUpdateElement () {
        this.messenger.on('structure.element.update', (id, newElement) => {
            this._doUpdateElement(id, newElement);
        });
    }

    _doUpdateElement (id, newElement) {
        let currentElement = this.find(id);

        if (!currentElement) {
            return;
        }

        // Implement basic comparison, only replace every key from newElement to currentElement.
        // @todo Try to detect which data changed, and update only the changed.
        for (let ni in newElement) {
            currentElement[ni] = newElement[ni];
        }

        this.messenger.notify('structure.element.updated', id);
    }

    moveElementUsingDelta (delta) {
        this.messenger.notify('structure.element.move', delta);
    }

    _listenToMoveElementUsingDelta () {
        this.messenger.on('structure.element.move', (delta) => {
            let element = toRaw(this.find(delta.element.id));

            if (delta.from.parent.type === 'structure' && delta.to.parent.type === 'structure') {
                this._doRemoveElement(delta.element.id);
                this.structure.sections.splice(delta.to.index, 0, element);
            } else {
                let newParent = this.find(delta.to.parent.id);

                this._doRemoveElement(delta.element.id);

                if (newParent.type === 'column') {
                    newParent.blocks.splice(delta.to.index, 0, element);
                } else if (newParent.type === 'row') {
                    newParent.columns.splice(delta.to.index, 0, element);
                } else if (newParent.type === 'section') {
                    newParent.rows.splice(delta.to.index, 0, element);
                }
            }

            this.messenger.notify('structure.element.moved', delta);
            this.messenger.notify('structure.element.updated', delta.element.id);
        });
    }
};


/***/ }),

/***/ "./src/js/shared/Utils/ClassObserver.js":
/*!**********************************************!*\
  !*** ./src/js/shared/Utils/ClassObserver.js ***!
  \**********************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ ClassObserver)
/* harmony export */ });
class ClassObserver {
    element;
    callback;
    classname;

    constructor (element, classname, callback) {
        this.callback = callback;
        this.element = element;
        this.classname = classname;

        this.observe();
    }

    observe() {
        let prevClass = this.element.classList.contains(this.classname);

        let observer = new MutationObserver((mutations) => {
            mutations.forEach((mutation) => {
                if (mutation.attributeName === 'class') {
                    let currentClass = mutation.target.classList.contains(this.classname);

                    if (prevClass !== currentClass) {
                        prevClass = currentClass;

                        this.callback(currentClass);
                    }
                }
            });
        });

        observer.observe(this.element, {attributes: true});
    }
};


/***/ }),

/***/ "./src/js/shared/Utils/Location.js":
/*!*****************************************!*\
  !*** ./src/js/shared/Utils/Location.js ***!
  \*****************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ Location)
/* harmony export */ });
class Location {
    static getQueryVariable (variable) {
        let query = window.location.search.substring(1);
        let vars = query.split('&');

        for (let i = 0; i < vars.length; i++) {
            let pair = vars[i].split('=');

            if (decodeURIComponent(pair[0]) === variable) {
                return decodeURIComponent(pair[1]);
            }
        }

        console.error('Query variable %s not found', variable);
    }
};


/***/ }),

/***/ "./src/js/shared/Utils/ObjectCloner.js":
/*!*********************************************!*\
  !*** ./src/js/shared/Utils/ObjectCloner.js ***!
  \*********************************************/
/***/ ((__unused_webpack_module, __webpack_exports__, __webpack_require__) => {

"use strict";
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "default": () => (/* binding */ ObjectCloner)
/* harmony export */ });
class ObjectCloner {
    static deepClone (source) {
        return JSON.parse(JSON.stringify(source));
    }
};


/***/ }),

/***/ "Tulia":
/*!************************!*\
  !*** external "Tulia" ***!
  \************************/
/***/ ((module) => {

"use strict";
module.exports = window["Tulia"];

/***/ }),

/***/ "vue":
/*!**********************!*\
  !*** external "Vue" ***!
  \**********************/
/***/ ((module) => {

"use strict";
module.exports = window["Vue"];

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
// This entry need to be wrapped in an IIFE because it need to be in strict mode.
(() => {
"use strict";
/*!***************************************!*\
  !*** ./src/js/tulia-editor.editor.js ***!
  \***************************************/
__webpack_require__.r(__webpack_exports__);
/* harmony export */ __webpack_require__.d(__webpack_exports__, {
/* harmony export */   "Canvas": () => (/* binding */ Canvas)
/* harmony export */ });
/* harmony import */ var _css_tulia_editor_editor_scss__WEBPACK_IMPORTED_MODULE_0__ = __webpack_require__(/*! ./../css/tulia-editor.editor.scss */ "./src/css/tulia-editor.editor.scss");


const Vue = __webpack_require__(/*! vue */ "vue");
const Messenger = (__webpack_require__(/*! shared/Messaging/Messenger.js */ "./src/js/shared/Messaging/Messenger.js")["default"]);
const Location = (__webpack_require__(/*! shared/Utils/Location.js */ "./src/js/shared/Utils/Location.js")["default"]);
const EventDispatcher = (__webpack_require__(/*! shared/EventDispatcher.js */ "./src/js/shared/EventDispatcher.js")["default"]);
const Translator = (__webpack_require__(/*! shared/I18n/Translator.js */ "./src/js/shared/I18n/Translator.js")["default"]);
const EditorRoot = (__webpack_require__(/*! components/Editor/Root.vue */ "./src/js/Components/Editor/Root.vue")["default"]);
const ObjectCloner = (__webpack_require__(/*! shared/Utils/ObjectCloner.js */ "./src/js/shared/Utils/ObjectCloner.js")["default"]);
const extensions = (__webpack_require__(/*! extensions/extensions.js */ "./src/js/extensions/extensions.js")["default"]);
const blocks = (__webpack_require__(/*! blocks/blocks.js */ "./src/js/blocks/blocks.js")["default"]);


class Canvas {
    instanceId = null;
    vue = null;

    constructor () {
        this.instanceId = Location.getQueryVariable('tuliaEditorInstance');

        this.start();
    }

    start () {
        let self = this;
        let messenger = new Messenger(this.instanceId, 'editor', [window, window.parent]);

        messenger.on('editor.ready', () => {
            messenger.execute('editor.init.fetch').then((options) => {
                self.vue = Vue.createApp(EditorRoot, {
                    container: {
                        translator: new Translator(
                            options.locale,
                            options.fallback_locales,
                            options.translations
                        ),
                        eventDispatcher: new EventDispatcher(),
                        messenger: messenger,
                    },
                    instanceId: self.instanceId,
                    options: options,
                    availableBlocks: blocks,
                    structure: ObjectCloner.deepClone(options.structure.source)
                });
                self.loadExtensions(self.vue);
                self.loadBlocks(self.vue);
                self.vue.config.devtools = true;
                self.vue.config.performance = true;

                self.vue.mount('#tulia-editor');
            });
        });
    }

    loadExtensions (vueApp) {
        for (let i in extensions) {
            vueApp.component(i, extensions[i]);
        }
    }

    loadBlocks (vueApp) {
        for (let i in blocks) {
            vueApp.component('block-' + blocks[i].code + '-editor', blocks[i].editor);
            vueApp.component('block-' + blocks[i].code + '-render', blocks[i].render);
        }
    }
}

})();

window.TuliaEditor = __webpack_exports__;
/******/ })()
;
//# sourceMappingURL=tulia-editor.editor.js.map