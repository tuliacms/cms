Tulia.DynamicForm.plugin('filemanager', {
    on: {
        'open-filemanager': function (button) {
            alert(1);
            const fieldId = button.attr('data-input-target');
            const filter = button.attr('data-filemanager-filter');

            TuliaFilemanager.create({
                targetInput: '#' + fieldId,
                showOnInit: true,
                endpoint: button.attr('data-filemanager-endpoint'),
                filter: {
                    type: filter === '*' ? '*' : JSON.parse(filter)
                },
                multiple: false,
                closeOnSelect: true
            });
        },
    }
});
