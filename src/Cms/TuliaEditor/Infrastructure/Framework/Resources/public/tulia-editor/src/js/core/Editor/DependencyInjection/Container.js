import AbstractContainer from "core/Shared/DependencyInjection/AbstractContainer";
import View from "core/Editor/View/View";
import BuildVueOnHtmlReady from "core/Editor/View/Subscriber/BuildVueOnHtmlReady";
import { useStructureStore } from "core/Editor/Data/Store/Structure";
import { useSelectionStore } from "core/Editor/Data/Store/Selection";
import AdminSelectionSubscriber from "core/Editor/Subscriber/Admin/SelectionSubscriber";
import AdminStructureSubscriber from "core/Editor/Subscriber/Admin/StructureSubscriber";
import EditorSelectionSubscriber from "core/Editor/Subscriber/Editor/SelectionSubscriber";
import SelectedElementBoundaries from "core/Editor/Selection/Boundaries/SelectedElementBoundaries";
import Selection from "core/Editor/UseCase/Selection";

export default class Container extends AbstractContainer {
    build() {
        super.build();

        this.register('view', () => new View(this.get('eventBus')));
        this.register('structure', () => useStructureStore());
        this.register('selection', () => useSelectionStore());
        this.register('selection.selectedElementBoundaries', () => new SelectedElementBoundaries(this.get('selection')));
        this.register('usecase.selection', () => new Selection(this.get('messenger')));

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
        this.register(
            'subscriber.selectionSubscriber',
            () => new AdminSelectionSubscriber(
                this.get('selection'),
                this.get('messenger'),
                this.get('eventBus'),
            ),
            { tags: [{ name: 'event_listener', on: 'editor.ready', call: 'registerReceivers' }] }
        );
        this.register(
            'subscriber.StructureSubscriber',
            () => new AdminStructureSubscriber(
                this.get('structure'),
                this.get('messenger'),
                this.get('eventBus'),
            ),
            { tags: [{ name: 'event_listener', on: 'editor.ready', call: 'registerReceivers' }] }
        );
        this.register(
            'subscriber.EditorSelectionSubscriber',
            () => new EditorSelectionSubscriber(
                this.get('selection.selectedElementBoundaries'),
            ),
            { tags: [
                { name: 'event_listener', on: 'selection.selected', call: 'select' },
                { name: 'event_listener', on: 'selection.deselected', call: 'deselect' },
                { name: 'event_listener', on: 'structure.changed', call: 'update' },
            ] }
        );

        super.finish();
    }
}
