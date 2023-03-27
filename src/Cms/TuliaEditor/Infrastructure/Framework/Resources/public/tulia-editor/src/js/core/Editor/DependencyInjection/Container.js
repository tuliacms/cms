import AbstractContainer from "core/Shared/DependencyInjection/AbstractContainer";
import View from "core/Editor/View/View";
import BuildVueOnHtmlReady from "core/Editor/View/Subscriber/BuildVueOnHtmlReady";
import { useStructureStore } from "core/Editor/Data/Store/Structure";
import { useSelectionStore } from "core/Editor/Data/Store/Selection";
import AdminSelectionSubscriber from "core/Editor/Subscriber/Admin/SelectionSubscriber";
import AdminStructureSubscriber from "core/Editor/Subscriber/Admin/StructureSubscriber";
import EditorSelectionSubscriber from "core/Editor/Subscriber/Editor/SelectionSubscriber";
import SelectedElementBoundaries from "core/Editor/Selection/Boundaries/SelectedElementBoundaries";
import HoveredElementBoundaries from "core/Editor/Selection/Boundaries/HoveredElementBoundaries";
import Selection from "core/Editor/UseCase/Selection";
import HoverResolver from "core/Editor/Selection/Boundaries/HoverResolver";
import ContextmenuUsecase from "core/Editor/UseCase/Contextmenu";
import Contextmenu from "core/Editor/Contextmenu/Contextmenu";

export default class Container extends AbstractContainer {
    build() {
        super.build();

        this.register('view', () => new View(this.get('eventBus')));
        this.register('structure.store', () => useStructureStore());
        this.register('selection.store', () => useSelectionStore());
        this.register('selection.selectedElementBoundaries', () => new SelectedElementBoundaries(this.get('selection.store')));
        this.register('selection.hoveredElementBoundaries', () => new HoveredElementBoundaries(this.get('selection.store')));
        this.register('selection.hoveredElementResolver', () => new HoverResolver(this.get('usecase.selection')));
        this.register('usecase.selection', () => new Selection(this.get('messenger')));
        this.register('usecase.contextmenu', () => new ContextmenuUsecase(this.get('messenger')));
        this.register('contextmenu', () => new Contextmenu());

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
                this.get('selection.store'),
                this.get('messenger'),
                this.get('eventBus'),
            ),
            { tags: [{ name: 'event_listener', on: 'editor.ready', call: 'registerReceivers' }] }
        );
        this.register(
            'subscriber.StructureSubscriber',
            () => new AdminStructureSubscriber(
                this.get('structure.store'),
                this.get('messenger'),
                this.get('eventBus'),
            ),
            { tags: [{ name: 'event_listener', on: 'editor.ready', call: 'registerReceivers' }] }
        );
        this.register(
            'subscriber.EditorSelectionSubscriber',
            () => new EditorSelectionSubscriber(
                this.get('selection.selectedElementBoundaries'),
                this.get('selection.hoveredElementBoundaries'),
            ),
            { tags: [
                { name: 'event_listener', on: 'selection.selected', call: 'select' },
                { name: 'event_listener', on: 'selection.deselected', call: 'deselect' },
                { name: 'event_listener', on: 'selection.hovered', call: 'hover' },
                { name: 'event_listener', on: 'selection.dehovered', call: 'dehover' },
                { name: 'event_listener', on: 'structure.changed', call: 'update' },
            ] }
        );

        super.finish();
    }
}
