$(function() {
    $('.confirm-delete').on('submit', function () {
        if (! confirm('Are you sure?')) {
            return false;
        }
    });

    // Add Playlist rule
    $('.filter-rule-row .fa-plus').parent().on('click', function(event) {
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

            showHideFilterRuleComparisonOperators($('.filter-rule-row:last select:first'));
    });

    // Remove Playlist rule
    $('.filter-rule-row .fa-minus').parent().on('click', function(event) {
        event.preventDefault();
        $(this).parents('.form-group').remove();
    });

    // Add Playlist Rules
    if ($('.filter-rule-row select:first').length) {
        showHideFilterRuleComparisonOperators($('.filter-rule-row select:first'));
    }
    $(document).on('change', '.rule-key-select', function(event) {
        showHideFilterRuleComparisonOperators($(this));

        // Change placeholder text depending on selected Rule
        var $input = $(this).parents('.filter-rule-row').find('input[type=text]');

        if (this.value == 'artist') {
            $input.attr('placeholder', 'Tenacious D');
        } else if (this.value == 'album') {
            $input.attr('placeholder', 'Rize of the Fenix');
        } else if (this.value == 'year') {
            $input.attr('placeholder', '2012');
        } else if (this.value == 'date_added') {
            $input.attr('placeholder', 'e.g. dd-mm-yyyy');
        }
    });

    function showHideFilterRuleComparisonOperators($this) {
        $this.parent().next().find('option').show();
        $this.parent().next().find('option').not('.option-' + $this.val()).hide();
        $this.parent().next().find('option:selected').prop('selected', false);
    }
});
