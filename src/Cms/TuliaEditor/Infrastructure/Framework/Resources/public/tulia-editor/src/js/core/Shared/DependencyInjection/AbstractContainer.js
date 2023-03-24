import Translator from "core/Shared/I18n/Translator";
import VueFactory from "core/Shared/Vue/VueFactory";
import EventBus from "core/Shared/Bus/Event/EventBus";

export default class AbstractContainer {
    constructor(options) {
        this.options = options;
        this.definitions = {};
        this.services = {};
        this.parameters = { options: options };
    }

    get(id) {
        return this.services[id] ?? this.create(id);
    }

    set(id, obj) {
        this.services[id] = obj;
    }

    setParameter(name, data) {
        this.parameters[name] = data;
    }

    getParameter(name) {
        return this.parameters[name];
    }

    build() {
        this.register('eventBus', () => new EventBus());
        this.register('translator', this._buildTranslator);
        this.register('vueFactory', () => new VueFactory());
    }

    register(id, factory, options) {
        this.definitions[id] = {
            factory,
            options: options ?? {},
        };
    }

    create(id) {
        if (!this.definitions[id]) {
            throw new Error(`Service "${id}" does not exists.`);
        }

        const service = this.definitions[id].factory.apply(this);

        if (!service) {
            throw new Error(`Factory for "${id}" service does not return a valid service object.`);
        }

        return this.services[id] = service;
    }

    finish() {
        this.get('eventBus').listen('*', (data, event) => {
            for (let id in this.definitions) {
                if (this.definitions[id].options.tags) {
                    for (let t in this.definitions[id].options.tags) {
                        if (
                            this.definitions[id].options.tags[t].name === 'event_listener'
                            && this.definitions[id].options.tags[t].on === event
                        ) {
                            const service = this.get(id);

                            service[this.definitions[id].options.tags[t].call].call(service, data, event);
                        }
                    }
                }
            }
        });
    }

    _buildTranslator() {
        return new Translator(
            this.options.locale,
            this.options.fallback_locales,
            this.getParameter('options.translations'),
        );
    }
}
