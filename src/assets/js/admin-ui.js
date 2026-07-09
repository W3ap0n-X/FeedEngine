
(function($) {
    'use strict';

    $(function() {
        const prefix = 'qckfe';
        // const prefix = $('.data-wrap').data('prefix'); 
        const settings = window[prefix + '_vars'];
        console.log(prefix + " Admin-ui JS Loaded");
        // THE FIX: Use $form consistently
        if(prefix){
            // * collapsable fields
            $(document).on('click', '.' + settings.prefix + '-field-collapsible>.' + settings.prefix + '-field-label', function(e) {
                let $parent = $(this).closest('.' + settings.prefix + '-field-collapsible');
                // let $content = $parent.children('.' + settings.prefix + '-field-content');
                $parent.toggleClass('collapsed');
            });



            /*  
            * *****  REPEATER FIELDS  *****
            */
           // * Get current date/timestamp for unique string key
            function getDateIdString(){
                function checkDigits(num){
                    if(num >= 0 && num < 10){
                        return 0 + '' + num;
                    } else {
                        return num;
                    }
                }
                const d = new Date();
                let year = ( d.getUTCFullYear() + '' ).substring(2);
                let month = d.getUTCMonth();
                let day = d.getUTCDate();
                let hour = d.getUTCHours();
                let min = d.getUTCMinutes();
                return checkDigits(year) + '' + checkDigits(month) + checkDigits(day) + '' + checkDigits(hour) + checkDigits(min);
            }

            // * Get list of indexes for a repeater field
            function getRepeaterChildrenIndexes( $repeaterContent ){

                let $indexes = [];

                $repeaterContent.children('.' + settings.prefix + '-repeat_field-item').each(function() {

                    let currentIndex = $(this).find('.' + settings.prefix + '-repeat_field-index').text();
                    console.log('currentIndex: ' + currentIndex);
                    $indexes.push(currentIndex);
                });
                return $indexes;
            }

            // * Get lunique key for new repeater item
            function getRepeaterNewChildIndex( $indexes, newIndex ){
                while($indexes.includes(newIndex + '')){
                            
                    if( !isNaN(newIndex) && !isNaN(parseFloat(newIndex)) && isFinite(newIndex) ) {
                        newIndex = parseFloat(newIndex) + 1;
                    } else {
                        newIndex = newIndex + "_" + getDateIdString(); 
                    }
                }
                return newIndex;
            }

            // $('.' + settings.prefix + '-repeat_field-content').sortable({});

            // * Add New Repeater field item
            $('.' + settings.prefix + '-repeat_field-add').on('click', function(e) {
                // console.log(settings.prefix);
                let $button = $(this);
                let $parent = $button.closest('.' + settings.prefix + '-repeat_field');

                let $templateRow = $parent.find('.' + settings.prefix + '-repeat_field-template').find('.' + settings.prefix + '-repeat_field-item'); 
                let $contentRow = $parent.find('.' + settings.prefix + '-repeat_field-content'); 
                let $indexes = getRepeaterChildrenIndexes( $contentRow );
                let $newRowLabel = $parent.find('.' + settings.prefix + '-repeat_field-add-new_item_label').val(); 
                // let $contentCount = $contentRow.children('.' + settings.prefix + '-element, .' + settings.prefix + '-field').length;
                let $contentCount = $contentRow.children('.' + settings.prefix + '-repeat_field-item').length;
                
                console.log('$indexes: '); console.log($indexes);
                let newIndex = $newRowLabel ? $newRowLabel : $contentCount ;
                console.log('newIndex: ' + newIndex);
                newIndex = getRepeaterNewChildIndex( $indexes, newIndex );
                console.log('newIndex: ' + newIndex);

                
                let newRow = $templateRow.clone();
                newRow.html( newRow.html().replaceAll('__INDEX__', newIndex) );
                newRow.prop('name', newRow.prop('name').replaceAll('[__INDEX__]', '[' + newIndex + ']'));
                newRow.find('input, select, textarea').each(function(input) {
                    $(this).prop('disabled', false);
                });
                if(newRow.data('new') !== 1) {
                    newRow.data('new', 1);

                }
                $contentRow.append(newRow);

                newRow.trigger('auxilia:repeater:after_row_add', [newRow]);

            });

            // * Remove Repeater field item
            $(document).on('click', '.' + settings.prefix + '-repeat_field-remove', function(e) {
                // console.log(this);
                let $button = $(this);
                let $parent = $button.closest('.' + settings.prefix + '-repeat_field-item');
                let $parentField = $parent.closest('.' + settings.prefix + '-repeat_field');
                
                
                // 
                let rmData = {};
                rmData[$parentField.prop('name')] = $parent.prop('name');
                console.log(rmData);
                if ( ! confirm('Are you sure you want to remove this field?') ) return;

                $parent.trigger('auxilia:repeater:before_row_remove', [$parent]);


                if($parent.data('new') !== 1) {

                    $.ajax({
                        url: settings.rest_url + 'settings/remove',
                        method: 'POST',
                        beforeSend: function(xhr) {
                            xhr.setRequestHeader('X-WP-Nonce', settings.nonce);
                        },
                        contentType: 'application/json; charset=utf-8',
                        data: JSON.stringify(rmData),
                        success: function(response) {
                            console.log(response);
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
                            $parent.remove();
                        }
                    });
                } else {
                    $parent.remove();
                }


            });

            // * Copy Repeater field item
            $(document).on('click', '.' + settings.prefix + '-repeat_field-copy', function(e) {
                

                // console.log(this);
                let $button = $(this);
                let $parent = $button.closest('.' + settings.prefix + '-repeat_field-item');

                $parent.trigger('auxilia:repeater:before_row_copy', [$parent]);


                let $parentField = $parent.closest('.' + settings.prefix + '-repeat_field');
                console.log('$parent: '); console.log($parent);
                let $contentRow = $parent.closest('.' + settings.prefix + '-repeat_field-content'); 
                let $indexes = getRepeaterChildrenIndexes( $contentRow );
                let $contentCount = $contentRow.children('.' + settings.prefix + '-repeat_field-item').length;
                console.log('$contentRow: '); console.log($contentRow);
                console.log('$indexes: '); console.log($indexes);
                console.log('$contentCount: ' + $contentCount);
                let $newRowLabel = $parent.find('.' + settings.prefix + '-repeat_field-index').text();
                console.log('$newRowLabel: ' + $newRowLabel);
                let newIndex = $newRowLabel ? $newRowLabel : $contentCount ;
                console.log('newIndex: ' + newIndex);
                newIndex = getRepeaterNewChildIndex( $indexes, newIndex );
                console.log('newIndex: ' + newIndex);
                let newRow = $parent.clone(true,true);

                

                newRow.html( newRow.html().replaceAll('[' + $newRowLabel + ']', '[' + newIndex + ']') );
                console.log('name: ' + newRow.prop('name'));
                newRow.prop('name', newRow.prop('name').replaceAll('[' + $newRowLabel + ']', '[' + newIndex + ']'));
                newRow.find('.' + settings.prefix + '-repeat_field-index').text(newIndex);
                newRow.find('input, select, textarea').each(function(input) {
                    $(this).prop('disabled', false);
                });
                // (Optional) Handle regular inputs/select boxes if they lost their value state:
                $parent.find('input, select, textarea').each(function(index, originalInput) {
                    newRow.find('input, select, textarea').eq(index).val($(originalInput).val());
                    newRow.find('input, select, textarea').eq(index).prop('checked', $(originalInput).prop('checked'));
                    newRow.find('input, select, textarea', 'option').eq(index).prop('selected', $(originalInput).prop('selected'));
                });
                if(newRow.data('new') !== 1) {
                    newRow.data('new', 1);

                }
                console.log('name: ' + newRow.prop('name'));
                $contentRow.append(newRow);
                $parent.trigger('auxilia:repeater:after_row_copy', [$parent]);

                newRow.trigger('auxilia:repeater:after_row_add', [newRow]);


            });


            /*  
            * *****  Image Controls  *****
            */
            let mediaFrame;

            // * Select from media library
            $('.' + settings.prefix + '-select-img').on('click', function(e) {
                e.preventDefault();
                // console.log(this);
                let $button = $(this);
                let $parent = $button.closest('.' + settings.prefix + '-image-preview-wrapper');
                console.log($parent);
                let $removeButton = $parent.find('.' + settings.prefix + '-remove-img');
                console.log($parent);
                let $previewImg = $parent.find('.' + settings.prefix + '-image-preview');
                console.log($previewImg);
                let $hiddenInput = $parent.find('.' + settings.prefix + '-image-id');
                // If the frame exists, just open it
                if (mediaFrame) {
                    mediaFrame.off('select');
                }

                // Create the media frame object
                mediaFrame = wp.media({
                    title: 'Select Image',
                    button: { text: 'Use this image' },
                    multiple: false // Only one image allowed
                });

                // "Select" event handler
                mediaFrame.on('select', function() {
                    const attachment = mediaFrame.state().get('selection').first().toJSON();
                    
                    // 1. Update the hidden input with the ID (The Schema)
                    $hiddenInput.val( attachment.id );

                    // 2. Update the Preview (The UI)
                    $previewImg.attr('src', attachment.url); 
                    $previewImg.show();
                    $removeButton.show();
                });

                mediaFrame.open();
            });

            // * Remove selected image.
            $('.' + settings.prefix + '-remove-img').on('click', function(e) {
                e.preventDefault();
                const $parent = $(this).closest('.' + settings.prefix + '-image-preview-wrapper');
                $parent.find('.' + settings.prefix + '-image-id').val('');
                $parent.find('.' + settings.prefix + '-image-preview').hide();
                $(this).hide();
            });


            

        }
    });
})(jQuery);


