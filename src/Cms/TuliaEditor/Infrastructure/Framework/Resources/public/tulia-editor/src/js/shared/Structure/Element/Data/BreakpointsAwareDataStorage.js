const { inject, reactive, watch, toRaw, ref } = require('vue');

class ObjectBreakpointsAwareDataStorage {
    reactiveData;
    canvas;
    options;
    defaultValue;

    constructor (source, propertyPath, structure, defaultValue) {
        this.canvas = inject('canvas');
        this.breakpoints = inject('options').canvas.size.breakpoints;

        this.defaultValue = defaultValue === undefined
            ? null
            : defaultValue;

        this.reactiveData = reactive(this.createValuesRecursive(structure, this.defaultValue));
        this.copyValuesRecursive(source.get(propertyPath), this.reactiveData);

        const self = this;

        watch(this.reactiveData, async (newValue) => {
            source.set(propertyPath, self.deepCopyWithoutEmptyValues(toRaw(newValue)));
        });
    }

    get data () {
        return this.createProxy(this.reactiveData);
    }

    createProxy (reactiveData) {
        const self = this;

        return new Proxy(reactiveData, {
            get(target, key) {
                if (!target.hasOwnProperty(key)) {
                    return undefined;
                }

                if (typeof target[key] === 'object') {
                    if (target[key].hasOwnProperty(self.canvas.getBreakpointName())) {
                        return target[key][self.canvas.getBreakpointName()];
                    }

                    return self.createProxy(target[key]);
                }

                return target[key];
            },
            set(target, key, value) {
                if (typeof target[key] === 'object') {
                    if (target[key].hasOwnProperty(self.canvas.getBreakpointName())) {
                        target[key][self.canvas.getBreakpointName()] = value;
                    } else {
                        target[key] = value;
                    }
                } else {
                    throw new Error('What to do when is nt object?');
                }

                return true;
            }
        });
    }

    deepCopyWithoutEmptyValues (source) {
        let result = {};

        for (let i in source) {
            if (typeof source[i] === 'object') {
                let copy = this.deepCopyWithoutEmptyValues(source[i]);

                if (Object.keys(copy).length) {
                    result[i] = copy;
                }
            } else if (source[i] !== '' && source[i] !== null) {
                result[i] = source[i];
            }
        }

        return result;
    }

    copyValuesRecursive (source, destination) {
        for (let i in source) {
            if (typeof source[i] === 'object') {
                if (!destination.hasOwnProperty(i)) {
                    destination[i] = null;
                }

                this.copyValuesRecursive(source[i], destination[i]);
            } else if (source[i]) {
                destination[i] = source[i];
            }
        }
    }

    createValuesRecursive (data, value) {
        for (let i in data) {
            if (data[i] === undefined) {
                data[i] = this.createValues(value);
            } else if(typeof data[i] === 'object') {
                data[i] = this.createValuesRecursive(data[i], value);
            }
        }

        return data;
    }

    createValues (value) {
        const result = {};

        for (let i in this.breakpoints) {
            result[this.breakpoints[i].name] = value;
        }

        return result;
    }
}

class SingularBreakpointsAwareDataStorage {
    reactiveData;
    canvas;
    options;
    defaultValue;

    constructor (source, propertyPath, defaultValue) {
        this.canvas = inject('canvas');
        this.breakpoints = inject('options').canvas.size.breakpoints;

        this.defaultValue = defaultValue === undefined
            ? null
            : defaultValue;

        this.reactiveData = reactive({ value: this.createValues(this.defaultValue)} );
        this.copyValues(source.get(propertyPath), this.reactiveData.value);

        const self = this;
        watch(this.reactiveData, async (newValue) => {
            source.set(propertyPath, self.deepCopyWithoutEmptyValues(toRaw(newValue.value)));
        });
    }

    get data () {
        return this.createProxy(this.reactiveData);
    }

    createProxy (reactiveData) {
        const self = this;

        return new Proxy(reactiveData, {
            get(target, key) {
                if (!target.hasOwnProperty(key)) {
                    return undefined;
                }

                if (typeof target[key] === 'object') {
                    if (target[key].hasOwnProperty(self.canvas.getBreakpointName())) {
                        return target[key][self.canvas.getBreakpointName()];
                    }

                    return self.createProxy(target[key]);
                }

                return target[key];
            },
            set(target, key, value) {
                if (typeof target[key] === 'object') {
                    if (target[key].hasOwnProperty(self.canvas.getBreakpointName())) {
                        target[key][self.canvas.getBreakpointName()] = value;
                    } else {
                        target[key] = value;
                    }
                } else {
                    throw new Error('What to do when is nt object?');
                }

                return true;
            }
        });
    }

    deepCopyWithoutEmptyValues (source) {
        let result = {};

        for (let i in source) {
            if (typeof source[i] === 'object') {
                let copy = this.deepCopyWithoutEmptyValues(source[i]);

                if (Object.keys(copy).length) {
                    result[i] = copy;
                }
            } else if (source[i] !== '' && source[i] !== null) {
                result[i] = source[i];
            }
        }

        return result;
    }

    copyValues (source, destination) {
        if (typeof source !== 'object') {
            return;
        }

        for (let i in destination) {
            if (source.hasOwnProperty(i)) {
                destination[i] = source[i];
            }
        }
    }

    createValues (value) {
        const result = {};

        for (let i in this.breakpoints) {
            result[this.breakpoints[i].name] = value;
        }

        return result;
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
