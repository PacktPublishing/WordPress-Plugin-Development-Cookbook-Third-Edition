<?php 
/*
  Plugin Name: Chapter 5 - Custom File Attachment
  Plugin URI: 
  Description: Companion to recipe 'Extending the post editor to attach custom files with the Media Uploader'
  Author: ylefebvre
  Version: 1.0
  Author URI: http://ylefebvre.ca/
 */

add_action( 'add_meta_boxes', 'ch5_cfa_register_meta_box' ); 

function ch5_cfa_register_meta_box() {
    add_meta_box( 'ch5_cfa_attach_file', 'File Attachment', 'ch5_cfa_attach_meta_box', array( 'post', 'page' ), 'normal' );
} 

function ch5_cfa_attach_meta_box( $post ) {
    $attach_url = get_post_meta( get_the_ID(), 'attach_url', true ); 
    ?>
    
    <table>
        <tr>
            <td style="width: 150px">Attachment URL</td>
            <td><input type="text" class="components-text-control__input" name="attach_url" id="attach_url" size="80" value="<?php echo $attach_url; ?>" /></td>
            <td><input type="button" id="attach_file_button" class="components-button is-secondary" value="Assign using Media Uploader"></td>
        </tr>
    </table>

    <script type="text/javascript">
    jQuery( document ).ready( function () {
        var file_frame;
        jQuery( '#attach_file_button' ).on( 'click', function ( event ) {
            event.preventDefault();

            // If the media frame already exists, reopen it.
            if ( file_frame ) {
                file_frame.open();
                return;
            }

            // Create the media frame.
            file_frame = wp.media.frames.file_frame = wp.media( {
                title : 'Select post file attachment',
                button : { text: 'Assign attachment' },
                multiple: false
            } );

            // When an image is selected, run a callback.
            file_frame.on( 'select', function () {
                attachment = file_frame.state().get( 'selection' ).first().toJSON();
                jQuery( '#attach_url' ).val( attachment.url );
            });

            file_frame.open();
        });
    });
    </script>
<?php }

add_action( 'save_post', 'ch5_cfa_save_attachment', 10, 2 );

function ch5_cfa_save_attachment( $post_id = false, $post = false ) {
    if ( in_array( $post->post_type, array( 'page', 'post' ) ) ) {
        if ( isset( $_POST['attach_url'] ) ) {
            update_post_meta( $post_id, 'attach_url', esc_url_raw( $_POST['attach_url'] ) );
        }
    }
} 

add_filter( 'the_content', 'ch5_cfa_display_pdf_link' );

function ch5_cfa_display_pdf_link( $content ) {
    $post_id = get_the_ID();
    if ( empty( $post_id ) || !in_array( get_post_type( $post_id ), array( 'page', 'post' ) ) ) {
        return $content;
    }
    
    $attach_url = get_post_meta( $post_id, 'attach_url', true );
    if ( !empty( $attach_url ) ) {
        $content .= '<div class="file_attachment">';
        $content .= '<a target="_blank" href="';
        $content .= esc_url( $attach_url );
        $content .= '">';
        $content .= 'Download additional info</a></div>';
    }
    return $content;
}   