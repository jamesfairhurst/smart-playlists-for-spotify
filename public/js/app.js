$(function() {
    // Add Playlist rule
    $('.glyphicon-plus').parent().on('click', function(event) {
        event.preventDefault();

        // Get number of current rules, used to index new rules correctly
        var $ruleCount = $('.filter-rule-row').length;

        // Find the parent group
        $(this).parents('.form-group')
            .clone(true)
            // Update the key in the form input name
            .find(':input').each(function(k,v) {
                this.name= this.name.replace('[0]', '[' + $ruleCount + ']');
            }).end()
            // Remove all copied values
            .find('input').val('').end()
            // Remove the label
            .find('label').html('').end()
            // Hide the add button
            .find('.btn-success').addClass('hidden').end()
            // Show the delete button
            .find('.btn-danger').removeClass('hidden').end()
            // Finally add to end of filters
            .insertAfter('.filter-rule-row:last');
    });

    // Remove Playlist rule
    $('.glyphicon-minus').parent().on('click', function(event) {
        event.preventDefault();
        $(this).parents('.form-group').remove();
    });
});
