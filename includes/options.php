<?php
add_action( 'admin_menu', 'pdf2img_add_admin_menu' );
add_action( 'admin_init', 'pdf2img_settings_init' );


function pdf2img_add_admin_menu(  ) {

    add_options_page( 'PDF2IMG converter', 'PDF2IMG converter', 'manage_options', 'pdf2img_converter', 'pdf2img_options_page' );

}


function pdf2img_settings_init(  ) {

    register_setting( 'pluginPage', 'pdf2img_settings' );
//////////////////////////////////////////
    add_settings_section(
        'pdf2img_image_properties_section',
        __( 'General Image properties', 'wordpress' ),
		'pdf2img_settings_section_callback', 
		'pluginPage'
	);

	add_settings_field(
        'pdf2img_text_field_1',
        __( 'Count of convert pages', 'wordpress' ),
		'pdf2img_text_field_pages_to_convert_render', 
		'pluginPage',
		'pdf2img_image_properties_section'
	);

	add_settings_field(
        'pdf2img_select_field_2',
        __( 'Format of converted images', 'wordpress' ),
		'pdf2img_select_field_img_extension_render', 
		'pluginPage',
		'pdf2img_image_properties_section'
	);
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    add_settings_section(
        'pdf2img_customize_image_properties_section',
        __( 'Customize Generated Image properties', 'wordpress' ),
        'pdf2img_settings_section_callback',
        'pluginPage'
    );

    add_settings_field(
        'pdf2img_text_field_20',
        __( 'Max width (px):', 'wordpress' ),
        'pdf2img_text_field_max_width_render',
        'pluginPage',
        'pdf2img_customize_image_properties_section'
    );

    add_settings_field(
        'pdf2img_text_field_21',
        __( 'Max height (px):', 'wordpress' ),
        'pdf2img_text_field_max_height_render',
        'pluginPage',
        'pdf2img_customize_image_properties_section'
    );

    add_settings_field(
        'pdf2img_text_field_22',
        __( 'Compression Quality (1-100):', 'wordpress' ),
        'pdf2img_text_field_quality_render',
        'pluginPage',
        'pdf2img_customize_image_properties_section'
    );
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    add_settings_section(
        'pdf2img_PDF_properties_section',
        __( 'PDF properties', 'wordpress' ),
        'pdf2img_settings_section_callback',
        'pluginPage'
    );

    add_settings_field(
        'pdf2img_checkbox_field_30',
        __( 'Remove PDF after conversion', 'wordpress' ),
        'pdf2img_checkbox_field_remove_pdf_render',
        'pluginPage',
        'pdf2img_PDF_properties_section'
    );
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
}


function pdf2img_text_field_pages_to_convert_render(  ) {

    $options = get_option( 'pdf2img_settings' );
    $options['pages_to_convert'] ? $options['pages_to_convert']:1;
    if ( $options['pages_to_convert'] > 5 ) $options['pages_to_convert'] = 5;

    ?>
    <input type='text' name='pdf2img_settings[pages_to_convert]' value='<?php
        echo $options['pages_to_convert'] ? $options['pages_to_convert']:1;
    ?>'>
    <br/>
    <small>Please type value from 1 to 5. Default value is 1.</small>
    <?php

}


function pdf2img_select_field_img_extension_render(  ) {

    $options = get_option( 'pdf2img_settings' );
    ?>
    <select name='pdf2img_settings[img_extension]'>
        <option value='jpg' <?php selected( $options['img_extension'], 'jpg' ); ?>>jpg</option>
        <option value='png' <?php selected( $options['img_extension'], 'png' ); ?>>png</option>
        <option value='gif' <?php selected( $options['img_extension'], 'gif' ); ?>>gif</option>
        <option value='bmp' <?php selected( $options['img_extension'], 'bmp' ); ?>>bmp</option>
    </select>
    <br/>
    <small>Default format is "jpeg"</small>
    <?php

}

////////////////////////
function pdf2img_text_field_max_width_render(  ) {

    $options = get_option( 'pdf2img_settings' );
    ?>
    <input type='text' name='pdf2img_settings[max_width]' value='<?php echo $options['max_width'] ? $options['max_width']:1024; ?>'>
    <?php

}

function pdf2img_text_field_max_height_render(  ) {

    $options = get_option( 'pdf2img_settings' );
    ?>
    <input type='text' name='pdf2img_settings[max_height]' value='<?php echo $options['max_height'] ? $options['max_height'] : 1024; ?>'>

    <?php

}

function pdf2img_text_field_quality_render(  ) {

    $options = get_option( 'pdf2img_settings' );
    ?>
    <input type='text' name='pdf2img_settings[quality]' value='<?php echo $options['quality'] ? $options['quality'] : 80 ; ?>'>
    <br/><small>The parameter will be calculated if 0 or blank is entered.</small>
    <?php

}


/////////////////////////


function pdf2img_checkbox_field_remove_pdf_render(  ) {

    $options = get_option( 'pdf2img_settings' );
    ?>
    <input type='checkbox' name='pdf2img_settings[remove_pdf]' <?php checked( $options['remove_pdf'], 1 ); ?> value='1'>
    <br/><small>This property activate removing PDF file after conversion to IMG. Default property 'do not remove'.</small>
    <?php

}

//////////////////////////



function pdf2img_settings_section_callback(  ) {

//    echo __( 'This section1 description', 'wordpress' );

}


function pdf2img_options_page(  ) {

    ?>
    <form action='options.php' method='post'>

        <h2>PDF2IMG converter</h2>

        <?php
        settings_fields( 'pluginPage' );
        do_settings_sections( 'pluginPage' );
        submit_button();
        ?>

    </form>
    <?php
//    var_dump(get_option( 'pdf2img_settings' ));/
}

?>