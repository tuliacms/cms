export default class ColumnClassnameGenerator {
    static computer (block) {
        return () => {
            let classname = '';

            switch (block.data.columns) {
                case '2': classname += ' col-6'; break;
                case '3': classname += ' col-6 col-md-4'; break;
                case '4': classname += ' col-6 col-md-4 col-lg-3'; break;
                case '6': classname += ' col-6 col-md-4 col-lg-3 col-xl-2'; break;
            }

            classname += ' mb-' + block.data.marginBottom;

            return classname;
        };
    }
}
