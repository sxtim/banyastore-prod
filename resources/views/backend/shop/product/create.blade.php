@extends('layouts.backend')

@section('pagetitle', 'Добавить продукт')
@section('aside_shop', 'active')

@section('content')

    <a href="{{ route('backend.product.index') }}" class="back-link">
        Продукты
    </a>

    <div class="pagetitle">
        Добавить продукт
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

    <form action="{{ route('backend.product.store') }}" method="post" enctype="multipart/form-data" id="product_form">
        @include('backend.shop.product.form')
    </form>
    @php
        $content = old('description', '')
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
                            endpoint: '/backend/product/add-image',
                            deleteFileEndpoint: '/backend/product/delete-image',
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

            document.getElementById('product_form').addEventListener('submit', function () {

                editor.save().then((outputData) => {
                    console.log(JSON.stringify(outputData));

                    document.getElementById('description').value = JSON.stringify(outputData);
                    //    $('#blog_description', this).val(JSON.stringify(outputData));
                }).catch((error) => {
                    console.log('Saving failed: ', error)
                });
            });
        }
        initEditor({!! $content !!} );

        // document.getElementById('blog_form').addEventListener('submit', function () {
        //     console.log('stop')
        //     return false;
        //     editor.save().then((outputData) => {
        //         console.log(JSON.stringify(outputData));
        //
        //         $('#blog_description', this).val(JSON.stringify(outputData));
        //     }).catch((error) => {
        //         console.log('Saving failed: ', error)
        //     });
        // });

    </script>
@endsection
