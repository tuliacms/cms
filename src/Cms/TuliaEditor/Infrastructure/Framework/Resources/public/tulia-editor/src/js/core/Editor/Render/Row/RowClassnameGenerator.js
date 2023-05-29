export default class RowClassnameGenerator {
    static generate (row, section, defaultClassList) {
        defaultClassList = defaultClassList ?? '';
        let classname = defaultClassList + ' tued-row row';

        if (section.config.containerWidth === 'full-width-no-padding') {
            classname += ' g-0';
        }

        return classname;
    }
}
