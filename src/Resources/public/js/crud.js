$(document).ready(function() {

    $('.crud-modal').each(function () {
        var $modal = $(this);
        var action = $modal.attr('data-action');

        // Open modal
        $('table.crud-grid .prezent-grid-actions a.' + action).on('click', function (event) {
            event.preventDefault();
            $modal.find('.button-group .' + action).attr('href', $(this).attr('href'));
            $modal.foundation('open');
        });

        // Close modal
        $modal.find('.button-group .cancel').on('click', function (event) {
            event.preventDefault();
            $modal.foundation('close');
        });
    });

});
