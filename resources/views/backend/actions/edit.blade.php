@extends('layouts.backend')

@section('pagetitle', 'Редактировать акцию')
@section('aside_actions', 'active')

@section('content')
    <a href="{{ route('backend.actions.index') }}" class="back-link">
        Акции
    </a>

    <div class="pagetitle">
        Редактировать акцию
    </div>

    @if (\Session::has('success'))
        <div class="alert alert-success">
            {!! \Session::get('success') !!}
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form class="form-wrap-modif"
          action="{{ route('backend.actions.update', ['actionId' => $action->id]) }}"
          method="post" enctype="multipart/form-data"
          id="action_form"
    >
        @method('PATCH')
        @include('backend.actions.form')
    </form>

    @php
        $content = json_encode($action->detail_text, JSON_UNESCAPED_UNICODE);
        $content = old('detail_text', $content)
    @endphp
@endsection

@section('scripts')
    <script>

        window.initEditor = function (data) {
            let element = document.getElementById('editorjs')
            const editor = new EditorJS({
                holder: 'editorjs',
                tools: {
                    header: HeaderEditorJs,

                    image: {
                        class: ImageBlockEditorJs,
                        config: {
                            endpoint: '/backend/actions/add-image',
                            deleteFileEndpoint: '/backend/actions/delete-image',
                        },
                    },

                },
                onChange: () => {
                    editor.save().then((outputData) => {
                        console.log(JSON.stringify(outputData));
                        localStorage.setItem('lastText', JSON.stringify(outputData));
                    })
                },
                data: data,
                i18n: {
                    messages: {
                        ui: {
                            "blockTunes": {
                                "toggler": {
                                    "Click to tune": "Нажмите, чтобы настроить",
                                    "or drag to move": "или перетащите"
                                },
                            },
                            "inlineToolbar": {
                                "converter": {
                                    "Convert to": "Конвертировать в"
                                }
                            },
                            "toolbar": {
                                "toolbox": {
                                    "Add": "Добавить"
                                }
                            }
                        },
                        toolNames: {
                            "Text": "Параграф",
                            "Heading": "Заголовок",
                            "Image": "Изображение",
                        },
                        tools: {
                            "image": {
                                "Caption": "Подпись"
                            }
                        }
                    }
                }
            });

            document.getElementById('action_form').addEventListener('submit', function () {

                editor.save().then((outputData) => {
                    // console.log(JSON.stringify(outputData));

                    document.getElementById('detail_text').value = JSON.stringify(outputData);
                }).catch((error) => {
                    console.log('Saving failed: ', error)
                });
            });
        }
        initEditor({!! $content !!} );

    </script>
@endsection
