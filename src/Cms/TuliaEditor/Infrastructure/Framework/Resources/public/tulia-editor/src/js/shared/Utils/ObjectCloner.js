export default class ObjectCloner {
    static deepClone (source) {
        return JSON.parse(JSON.stringify(source));
    }
};
