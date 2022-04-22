/**!
 * Tulia Editor
 * @author	Adam Banaszkiewicz <adam@codevia.com>
 * @license MIT only with Tulia CMS package. Usage outside the Tulia CMS package is prohibited.
 */

const Canvas = require('./Canvas.js').default;
const Editor = require('./Editor.js').default;

export default {
    Canvas,
    Editor,
    extensions: require("extensions/extensions.js").default,
    blocks: require("blocks/blocks.js").default,
    translations: {
        en: {
            save: 'Save',
            cancel: 'Cancel',
            newSection: 'New section',
            newBlock: 'New block',
            section: 'Section',
            row: 'Row',
            column: 'Column',
            block: 'Block',
            selected: 'Selected',
            structure: 'Structure',
        }
    },
    instances: {},
    defaults: {
        structure: {},
        editor: {
            view: null,
        },
        /**
         * 'default' - default view.
         * 'editor' - opens editor immediately
         */
        start_point: 'default',
        sink: {
            // HTML input/textarea selector, where to store the structure.
            structure: null,
            // HTML input/textarea selector, where to store the rendered content.
            content: null
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
                ]
            }
        },
        locale: 'en_en',
        fallback_locales: ['en']
    }
}
