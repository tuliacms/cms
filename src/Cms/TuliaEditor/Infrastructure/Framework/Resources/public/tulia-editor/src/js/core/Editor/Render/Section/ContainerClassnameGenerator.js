export default class ContainerClassnameGenerator {
    static generate (section) {
        let classname;

        switch (section.config.containerWidth) {
            case 'full-width':
                classname = 'container-fluid'; break;
            case 'full-width-no-padding':
                classname = ''; break;
            default:
                classname = 'container-xxl'; break;
        }

        return classname;
    }
}
