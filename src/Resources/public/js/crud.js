$(document).ready(function() {

    var $modal = $('#crud-delete-modal');

    // Open delete modal
    $('table.crud-grid .prezent-grid-actions a.delete').on('click', function (event) {
        event.preventDefault();
        $modal.find('.button-group .delete').attr('href', $(this).attr('href'));
        $modal.foundation('open');
    });

    // Close delete modal
    $modal.find('.button-group .cancel').on('click', function (event) {
        event.preventDefault();
        $modal.foundation('close');
    });

});
