{% assets ['filemanager'] %}

<div class="form-group mb-3 {{ is_multilingual ? ' form-group-multilingual' : '' }}">
    <label class="customizer-label">{{ label|trans({}, translation_domain) }}</label>
    <div class="input-group">
        <input type="text" id="{{ control_id }}" name="{{ control_name }}" class="customizer-control form-control" value="{{ value }}" data-transport="{{ transport }}" />
        <div class="input-group-append">
            <button class="btn btn-default btn-icon-only" type="button" id="{{ control_id }}-trigger">
                <i class="btn-icon fas fa-folder-open"></i>
            </button>
        </div>
    </div>
</div>

<script nonce="{{ csp_nonce() }}">
    $(function () {
         TuliaFilemanager.create({
            targetInput: '#{{ control_id }}',
            endpoint: '{{ path('backend.filemanager.endpoint') }}',
            filter: {
                type: '{{ file_type ?? '*' }}',
            },
            multiple: false,
            closeOnSelect: true,
            openTrigger: '#{{ control_id }}-trigger',
        });
    });
</script>
