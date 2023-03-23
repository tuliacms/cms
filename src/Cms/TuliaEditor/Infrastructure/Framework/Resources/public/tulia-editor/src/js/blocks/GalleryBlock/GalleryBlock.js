const Editor = require('./Editor.vue').default;
const Render = require('./Render.vue').default;
const Manager = require('./Manager.vue').default;

export default {
    theme: '*',
    framework: '*',
    code: 'core-galleryblock',
    name: 'Images gallery',
    icon: 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAK8AAABGCAMAAACNBzQNAAAAM1BMVEX///8AAAA/Pz+/v7+AgIBgYGDv7+8PDw8vLy/Pz89vb2+fn5+Pj48fHx+vr6/f399PT09UL8YPAAABMklEQVRo3u3Yy27FIAxFUZsbnnnd///axk0bh0FVolbCSGePkVhiApgQQgghhFBLYXJNvReyUI7cmgvUvbBye566N/OT+h9wEUZyv7fy0Yt654RBDb3g/Zv35aJbxvFuLBWz3jAXl2/eyJ/lao0db15le/XufDaT5tlb8YbItTfw2XbnMnsj3okr71FiKe53roBteF3l1XfEXHMFbMabLvj3O63kivsFtuJ16tWUq2Cr3pBiVq7mbXpDYo5ZuZpJr3AFLNwBvMI9wZ5H8E7Kgxfe4byz+zmLXtv/IXjr4D28A81LLu8yxjzq8obI7b2pU+qlrR2cAvVKvbQXxw3J8KdP6h0neKv6eSc+ou6JN04NJRtezw+K1L2NH1Sof55HuCZuLYmbWosJLkIIIYQQ+vc+AGHcCn0/nuMCAAAAAElFTkSuQmCC',
    editor: Editor,
    render: Render,
    manager: Manager,
    state: {
        data: {
            images: [{
                id: '1',
                file: { id: null, filename: null }
            }],
        },
        config: {
            marginBottom: '4',
            size: 'thumbnail',
            columns: '3',
            onclickGallery: '1',
        },
    },
    defaults: {
        marginBottom: '4',
        size: 'thumbnail',
        columns: '3',
        onclickGallery: '1',
        images: [{
            id: '1',
            file: { id: null, filename: null }
        }],
    }
};
