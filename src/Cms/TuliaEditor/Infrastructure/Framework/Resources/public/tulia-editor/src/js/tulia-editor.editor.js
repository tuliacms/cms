import './../css/tulia-editor.editor.scss';
import TuliaEditorInstance from './admin/Vue/TuliaEditorInstance.vue';
import Messanger from './shared/Messenger';

window.TuliaEditor = {};
window.TuliaEditor.blocks = [];
window.TuliaEditor.extensions = {};

window.TuliaEditor.registerBlocks = function () {
    for (let i in TuliaEditor.blocks) {
        let data = TuliaEditor.blocks[i].data();
        let dataBinds = [];
        let props = [];

        for (let d in data) {
            dataBinds.push(` :${d}="blockData.${d}"`);
            props.push(d);
        }

        Vue.component(TuliaEditor.blocks[i].name + '-component-frame', {
            props: ['blockData'],
            template: `<div><component :is="'${TuliaEditor.blocks[i].name}'" ${dataBinds.join()}></component></div>`
        });

        Vue.component(TuliaEditor.blocks[i].name, {
            props: props,
            data () {
                return data;
            },
            template: TuliaEditor.blocks[i].template()
        });
    }
};

window.TuliaEditor.registerExtensions = function () {
    for (let i in TuliaEditor.extensions) {
        TuliaEditor.extensions[i].call();
    }
};

document.addEventListener('DOMContentLoaded', function () {
    function getQueryVariable(variable) {
        let query = window.location.search.substring(1);
        let vars = query.split('&');

        for (let i = 0; i < vars.length; i++) {
            let pair = vars[i].split('=');

            if (decodeURIComponent(pair[0]) === variable) {
                return decodeURIComponent(pair[1]);
            }
        }

        console.error('Query variable %s not found', variable);
    }

    let instanceId = getQueryVariable('tuliaEditorInstance');
    let messanger = new Messanger(instanceId, window.parent, 'editor');

    messanger.listen('editor.init.data', function (options) {
        Vue.config.devtools = true;

        TuliaEditor.registerExtensions();
        TuliaEditor.registerBlocks();

        new Vue({
            el: '#tulia-editor',
            template: '<TuliaEditorInstance :instanceId="instanceId" :options="options" :messanger="messanger" :availableBlocks="availableBlocks"></TuliaEditorInstance>',
            components: {
                TuliaEditorInstance
            },
            data: {
                instanceId: instanceId,
                options: options,
                messanger: messanger,
                availableBlocks: TuliaEditor.blocks
            }
        });
    });

    messanger.send('editor.init.fetch');
});

class AbstractTuliaEditorBlock {
    static name = '';

    static data () {
        return {};
    }

    static template () {
        return '<div></div>';
    }
}

class TextBlock extends AbstractTuliaEditorBlock {
    static name = 'core-textblock';

    static data () {
        return {
            text: '<p>Some default text :(</p>'
        };
    }

    static template () {
        return '<div><WysiwygEditor :content="text"></WysiwygEditor></div>';
    }
}

window.TuliaEditor.blocks.push(TextBlock);

TuliaEditor.extensions['WysiwygEditor'] = function () {
    return Vue.component('WysiwygEditor', {
        props: ['content'],
        template: '<div v-html="content" contenteditable="true"></div>',
    });
};
