import AbstractContainer from "core/Shared/DependencyInjection/AbstractContainer";
import View from "core/Admin/View/View";
import BuildVueOnHtmlReady from "core/Admin/View/Subscriber/BuildVueOnHtmlReady";
import StructureStoreFactory from "core/Admin/Data/Store/StructureStoreFactory";
import Sections from "core/Admin/UseCase/Sections";
import Selection from "core/Admin/UseCase/Selection";
import { useSelectionStore } from "core/Admin/Data/Store/Selection";
import { useContextmenuStore } from "core/Admin/Data/Store/Contextmenu";
import Canvas from "core/Admin/View/Canvas";
import EditorSelectionSubscriber from "core/Admin/Subscriber/Editor/SelectionSubscriber";
import Contextmenu from "core/Admin/UseCase/Contextmenu";
import ContextmenuAdminSubscriber from "core/Admin/Subscriber/Admin/ContextmenuSubscriber";
import ContextmenuEditorSubscriber from "core/Admin/Subscriber/Editor/ContextmenuSubscriber";
import Draggable from "core/Admin/UseCase/Draggable";
import Rows from "core/Admin/UseCase/Rows";
import Columns from "core/Admin/UseCase/Columns";
import ConfigStoreFactory from "core/Admin/Data/Store/ConfigStoreFactory";
import AdminElementConfigStoreRegistry from "core/Admin/Data/AdminElementConfigStoreRegistry";
import ConfigSynchronizer from "core/Admin/Structure/Element/ConfigSynchronizer";
import ColumnSize from "core/Admin/Structure/Element/ColumnSize";
import DataStoreFactory from "core/Admin/Data/Store/DataStoreFactory";
import ElementDataSubscriber from "core/Admin/Subscriber/Editor/ElementDataSubscriber";
import EditorWindow from "core/Admin/UseCase/EditorWindow";
import SelectionSubscriber from "core/Admin/Subscriber/Admin/SelectionSubscriber";

export default class Container extends AbstractContainer {
    build() {
        super.build();

        this.registerFactory('view', () => new View(this.get('root'), this.getParameter('instanceId'), this.get('translator'), this.get('eventBus')));
        this.registerFactory('usecase.sections', () => new Sections(this.get('structure.store'), this.get('messenger'), this.get('usecase.selection')));
        this.registerFactory('usecase.rows', () => new Rows(this.get('structure.store'), this.get('messenger'), this.get('usecase.selection')));
        this.registerFactory('usecase.columns', () => new Columns(this.get('structure.store'), this.get('messenger'), this.get('usecase.selection')));
        this.registerFactory('usecase.selection', () => new Selection(this.get('selection.store'), this.get('messenger')));
        this.registerFactory('usecase.draggable', () => new Draggable(this.get('usecase.selection'), this.get('structure.store'), this.get('eventBus'), this.get('messenger')));
        this.registerFactory('usecase.contextmenu', () => new Contextmenu(this.get('contextmenu.store'), this.get('usecase.selection')));
        this.register('usecase.editorWindow', EditorWindow, ['@eventBus', '@view'], { tags: [{ name: 'event_subscriber' }] });
        this.registerFactory('canvas', () => new Canvas(this.getParameter('options'), this.get('eventBus')));
        this.registerFactory('structure.store', () => (new StructureStoreFactory(this.getParameter('options'))).factory());
        this.registerFactory('selection.store', () => useSelectionStore());
        this.registerFactory('contextmenu.store', () => useContextmenuStore());
        this.registerFactory('element.config.storeFactory', () => new ConfigStoreFactory(this.get('blocks.registry'), this.get('structure.store')));
        this.registerFactory('element.config.registry', () => new AdminElementConfigStoreRegistry(this.get('element.config.storeFactory'), this.get('element.config.synchronizer')));
        this.registerFactory('element.config.synchronizer', () => new ConfigSynchronizer(this.get('messenger')));
        this.registerFactory('element.data.storeFactory', () => new DataStoreFactory(this.get('blocks.registry'), this.get('structure.store')));
        this.registerFactory('columnSize', () => new ColumnSize());

        // Subscribers
        this.register('subscriber.BuildVueOnHtmlReady', BuildVueOnHtmlReady, ['@vueFactory', '%options', '%instanceId', '%options.directives', '%options.controls', '%options.extensions', '%options.blocks', this], { tags: [{ name: 'event_subscriber' }] });
        this.register('subscriber.EditorSelectionSubscriber', EditorSelectionSubscriber, ['@messenger', '@usecase.selection'], { tags: [{ name: 'event_subscriber' }] });
        this.register('subscriber.SelectionSubscriber', SelectionSubscriber, ['@usecase.selection'], { tags: [{ name: 'event_subscriber' }] });
        this.register('subscriber.ContextmenuAdminSubscriber', ContextmenuAdminSubscriber, ['@usecase.contextmenu'], { tags: [{ name: 'event_subscriber' }] });
        this.register('subscriber.ContextmenuEditorSubscriber', ContextmenuEditorSubscriber, ['@messenger', '@usecase.contextmenu'], { tags: [{ name: 'event_subscriber' }] });
        this.register('subscriber.ElementDataSubscriber', ElementDataSubscriber, ['@messenger', '@element.data.registry'], { tags: [{ name: 'event_subscriber' }] });

        super.finish();
    }
}
