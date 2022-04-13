export default {
    data: {
        type: Object,
        default (rawProps) {
            const defaults = {
                text: 'some text'
            };

            return {...defaults, ...rawProps};
        }
    }
};
