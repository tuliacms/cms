const Catalogue = require('./Catalogue.js').default;

export default class Translator {
    catalogues = {};
    locale;
    fallbackLocales = [];
    translations;

    constructor (locale, fallbackLocales, translations) {
        this.translations = translations;
        this.locale = locale;
        this.fallbackLocales = fallbackLocales;
    }

    trans (name) {
        let locales = [this.locale].concat(this.fallbackLocales);
        let translation = null;

        locales = this.resolveLocalesCodes(locales);

        for (let locale of locales) {
            translation = this.getCatalogue(locale).get(name);

            // Break if found in this locale.
            if (translation !== null) {
                break;
            }
        }

        if (translation === null) {
            translation = name;
        }

        return translation;
    }

    getCatalogue (locale) {
        if (this.catalogues[locale]) {
            return this.catalogues[locale];
        }

        this.catalogues[locale] = new Catalogue(this.translations[locale] ?? {});

        return this.catalogues[locale];
    }

    /**
     * Splits all ISO locales, and after every ISO locale,
     * adds simple locale code without region. Like for `en_US`, creates `en`.
     * @internal
     */
    resolveLocalesCodes (locales) {
        let resolved = [];

        for (let locale of locales) {
            if (locale.indexOf('_')) {
                let d = locale.split('_');

                resolved.push(locale);
                resolved.push(d[0]);
            }
        }

        return resolved;
    }
};
