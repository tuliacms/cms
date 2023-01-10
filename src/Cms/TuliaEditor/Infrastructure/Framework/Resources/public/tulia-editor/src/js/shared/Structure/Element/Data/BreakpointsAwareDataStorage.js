const { inject, reactive, watch, toRaw } = require('vue');

const canvas = inject('canvas');
const breakpoints = inject('options').canvas.size.breakpoints;

const createProxy = (reactiveData) => {
    return new Proxy(reactiveData, {
        get(target, key) {
            if (!target.hasOwnProperty(key)) {
                return undefined;
            }

            if (typeof target[key] === 'object') {
                if (target[key].hasOwnProperty(canvas.getBreakpointName())) {
                    return target[key][canvas.getBreakpointName()];
                }

                return createProxy(target[key]);
            }

            return target[key];
        },
        set(target, key, value) {
            if (typeof target[key] === 'object') {
                if (target[key].hasOwnProperty(canvas.getBreakpointName())) {
                    target[key][canvas.getBreakpointName()] = value;
                } else {
                    target[key] = value;
                }
            } else {
                throw new Error('What to do when is nt object?');
            }

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

const createValuesRecursive = (data, value) => {
    for (let i in data) {
        if (data[i] === undefined) {
            data[i] = createValues(value);
        } else if(typeof data[i] === 'object') {
            data[i] = createValuesRecursive(data[i], value);
        }
    }

    return data;
};

const createValues = (value) => {
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

    constructor (source, propertyPath, structure, defaultValue) {
        defaultValue = defaultValue === undefined
            ? null
            : defaultValue;

        this.reactiveData = reactive(createValuesRecursive(structure, defaultValue));
        copyValuesRecursive(source.get(propertyPath), this.reactiveData);

        watch(this.reactiveData, async (newValue) => {
            source.set(propertyPath, deepCopyWithoutEmptyValues(toRaw(newValue)));
        });
    }

    get data () {
        return createProxy(this.reactiveData);
    }
}

class SingularBreakpointsAwareDataStorage {
    reactiveData;

    constructor (source, propertyPath, defaultValue) {
        defaultValue = defaultValue === undefined
            ? null
            : defaultValue;

        this.reactiveData = reactive({ value: createValues(defaultValue)} );
        copyValues(source.get(propertyPath), this.reactiveData.value);

        watch(this.reactiveData, async (newValue) => {
            source.set(propertyPath, deepCopyWithoutEmptyValues(toRaw(newValue.value)));
        });
    }

    get data () {
        return createProxy(this.reactiveData);
    }
}

class BreakpointsAwareDataStorage {
    static ref(source, propertyPath, defaultValue) {
        return new SingularBreakpointsAwareDataStorage(source, propertyPath, defaultValue);
    }

    static reactive(source, propertyPath, structure, defaultValue) {
        return new ObjectBreakpointsAwareDataStorage(source, propertyPath, structure, defaultValue);
    }
}

export default BreakpointsAwareDataStorage;
