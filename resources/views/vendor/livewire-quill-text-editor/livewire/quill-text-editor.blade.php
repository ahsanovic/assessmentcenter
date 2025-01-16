<div wire:ignore>
    <div id="{{ $quillId }}"></div>
</div>

@script
    <script>
        const quill = new Quill('#' + @js($quillId), {
            theme: @js($theme),
            placeholder: 'jawaban anda',
            modules: {
                toolbar: [
                    ['bold', 'italic', 'underline'],
                    [{
                        list: 'ordered'
                    }, {
                        list: 'bullet'
                    }],
                    ['clean'],
                ],
            },
        });

        quill.root.innerHTML = $wire.get('value');

        quill.on('text-change', function() {
            let value = quill.root.innerHTML;
            @this.set('value', value);
        });
    </script>
@endscript
