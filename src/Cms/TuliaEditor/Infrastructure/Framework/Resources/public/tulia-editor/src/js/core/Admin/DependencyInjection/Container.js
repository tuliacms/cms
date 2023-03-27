import AbstractContainer from "core/Shared/DependencyInjection/AbstractContainer";
import View from "core/Admin/View/View";
import BuildVueOnHtmlReady from "core/Admin/View/Subscriber/BuildVueOnHtmlReady";
import StructureStoreFactory from "core/Admin/Data/Store/StructureStoreFactory";
import Sections from "core/Admin/UseCase/Sections";
import Selection from "core/Admin/UseCase/Selection";
import { useSelectionStore } from "core/Admin/Data/Store/Selection";
import Canvas from "core/Admin/View/Canvas";
import EditorSelectionSubscriber from "core/Admin/Subscriber/Editor/SelectionSubscriber";

export default class Container extends AbstractContainer {
    build() {
        super.build();

        this.register('view', this._buildView);
        this.register('usecase.sections', () => new Sections(this.get('structure.store'), this.get('messenger')));
        this.register('usecase.selection', () => new Selection(this.get('selection.store'), this.get('messenger')));
        this.register('canvas', () => new Canvas(this.getParameter('options')));
        this.register('structure.store', () => {
            return (new StructureStoreFactory(this.getParameter('options'))).factory();
        });
        this.register('selection.store', () => useSelectionStore());

        // Subscribers
        this.register(
            'subscriber.BuildVueOnHtmlReady',
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
            { tags: [{ name: 'event_listener', on: 'admin.view.ready', call: 'build' }] }
        );
        this.register(
            'subscriber.EditorSelectionSubscriber',
            () => new EditorSelectionSubscriber(
                this.get('messenger'),
                this.get('usecase.selection'),
            ),
            { tags: [{ name: 'event_listener', on: 'admin.view.ready', call: 'registerReceivers' }] }
        );

        super.finish();
    }

    _buildView() {
        return new View(
            this.get('root'),
            this.getParameter('instanceId'),
            this.get('translator'),
            this.get('admin'),
            this.get('eventBus'),
        );
    }
}
