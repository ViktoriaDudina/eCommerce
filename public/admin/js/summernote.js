
        "use strict";

        // sommer note for Products description 

        $(document).ready(function() {
            $("#products_description").summernote({
                placeholder: 'Type your Description',
                height: 150,
                toolbar: [
                    ['style', ['style', 'fontname', 'bold', 'italic', 'underline', 'clear']],
                    ['font', ['strikethrough', 'superscript', 'subscript']],
                    ['fontsize', ['fontsize']],
                    ['color', ['color']],
                    ['para', ['ul', 'ol', 'paragraph']],
                    ['height', ['height']]
                ]
            });
        });

        $('.dropdown-toggle').dropdown();
