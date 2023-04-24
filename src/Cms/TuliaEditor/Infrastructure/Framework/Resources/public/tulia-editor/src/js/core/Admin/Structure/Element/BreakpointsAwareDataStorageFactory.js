const { reactive, watch, ref } = require('vue');

const createProxy = (reactiveData, breakpointName) => {
    return new Proxy(reactiveData, {
        get(target, key) {
            return target[breakpointName.value];
        },
        set(target, key, value) {
            target[breakpointName.value] = value;
            return true;
        }
    });
};

const deepCopyWithoutEmptyValues = (source) => {
    let result = {};

    for (let i in source) {
        if (typeof source[i] === 'object') {
            let copy = deepCopyWithoutEmptyValues(source[i]);

            if (Object.keys(copy).length) {
                result[i] = copy;
            }
        } else if (source[i] !== '' && source[i] !== null) {
            result[i] = source[i];
        }
    }

    return result;
};

const copyValuesRecursive = (source, destination) => {
    for (let i in source) {
        if (typeof source[i] === 'object') {
            if (!destination.hasOwnProperty(i)) {
                destination[i] = null;
            }

            copyValuesRecursive(source[i], destination[i]);
        } else if (source[i]) {
            destination[i] = source[i];
        }
    }
};

const createValuesRecursive = (data, value, breakpoints) => {
    for (let i in data) {
        if (data[i] === undefined) {
            data[i] = createValues(value, breakpoints);
        } else if(typeof data[i] === 'object') {
            data[i] = createValuesRecursive(data[i], value, breakpoints);
        }
    }

    return data;
};

const createValues = (value, breakpoints) => {
    const result = {};

    for (let i in breakpoints) {
        result[breakpoints[i].name] = value;
    }

    return result;
};

const copyValues = (source, destination) => {
    if (typeof source !== 'object') {
        return;
    }

    for (let i in destination) {
        if (source.hasOwnProperty(i)) {
            destination[i] = source[i];
        }
    }
};

class ObjectBreakpointsAwareDataStorage {
    reactiveData;
    breakpointName;

    constructor (breakpoints, eventBus, source, structure, defaultValue) {
        defaultValue = defaultValue === undefined
            ? null
            : defaultValue;

        this.breakpointName = ref('xl');

        eventBus.listen('canvas.breakpoint.changed', (size) => {
            this.breakpointName.value = size;
        });

        this.reactiveData = reactive(createValuesRecursive(structure, defaultValue, breakpoints));
        copyValuesRecursive(source, this.reactiveData);

        watch(this.reactiveData, async (newValue) => {
            console.log(newValue);
            //source.set(propertyPath, deepCopyWithoutEmptyValues(toRaw(newValue)));
        });
    }

    get data () {
        return createProxy(this.reactiveData, this.breakpointName);
    }

    get breakpointName () {
        return this.breakpointName;
    }
}

class SingularBreakpointsAwareDataStorage {
    constructor (breakpoints, eventBus, source) {
        this.reactiveData = source;
        this.breakpoint = ref('xl');

        eventBus.listen('canvas.breakpoint.changed', (size) => {
            this.breakpoint.value = size;
        });
    }

    get source () {
        return this.reactiveData;
    }

    get data () {
        return createProxy(this.reactiveData, this.breakpoint);
    }

    get breakpointName () {
        return this.breakpoint.value;
    }
}

class BreakpointsAwareDataStorageFactory {
    constructor(options, eventBus) {
        this.options = options;
        this.eventBus = eventBus;
    }

    ref(source, defaultValue) {
        return new SingularBreakpointsAwareDataStorage(this.options.canvas.size.breakpoints, this.eventBus, source, defaultValue);
    }

    /*reactive(source, structure, defaultValue) {
        return new ObjectBreakpointsAwareDataStorage(this.options.canvas.size.breakpoints, this.eventBus, source, structure, defaultValue);
    }*/
}

export default BreakpointsAwareDataStorageFactory;
