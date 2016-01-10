<?php
add_filter( 'wp_handle_upload', 'attachment', 100, 3 );
add_filter( 'add_attachment', 'remove_pdf',1, 1 );
add_filter( 'async_upload_file', function(){ echo "afarr";} );


function attachment($attachment){
    if(is_pdf($attachment)){
        add_attachment($attachment);
    }
    return $attachment;
}

function is_pdf($attachment){
    return get_extension($attachment) === "pdf";
}

function get_extension($attachment){
    return pathinfo(get_path($attachment), PATHINFO_EXTENSION);
}

function get_dir($attachment){
    return pathinfo(get_path($attachment), PATHINFO_DIRNAME);
}

function get_basename($attachment){
    return pathinfo(get_path($attachment), PATHINFO_BASENAME);
}

function get_path($attachment){
    return $attachment["file"];
}

function add_attachment( $attachment){
    $options = get_option( 'pdf2img_settings' );

    $converted_images = convert_to_IMG($attachment, $options);
    foreach($converted_images as $k =>$image){
        $img_title = preg_replace( '/\.[^.]+$/', '', basename( $image ) );
        $thumbnail = array(
            'post_type' => 'attachment',
            'post_mime_type' => 'image/jpeg',
            'post_title'     => $img_title,
            'post_excerpt' => '',
            'post_content' => '',
        );
        //Add attachment to DB
        $thumbnail_id = wp_insert_attachment( $thumbnail, $image);

        $metadata = wp_generate_attachment_metadata( $thumbnail_id, $image );
        $metadata['pdf_url'] = $attachment['path'];
        if ( !empty( $metadata ) && ! is_wp_error( $metadata ) ) {
            wp_update_attachment_metadata( $thumbnail_id, $metadata );
        }
    }
//    var_dump($attachment);
    return $attachment;
}

function convert_to_IMG($attachment, $options){
    $path = get_path($attachment);
    $dir = get_dir($attachment);
    $basename = get_basename($attachment);

    $converted_images = array();

    $max_width = ( $options[ 'max_width' ] ? (int) $options[ 'max_width' ] : 0 );
    $max_height = ( $options[ 'max_height' ] ? (int) $options[ 'max_height' ] : 0 );

    $img_extension = ( $options[ 'img_extension' ] ? $options[ 'img_extension' ] : 'jpg' );

    $pages_to_convert = ($options["pages_to_convert"] ? (int) $options["pages_to_convert"] : 0);
    if ( $pages_to_convert > 5 ) $pages_to_convert = 5;
    $pages_to_convert = $pages_to_convert-1;

    $quality = ( $options[ 'quality' ] ? (int) $options[ 'quality' ] : 80 );
    if ( $quality > 100 ) $quality = 100;

    try {
        $imagick = new Imagick();
        $imagick->clear();
        $imagick->destroy();
        if ( $options ) {
            $imagick->setResolution( 150, 150 );
            $imagick->readimage( $path );
            $imagick->setCompressionQuality( $quality );

        } else {
            $imagick->setResolution( 72, 72 );
            $imagick->readimage( $path );
        }

        foreach ($imagick as $c => $_page) {

            if($pages_to_convert == -1 || $c<=$pages_to_convert){

                $_page->setImageBackgroundColor('white');
                $_page->setImageFormat($img_extension);

                if($max_width && $max_height){

                    $_page->adaptiveResizeImage( $max_width, $max_height, true);
                }

                $blankPage = new \Imagick();

                $blankPage->newPseudoImage( $_page->getImageWidth(), $_page->getImageHeight(), "canvas:white" );
                $blankPage->compositeImage( $_page, \Imagick::COMPOSITE_OVER, 0, 0 );

                if($blankPage->writeImage( $dir."/".$basename.'-'.$c.'.'.$img_extension )){
                    array_push($converted_images, $dir."/".$basename.'-'.$c.'.'.$img_extension);
                }

                $blankPage->clear();
                $blankPage->destroy();
            }
        }

    } catch ( ImagickException $e ){
        $converted_images = false;
    } catch ( Exception $e ){
        $converted_images = false;
    }

    return $converted_images;
}

function remove_pdf($attachment_id){
    $mime = get_post_mime_type($attachment_id);
    $options = get_option( 'pdf2img_settings' );

    $remove_pdf = ( $options[ 'remove_pdf' ] ? $options[ 'remove_pdf' ] : false );
    $image_url = wp_get_attachment_url($attachment_id, '')."-0.".$options[ 'img_extension' ];

    if($remove_pdf && $mime==="application/pdf"){
        wp_delete_attachment( $attachment_id );
        $attachment_id = get_attachment_id_from_src ($image_url);
        render_output($attachment_id);
    }

    return $attachment_id;
}

function get_attachment_id_from_src ($src) {
    global $wpdb, $table_prefix;
    $reg = "/-[0-9]+x[0-9]+?.(jpg|jpeg|png|gif)$/i";
    $src1 = preg_replace($reg,'',$src);
    if($src1 != $src){
        $ext = pathinfo($src, PATHINFO_EXTENSION);
        $src = $src1 . '.' .$ext;
    }
    $res = $wpdb->get_results('select post_id from ' . $table_prefix . 'postmeta where meta_value like "%' . basename($src). '%"');
    $id = end($res)->post_id;
    return $id;
}

function render_output($id){
    $time = time();

    get_media_items( $id, null);
    $post = get_post( $id );
    if ( $thumb_url = wp_get_attachment_image_src( $id, 'thumbnail', true ) )
        $pinkynail =  '<img class="pinkynail" src="' . esc_url( $thumb_url[0] ) . '" alt="" />';
    $edit_link = '<a class="edit-attachment" href="' . esc_url( get_edit_post_link( $id ) ) . '" target="_blank">' . _x( 'Edit', 'media item' ) . '</a>';

    // Title shouldn't ever be empty, but use filename just in case.
    $file = get_attached_file( $post->ID );
    $title = $post->post_title ? $post->post_title : wp_basename( $file );
    $file_name_new = '<div class="filename new"><span class="title">' . esc_html( wp_html_excerpt( $title, 60, '&hellip;' ) ) . '</span></div>';

    $pinkynail = isset($pinkynail) ? $pinkynail : '';

    ?>

    <script id="<?php echo $time; ?>">
        var parent;


        setTimeout(function(){
            console.log("<?php echo $time; ?>");
            parent = document.getElementById("<?php echo $time; ?>").parentNode;
            var progress = parent.getElementsByClassName("progress");
            var filename = parent.getElementsByClassName("filename original");

            console.log(parent);
            console.log(progress);
            console.log(filename);


            parent.innerHTML = "";

            aaa();
        }, 1000);

        var aaa = function(){
            console.log(parent);
            var pinkynail = '<?php echo $pinkynail; ?>';
            var edit_link = '<?php echo $edit_link;?>';
            var file_name_new = '<?php echo $file_name_new;?>';

            parent.innerHTML = pinkynail+edit_link+file_name_new;
            console.log(pinkynail+edit_link+file_name_new);
        }


    </script>
    <?php


}

