const Editor = require('./Editor.vue').default;
const Render = require('./Render.vue').default;
const Manager = require('./Manager.vue').default;

export default {
    theme: '*',
    framework: '*',
    code: 'core-imageblock',
    name: 'Image',
    icon: 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAPoAAABkBAMAAABdrjA9AAAAJ1BMVEX///8AAAA/Pz+fn58vLy8PDw/v7++AgICvr69gYGC/v7+Pj49vb2+gyKB9AAABQUlEQVRo3u3ZPU7DQBDF8QFH+ZBS8EhcQAVISJS7NyBHMCdIR0FDhWg5gm/AEaioORqWtxgJodhKsjNy/H5d0vz14sRSvEJEREREZOx5g93Wb5LNvEKX8lNy+UC3a8lkWqFbuZU8ZujjS/KYoI9HyeMMfVxIkqP+2nFt8tZlN9ZbrB+/Xtw/Bbf6dAOst171id7RHOoRjZVX/RaNS317/m5Zr9Ao9e2I4FcvgJXfJx8BBK9vXaGvLH9xOr0RjO82Ol3Hm99pI5JgU1c6Xcdb1XV6EgzrOl3Hm9cjVDCrzx50uo63qtcIOl3H29QX7dI0Xd0Y1eu0NLrUF+kyF3Cp1+kyR5f6Eq0ruNTPkbDO+ljqy7v//YzhHzTrrP9x0s8q935OO/Bn1Ac9nx/42cRh5zIDP5OSl87zuG8hIiIiIhqTX04AaDze3mN2AAAAAElFTkSuQmCC',
    editor: Editor,
    render: Render,
    manager: Manager,
    defaults: {
        image: {
            id: null,
            filename: null,
            size: 'thumbnail',
        },
    }
};
