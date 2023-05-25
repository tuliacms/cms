import AbstractContainer from "core/Shared/DependencyInjection/AbstractContainer";
import View from "core/Admin/View/View";
import BuildVueOnHtmlReady from "core/Admin/View/Subscriber/BuildVueOnHtmlReady";
import Sections from "core/Admin/UseCase/Sections";
import Selection from "core/Admin/UseCase/Selection";
import { useSelectionStore } from "core/Admin/Data/Store/Selection";
import { useContextmenuStore } from "core/Admin/Data/Store/Contextmenu";
import { useStructureStore } from "core/Admin/Data/Store/Structure";
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
import Structure from "core/Admin/Structure/Structure";
import EditorWindowSubscriber from "core/Admin/Subscriber/Admin/EditorWindowSubscriber";
import Assets from "core/Admin/Assets";
import BlocksPicker from "core/Admin/Structure/Blocks/BlocksPicker";
import Modals from "core/Admin/View/Modals";
import Blocks from "core/Admin/UseCase/Blocks";
import CreateBlockSubscriber from "core/Admin/Subscriber/Editor/CreateBlockSubscriber";
import BreakpointsAwareDataStorageFactory from "core/Admin/Structure/Element/BreakpointsAwareDataStorageFactory";
import BreakpointsStateCalculatorFactory from "core/Admin/Structure/Element/BreakpointsStateCalculatorFactory";
import ContentRenderingSubscriber from "core/Admin/Subscriber/Editor/ContentRenderingSubscriber";
import StructureRenderer from "core/Admin/Structure/StructureRenderer";
import PreviewSubscriber from "core/Admin/Subscriber/Admin/PreviewSubscriber";
import Preview from "core/Admin/View/Preview";
import SectionConfigurator from "core/Admin/Structure/Element/Config/SectionConfigurator";

export default class Container extends AbstractContainer {
    build() {
        super.build();

        this.registerFactory('view', () => new View(this.get('root'), this.getParameter('instanceId'), this.get('translator'), this.get('eventBus')));
        this.register('view.preview', Preview, ['@root', '@eventBus', '@translator', '%instanceId', '%options']);
        this.registerFactory('usecase.sections', () => new Sections(this.get('structure.store'), this.get('usecase.selection'), this.get('structure.admin')));
        this.registerFactory('usecase.rows', () => new Rows(this.get('structure.store'), this.get('usecase.selection'), this.get('structure.admin')));
        this.registerFactory('usecase.columns', () => new Columns(this.get('structure.store'), this.get('usecase.selection'), this.get('structure.admin')));
        this.registerFactory('usecase.blocks', () => new Blocks(this.get('blocks.registry'), this.get('structure.store'), this.get('usecase.selection'), this.get('structure.admin'), this.get('usecase.columns'), this.get('usecase.rows'), this.get('usecase.sections')));
        this.registerFactory('usecase.selection', () => new Selection(this.get('selection.store'), this.get('messenger'), this.get('eventBus')));
        this.registerFactory('usecase.draggable', () => new Draggable(this.get('usecase.selection'), this.get('structure.store'), this.get('eventBus'), this.get('messenger')));
        this.registerFactory('usecase.contextmenu', () => new Contextmenu(this.get('contextmenu.store'), this.get('usecase.selection')));
        this.register('usecase.editorWindow', EditorWindow, ['@eventBus', '@view', '@structure.admin', '@structure.renderer']);
        this.registerFactory('canvas', () => new Canvas(this.getParameter('options'), this.get('eventBus')));
        this.registerFactory('structure.store', () => useStructureStore());
        this.registerFactory('selection.store', () => useSelectionStore());
        this.registerFactory('contextmenu.store', () => useContextmenuStore());
        this.registerFactory('element.config.storeFactory', () => new ConfigStoreFactory(this.get('blocks.registry'), this.get('structure.store')));
        this.registerFactory('element.config.registry', () => new AdminElementConfigStoreRegistry(this.get('element.config.storeFactory'), this.get('element.config.synchronizer')));
        this.registerFactory('element.config.synchronizer', () => new ConfigSynchronizer(this.get('messenger')));
        this.registerFactory('element.data.storeFactory', () => new DataStoreFactory(this.get('blocks.registry'), this.get('structure.store')));
        this.registerFactory('columnSize', () => new ColumnSize());
        this.register('structure.admin', Structure, ['@structure.store', '@element.config.registry', '@element.data.registry', '@messenger', '%options']);
        this.register('structure.renderer', StructureRenderer, ['@assets']);
        this.register('assets', Assets);
        this.register('blocks.picker', BlocksPicker, ['@usecase.blocks', '@modals']);
        this.register('modals', Modals);
        this.register('breakpointsAwareDataStorageFactory', BreakpointsAwareDataStorageFactory, ['%options', '@eventBus']);
        this.register('breakpointsStateCalculatorFactory', BreakpointsStateCalculatorFactory, ['%options', '@eventBus']);
        this.register('configurator.section', SectionConfigurator);

        // Subscribers
        this.register('subscriber.BuildVueOnHtmlReady', BuildVueOnHtmlReady, ['@vueFactory', '%options', '%instanceId', '%options.directives', '%options.controls', '%options.extensions', '%options.blocks', this], { tags: [{ name: 'event_subscriber' }] });
        this.register('subscriber.EditorSelectionSubscriber', EditorSelectionSubscriber, ['@messenger', '@usecase.selection'], { tags: [{ name: 'event_subscriber' }] });
        this.register('subscriber.SelectionSubscriber', SelectionSubscriber, ['@usecase.selection'], { tags: [{ name: 'event_subscriber' }] });
        this.register('subscriber.ContextmenuAdminSubscriber', ContextmenuAdminSubscriber, ['@usecase.contextmenu'], { tags: [{ name: 'event_subscriber' }] });
        this.register('subscriber.ContextmenuEditorSubscriber', ContextmenuEditorSubscriber, ['@messenger', '@usecase.contextmenu'], { tags: [{ name: 'event_subscriber' }] });
        this.register('subscriber.ElementDataSubscriber', ElementDataSubscriber, ['@messenger', '@element.data.registry'], { tags: [{ name: 'event_subscriber' }] });
        this.register('subscriber.EditorWindowSubscriber', EditorWindowSubscriber, ['@usecase.editorWindow'], { tags: [{ name: 'event_subscriber' }] });
        this.register('subscriber.CreateBlockSubscriber', CreateBlockSubscriber, ['@messenger', '@blocks.picker'], { tags: [{ name: 'event_subscriber' }] });
        this.register('subscriber.ContentRenderingSubscriber', ContentRenderingSubscriber, ['@messenger', '@structure.renderer'], { tags: [{ name: 'event_subscriber' }] });
        this.register('subscriber.PreviewSubscriber', PreviewSubscriber, ['@view.preview', '@structure.renderer'], { tags: [{ name: 'event_subscriber' }] });

        super.finish();
    }
}
