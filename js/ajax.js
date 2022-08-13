var $ = $ || jQuery;

$(function() {
    console.log($)

    $('#client_config_create_field').submit(function(event, data) {
        event.preventDefault();

        console.log(event, data);
    });

});