$(function() {
    var search = $('#ubicazioni_form_filtro');
    // When sport gets selected ...
    search.change(function() {
        // ... retrieve the corresponding form.
        var form = $(this).closest('form');
        // Simulate form data, but only include the selected sport value.
        var data = {};
        data['filtro'] = search.val();
        // Submit data via AJAX to the form's action path.
        $.ajax({
            url : window.location.pathname,
            type: form.attr('method'),
            data : data,
            success: function(html) {
                // Replace current position field ...
                $('#ubicazioni_form_articolo').replaceWith(
                    // ... with the returned one from the AJAX response.
                    $(html).find('#ubicazioni_form_articolo')
                );
                // Position field now displays the appropriate positions.
            }
        });
    });
});

