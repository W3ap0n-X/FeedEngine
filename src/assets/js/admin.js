

(function($) {
    'use strict';

    /**
     * Look for the dynamic variable created by the Manifest Prefix.
     * If Manifest::PREFIX is 'qckfe', this looks for qckfe_vars.
     */
    const prefix = $('.wrap').data('prefix'); // Option A: Data Attribute
    const settings = window[prefix + '_vars'];

    $(function() {
        const $form = $('#' + settings.slug + '_admin_form');
        
        $form.on('submit', function(e) {
            e.preventDefault();
            
            $.ajax({
                url: settings.rest_url + 'settings',
                method: 'POST',
                beforeSend: function(xhr) {
                    xhr.setRequestHeader('X-WP-Nonce', settings.nonce);
                },
                data: $form.serialize(),
                done: function(response) {
                    // Use settings.prefix to find your notice anchor!
                    const anchor = $('#' + settings.prefix + '_notices');
                    anchor.html('<div class="notice notice-success"><p>' + response.message + '</p></div>');
                },
                fail: function(xhr) {
                    // 2. Handle "Hard" errors (Status 400, 500, etc.)
                    const errorMsg = xhr.responseJSON ? xhr.responseJSON.message : 'Critical Server Error';
                    $noticeContainer.html(`<div class="notice notice-error"><p>${errorMsg}</p></div>`);
                },
                always: function() {
                    // 3. Re-enable the button regardless of outcome
                    $form.find('button[type="submit"]').prop('disabled', false).removeClass('updating');
                }
            });
        });
    });

})(jQuery);