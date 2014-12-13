<?php

/**
 * @author Jacek Glogosz <jacek@cyberprojekt.pl>
 */
class View_Admin
{

    /**
     * 
     * @param Model_Ajax_Upload $model
     */
    function create_manage_fields( $model )
    {
        ?>
        <div id="<?php echo $model->id; ?>-manage" style="display:none;">
            <p>place files here: <?php echo $model->dir; ?> or upload here:</p>
            <form>
                <input type="file" name="<?php echo $model->id; ?>-fileupload" id="<?php echo $model->id; ?>-fileupload" class="fileUpload" />
            </form>
            <div id="<?php echo $model->id; ?>-fileupload-display"></div>
            <div id="<?php echo $model->id; ?>-manage-fields"></div>
        </div>
        <script>
            jQuery(document).ready(function() {
                ciw.addUploader("<?php echo $model->id; ?>", '<?php echo $model->action; ?>', '<?php echo implode( ",", $model->allowed_types ); ?>', '<?php echo $model->type; ?>');
            });
        </script>
        <?php
    }

    public function create_admin_error_page( $paths )
    {
        echo '<div>' . __( 'This paths must be writable', 'cyberprojekt_iw' ) . '</div>';
        foreach ( $paths as $path ) {
            echo "<div>$path</div>";
        }
    }

    /**
     * 
     * @param Logic_Admin_Form $form
     */
    function create_admin_page( $form )
    {
        wp_enqueue_style( 'jquery-ui-css', 'http://ajax.googleapis.com/ajax/libs/jqueryui/' . $GLOBALS['wp_scripts']->registered['jquery-ui-core']->ver . '/themes/smoothness/jquery-ui.css', false );
        wp_enqueue_style( 'wp-jquery-ui-dialog' );
        wp_enqueue_script( "jquery-ui-accordion" );
        wp_enqueue_script( "jquery-ui-dialog" );
        wp_enqueue_script( "jquery-ui-tabs" );
        wp_enqueue_script( "jquery-ui-tooltip" );
        wp_enqueue_media();
        $uploadParams = array(
            "watermark" => new Model_Ajax_Upload( "watermark" ),
            "fonts" => new Model_Ajax_Upload( "fonts" )
        );
        ?>
        <div class="wrap">
        <?php $url = admin_url( 'options-general.php?page=image_watermark' ); ?>
            <script>
                jQuery(function() {
                    ciw.urlwm = "<?php echo Logic_Data::get()->get_paths()->watermark_url; ?>";
                    ciw.urlfonts = "<?php echo Logic_Data::get()->get_paths()->fonts_url; ?>";
                    ciw.includeUrl = "<?php echo Logic_Data::get()->get_paths()->inc; ?>";
                    ciw.uploadParams = <?php echo json_encode( $uploadParams ); ?>;
                    ciw.texts.errorWrongType = "<?php _e( 'Wrong type, allowed: ', 'cyberprojekt_iw' ) ?>";
                    ciw.texts.errorWrongSize = "<?php _e( 'Wrong size, max allowed: ', 'cyberprojekt_iw' ) ?>";
                    ciw.texts.errorOther = "<?php _e( 'Other error: ', 'cyberprojekt_iw' ) ?>";
                    ciw.texts.noErrors = "<?php _e( 'No errors', 'cyberprojekt_iw' ) ?>";
                    ciw.texts.confirmRemove = "<?php _e( 'Are you sure you want to delete this file ', 'cyberprojekt_iw' ) ?>";
                    ciw.texts.validationError = "<?php _e( 'Validation error', 'cyberprojekt_iw' ) ?>";
                    ciw.texts.popupTitle = "<?php _e( 'Choose file for preview', 'cyberprojekt_iw' ) ?>";
                    ciw.texts.popupButton = "<?php _e( 'Choose', 'cyberprojekt_iw' ) ?>";
                    ciw.texts.addWatermark = "<?php _e( 'Add Watermark', 'cyberprojekt_iw' ) ?>";
                    jQuery(function() {
                        jQuery( "#tabs" ).tabs({
                            activate: function( event, ui ) {
                                if( 2 == ui.newTab.context.hash.replace("#tabs-","") ){
                                    ciw.massive();
                                }
                            }
                        });
                        jQuery("#tabswm").accordion();
                    });
                    jQuery('#text_color').minicolors({theme: 'none'});
                    ciw.iwRefresh();
                    jQuery('#image_url,#text_font').change(function() {
                        ciw.imageUrlShow(this);
                    });
                    jQuery('#image_url,#text_font').trigger('change');
                    jQuery('#iw_form').submit(function() {
                        jQuery.post(ajaxurl, 'action=ciw_ajax_submit&' + jQuery('#iw_form').serialize(), function(data) {
                            jQuery('.error').remove();
                            if (data.status == 'ok') {
                                ciw.alert('ok',data.data);
                            } else {
                                ciw.alert('error',"<?php _e( 'Validation error', 'cyberprojekt_iw' ) ?>");
                                ciw.putValidationErrors(data.data);
                            }
                        }, "json");
                        return false;
                    });
                    jQuery("[name='thumb_size_type']").change(function() {
                        if (jQuery("[name='thumb_size_type']:checked").val() == 1) {
                            jQuery(".thumb_sizes_ver").show();
                            jQuery(".thumb_sizes_px").hide();
                        } else {
                            jQuery(".thumb_sizes_ver").hide();
                            jQuery(".thumb_sizes_px").show();
                        }
                    });
                    jQuery("[name='thumb_size_type']").trigger('change');
                });
            </script>
            <div style="width:100%">
                <div style="width:50%;float:left;vertical-align:middle;"><?php echo "<h2>" . __( 'Image Watermark', 'cyberprojekt_iw' ) . "</h2>"; ?></div>
                <div class="top-belt-right">
                    <a href="http://www.cyberprojekt.pl" >
                        <img style="vertical-align:middle;" src="<?php echo Logic_Data::get()->get_paths()->inc; ?>/home.png" title="CYBERPROJEKT" alt="CYBERPROJEKT"/></a>
                    <a href="http://www.cyberprojekt.pl/image-watermark/" >
                        <img style="vertical-align:middle;" src="<?php echo Logic_Data::get()->get_paths()->inc; ?>/browser.png" title="<?php _e( 'PLUGIN HOME PAGE', 'cyberprojekt_iw' ) ?>" alt="PLUGIN HOME PAGE" /></a>
                    <a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=96CRM4FX29V74" title="<?php _e( 'DONATE', 'cyberprojekt_iw' ) ?>" alt="<?php _e( 'DONATE', 'cyberprojekt_iw' ) ?>">
                        <img style="vertical-align:middle;" src="<?php echo Logic_Data::get()->get_paths()->inc; ?>/credit_card_paypal_gold.png" /></a>
                </div>
            </div>
            <div id="tabs">
                <ul>
                    <li><a href="#tabs-1"><?php _e( 'Setup', 'cyberprojekt_iw' ) ?></a></li>
                    <li><a href="#tabs-2"><?php _e( 'Choose Files', 'cyberprojekt_iw' ) ?></a></li>

                </ul>
                <div id="tabs-1">
                    <table style="width:100%;">
                        <tr>
                            <td style="vertical-align: top;width:50%;">
                                <form id="iw_form" name="iw_form" method="post" action="<?php echo str_replace( '%7E', '~', $_SERVER['REQUEST_URI'] ); ?>">
                                    <input type="hidden" name="cp_iw_hidden" value="Y">
                                    <div id="tabswm">
                                        <h3><?php _e( "Main", 'cyberprojekt_iw' ) ?></h3>
                                        <div id="tabswm-3">
                                            <?php echo $form->get_field( 'auto' ) ?><br />
                                                <?php echo $form->get_field( 'preserve_original' ) ?><br />
                                                <?php echo $form->get_field( 'type' ) ?><br />
                                                <?php echo $form->get_field( 'thumb_size_type' ) ?><br />
                                                <span class='thumb_sizes_ver'>
                                                    <?php echo $form->get_field( 'thumb' ) ?>
                                                </span>
                                                <span class='thumb_sizes_px'>
                                                    <?php echo $form->get_field( 'thumb_min_w' ) ?>
                                                    <?php echo $form->get_field( 'thumb_min_h' ) ?>
                                                    <?php echo $form->get_field( 'thumb_max_w' ) ?>
                                                    <?php echo $form->get_field( 'thumb_max_h' ) ?>
                                                </span><br />
                                                <?php echo $form->get_field( 'image_type' ) ?><br />
                                                <?php echo $form->get_field( 'image_rotate' ) ?><br />
                                                <?php echo $form->get_field( 'preview' ) ?>
                                                <?php echo $form->get_field( 'quality' ) ?>
                                        </div>
                                        <h3><?php _e( "Text", 'cyberprojekt_iw' ) ?></h3>
                                        <div id="tabswm-1">
                                            <table>
                                                <tr><td><?php echo $form->get_field( 'text_text' ) ?></td></tr>
                                                <tr><td><?php echo $form->get_field( "text_font" ) ?><span><a href="javascript:ciw.iwManage('fonts');">
                                                    <img src='<?php echo Logic_Data::get()->get_paths()->inc; ?>/lin_agt_wrench.png' title='<?php _e( 'Manage', 'cyberprojekt_iw' ) ?>'/></a></span></td></tr>
                                                <tr><td><?php echo $form->get_field( 'text_width' ) ?></td></tr>
                                                <tr><td><?php echo $form->get_field( 'text_color' ) ?></td></tr>
                                                <tr><td><?php echo $form->get_field( 'text_transparency' ) ?></td></tr>
                                                <tr><td><?php echo $form->get_field( 'text_v_pos' ) ?></td></tr>
                                                <tr><td><?php echo $form->get_field( 'text_h_pos' ) ?></td></tr>
                                                <tr><td><?php echo $form->get_field( 'text_rotate' ) ?></td></tr>
                                            </table>
                                        </div>
                                        <h3><?php _e( "Image", 'cyberprojekt_iw' ) ?></h3>
                                        <div id="tabswm-2">
                                            <table>
                                                <tr><td><?php echo $form->get_field( 'image_url' ) ?><span><a href="javascript:ciw.iwManage('watermark');">
                                                            <img src='<?php echo Logic_Data::get()->get_paths()->inc; ?>/lin_agt_wrench.png' title='<?php _e( 'Manage', 'cyberprojekt_iw' ) ?>'/></a></span></td></tr>
                                                <tr><td><?php echo $form->get_field( 'image_width' ) ?></td></tr>
                                                <tr><td><?php echo $form->get_field( 'image_v_pos' ) ?></td></tr>
                                                <tr><td><?php echo $form->get_field( 'image_h_pos' ) ?></td></tr>
                                            </table>
                                        </div>
                                    </div>

                                    <p class="submit">
                                        <input type="image" src="<?php echo Logic_Data::get()->get_paths()->inc; ?>/save.png" name="saveForm" title="<?php _e( 'Update setup', 'cyberprojekt_iw' ) ?>" alt="<?php _e( 'Update setup', 'cyberprojekt_iw' ) ?>" />
                                    </p>
                                </form>
                                <?php
                                $this->create_manage_fields( $uploadParams['watermark'] );
                                $this->create_manage_fields( $uploadParams['fonts'] );
                                ?>
                            </td>
                            <td style="vertical-align: top;width:50%;">
                                <div><a href="javascript:ciw.iwRefresh();"><img src='<?php echo Logic_Data::get()->get_paths()->inc; ?>/reload.png' title='<?php _e( 'Refresh', 'cyberprojekt_iw' ) ?>'/></a> 
                                    <a href="javascript:ciw.previewImageChoose();"><img src='<?php echo Logic_Data::get()->get_paths()->inc; ?>/max.png' title='<?php _e( 'Choose preview image', 'cyberprojekt_iw' ) ?>'/></a>
                                    <div id="preview-manage" style="display: none;"></div>
                                    <div id="preview-image"></div>
                                </div>
                            </td>
                        </tr>
                    </table>               
                </div>
                <div id="tabs-2">
                    <div class="wrap">
                        <div class='massive_folder_icons'>
                            <div class='massive_folder_icon'>
                                <a href="javascript:ciw.massive('/uploads')">
                                    <img src='<?php echo Logic_Data::get()->get_paths()->inc; ?>/export.png' /><br />
                                    <?php _e( 'Wordpress Upload Dir', 'cyberprojekt_iw' ) ?>
                                </a>
                            </div>
                            <div class='massive_folder_icon'>
                                <a href="javascript:ciw.massive('/plugins')">
                                    <img src='<?php echo Logic_Data::get()->get_paths()->inc; ?>/plugin.png' /><br />
                                    <?php _e( 'Wordpress Plugins Directory', 'cyberprojekt_iw' ) ?>
                                </a>
                            </div>
                        </div>
                        <div class='massive_folder_view'>
                            <?php _e( 'Path:', 'cyberprojekt_iw' ) ?> <span id='mass_actual_path'></span><br><br>
                            <div id='mass_dir'>

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }

}
