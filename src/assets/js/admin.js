(function($) {
    'use strict';

    $(function() {
        const prefix = $('.wrap').data('prefix'); 
        const settings = window[prefix + '_vars'];
console.log("FEEDENGINE JS LOADED");
console.log(settings.nonce);
        // THE FIX: Use $form consistently
        const $form = $('.' + prefix + '_admin_form');
        
        $form.on('submit', function(e) {
            e.preventDefault();
            let testjson = JSON.stringify($form.serializeArray());
            console.log(testjson);
            // Visual feedback: disable button
            const $submitBtn = $form.find('input[type="submit"], button[type="submit"]');
            $submitBtn.prop('disabled', true).addClass('updating');

            $.ajax({
                url: settings.rest_url + 'settings',
                method: 'POST',
                headers: {
                    'X-WP-Nonce': settings.nonce, // Necessary for REST API authentication
                    'Content-Type': 'application/json'
                },
                data: $form.serialize(),
                success: function(response) {
                    const anchor = $('#' + settings.prefix + '_notices');
                    anchor.html('<div class="notice notice-success is-dismissible"><p>' + response.message + '</p></div>');
                },
                error: function(xhr) {
                    const errorMsg = xhr.responseJSON ? xhr.responseJSON.message : 'Critical Server Error';
                    const anchor = $('#' + settings.prefix + '_notices');
                    anchor.html('<div class="notice notice-error"><p>' + errorMsg + '</p></div>');
                },
                complete: function() {
                    $submitBtn.prop('disabled', false).removeClass('updating');
                }
            });
        });
    });

})(jQuery);


