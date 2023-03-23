import AbstractContainer from "core/Shared/DependencyInjection/AbstractContainer";
import View from "core/Editor/View/View";
import BuildVueOnHtmlReady from "core/Editor/View/Subscriber/BuildVueOnHtmlReady";
import { useStructureStore } from "core/Editor/Data/Store/Structure";

export default class Container extends AbstractContainer {
    build() {
        super.build();

        this.register('view', () => new View(this.get('eventBus')));
        this.register('structure', () => useStructureStore());

        // Subscribers
        this.register(
            'subscriber.buildVueOnHtmlReady',
            () => new BuildVueOnHtmlReady(
                this.get('vueFactory'),
                this.getParameter('options'),
                this.getParameter('instanceId'),
                this.getParameter('options.directives'),
                this.getParameter('options.controls'),
                this.getParameter('options.extensions'),
                this.getParameter('options.blocks'),
                this,
            ),
            { tags: [{ name: 'event_listener', on: 'editor.view.ready', call: 'build' }] }
        );

        super.finish();
    }
}
