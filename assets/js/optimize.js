require ('../css/optimize.css');

let $addRigaButton = $('<a id="rigaAddBtn" class="btn btn-success mb-3"><i class="fas fa-plus-circle"></i> Aggiungi pezzi</a>');
// let $addButton = $('#rigaAddBtn');

// let $newLinkLi = $addButton;

$(document).ready(function() {
    $collectionHolder = $('#listaMisure');

    $collectionHolder.find('div').each(function() {
        addTagFormDeleteLink($(this));
    });

    // $collectionHolder.append($addRigaButton);
    $('#titoloListaPezzi').append($addRigaButton);
    $collectionHolder.data('index', $collectionHolder.find(':input').length);

    $addRigaButton.on('click', function () {
        addRigaForm($collectionHolder, $addRigaButton);
    })
});

function addRigaForm($collectionHolder, $addRigaButton) {
    // Get the data-prototype explained earlier
    let prototype = $collectionHolder.data('prototype');

    // get the new index
    let index = $collectionHolder.data('index');

    let newForm = prototype;
    // You need this only if you didn't set 'label' => false in your tags field in TaskType
    // Replace '__name__label__' in the prototype's HTML to
    // instead be a number based on how many items we have
    // newForm = newForm.replace(/__name__label__/g, index);

    // Replace '__name__' in the prototype's HTML to
    // instead be a number based on how many items we have
    newForm = newForm.replace(/__index__/g, index);

    // increase the index with one for the next item
    $collectionHolder.data('index', index + 1);

    // Display the form in the page in an li, before the "Add a tag" link li
    // var $newFormLi = $('<li></li>').append(newForm);
    $collectionHolder.append($.parseHTML(newForm));
    // $addRigaButton.before($.parseHTML(newForm));

    // add a delete link to the new form
    addTagFormDeleteLink($('#articolo_'+index));
}

function addTagFormDeleteLink($tagFormLi) {
    let $removeRigaButton = $('<button id="rigaRemoveBtn" class="btn btn-danger ml-3"><i class="fas fa-minus-circle"></i> Elimina</button>');
    $tagFormLi.append($removeRigaButton );

    $removeRigaButton.on('click', function(e) {
        // remove the li for the tag form
        $tagFormLi.remove();
    });
}