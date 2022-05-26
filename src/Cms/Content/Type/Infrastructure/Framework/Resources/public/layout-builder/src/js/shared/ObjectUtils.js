export default class ObjectUtils {
    static isEmpty (object) {
        return JSON.stringify(object) === JSON.stringify({});
    }

    static get (object, path, defaultValue = null) {
        let result = object;
        let slices = [];
        let breaked = false;

        // Cast to string, for integer inded arrays
        path = String(path);

        if (path.indexOf('.') >= 0) {
            slices = path.split('.');
        } else {
            slices = [path];
        }

        for (let piece of slices) {
            if (result[piece]) {
                result = result[piece];
            } else {
                breaked = true;
                break;
            }
        }

        if (breaked) {
            return defaultValue;
        }

        return result || defaultValue;
    };
}
