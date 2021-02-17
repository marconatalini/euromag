$(function() {
    var search = $('#ubicazioni_form_filtro');
    // When sport gets selected ...
    search.change(function() {
        // ... retrieve the corresponding form.
        let form = $(this).closest('form');
        // Simulate form data, but only include the selected sport value.
        let data = {};
        data['filtro'] = search.val();
        // Submit data via AJAX to the form's action path.
        let overlay = document.querySelector('.overlay');
        let spinner = document.querySelector('.spinner-border');
        let blurtarget = document.getElementsByName('ubicazioni_form')[0];

        console.log(blurtarget);

        overlay.style.visibility = 'visible';
        spinner.style.visibility = 'visible';
        blurtarget.classList.add('loading');

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
                overlay.style.visibility = 'hidden';
                spinner.style.visibility = 'hidden';
                blurtarget.classList.remove('loading');

            }
        });
    });
});

