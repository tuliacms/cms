/**!
 * Tulia Filemanager
 * @author	Adam Banaszkiewicz <adam@codevia.com>
 * @license MIT only with Tulia CMS package. Usage outside the Tulia CMS package is prohibited.
 */
const Filemanager = require('components/Filemanager.vue').default;

export default {
    create: function (selector, options) {
        const data = {
            options: options
        };

        this.instances[selector] = Vue.createApp(Filemanager, data);
        this.instances[selector].config.devtools = true;
        this.instances[selector].config.performance = true;
        this.instances[selector].mount(selector);
    },
    trans: function (locale, domain, translations) {

    },
    translations: {},
    instances: {},
    defaults: {}
}
