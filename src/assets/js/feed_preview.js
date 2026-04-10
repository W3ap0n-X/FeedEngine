console.log("FEEDENGINE META JS 1.0");

document.addEventListener( 'DOMContentLoaded' , function() {
    console.log("loaded...");
    runQckFeedPreview(); 
    // Check if we are on the right screen
    // if ( jQuery( '#qckfe-refresh-btn' ).length ) {
    //     // Trigger the refresh immediately so management sees the grid 
    //     // as soon as the page loads.
    //     runQckFeedPreview(); 
    // }

} );

function gather_meta_data(elementName) {
    const data = {};
    jQuery( '[name^="' + elementName + '"]' ).each( function() {
        const input = jQuery( this );
        let value;
        const type = jQuery( this ).attr('type');
        switch (type) {

            case 'checkbox':
            case 'radio':
                if( !input.is(':checked') ) {
                    return;
                } else {
                    value = input.val();
                    break;
                }

            case 'hidden':
                if(jQuery(this).hasClass('qckfe-image-id') && input.val() != '') {
                    value = input.val();
                    break;
                } else if(jQuery(this).hasClass('qckfe-post-id') && input.val() != '') {
                    value = input.val();
                    break;
                } else {
                    return;
                }

            default:
                // console.log(`Element is ${type}.`);
                value = input.val();
        }
        
        // 1. Extract the keys from the name (e.g., ["_qckfe_feed_settings", "example", "source_type"])
        // This regex grabs everything that isn't a bracket.
        const path = this.name.match( /[^[\]]+/g );
        // 2. Remove the "root" prefix (_qckfe_feed_settings) so we start fresh
        path.shift();
        // 2. Determine if this is meant to be an array (ends in [])
        const isArray = this.name.endsWith( '[]' )
        // 3. Drill down into the object to set the value
        let current = data;
        while ( path.length > 1 ) {
            const key = path.shift();
            // Create the nested object if it doesn't exist yet
            current[ key ] = current[ key ] || {};
            // Move the pointer deeper
            current = current[ key ];
        }
        const finalKey = path.shift();
        // 3. The Logic Branch
        if ( isArray ) {
            // Initialize as array if it doesn't exist, then push
            current[ finalKey ] = current[ finalKey ] || [];
            if ( ! Array.isArray( current[ finalKey ] ) ) {
                current[ finalKey ] = [ current[ finalKey ] ]; // Safety catch
            }
            current[ finalKey ].push( value );
        } else {
            // Standard key/value assignment
            current[ finalKey ] = value;
        }

    } );
    return data;
}

function runQckFeedPreview() {
    const $button = jQuery('#qckfe-refresh-btn');
    const $container = jQuery('#qckfe-preview-results');
    const feedSettings = gather_meta_data('_qckfe_feed_settings');
    const post_types = gather_meta_data('_qckfe_feed_post_types');
    const feed_info = {
        'id' : jQuery('#post_ID').val(),
        'image' : (jQuery('#_thumbnail_id').val() > 0 ? jQuery('#_thumbnail_id').val() : null ),
    };
    
    const categories = gather_meta_data('_qckfe_feed_categories');
    const tags = gather_meta_data('_qckfe_feed_tags');
    
    const feed_data = {
        "feed_info": feed_info,
        "feedSettings":feedSettings,
        "post_types":post_types, 
        "categories": categories,
        "tags": tags
    };
    console.log(feed_data);

    $button.prop('disabled', true).text('Loading...');

    // 2. Hit the REST Preview Route
    wp.apiFetch({
        path: '/qckfe/v1/feed/preview',
        method: 'POST',
        data: feed_data
    }).then(response => {
        console.log(response);
        
        let itemSets = response.html;
        
        let html = '';
        for (set in itemSets) {
            html +='<div class="qckfe-preview-grid qckfe-' + set + '-item" >';
            
            itemSets[set].forEach( item => {
                // console.log(item);
                html += `
                    <div class="qckfe-preview-item" >
                        <img class="qckfe-image-preview" src="${item.image_url}">
                        <div class="qckfe-preview-info" >
                        <h4>${item.title}</h4>
                            <ul>
                                <li>
                                    <strong>[${item.id}]</strong> 
                                </li>
                                <li>
                                    ${set}
                                </li>
                                <li>
                                    <small class="qckfe-source-badge">(${item.source} | ${item.type})</small>
                                </li>
                            
                            </ul>
                        </div>
                    </div>
                `;
            } );
            
            html += '</div>';
        }

        // 3. Render the simple list
        
        
        $container.html(html);
    }).finally(() => {
        $button.prop('disabled', false).text('Refresh List');
    });
}

jQuery(document).ready(function($) {
    jQuery('#qckfe-refresh-preview').on('click', function() {
        runQckFeedPreview();
    });
    
    // 1. Initialize Drag and Drop
    $('#qckfe-manual-list').sortable({
        placeholder: "ui-state-highlight",
        // update: function(event, ui) {
        //     // Optional: Trigger your preview refresh when they finish dragging
        //     runQckFeedPreview();
        // }
    });

    // 2. Handle Search (Simplified)
    $('#qckfe-post-search').on('keyup', function() {
        let term = $(this).val();
        if(term.length < 3) return;

        // Hit your REST endpoint or admin-ajax
        $.get(wpApiSettings.root + 'wp/v2/posts?search=' + term, function(posts) {
            let html = '';
            posts.forEach(post => {
                html += `
                <li data-id="${post.id}">${post.title.rendered} <button type="button" class="add-btn">+</button></li>`;
            });
            $('#qckfe-search-results').html(html);
        });
    });

    // 3. Add to Feed
    $(document).on('click', '.add-btn', function() {
        let $li = $(this).parent();
        let id = $li.data('id');
        let title = $li.text().replace('+', '').trim();

        // Add to the sortable list with a hidden input
        $('#qckfe-manual-list').append(`
            <li>
                <span class="dashicons dashicons-menu"></span> ${title}
                <input class="qckfe-post-id" type="hidden" name="_qckfe_feed_settings[manual_ids][]" value="${id}">
                <button type="button" class="remove-btn">-</button>
            </li>
        `);
        $li.remove();
    });
    // 3. Add to Feed
    $(document).on('click', '.remove-btn', function() {
        let $li = $(this).parent();
        
        $li.remove();
    });
});

