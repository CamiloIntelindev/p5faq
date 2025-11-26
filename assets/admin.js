jQuery(document).ready(function($) {
    var itemIndex = $('#p5faq-items .p5faq-item').length;

    // Add new question
    $('#p5faq-add-item').on('click', function(e) {
        e.preventDefault();
        
        var template = $('#p5faq-item-template').html();
        var newItem = template.replace(/\{\{INDEX\}\}/g, itemIndex);
        
        $('#p5faq-items').append(newItem);
        itemIndex++;
        
        reindexItems();
    });

    // Remove question
    $(document).on('click', '.p5faq-remove', function(e) {
        e.preventDefault();
        
        if (confirm('Are you sure you want to remove this question?')) {
            $(this).closest('.p5faq-item').remove();
            reindexItems();
        }
    });

    // Reindex all items to maintain sequential order
    function reindexItems() {
        $('#p5faq-items .p5faq-item').each(function(index) {
            // Update display number
            $(this).find('.p5faq-number').text(index + 1);
            
            // Update data-index attribute
            $(this).attr('data-index', index);
            
            // Update input field names to maintain order
            $(this).find('input[type="text"]').attr('name', 'p5faq_items[' + index + '][question]');
            $(this).find('textarea').attr('name', 'p5faq_items[' + index + '][answer]');
            
            // Update remove button data-index
            $(this).find('.p5faq-remove').attr('data-index', index);
        });
        
        // Update itemIndex for next additions
        itemIndex = $('#p5faq-items .p5faq-item').length;
    }
});
