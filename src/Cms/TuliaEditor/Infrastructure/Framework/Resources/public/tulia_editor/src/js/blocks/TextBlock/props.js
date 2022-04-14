export default {
    data: {
        type: Object,
        default (rawProps) {
            const defaults = {
                text: ''
            };

            return {...defaults, ...rawProps};
        }
    }
};
