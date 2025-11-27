jQuery(document).ready(function($) {
    console.log('P5 FAQ Admin Script Loaded - Timestamp:', Date.now());

    // Add new FAQ item (solo una vez)
    $(document).off('click', '#p5faq-add-item').on('click', '#p5faq-add-item', function(e) {
        e.preventDefault();
        console.log('Add button clicked - Timestamp:', Date.now());
        
        // Find the highest index from existing items
        var maxIndex = -1;
        $('.p5faq-item .question').each(function() {
            var currentIndex = parseInt($(this).attr('question-index-id')) || 0;
            if (currentIndex > maxIndex) {
                maxIndex = currentIndex;
            }
        });
        
        // New index is maxIndex + 1
        var newIndex = maxIndex + 1;
        console.log('Creating new item with index:', newIndex);
        
        // Clone the first item
        var $newItem = $('.p5faq-item').first().clone();
        
        // Update the question input attributes
        $newItem.find('.question')
            .attr('question-index-id', newIndex)
            .attr('name', 'p5faq_items[' + newIndex + '][question]')
            .val(''); // Clear value
        
        // Update the answer input attributes
        $newItem.find('.answer')
            .attr('answer-index-id', newIndex)
            .attr('name', 'p5faq_items[' + newIndex + '][answer]')
            .val(''); // Clear value
        
        // Insert before the "Add Question" button
        $newItem.insertBefore('#p5faq-add-item');
        
        console.log('New item added with index:', newIndex);
    });

    // Remove FAQ item
    $(document).off('click', '.remove-faq-item').on('click', '.remove-faq-item', function(e) {
        e.preventDefault();
        
        // Don't remove if it's the only item
        if ($('.p5faq-item').length === 1) {
            alert('You must have at least one FAQ item.');
            return;
        }
        
        if (confirm('Are you sure you want to remove this question?')) {
            $(this).closest('.p5faq-item').remove();
            console.log('Item removed');
            
            // Reindex all remaining items
            reindexItems();
        }
    });
    
    /**
     * Reindex all FAQ items after removal
     */
    function reindexItems() {
        $('.p5faq-item').each(function(newIndex) {
            var $item = $(this);
            
            // Update question input
            $item.find('.question')
                .attr('question-index-id', newIndex)
                .attr('name', 'p5faq_items[' + newIndex + '][question]');
            
            // Update answer input
            $item.find('.answer')
                .attr('answer-index-id', newIndex)
                .attr('name', 'p5faq_items[' + newIndex + '][answer]');
            
            console.log('Reindexed item to index:', newIndex);
        });
    }
});
