(function( $ ) {
	'use strict';

	/**
	 * All of the code for your admin-specific JavaScript source
	 * should reside in this file.
	 *
	 * Note that this assume you're going to use jQuery, so it prepares
	 * the $ function reference to be used within the scope of this
	 * function.
	 *
	 * From here, you're able to define handlers for when the DOM is
	 * ready:
	 *
	 * $(function() {
	 *
	 * });
	 *
	 * Or when the window is loaded:
	 *
	 * $( window ).load(function() {
	 *
	 * });
	 *
	 * ...and so on.
	 *
	 * Remember that ideally, we should not attach any more than a single DOM-ready or window-load handler
	 * for any particular page. Though other scripts in WordPress core, other plugins, and other themes may
	 * be doing this, we should try to minimize doing that in our own work.
	 */
    $(function() {
        var ELM_Settings = {
            init : function() {
                this.general();
            },

            general : function() {
                // Settings Upload field JS
                if ( typeof wp == "undefined" ) {
                    //Old Thickbox uploader
                    if ( $( '.elm_settings_upload_button' ).length > 0 ) {
                        window.formfield = '';

                        $('body').on('click', '.elm_settings_upload_button', function(e) {
                            e.preventDefault();
                            window.formfield = $(this).parent().prev();
                            window.tbframe_interval = setInterval(function() {
                                jQuery('#TB_iframeContent').contents().find('.savesend .button').val(elm_vars.use_this_file).end().find('#insert-gallery, .wp-post-thumbnail').hide();
                            }, 2000);
                            tb_show( elm_vars.add_new_download, 'media-upload.php?referrer_page=elm-settings&TB_iframe=true' );
                        });

                        window.edd_send_to_editor = window.send_to_editor;
                        window.send_to_editor = function (html) {
                            if (window.formfield) {
                                var imgurl = $('a', '<div>' + html + '</div>').attr('href');
                                window.formfield.val(imgurl);
                                window.clearInterval(window.tbframe_interval);
                                tb_remove();
                            } else {
                                window.edd_send_to_editor(html);
                            }
                            window.send_to_editor = window.edd_send_to_editor;
                            window.formfield = '';
                            window.imagefield = false;
                        };
                    }
                } else {
                    // WP 3.5+ uploader
                    var file_frame;
                    window.formfield = '';

                    $('body').on('click', '.elm_settings_upload_button', function(e) {

                        e.preventDefault();

                        var button = $(this);

                        window.formfield = $(this).parent().prev();

                        // If the media frame already exists, reopen it.
                        if ( file_frame ) {
                            //file_frame.uploader.uploader.param( 'post_id', set_to_post_id );
                            file_frame.open();
                            return;
                        }

                        // Create the media frame.
                        file_frame = wp.media.frames.file_frame = wp.media({
                            frame: 'post',
                            state: 'insert',
                            title: button.data( 'uploader_title' ),
                            button: {
                                text: button.data( 'uploader_button_text' )
                            },
                            multiple: false
                        });

                        file_frame.on( 'menu:render:default', function( view ) {
                            // Store our views in an object.
                            var views = {};

                            // Unset default menu items
                            view.unset( 'library-separator' );
                            view.unset( 'gallery' );
                            view.unset( 'featured-image' );
                            view.unset( 'embed' );

                            // Initialize the views in our view object.
                            view.set( views );
                        } );

                        // When an image is selected, run a callback.
                        file_frame.on( 'insert', function() {

                            var selection = file_frame.state().get('selection');
                            selection.each( function( attachment, index ) {
                                attachment = attachment.toJSON();
                                window.formfield.val(attachment.url);
                            });
                        });

                        // Setting page param
                        file_frame.on( 'ready', function() {
                            file_frame.uploader.options.uploader.params = {
                                referrer_page: 'elm-settings'
                            };
                        });

                        // Finally, open the modal
                        file_frame.open();
                    });
                }
            }
        };
        ELM_Settings.init();

        // Tooltips
        jQuery( '.help_tip' ).tipTip( {
            'attribute' : 'data-tip',
            'fadeIn' : 50,
            'fadeOut' : 50,
            'delay' : 200
        } );

    });

})( jQuery );
