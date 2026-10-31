{{-- CKEditor 5 --}}
<script src="https://cdn.ckeditor.com/ckeditor5/41.4.2/classic/ckeditor.js"></script>

@stack('scripts')

<script>
    class MyUploadAdapter {

        constructor(loader, uploadUrl) {
            this.loader = loader;
            // Textarea có data-upload-url thì upload về đó, không thì dùng route ảnh sản phẩm
            this.uploadUrl = uploadUrl || '{{ route('admin.products.upload-image') }}';
        }

        upload() {
            return this.loader.file.then(file => {

                return new Promise((resolve, reject) => {

                    const data = new FormData();

                    data.append('upload', file);

                    const csrfToken = document
                        .querySelector('meta[name="csrf-token"]')
                        ?.getAttribute('content');

                    if (csrfToken) {
                        data.append('_token', csrfToken);
                    }

                    fetch(this.uploadUrl, {
                        method: 'POST',
                        body: data,
                        headers: {
                            'Accept': 'application/json'
                        }
                    })
                        .then(response => {

                            if (!response.ok) {
                                throw new Error('Upload ảnh thất bại');
                            }

                            return response.json();
                        })
                        .then(result => {

                            if (result.uploaded && result.url) {

                                resolve({
                                    default: result.url
                                });

                            } else {

                                reject(
                                    result.error?.message ||
                                    'Không thể upload ảnh'
                                );
                            }

                        })
                        .catch(error => {
                            reject(error);
                        });

                });

            });
        }

        abort() {
            // Không cần xử lý
        }
    }


    function MyCustomUploadAdapterPlugin(editor) {

        editor.plugins
            .get('FileRepository')
            .createUploadAdapter = loader => {

            return new MyUploadAdapter(loader, editor.config.get('uploadUrl'));

        };
    }


    document.addEventListener('DOMContentLoaded', function () {

        document
            .querySelectorAll('textarea.ckeditor')
            .forEach(function (element) {

                ClassicEditor.create(element, {

                    extraPlugins: [
                        MyCustomUploadAdapterPlugin
                    ],

                    uploadUrl: element.dataset.uploadUrl || null,

                    toolbar: [
                        'heading',
                        '|',
                        'bold',
                        'italic',
                        'underline',
                        'strikethrough',
                        '|',
                        'link',
                        'bulletedList',
                        'numberedList',
                        '|',
                        'blockQuote',
                        'insertTable',
                        '|',
                        'imageUpload',
                        'mediaEmbed',
                        '|',
                        'undo',
                        'redo'
                    ],

                    language: 'vi'

                })
                    .then(editor => {

                        console.log(
                            'CKEditor đã khởi tạo:',
                            element.name
                        );

                    })
                    .catch(error => {

                        console.error(
                            'CKEditor error:',
                            error
                        );

                    });

            });

    });
</script>