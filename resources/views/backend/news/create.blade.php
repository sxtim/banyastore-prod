@extends('layouts.backend')

@section('pagetitle', 'Добавить новость')
@section('aside_news', 'active')

@section('content')

    <a href="{{ route('backend.news.index') }}" class="back-link">
        Акции
    </a>

    <div class="pagetitle">
        Добавить новость
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

    <form action="{{ route('backend.news.store') }}" method="post" enctype="multipart/form-data" id="news_form">
        @include('backend.news.form')
    </form>

@endsection
@php
    $content = old('detail_text', '')
@endphp


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
                            endpoint: '/backend/news/add-image',
                            deleteFileEndpoint: '/backend/news/delete-image',
                        },
                    },
                    // image: {
                    //     class: ImageTool,
                    //     config: {
                    //         endpoints: {
                    //             byFile: upload_dir
                    //         }
                    //     }
                    // }
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

            document.getElementById('news_form').addEventListener('submit', function () {

                editor.save().then((outputData) => {
                 //   console.log(JSON.stringify(outputData));

                    document.getElementById('detail_text').value = JSON.stringify(outputData);

                }).catch((error) => {
                    console.log('Saving failed: ', error)
                });
            });
        }
        initEditor({!! $content !!} );

    </script>
@endsection
