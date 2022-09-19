<template>
    <div>
        <div :class="{ 'vertical-centered-box viewbox content-loader': true, 'active': activeBox === 'viewbox-loader' }" id="viewbox-loader">
            <div class="content-loader-inner">
                <div class="content-loader-circle"></div>
                <div class="content-loader-line-mask"><div class="content-loader-line"></div></div>
                <svg class="content-loader-logo" width="60" height="60" xmlns="http://www.w3.org/2000/svg" version="1.1" xml:space="preserve"><g stroke="null"><path stroke="null" class="st0" d="m55.84563,5.5l-51.77311,0l-4.07252,7.02239l25.6376,43.72672l8.63825,0l25.6376,-43.72672l-4.06782,-7.02239zm-42.10147,13.45293l4.11949,0l15.86732,27.06087l21.49932,-37.11299l2.09967,3.61688l-23.67885,40.38697l-19.90695,-33.95174zm6.71237,0l19.26343,0l-9.62937,16.42629l-9.63406,-16.42629zm-15.09697,-11.21704l47.96364,0l-19.60163,33.839l-2.33923,-3.98797l15.35532,-26.18718l-43.50595,0l2.12786,-3.66386zm-2.11846,5.89975l39.59314,0l-1.80844,3.0814l-31.18036,0l21.87041,37.29618l-4.79589,0l-23.67885,-40.37758z" fill="#0076F8" id="svg_1"/><g stroke="null" id="svg_2"/></g></svg>
            </div>
        </div>
        <div :class="{ 'vertical-centered-box viewbox': true, 'active': activeBox === 'viewbox-login' }" id="viewbox-login">
            <div class="centered-element">
                <div class="box">
                    <div class="box-body">
                        <div class="logo">
                            <img class="logo-image" :src="options.logo" alt="Tulia CMS" />
                        </div>
                        <p class="logo-slogan">{{ options.translations.loginToAdminPanel }}</p>
                        <div v-html="options.flashes"></div>
                        <div class="alert alert-danger" v-if="options.error">{{ options.error }}</div>
                        <form method="POST" :action="options.form.action">
                            <input type="hidden" name="_token" :value="options.form.csrfToken" />
                            <fieldset class="form-group mb-3">
                                <label class="d-none">{{ options.translations.username }}</label>
                                <div class="input-group mb-1">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-user"></i></span>
                                    </div>
                                    <input type="text" class="form-control form-control-autofocus" id="username" name="_username" v-model="username" :placeholder="options.translations.username" />
                                </div>
                            </fieldset>
                            <fieldset class="form-group mb-3">
                                <label class="d-none">{{ options.translations.password }}</label>
                                <div class="input-group m-0">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-key"></i></span>
                                    </div>
                                    <input type="password" class="form-control form-control-autofocus" id="password" name="_password" v-model="password" :placeholder="options.translations.password" />
                                </div>
                            </fieldset>
                            <div class="row">
                                <div class="col">
                                    <div class="form-check mb-5">
                                        <input class="form-check-input" type="checkbox" value="" id="remember-me-checkbox" name="_remember_me">
                                        <label class="form-check-label" for="remember-me-checkbox">
                                            {{ options.translations.rememberMe }}
                                        </label>
                                    </div>
                                </div>
                                <div class="col text-end">
                                    <button type="submit" class="btn btn-primary login-btn" @click="showBox('viewbox-loader')">{{ options.translations.signIn }}</button>
                                </div>
                            </div>
                            <div class="mb-5"></div>
                            <input type="hidden" name="_csrf_token" :value="options.form.csrfToken">
                            <div class="row">
                                <div class="col">
                                    <a class="text-white dropdown-toggle" style="padding: 0 10px;" type="button" data-bs-toggle="dropdown" data-bs-auto-close="true" aria-expanded="false">
                                        {{ currentLocale.replace('_', '-') }}
                                    </a>
                                    <ul class="dropdown-menu">
                                        <li v-for="locale in locales"><a :class="{ 'dropdown-item': true, 'active': locale.code === currentLocale }" href="#" @click.prevent="changeLocale(locale.code)">{{ locale.label }}</a></li>
                                    </ul>
                                </div>
                                <div class="col text-end">
                                    <a href="#" class="text-white" @click.prevent="showBox('viewbox-password-remember')">{{ options.translations.didYouForgotPassword }}</a>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div :class="{ 'vertical-centered-box viewbox': true, 'active': activeBox === 'viewbox-password-remember' }" id="viewbox-password-remember">
            <div class="centered-element">
                <div class="box">
                    <div class="box-body">
                        <div class="logo">
                            <img class="logo-image" :src="options.logo" alt="Tulia CMS" />
                        </div>
                        <p class="logo-slogan">{{ options.translations.insertEmailToRestorePassword }}</p>
                        <fieldset class="form-group mb-3">
                            <label class="d-none">{{ options.translations.username }}</label>
                            <div class="input-group mb-1">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-user"></i></span>
                                </div>
                                <input type="text" class="form-control form-control-autofocus" :placeholder="options.translations.username">
                            </div>
                        </fieldset>
                        <div class="row">
                            <div class="col"></div>
                            <div class="col text-end">
                                <a href="#" class="btn btn-primary login-btn" @click="showBox('viewbox-loader')">{{ options.translations.doProcess }}</a>
                            </div>
                        </div>
                        <div class="mb-5"></div>
                        <div class="row">
                            <div class="col">
                                <a class="text-white dropdown-toggle" style="padding: 0 10px;" type="button" data-bs-toggle="dropdown" data-bs-auto-close="true" aria-expanded="false">
                                    {{ currentLocale.replace('_', '-') }}
                                </a>
                                <ul class="dropdown-menu">
                                    <li v-for="locale in locales"><a :class="{ 'dropdown-item': true, 'active': locale.code === currentLocale }" href="#" @click.prevent="changeLocale(locale.code)">{{ locale.label }}</a></li>
                                </ul>
                            </div>
                            <div class="col text-end">
                                <a href="#" class="text-white" @click.prevent="showBox('viewbox-login')">{{ options.translations.signIn }}</a>
                            </div>
                        </div>
                        <div style="height:72px;"></div>
                    </div>
                </div>
            </div>
        </div>
        <div class="credit-links">
            <a href="#">Created with &#10084; by Codevia Studio</a>
        </div>
    </div>
</template>

<script setup>
const Cookies = require('Cookies');
const { defineProps, provide, onMounted, reactive, ref, computed } = require('vue');

const username = ref(LoginFormConfiguration.lastUsername);
const password = ref(LoginFormConfiguration.password);

const locales = [
    {label: 'English (United States)', code: 'en_US'},
    {label: 'Polski (Polska)', code: 'pl_PL'},
];
const detectClientLocale = locales => {
    const selectedLocale = Cookies.get('tulia_login_locale');

    if (selectedLocale && LoginFormConfiguration.translations.hasOwnProperty(selectedLocale)) {
        return selectedLocale;
    }

    for (let i in locales) {
        if (LoginFormConfiguration.translations.hasOwnProperty(i)) {
            return i;
        }
    }

    return 'en_US';
};
const currentLocale = ref(detectClientLocale(LoginFormConfiguration.clientLocales));

const options = reactive({
    error: LoginFormConfiguration.error,
    logo: LoginFormConfiguration.logo,
    form: LoginFormConfiguration.form,
    flashes: LoginFormConfiguration.flashes,
    translations: LoginFormConfiguration.translations[currentLocale.value]
});
const changeLocale = locale => {
    currentLocale.value = locale;
    options.translations = LoginFormConfiguration.translations[currentLocale.value];
    Cookies.set('tulia_login_locale', currentLocale.value);
};

const activeBox = ref('viewbox-login');
const showBox = box => {
    activeBox.value = box;
};
</script>

