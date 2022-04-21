const defaults = {
    text: ''
};

export default {
    id: String,
    data: {
        type: Object,
        default (rawProps) {
            return Object.assign({}, defaults, rawProps);
        }
    }
};
