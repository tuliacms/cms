export default class ClassObserver {
    element;
    callback;
    classname;

    constructor (element, classname, callback) {
        this.callback = callback;
        this.element = element;
        this.classname = classname;

        this.observe();
    }

    observe() {
        let prevClass = this.element.classList.contains(this.classname);

        let observer = new MutationObserver((mutations) => {
            mutations.forEach((mutation) => {
                if (mutation.attributeName === 'class') {
                    let currentClass = mutation.target.classList.contains(this.classname);

                    if (prevClass !== currentClass) {
                        prevClass = currentClass;

                        this.callback(currentClass);
                    }
                }
            });
        });

        observer.observe(this.element, {attributes: true});
    }
};
