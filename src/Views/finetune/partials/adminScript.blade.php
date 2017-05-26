</form>
<script src="/finetune/assets/js/tinymce/tinymce.js"></script>
<script src="/finetune/assets/js/jquery-3.1.1.min.js"></script>

<script>
    tinymce.init({
        selector: '#title',
        inline: true,
        toolbar: 'undo redo',
        menubar: false
    });


    tinymce.init({
        selector: '#body-content',
        inline: true,
        theme: 'modern',
        skin: "finetune",
        convert_urls: false,
        plugins: [
            'autolink link image hr',
            'searchreplace wordcount visualblocks code fullscreen',
            'insertdatetime media table contextmenu directionality',
            'paste', 'save'
        ],
        menubar: false,
        toolbar1: 'insertfile undo redo | styleselect | bold italic  | bullist numlist| save',
        image_advtab: true,
    });
</script>
</body>