{% assets ['tulia_editor', 'tulia_editor_new'] %}

<div id="{{ params.id }}-qaaaa"></div>

<script>
    $(function () {
        new TuliaEditorAdmin('#{{ params.id }}-qaaaa', {
            //data: $('.tulia-editor-payload-field[data-tulia-editor-group-id="{{ params.group_id }}"]').val(),
            structure: {
                /*"manifest": {
                    "version": "1.0.0",
                    "framework": "bootstrap-5"
                },
                "metadata": {
                    "some-key": "some value"
                },
                "styles": [
                    "https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/css/bootstrap.min.css"
                ],
                "scripts": [
                    "https://cdn.jsdelivr.net/npm/bootstrap@5.0.0-beta1/dist/js/bootstrap.bundle.min.js"
                ],*/
                "source": {
                    "sections": [
                        {
                            "id": "4607658a-38e5-463a-b3f1-7e8458383f82",
                            "rows": [
                                {
                                    "id": "ec92759f-9731-45c9-acad-79b090545276",
                                    "columns": [
                                        {
                                            "id": "cdb2717e-3860-4fce-9229-d502a7e580b5",
                                            "size": [
                                                {
                                                    "breakpoint": "xs",
                                                    "size": 12
                                                },
                                                {
                                                    "breakpoint": "lg",
                                                    "size": 7
                                                }
                                            ],
                                            "blocks": [
                                                {
                                                    "id": "5c28bbf6-9f98-4ecd-90cc-51cc85b3728b",
                                                    "type": "core-textblock",
                                                    "data": {
                                                        "text": "<p>Some sample text, MY TEXT :)</p>"
                                                    }
                                                },
                                                {
                                                    "id": "5c28bbf6-9f98-4ecd-90cc-41cc85b3728b",
                                                    "type": "core-textblock",
                                                    "data": {}
                                                }
                                            ]
                                        },
                                        {
                                            "id": "8201ffcd-e811-407e-8aec-4de28ca0f70f",
                                            "row_id": "ec92759f-9731-45c9-acad-79b090545276",
                                            "size": [
                                                {
                                                    "breakpoint": "xs",
                                                    "size": 12
                                                },
                                                {
                                                    "breakpoint": "lg",
                                                    "size": 5
                                                }
                                            ],
                                            "blocks": [
                                                {
                                                    "id": "5c28bbf6-9f98-4ecd-90cc-51cc85b3728b",
                                                    "type": "core-textblock",
                                                    "data": {
                                                        "text": "<p>Some sample text, MY TEXT :)</p>"
                                                    }
                                                },
                                                {
                                                    "id": "5c28bbf6-9f98-4ecd-90cc-41cc85b3728b",
                                                    "type": "core-textblock",
                                                    "data": {}
                                                }
                                            ]
                                            /*"blocks": [
                                                {
                                                    "id": "f863ee0b-8e8e-455a-82ef-4f2171f7fad7",
                                                    "column_id": "8201ffcd-e811-407e-8aec-4de28ca0f70f",
                                                    "type": "core/text",
                                                    "data": {
                                                        "content": "<p>Pellentesque eleifend dolor eget ante ultrices lobortis.</p><p>Pellentesque eleifend dolor eget ante ultrices lobortis. Sed sed sem vitae massa lacinia maximus nec quis enim. Aenean placerat, felis varius laoreet egestas, lorem tortor tincidunt odio, eget scelerisque dolor ante in mi. Nulla quis mauris pulvinar magna lacinia ullamcorper. Aliquam quis tortor sem. Sed in lectus dictum, dapibus ex vitae, ultricies mi. Vivamus vel orci hendrerit, faucibus neque nec, dapibus mauris. Duis at laoreet lorem, quis scelerisque ante. Nunc congue et leo sit amet porttitor. Phasellus quis sollicitudin sapien. Donec venenatis hendrerit orci.</p>"
                                                    }
                                                }
                                            ]*/
                                        }
                                    ]
                                },
                                {
                                    "id": "441fe4f9-8d39-4cf9-8269-536d30cc474d",
                                    "section_id": "4607658a-38e5-463a-b3f1-7e8458383f82",
                                    "columns": [
                                        {
                                            "id": "f28bb61d-51a9-4766-b307-05a92f358ef5",
                                            "row_id": "441fe4f9-8d39-4cf9-8269-536d30cc474d",
                                            "size": [
                                                {
                                                    "breakpoint": "xs",
                                                    "size": 12
                                                }
                                            ],
                                            "blocks": [
                                                {
                                                    "id": "5c28bbf6-9f98-4ecd-90cc-51cc85b3728b",
                                                    "type": "core-textblock",
                                                    "data": {
                                                        "text": "<p>Some sample text, MY TEXT :)</p>"
                                                    }
                                                },
                                                {
                                                    "id": "5c28bbf6-9f98-4ecd-90cc-41cc85b3728b",
                                                    "type": "core-textblock",
                                                    "data": {
                                                        "text": "<p>Quisque sit amet sem scelerisque, aliquam nisl ut, fringilla mi. Nulla commodo et nisl in suscipit. Mauris id turpis sit amet sapien feugiat venenatis. Aliquam pellentesque ac quam eget venenatis. Nullam pellentesque, arcu vel tempor bibendum, mauris arcu accumsan ex, quis condimentum mauris tellus sed nisi. In condimentum velit porta feugiat accumsan. Nulla facilisi. Cras sed facilisis nisl. Suspendisse auctor ex diam, ut pulvinar nisi rutrum eget. Nunc luctus tortor ac ante faucibus cursus. Nam quis imperdiet nulla.Curabitur sollicitudin lectus sem, quis iaculis libero dapibus quis. Proin imperdiet elit vel elit molestie, vitae tempus est dignissim. Nam eu sem id mi fringilla mattis vitae dignissim dui. Sed non laoreet lorem, quis mattis velit. Aliquam sem nisl, mattis ut tortor et, scelerisque finibus nulla. Morbi sed dui eget lectus vulputate finibus ac sit amet libero. Sed a dapibus enim, nec auctor risus. Pellentesque pharetra nisl sollicitudin malesuada sodales. Praesent porta in libero rhoncus luctus. Pellentesque eleifend consequat eros, at iaculis ligula. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Duis sed rhoncus elit. Morbi sed dui vehicula, viverra orci at, suscipit urna. Vestibulum eget urna interdum ex dignissim vulputate.Cras convallis purus eget mattis placerat. Etiam tortor mi, dignissim et ornare nec, tincidunt eu lacus. Ut eu odio a velit maximus sollicitudin. Donec laoreet ullamcorper nulla, a venenatis metus congue et. Integer eu sapien non augue laoreet eleifend. Orci varius natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Aliquam erat volutpat. Phasellus vitae dolor vitae augue malesuada pulvinar sed ut magna. Nulla facilisi. Aliquam ultricies tortor sed enim mattis faucibus. Cras convallis nibh at urna venenatis tincidunt.Aenean nibh enim, porttitor ut magna ac, placerat vulputate metus. Fusce ut odio at lectus ornare semper aliquam et lorem. Vestibulum in ex eget urna faucibus vulputate. Vivamus nunc massa, dapibus non dictum sit amet, fringilla ac augue. Proin euismod fringilla lacus, sed finibus ex vestibulum ut. Vestibulum posuere libero sit amet massa gravida ultricies. Curabitur accumsan congue ex, a molestie neque sodales sed. Duis semper nisi metus, eget tempus turpis cursus in. In dignissim, eros vel ornare fermentum, magna odio maximus tortor, sit amet feugiat ipsum risus vitae dui.</p>"
                                                    }
                                                }
                                            ]
                                            /*"blocks": [
                                                {
                                                    "id": "af8fb198-e75c-4da6-a2db-1af975289bde",
                                                    "column_id": "f28bb61d-51a9-4766-b307-05a92f358ef5",
                                                    "type": "core/heading",
                                                    "data": {
                                                        "content": "Heading block",
                                                        "level": "h3",
                                                        "style": "heading-primary"
                                                    }
                                                },
                                                {
                                                    "id": "468059b4-4ef9-46db-b1ac-b20341788bd6",
                                                    "column_id": "f28bb61d-51a9-4766-b307-05a92f358ef5",
                                                    "type": "core/image",
                                                    "data": {
                                                        "source": "assets/images/202-1296x300.jpg"
                                                    }
                                                }
                                            ]*/
                                        }
                                    ]
                                },
                                {
                                    "id": "9be05ac7-8fa9-487a-a8db-f912a2704dff",
                                    "section_id": "4607658a-38e5-463a-b3f1-7e8458383f82",
                                    "columns": [
                                        {
                                            "id": "cb4c0859-e42d-4531-be8f-071f5889d099",
                                            "row_id": "9be05ac7-8fa9-487a-a8db-f912a2704dff",
                                            "size": [
                                                {
                                                    "breakpoint": "xs",
                                                    "size": 6
                                                }
                                            ],
                                            "blocks": [
                                                {
                                                    "id": "5c28bbf6-9f98-4ecd-90cc-51cc85b3728b",
                                                    "type": "core-textblock",
                                                    "data": {
                                                        "text": "<p>Some sample text, MY TEXT :)</p>"
                                                    }
                                                },
                                                {
                                                    "id": "5c28bbf6-9f98-4ecd-90cc-41cc85b3728b",
                                                    "type": "core-textblock",
                                                    "data": {}
                                                }
                                            ]
                                            /*"blocks": [
                                                {
                                                    "id": "d3aeb447-dc10-4822-a242-5cff07a32bd7",
                                                    "column_id": "cb4c0859-e42d-4531-be8f-071f5889d099",
                                                    "type": "core/text",
                                                    "data": {
                                                        "content": "<p>Maecenas et rutrum lectus. Sed finibus nibh mi, in rhoncus ante blandit eu.</p>"
                                                    }
                                                }
                                            ]*/
                                        },
                                        {
                                            "id": "717faea4-cce0-4bc7-8664-4dc2259bc6ee",
                                            "row_id": "9be05ac7-8fa9-487a-a8db-f912a2704dff",
                                            "size": [
                                                {
                                                    "breakpoint": "xs",
                                                    "size": 6
                                                }
                                            ],
                                            "blocks": [
                                                {
                                                    "id": "5c28bbf6-9f98-4ecd-90cc-51cc85b3728b",
                                                    "type": "core-textblock",
                                                    "data": {
                                                        "text": "<p>Quisque sit amet sem scelerisque, aliquam nisl ut, fringilla mi. Nulla commodo et nisl in suscipit. Mauris id turpis sit amet sapien feugiat venenatis. Aliquam pellentesque ac quam eget venenatis. Nullam pellentesque, arcu vel tempor bibendum, mauris arcu accumsan ex, quis condimentum mauris tellus sed nisi. In condimentum velit porta feugiat accumsan. Nulla facilisi. Cras sed facilisis nisl. Suspendisse auctor ex diam, ut pulvinar nisi rutrum eget. Nunc luctus tortor ac ante faucibus cursus. Nam quis imperdiet nulla.Curabitur sollicitudin lectus sem, quis iaculis libero dapibus quis. Proin imperdiet elit vel elit molestie, vitae tempus est dignissim. Nam eu sem id mi fringilla mattis vitae dignissim dui. Sed non laoreet lorem, quis mattis velit. Aliquam sem nisl, mattis ut tortor et, scelerisque finibus nulla. Morbi sed dui eget lectus vulputate finibus ac sit amet libero. Sed a dapibus enim, nec auctor risus. Pellentesque pharetra nisl sollicitudin malesuada sodales. Praesent porta in libero rhoncus luctus. Pellentesque eleifend consequat eros, at iaculis ligula. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Duis sed rhoncus elit. Morbi sed dui vehicula, viverra orci at, suscipit urna. Vestibulum eget urna interdum ex dignissim vulputate.Cras convallis purus eget mattis placerat. Etiam tortor mi, dignissim et ornare nec, tincidunt eu lacus. Ut eu odio a velit maximus sollicitudin. Donec laoreet ullamcorper nulla, a venenatis metus congue et. Integer eu sapien non augue laoreet eleifend. Orci varius natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Aliquam erat volutpat. Phasellus vitae dolor vitae augue malesuada pulvinar sed ut magna. Nulla facilisi. Aliquam ultricies tortor sed enim mattis faucibus. Cras convallis nibh at urna venenatis tincidunt.Aenean nibh enim, porttitor ut magna ac, placerat vulputate metus. Fusce ut odio at lectus ornare semper aliquam et lorem. Vestibulum in ex eget urna faucibus vulputate. Vivamus nunc massa, dapibus non dictum sit amet, fringilla ac augue. Proin euismod fringilla lacus, sed finibus ex vestibulum ut. Vestibulum posuere libero sit amet massa gravida ultricies. Curabitur accumsan congue ex, a molestie neque sodales sed. Duis semper nisi metus, eget tempus turpis cursus in. In dignissim, eros vel ornare fermentum, magna odio maximus tortor, sit amet feugiat ipsum risus vitae dui.</p>"
                                                    }
                                                },
                                                {
                                                    "id": "5c28bbf6-9f98-4ecd-90cc-41cc85b3728b",
                                                    "type": "core-textblock",
                                                    "data": {}
                                                }
                                            ]
                                        }
                                    ]
                                }
                            ]
                        }
                    ]
                },
                /*"content": "<div></div>"*/
            },
            editor: {
                view: '{{ path('backend.tulia_editor.editor_view') }}',
            },
            start_point: 'editor'
        });
    });
</script>

























{#{% assets theme.config.all('tulia_editor_plugin')|keys %}#}

{#{% set data = entity.attribute('tulia-editor-data', 'null') %}#}

<textarea style="display: none !important;" id="tulia-editor-{{ params.id }}-content" name="{{ name }}">{{ content|raw }}</textarea>

{#{% if content and (data is empty or data == 'null') %}
    <div class="alert alert-info alert-dismissible fade show">
        <p>{{ 'contentNotSupportedByEditorCopyToNotLoss'|trans({}, 'tulia-editor') }}</p>
        <textarea class="form-control">{{ content|raw }}</textarea>
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
    </div>
{% endif %}#}

<div id="{{ params.id }}"></div>

<script nonce="{{ csp_nonce() }}">
    $(function () {
        TuliaEditor.create('#{{ params.id }}', {
            data: $('.tulia-editor-payload-field[data-tulia-editor-group-id="{{ params.group_id }}"]'),
            framework: 'bootstrap-5',
            lang: '{{ user().locale }}',
            {#include: {
                stylesheets: {{ (assetter_standalone_assets(theme.config.all('tulia_editor_asset')|keys).stylesheets)|json_encode|raw }}
            },#}
            styles: {
                predefined: {
                    heading: {
                        "heading-primary": "style.predefined.heading.primary"
                    }
                }
            },
            setup: function (editor) {
                editor.on('save', function (content) {
                    $('#tulia-editor-{{ params.id }}-content').val(content.content);
                    $('#node_form_tulia_editor_data').val(JSON.stringify(content));
                });
            }
        });

        TuliaEditor.i18n['pl']['style.predefined.heading.primary'] = 'Styl 1';
        TuliaEditor.i18n['en']['style.predefined.heading.primary'] = 'Style 1';
    });
</script>