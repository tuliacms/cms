/**!
 * Tulia Editor
 * @author	Adam Banaszkiewicz <adam@codevia.com>
 * @license MIT only with Tulia CMS package. Usage outside the Tulia CMS package is prohibited.
 */
const Admin = require('./Admin.js').default;
const Editor = require('./Editor.js').default;
const _ = require('lodash');

export default {
    Admin,
    Editor,
    block: function (block) {
        const requiredProps = [
            'code',
            'name',
            'icon',
            'manager',
            'editor',
            'render',
            'store',
        ];

        for (let prop of requiredProps) {
            if (block.hasOwnProperty(prop) === false) {
                return;
                console.error(`Missing property "${prop}" in block. Cannot be registered.`);
                return;
            }
        }

        block.code = `${block.theme}:${block.code}`;

        this.blocks[block.code] = block;
    },
    trans: function (locale, domain, translations) {
        if (!this.translations[locale]) {
            this.translations[locale] = {};
        }
        if (!this.translations[locale][domain]) {
            this.translations[locale][domain] = {};
        }

        _.assign(this.translations[locale][domain], translations);
    },
    extensions: require("extensions/extensions.js").default,
    blocks: require("blocks/blocks.js").default,
    controls: require("controls/controls.js").default,
    directives: require("directives/directives.js").default,
    translations: {},
    instances: {},
    config: {
        set: function (key, value) {
            const parts = key.split('.');

            let workingObject = this.dynamic;

            for (let i in parts) {
                i = parseInt(i);

                if (parts.length - 1 === i) {
                    workingObject[parts[i]] = value;
                }

                if (!workingObject.hasOwnProperty(parts[i])) {
                    workingObject[parts[i]] = {};
                }

                workingObject = workingObject[parts[i]];
            }
        },
        dynamic: {},
        defaults: {
            structure: {},
            editor: {
                view: null,
                preview: null,
            },
            show_preview_in_canvas: false,
            /**
             * 'default' - Default view, only preview.
             * 'editor' - Opens editor immediately.
             */
            start_point: 'default',
            sink: {
                // HTML input/textarea selector, where to store the structure.
                structure: null,
                // HTML input/textarea selector, where to store the rendered content.
                content: null,
            },
            canvas: {
                size: {
                    default: 'xl',
                    breakpoints: [
                        { name: 'xxl', width: 1440 },
                        { name: 'xl', width: 1220 },
                        { name: 'lg', width: 1000 },
                        { name: 'md', width: 770 },
                        { name: 'sm', width: 580 },
                        { name: 'xs', width: 320 },
                    ],
                },
            },
            elements: {
                style: {
                    spacers: {
                        // Maximum implemented spacers in Bootstrap
                        max: 5,
                    },
                },
            },
            locale: 'en_en',
            fallback_locales: ['en'],
            // Blocks options
            blocks: {},
            // Columns options
            columns: {},
            // Rows options
            rows: {},
            // Sections options
            sections: {},
            filemanager: {
                image_resolve_path: null,
                endpoint: null,
            },
            // Themes supported by this instance of Editor
            themes: [],
            // CSS framework, supported by Theme(s).
            css_framework: '',
        }
    },
}
