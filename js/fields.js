$('#wpbody form input').on('input', function() {
    if ($(this).val()) {
        $(this).removeClass('error');
    }
});