jQuery(document).ready(function($) {
    var itemIndex = $('#p5faq-items .p5faq-item').length;

    // Agregar nueva pregunta
    $('#p5faq-add-item').on('click', function(e) {
        e.preventDefault();
        
        var template = $('#p5faq-item-template').html();
        var newItem = template.replace(/\{\{INDEX\}\}/g, itemIndex);
        
        $('#p5faq-items').append(newItem);
        itemIndex++;
        
        updateItemNumbers();
    });

    // Eliminar pregunta
    $(document).on('click', '.p5faq-remove', function(e) {
        e.preventDefault();
        
        if (confirm('¿Estás seguro de que deseas eliminar esta pregunta?')) {
            $(this).closest('.p5faq-item').remove();
            updateItemNumbers();
        }
    });

    // Actualizar números de las preguntas
    function updateItemNumbers() {
        $('#p5faq-items .p5faq-item').each(function(index) {
            $(this).find('.p5faq-number').text(index + 1);
        });
    }
});
