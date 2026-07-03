
(function($) {
    'use strict';

    $(function() {
        // const prefix = $('.data-wrap').data('prefix'); 
        const prefix = 'qckfe';
        const settings = window[prefix + '_vars'];
console.log(prefix + " JS LOADED");
        // THE FIX: Use $form consistently
        if(prefix){
            /*  
            * *****  REST SUBMIT  *****
            */
            const $form = $('.' + prefix + '_admin_form');
        
            // * Submit Settings via REST
            $form.on('submit', function(e) {
                e.preventDefault();
                const formData = {};
                // const _formData = {};
                $form.serializeArray().forEach(item => {
                    const isArray = item.name.endsWith( '[]' )
                    if(isArray) {
                        let listName = item.name.slice(0, -2);
                        console.log(listName);
                        formData[ listName ] = formData[ listName ] || [];
                        if ( ! Array.isArray( formData[ listName ] ) ) {
                            formData[ listName ] = [ formData[ listName ] ]; // Safety catch
                        }
                        formData[ listName ].push( item.value );
                        
                    } else {
                        formData[item.name] = item.value;
                    }
                    
                });
                console.log(formData);
                // Visual feedback: disable button
                const $submitBtn = $form.find('input[type="submit"], button[type="submit"]');
                $submitBtn.prop('disabled', true).addClass('updating');

                $.ajax({
                    url: settings.rest_url + 'settings',
                    method: 'POST',
                    beforeSend: function(xhr) {
                        xhr.setRequestHeader('X-WP-Nonce', settings.nonce);
                    },
                    contentType: 'application/json; charset=utf-8',
                    data: JSON.stringify(formData),
                    success: function(response) {
                        const anchor = $('#' + settings.prefix + '_notices');
                        anchor.html( response.message ).hide().fadeIn();
                        $(document).trigger('wp-updates-notice-added');
                    },
                    error: function(xhr) {
                        const errorMsg = xhr.responseJSON ? xhr.responseJSON.message : 'Critical Server Error';
                        const anchor = $('#' + settings.prefix + '_notices');
                        anchor.html('<div class="notice notice-error"><p>' + errorMsg + '</p></div>').hide().fadeIn();
                        $(document).trigger('wp-updates-notice-added');
                    },
                    complete: function() {
                        $submitBtn.prop('disabled', false).removeClass('updating');
                    }
                });
            });


            

        }
        

    });

    

})(jQuery);


