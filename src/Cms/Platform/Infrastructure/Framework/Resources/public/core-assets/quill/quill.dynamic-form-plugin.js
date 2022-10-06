Tulia.DynamicForm.plugin('quill-editor', {
    render: {
        'quill-editor': function (textarea) {
            const id = 'quilleditor-' + textarea.attr('id'),
                val = textarea.val(),
                editor_height = 200;

            const div = $('<div/>')
                .attr('id', id)
                .attr('class', 'quill-editor-preview')
                .css('height', editor_height + 'px')
                .html(val);

            textarea.addClass('d-none').parent().append(div);

            const quill = new Quill('#' + id, {
                modules: {
                    toolbar: [
                        ['bold', 'italic', 'underline', 'strike'],
                        ['blockquote', 'code-block'],
                        [{ 'list': 'ordered'}, { 'list': 'bullet' }],
                        [{ 'script': 'sub'}, { 'script': 'super' }],
                        [{ 'indent': '-1'}, { 'indent': '+1' }],
                        [{ 'size': ['small', false, 'large', 'huge'] }],
                        [{ 'header': [1, 2, 3, 4, 5, 6, false] }],
                        [{ 'color': [] }],
                        [{ 'align': [] }],
                        [{ 'direction': 'rtl' }],
                        ['clean'],
                    ],
                },
                placeholder: 'Compose an epic...',
                theme: 'snow'
            });
            quill.on('text-change', function() {
                textarea.val(quill.root.innerHTML);
            });
        },
    }
});
