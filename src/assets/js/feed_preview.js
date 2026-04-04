console.log("Freddie Mercury");
jQuery('#qckfe-refresh-preview').on('click', function() {
    const $button = jQuery(this);
    const $container = jQuery('#qckfe-preview-results');
    
    // 1. Gather all inputs within our settings prefix
    const formData = {};
    jQuery('[name^="_qckfe_settings"]').each(function() {
        jQuery(this).val();
        // Simple logic to extract keys and values into a JSON object
    });

    $button.prop('disabled', true).text('Loading...');

    // 2. Hit the REST Preview Route
    wp.apiFetch({
        path: '/qckfe/v1/feed/preview',
        method: 'POST',
        data: formData
    }).then(response => {
        console.log(response);
        let items = response.html;
        console.log(items);
        // 3. Render the simple list
        let html = '<ul>';
        items.forEach( item => {
            html += `<li><strong>[${item.id}]</strong> ${item.title} <small>(${item.source})</small></li>`;
        } );
        
        // items.forEach(item => {
        //     
        // });
        html += '</ul>';
        console.log(html);
        $container.html(html);
    }).finally(() => {
        $button.prop('disabled', false).text('Refresh List');
    });
});