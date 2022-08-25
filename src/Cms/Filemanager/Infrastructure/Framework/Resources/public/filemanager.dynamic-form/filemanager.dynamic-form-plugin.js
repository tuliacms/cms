Tulia.DynamicForm.plugin('filemanager', {
    on: {
        'open-filemanager': function (button) {
            const fieldId = button.attr('data-input-target');
            const filter = button.attr('data-filemanager-filter');
            const field = $('#' + fieldId);

            if (! field.data('filemanager-instance')) {
                instance = TuliaFilemanager.create({
                    targetInput: '#' + fieldId,
                    showOnInit: true,
                    endpoint: button.attr('data-filemanager-endpoint'),
                    filter: {
                        type: filter === '*' ? '*' : JSON.parse(filter)
                    },
                    multiple: false,
                    closeOnSelect: true
                });
                field.data('filemanager-instance', instance);
            } else {
                field.data('filemanager-instance').open();
            }
        },
    }
});
