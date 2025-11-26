jQuery(document).ready(function($) {
    // Toggle FAQ items
    $('.p5faq-question').on('click', function() {
        var $item = $(this).closest('.p5faq-item');
        var $answer = $item.find('.p5faq-answer');
        
        // Cerrar otras respuestas abiertas (opcional - comentar para permitir múltiples abiertas)
        $('.p5faq-item').not($item).removeClass('active');
        $('.p5faq-answer').not($answer).slideUp(300);
        
        // Toggle la respuesta actual
        $item.toggleClass('active');
        $answer.slideToggle(300);
    });
});
