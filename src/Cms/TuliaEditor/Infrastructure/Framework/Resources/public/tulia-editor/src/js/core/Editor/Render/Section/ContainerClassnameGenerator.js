export default class ContainerClassnameGenerator {
    static generate (section) {
        let classname = 'tued-container';

        if (section.config.containerWidth === 'full-width') {
            classname += ' container-fluid';
        } else if (section.config.containerWidth === 'default') {
            classname += ' container-xxl';
        }

        return classname;
    }
}
