if ($('[data-images-section]').length) {
    $('[data-images-section]').each(function (i, el) {
        $(el).data('images-counter', $(el).find('[data-image-form]').length);
    });
}

$('[data-images-add-new]').click(function(event) {
    var section = $(this).closest('[data-section]').find('[data-images-section]')
      , count   = section.data('images-counter')
      , form    = $('<div/>').html($('[data-image-form-tpl]').first().data('prototype')).html().replace(/__name__/g, count)
      ;

    section.data('images-counter', count + 1);
    
    $('[data-images-section]').append(form);
    $('[data-images-section] > [data-image-form]').last().find('[type=file]').click();
    
    event.preventDefault();
});

$('[data-images-section]').delegate('[data-image-delete]', 'click', function(event) {
    $(this).closest('[data-image-form]').remove();
    
    event.preventDefault();
});