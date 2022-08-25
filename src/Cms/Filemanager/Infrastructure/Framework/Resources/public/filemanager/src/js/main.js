/**!
 * Tulia Filemanager
 * @author	Adam Banaszkiewicz <adam@codevia.com>
 * @license MIT only with Tulia CMS package. Usage outside the Tulia CMS package is prohibited.
 */
import './../sass/filemanager.scss';

const Filemanager = require('components/Filemanager.vue').default;
const EventDispatcher = require('shared/EventDispatcher.js').default;
const CommandBus = require('shared/CommandBus.js').default;
const Selection = require('shared/Selection.js').default;

class Instance {
    vue;
    data;
    services;
    instance;

    constructor (selector, instance, options) {
        this.instance = instance;
        this.services = this.createServices(options);
        this.data = {
            options: options,
            instance: this.instance,
            services: this.services,
        };

        this.vue = Vue.createApp(Filemanager, this.data);

        // DEV
        //this.vue.config.devtools = true;
        //this.vue.config.performance = true;
        // PROD
        this.vue.config.devtools = false;
        this.vue.config.debug = false;
        this.vue.config.silent = true;

        this.vue.mount('#' + selector);
    }

    open () {
        this.services.commandBus.execute('open');
    }

    close () {
        this.services.commandBus.execute('close');
    }

    createServices (options) {
        const eventDispatcher = new EventDispatcher();
        const commandBus = new CommandBus();
        const selection = new Selection(eventDispatcher, options.multiple);

        return {
            eventDispatcher,
            commandBus,
            selection
        };
    }
}

const defaults = {
    targetInput: null,
    showOnInit: true,
    endpoint: null,
    multiple: false,
    filter: null,
    closeOnSelect: true,
    onSelect: (files) => {},
};

export default {
    instancesCounter: 0,
    create: function (options) {
        this.preconditions();

        this.instancesCounter++;
        return this.instances[this.instancesCounter] = new Instance(
            this.createContainerInBody(this.instancesCounter),
            this.instancesCounter,
            options
        );
    },
    trans: function (locale, domain, translations) {

    },
    createContainerInBody: function (instance) {
        let container = document.querySelector('#tulia-filemanager-container');

        if (!container) {
            container = document.createElement('div');
            container.id = 'tulia-filemanager-container';

            document.body.appendChild(container);
        }

        const instanceContainer = document.createElement('div');
        instanceContainer.id = 'tulia-filemanager-instance-' + instance;

        container.appendChild(instanceContainer);

        return instanceContainer.id;
    },
    translations: {},
    instances: {},
    defaults: {},
    preconditions() {
        if(! window.FileAPI) {
            console.error('FileAPI extension not loaded.');
            return;
        }
    }
}
