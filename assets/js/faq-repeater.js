/**
 * Eternal Product Category FAQ - Repeater Functionality
 *
 * Handles adding and removing FAQ rows in the admin interface
 */

(function($) {
    'use strict';

    /**
     * Initialize on document ready
     */
    $(document).ready(function() {

        /**
         * Add new FAQ row
         */
        $('#add-faq-button').on('click', function(e) {
            e.preventDefault();

            var container = $('#faq-repeater-container');
            var rowCount = container.find('.faq-row').length;
            var newRow = $('<div class="faq-row" style="margin-bottom: 15px; border: 1px solid #ddd; padding: 15px; background: #f9f9f9;"></div>');

            // Question field
            var questionDiv = $('<div style="margin-bottom: 10px;"></div>');
            questionDiv.append('<label style="font-weight: bold; color: #0073aa;">' + eternalFaqData.questionLabel + '</label>');
            questionDiv.append('<textarea name="faq_questions[' + rowCount + '][question]" class="faq-question" rows="2" style="width: 100%;" placeholder="' + eternalFaqData.questionPlaceholder + '"></textarea>');
            newRow.append(questionDiv);

            // Answer field
            var answerDiv = $('<div></div>');
            answerDiv.append('<label style="font-weight: bold; color: #0073aa;">' + eternalFaqData.answerLabel + '</label>');
            answerDiv.append('<textarea name="faq_questions[' + rowCount + '][answer]" class="faq-answer" rows="3" style="width: 100%;" placeholder="' + eternalFaqData.answerPlaceholder + '"></textarea>');
            newRow.append(answerDiv);

            // Remove button
            var removeButton = $('<button type="button" class="button remove-faq-button" style="margin-top: 10px; color: #a00;">' + eternalFaqData.removeText + '</button>');
            newRow.append(removeButton);

            container.append(newRow);

            // Re-index all rows
            reindexRows();
        });

        /**
         * Remove FAQ row
         */
        $(document).on('click', '.remove-faq-button', function(e) {
            e.preventDefault();

            var row = $(this).closest('.faq-row');

            // Don't remove if it's the only row (optional - uncomment if you want at least one row)
            /*
            if ($('#faq-repeater-container .faq-row').length <= 1) {
                return;
            }
            */

            row.remove();
            reindexRows();
        });

        /**
         * Re-index all FAQ rows after add/remove
         */
        function reindexRows() {
            $('#faq-repeater-container .faq-row').each(function(index) {
                $(this).find('textarea[name*="question"]').attr('name', 'faq_questions[' + index + '][question]');
                $(this).find('textarea[name*="answer"]').attr('name', 'faq_questions[' + index + '][answer]');
            });
        }
    });

})(jQuery);
