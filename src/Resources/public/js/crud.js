$(document).ready(function() {

    var $modal = $('#crud-delete-modal');
    var version = parseInt(window.Foundation.version[0]);

    // Open delete modal
    $('table.crud-grid .prezent-grid-actions a.delete').on('click', function (event) {
        event.preventDefault();
        $modal.find('.button-group .delete').attr('href', $(this).attr('href'));

        if (version == 5) {
            $modal.foundation('reveal', 'open');
        } else if (version == 6) {
            $modal.foundation('open');
        }
    });

    // Close delete modal
    $modal.find('.button-group .cancel').on('click', function (event) {
        event.preventDefault();

        if (version == 5) {
            $modal.foundation('reveal', 'close');
        } else if (version == 6) {
            $modal.foundation('close');
        }
    });

});
