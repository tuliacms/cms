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
import ElementConfigSubscriber from "core/Editor/Subscriber/Admin/ElementConfigSubscriber";
import ConfigStoreFactory from "core/Editor/Data/Store/ConfigStoreFactory";
import DataStoreFactory from "core/Editor/Data/Store/DataStoreFactory";
import EditorElementDataStoreRegistry from "core/Editor/Data/EditorElementDataStoreRegistry";
import DataSynchronizer from "core/Editor/Structure/Element/DataSynchronizer";
import ElementDataSubscriber from "core/Editor/Subscriber/Admin/ElementDataSubscriber";

export default class Container extends AbstractContainer {
    build() {
        super.build();

        this.registerFactory('view', () => new View(this.get('eventBus')));
        this.registerFactory('structure.store', () => useStructureStore());
        this.registerFactory('selection.store', () => useSelectionStore());
        this.registerFactory('selection.selectedElementBoundaries', () => new SelectedElementBoundaries(this.get('selection.store')));
        this.registerFactory('selection.hoveredElementBoundaries', () => new HoveredElementBoundaries(this.get('selection.store')));
        this.registerFactory('selection.hoveredElementResolver', () => new HoverResolver(this.get('usecase.selection')));
        this.registerFactory('usecase.selection', () => new Selection(this.get('messenger')));
        this.registerFactory('usecase.contextmenu', () => new ContextmenuUsecase(this.get('messenger')));
        this.registerFactory('contextmenu', () => new Contextmenu());
        this.registerFactory('element.config.storeFactory', () => new ConfigStoreFactory(this.get('blocks.registry'), this.get('structure.store')));
        this.registerFactory('element.data.storeFactory', () => new DataStoreFactory(this.get('blocks.registry'), this.get('structure.store')));
        this.registerFactory('element.data.registry', () => new EditorElementDataStoreRegistry(this.get('element.data.storeFactory'), this.get('element.data.synchronizer')));
        this.registerFactory('element.data.synchronizer', () => new DataSynchronizer(this.get('messenger'), this.get('eventBus')));

        // Subscribers
        this.register('subscriber.BuildVueOnHtmlReady', BuildVueOnHtmlReady, ['@vueFactory', '%options', '%instanceId', '%options.directives', '%options.controls', '%options.extensions', '%options.blocks', this], { tags: [{ name: 'event_subscriber' }] });
        this.register('subscriber.AdminSelectionSubscriber', AdminSelectionSubscriber, ['@selection.store', '@messenger', '@eventBus'], { tags: [{ name: 'event_subscriber' }] });
        this.register('subscriber.AdminStructureSubscriber', AdminStructureSubscriber, ['@structure.store', '@messenger', '@eventBus'], { tags: [{ name: 'event_subscriber' }] });
        this.register('subscriber.ElementConfigSubscriber', ElementConfigSubscriber, ['@messenger', '@element.config.registry', '@eventBus'], { tags: [{ name: 'event_subscriber' }] });
        this.register('subscriber.ElementDataSubscriber', ElementDataSubscriber, ['@messenger', '@element.data.registry'], { tags: [{ name: 'event_subscriber' }] });
        this.register('subscriber.EditorSelectionSubscriber', EditorSelectionSubscriber, ['@selection.selectedElementBoundaries', '@selection.hoveredElementBoundaries'], { tags: [{ name: 'event_subscriber' }] });

        super.finish();
    }
}
