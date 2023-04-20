import Translator from "core/Shared/I18n/Translator";
import VueFactory from "core/Shared/Vue/VueFactory";
import EventBus from "core/Shared/Bus/Event/EventBus";
import BlockInstantiator from "core/Shared/Structure/Element/Instantiator/Block/BlockInstantiator";
import ColumnInstantiator from "core/Shared/Structure/Element/Instantiator/Column/ColumnInstantiator";
import RowInstantiator from "core/Shared/Structure/Element/Instantiator/Row/RowInstantiator";
import SectionInstantiator from "core/Shared/Structure/Element/Instantiator/Section/SectionInstantiator";
import BlockRegistry from "core/Shared/Structure/Block/BlockRegistry";
import ElementConfigStoreRegistry from "core/Shared/Structure/Element/Config/ElementConfigStoreRegistry";
import ElementDataStoreRegistry from "core/Shared/Structure/Element/Data/ElementDataStoreRegistry";
import ExtensionRegistryFactory from "core/Shared/Extension/ExtensionRegistryFactory";
import ControlRegistry from "core/Shared/Control/ControlRegistry";
import ExtensionInstantiator from "core/Shared/Extension/Instance/ExtensionInstantiator";

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
        this.register('eventBus', EventBus);
        this.registerFactory('translator', () => new Translator(this.options.locale, this.options.fallback_locales, this.getParameter('options.translations')));
        this.register('vueFactory', VueFactory);
        this.registerFactory('instantiator.block', () => new BlockInstantiator(this.get('messenger'), this.get('element.config.registry'), this.get('element.data.registry'), this.get('blocks.registry'), this.get('structure.store')));
        this.registerFactory('instantiator.column', () => new ColumnInstantiator(this.get('messenger'), this.get('element.config.registry'), this.get('element.data.registry')));
        this.registerFactory('instantiator.row', () => new RowInstantiator(this.get('messenger'), this.get('element.config.registry'), this.get('element.data.registry')));
        this.registerFactory('instantiator.section', () => new SectionInstantiator(this.get('messenger'), this.get('element.config.registry'), this.get('element.data.registry')));
        this.registerFactory('instantiator.extension', () => new ExtensionInstantiator(this.get('messenger')));
        this.registerFactory('blocks.registry', () => new BlockRegistry(this.getParameter('options.blocks')));
        this.registerFactory('element.config.registry', () => new ElementConfigStoreRegistry(this.get('element.config.storeFactory')));
        this.registerFactory('element.data.registry', () => new ElementDataStoreRegistry(this.get('element.data.storeFactory')));
        this.registerFactory('controls.registry', () => new ControlRegistry(this.getParameter('options.controls')));
        this.registerFactory('extensions.registry', () => ExtensionRegistryFactory.create(this, this.getParameter('options.extensions')));
    }

    registerFactory(id, factory, options) {
        this.definitions[id] = {
            factory,
            options: options ?? {},
        };
    }

    register(id, classref, args, options) {
        this.definitions[id] = {
            classref,
            args,
            options: options ?? {},
        };
    }

    create(id) {
        if (!this.definitions[id]) {
            throw new Error(`Service "${id}" does not exists.`);
        }

        const definition = this.definitions[id];
        let service = null;

        if (definition.factory) {
            service = definition.factory.apply(this);
        } else if (definition.classref) {
            service = new definition.classref(...this.buildArgs(definition.args));
        } else {
            throw new Error(`Definition for "${id}" service must contains a factory or classref.`);
        }

        if (!service) {
            throw new Error(`Factory for "${id}" service does not return a valid service object.`);
        }

        return this.services[id] = service;
    }

    buildArgs(args) {
        let result = [];

        for (let i in args) {
            if (typeof args[i] === 'string') {
                const type = args[i].substring(0, 1);
                const value = args[i].substring(1, args[i].length);

                if (type === '@') {
                    result.push(this.get(value));
                } else if (type === '%') {
                    result.push(this.getParameter(value));
                } else {
                    result.push(args[i]);
                }

                continue;
            }

            result.push(args[i]);
        }

        return result;
    }

    finish() {
        const eventBus = this.get('eventBus');

        for (let id in this.definitions) {
            const def = this.definitions[id];

            if (def.options.tags) {
                for (let t in def.options.tags) {
                    const tag = def.options.tags[t];

                    if (tag.name === 'event_subscriber') {
                        if (!def.classref) {
                            throw new Error(`Definition of ${id} does not have classref, so cannot be registered as event_subscriber.`);
                        }

                        const events = def.classref.getSubscribedEvents();

                        for (let e in events) {
                            eventBus.listen(e, (data, event) => {
                                const service = this.get(id);
                                service[events[e]].call(service, data, event);
                            });
                        }
                    }
                }
            }
        }
    }
}
