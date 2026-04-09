console.log("Freddie Mercury");
jQuery('#qckfe-refresh-preview').on('click', function() {
    const $button = jQuery(this);
    const $container = jQuery('#qckfe-preview-results');
    
    // 1. Gather all inputs within our settings prefix
    

    const feedSettings = {};

    jQuery( '[name^="_qckfe_feed_settings"]' ).each( function() {

        const input = jQuery( this );
        let value;
        const type = jQuery( this ).attr('type');
        switch (type) {
            case 'checkbox':
        case 'radio':
            // let checked = jQuery(this).attr('checked');
            // let checked2 = ;
            console.log(`Element is CHECK. `);
            // console.log(checked);
            // console.log(checked2);
            
            if( !input.is(':checked') ) {
                return;
            } else {
                value = input.val();
                break;
            }

        case 'hidden':
            console.log(`Element is HIDDEN.`);
            if(jQuery(this).hasClass('qckfe-image-id') && input.val() != '') {
                value = input.val();
                break;
            } else {
                return;
            }
            

        default:
            console.log(`Element is ${type}.`);
            value = input.val();
        }
        
        
        // 1. Extract the keys from the name (e.g., ["_qckfe_feed_settings", "example", "source_type"])
        // This regex grabs everything that isn't a bracket.
        const path = this.name.match( /[^[\]]+/g );

        // 2. Remove the "root" prefix (_qckfe_feed_settings) so we start fresh
        path.shift();

        // 3. Drill down into the object to set the value
        let current = feedSettings;

        while ( path.length > 1 ) {
            
            const key = path.shift();
            
            // Create the nested object if it doesn't exist yet
            current[ key ] = current[ key ] || {};
            
            // Move the pointer deeper
            current = current[ key ];
            
        }

        // 4. Set the final key to the input value
        current[ path.shift() ] = value;

    } );
    console.log(feedSettings);

    // const formData = {};
    // jQuery('[name^="_qckfe_feed_settings"]').each(function() {
    //     let ckval = jQuery(this).val();
    //     console.log(ckval);

        
    //     // Simple logic to extract keys and values into a JSON object
    // });
    // console.log(formData);

    const post_types = {};
    jQuery( '[name^="_qckfe_feed_post_types"]' ).each( function() {

        const input = jQuery( this );
        let value;
        const type = jQuery( this ).attr('type');
        switch (type) {
            case 'checkbox':
        case 'radio':
            // let checked = jQuery(this).attr('checked');
            // let checked2 = ;
            console.log(`Element is CHECK. `);
            // console.log(checked);
            // console.log(checked2);
            
            if( !input.is(':checked') ) {
                return;
            } else {
                value = input.val();
                break;
            }

        case 'hidden':
            console.log(`Element is HIDDEN.`);
            if(jQuery(this).hasClass('qckfe-image-id') && input.val() != '') {
                value = input.val();
                break;
            } else {
                return;
            }
            

        default:
            console.log(`Element is ${type}.`);
            value = input.val();
        }
        
        
        // 1. Extract the keys from the name (e.g., ["_qckfe_feed_settings", "example", "source_type"])
        // This regex grabs everything that isn't a bracket.
        const path = this.name.match( /[^[\]]+/g );

        // 2. Remove the "root" prefix (_qckfe_feed_settings) so we start fresh
        path.shift();

        // 3. Drill down into the object to set the value
        let current = post_types;

        while ( path.length > 1 ) {
            
            const key = path.shift();
            
            // Create the nested object if it doesn't exist yet
            current[ key ] = current[ key ] || {};
            
            // Move the pointer deeper
            current = current[ key ];
            
        }

        // 4. Set the final key to the input value
        current[ path.shift() ] = value;

    } );
    console.log(post_types);

    const categories = {};
    jQuery( '[name^="_qckfe_feed_categories"]' ).each( function() {

        const input = jQuery( this );
        let value;
        const type = jQuery( this ).attr('type');
        switch (type) {
            case 'checkbox':
        case 'radio':
            // let checked = jQuery(this).attr('checked');
            // let checked2 = ;
            console.log(`Element is CHECK. `);
            // console.log(checked);
            // console.log(checked2);
            
            if( !input.is(':checked') ) {
                return;
            } else {
                value = input.val();
                break;
            }

        case 'hidden':
            console.log(`Element is HIDDEN.`);
            if(jQuery(this).hasClass('qckfe-image-id') && input.val() != '') {
                value = input.val();
                break;
            } else {
                return;
            }
            

        default:
            console.log(`Element is ${type}.`);
            value = input.val();
        }
        
        
        // 1. Extract the keys from the name (e.g., ["_qckfe_feed_settings", "example", "source_type"])
        // This regex grabs everything that isn't a bracket.
        const path = this.name.match( /[^[\]]+/g );

        // 2. Remove the "root" prefix (_qckfe_feed_settings) so we start fresh
        path.shift();

        // 3. Drill down into the object to set the value
        let current = categories;

        while ( path.length > 1 ) {
            
            const key = path.shift();
            
            // Create the nested object if it doesn't exist yet
            current[ key ] = current[ key ] || {};
            
            // Move the pointer deeper
            current = current[ key ];
            
        }

        // 4. Set the final key to the input value
        current[ path.shift() ] = value;

    } );
    console.log(categories);
    $button.prop('disabled', true).text('Loading...');

    // 2. Hit the REST Preview Route
    wp.apiFetch({
        path: '/qckfe/v1/feed/preview',
        method: 'POST',
        data: {"feedSettings":feedSettings,"post_types":post_types, "categories": categories}
    }).then(response => {
        // console.log(response);
        
        let items = response.html;
        console.log(items);
        // 3. Render the simple list
        let html = '<ul>';
        items.forEach( item => {
            console.log(item);
            html += `<li><img class="qckfe-image-preview" src="${item.image_url}" style="max-width:20px;"><strong>[${item.id}]</strong> ${item.title} <small>(${item.source} | ${item.type})</small></li>`;
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