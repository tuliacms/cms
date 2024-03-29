window.Tulia = Tulia ?? {};

window.Tulia.DynamicForm = function (form) {
    this.form = form;

    this.init = function () {
        let self = this;

        this.form.on('click', '[data-form-action]', function () {
            self.executeAction($(this).attr('data-form-action'), $(this));
        });

        this.rendered();
    };

    this.executeAction = function (action, target) {
        for (let i in Tulia.DynamicForm.plugins) {
            if (Tulia.DynamicForm.plugins[i].plugin.on[action]) {
                Tulia.DynamicForm.plugins[i].plugin.on[action].call(this, target);
            }
        }
    };

    this.executeRender = function (element, target) {
        for (let i in Tulia.DynamicForm.plugins) {
            if (Tulia.DynamicForm.plugins[i].plugin.render[element]) {
                Tulia.DynamicForm.plugins[i].plugin.render[element].call(this, target);
            }
        }
    };

    this.rendered = function () {
        let self = this;

        this.form.find('[data-dynamic-element]').filter(function () {
            return $(this).attr('data-dynamic-element-rendered') !== 'rendered';
        }).each(function () {
            $(this).attr('data-dynamic-element-rendered', 'rendered');

            self.executeRender($(this).attr('data-dynamic-element'), $(this));
        });
    };

    this.init();
};

window.Tulia.DynamicForm.plugins = [];

window.Tulia.DynamicForm.plugin = function (name, plugin) {
    window.Tulia.DynamicForm.plugins.push({
        name: name,
        plugin: $.extend({}, {
            render: {},
            on: {},
        }, plugin)
    });
};

(function () {
    const wrapElement = function (element) {
        let body = $('<div class="content-builder-repeatable-element-body" />');
        let header = $('<div class="content-builder-repeatable-element-header">\
            <button type="button" class="btn btn-sm btn-icon-only btn-default" data-form-action="create-from-prototype:move-down" data-toggle="tooltip" title=""><i class="btn-icon fas fa-chevron-down"></i></button>\
            <button type="button" class="btn btn-sm btn-icon-only btn-default" data-form-action="create-from-prototype:move-up" data-toggle="tooltip" title=""><i class="btn-icon fas fa-chevron-up"></i></button>\
            <button type="button" class="btn btn-sm btn-icon-only btn-default" data-form-action="create-from-prototype:remove" data-toggle="tooltip" title=""><i class="btn-icon fas fa-times"></i></button>\
        </div>');
        let repeatableElement = $('<div class="content-builder-repeatable-element" />');

        repeatableElement.append(header);
        repeatableElement.append(body);

        element.after(repeatableElement);
        body.append(element);
    };

    const refreshOrder = function (container) {
        let targetContainer = container.find('> .content-builder-repeatable-element');
        let orderInputs = container.find('.repeatable-element-order-store').filter(function () {
            return $(this).closest('.content-builder-repeatable-element').is(targetContainer);
        });

        orderInputs.each(function (index) {
            $(this).val(index);
        });
    };

    Tulia.DynamicForm.plugin('collection-field', {
        render: {
            'repeatable-element': function (element) {
                const target = $('<div class="content-builder-repeatable-target" />');
                element.append(target);
                element.find('> fieldset').each(function () {
                    wrapElement($(this));
                });
                element.find('> .content-builder-repeatable-element').appendTo(target);
                element.append('<button type="button" class="btn btn-success btn-icon-left" data-form-action="create-from-prototype"><i class="btn-icon fa fa-plus"></i>Add new row</button>');
            }
        },
        on: {
            'create-from-prototype': function (button) {
                const source = button.closest('.repeatable-field');
                const container = source.find('> .content-builder-repeatable-target');

                let index = source.data('index');

                if (!index) {
                    index = 98688776;
                }

                let html = source.data('prototype').replace(
                    new RegExp(source.data('prototype-name'), 'g'),
                    index
                );

                const element = $(html);

                container.append(element);
                wrapElement(element);

                source.data('index', index + 1);
                refreshOrder(container);

                this.rendered();
            },
            'create-from-prototype:move-down': function (button) {
                let element = button.closest('.content-builder-repeatable-element');
                element.next('.content-builder-repeatable-element').insertBefore(element);
                refreshOrder(element.parent());
            },
            'create-from-prototype:move-up': function (button) {
                let element = button.closest('.content-builder-repeatable-element');
                element.prev('.content-builder-repeatable-element').insertAfter(element);
                refreshOrder(element.parent());
            },
            'create-from-prototype:remove': function (button) {
                Tulia.Confirmation.warning().then((value) => {
                    if (value.value) {
                        let element = button.closest('.content-builder-repeatable-element');
                        let parent = element.parent();
                        element.remove();
                        refreshOrder(parent);
                    }
                });
            },
        }
    });
})();

$(function () {
    $('.tulia-dynamic-form').each(function () {
        new Tulia.DynamicForm($(this));
    });
});
