export default class RowClassnameGenerator {
    static generate (row, section) {
        let classname = 'tued-row row';

        if (section.config.containerWidth === 'full-width-no-padding') {
            classname += ' g-0';
        }

        return classname;
    }
}
