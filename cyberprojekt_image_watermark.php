<?php
/*
  Plugin Name: CyberProjekt Image Watermark
  Plugin URI: http://www.cyberprojekt.pl/image-watermark/
  Description: Plugin for adding watermark to images
  Donate link:    https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=96CRM4FX29V74
  Author: Jacek Głogosz
  Version: 0.3
  Author URI: http://www.cyberprojekt.pl
  Copyright 2014  Jacek Głogosz  (email : jacek@cyberprojekt.pl)

  This program is free software; you can redistribute it and/or modify
  it under the terms of the GNU General Public License, version 2, as
  published by the Free Software Foundation.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

new Cyberprojekt_Image_Watermark();

class Cyberprojekt_Image_Watermark
{

    /**
     * adds action and filter hooks
     */
    public function __construct()
    {
        define( 'BASE_DIR', dirname( __FILE__ ) );
        define( 'BASE_URL', plugins_url( '/', __FILE__ ) );
        load_plugin_textdomain( 'cyberprojekt_iw', false, basename( BASE_DIR ) . '/lang/' );
        require_once BASE_DIR . '/libs/class-logic-data.php';
        add_action( 'admin_menu', array( $this, 'admin_menu' ) );
        add_action( 'wp_ajax_ciw_ajax_submit', array( $this, 'ajax_submit_action' ) );
        add_action( 'wp_ajax_ciw_ajax_generate_image', array( $this, 'generate_image_preview_action' ) );
        add_action( 'wp_ajax_ciw_ajax_uwi', array( $this, 'upload_watermark_image_action' ) );
        add_action( 'wp_ajax_ciw_ajax_uwf', array( $this, 'upload_font_action' ) );
        add_action( 'wp_ajax_ciw_ajax_ipm', array( $this, 'font_image_preview_action' ) );
        add_action( 'wp_ajax_ciw_ajax_ri', array( $this, 'restore_original_image_action' ) );
        add_action( 'wp_ajax_ciw_ajax_woi', array( $this, 'watermark_one_image_action' ) );
        add_action( 'wp_ajax_ciw_ajax_mass', array( $this, 'watermark_massive' ) );
        if ( $_POST['action'] != 'ciw_ajax_ri' )
            add_filter( 'wp_generate_attachment_metadata', array( $this, 'watermark_filter' ), 10, 2 );
        if ( $_POST['action'] == 'ciw_ajax_woi' )
            add_filter( 'wp_update_attachment_metadata', array( $this, 'watermark_filter' ), 10, 2 );
        add_filter( 'wp_handle_upload', array( $this, 'upload_filter' ) );
        add_filter( 'delete_attachment', array( $this, 'delete_attachment_filter' ) );
        add_action( 'attachment_submitbox_misc_actions', array( $this, 'restore_original_image_button' ) );
        add_action( 'attachment_submitbox_misc_actions', array( $this, 'watermark_one_image_button' ) );
        add_filter( 'plugin_row_meta', array( $this, 'init_row_meta' ), 10, 2 );
        add_filter( 'plugin_action_links_' .plugin_basename( __FILE__ ), array( $this , 'init_action_links' ) );
        require_once BASE_DIR . '/libs/class-model.php';
    }
    
    public function init_row_meta( $input, $file )
    {
        if ( $file != plugin_basename( __FILE__ ) ) {
            return $input;
        }
        return array_merge(
                $input, array(
            '<a href="https://www.paypal.com/cgi-bin/webscr?cmd=_s-xclick&hosted_button_id=96CRM4FX29V74" target="_blank">PayPal</a>'
                )
        );
    }
    
    public function init_action_links( $data )
    {
        if ( !current_user_can( 'manage_options' ) ) {
            return $data;
        }

        return array_merge(
                $data, array(
            sprintf(
                    '<a href="%s">%s</a>', add_query_arg(
                            array(
                'page' => 'image_watermark'
                            ), admin_url( 'options-general.php' )
                    ), __( 'Settings', 'cyberprojekt_iw' )
            )
                )
        );
    }

    /**
     * adds admin options page
     */
    function admin_menu()
    {
        add_options_page( "Image Watermark", "Image Watermark", 1, "image_watermark", array( $this, "admin_action" ) );
    }

    /**
     * main admin action
     */
    function admin_Action()
    {
        require_once BASE_DIR . '/libs/class-logic-admin-form.php';
        $form = new Logic_Admin_Form();
        wp_enqueue_style( 'cyberprojekt_image_watermark', BASE_URL . "/inc/cyberprojekt_image_watermark.css", false, null );
        wp_enqueue_script( "cyberprojekt_image_watermark", Logic_Data::get()->get_paths()->inc . "cyberprojekt_image_watermark.js" );
        if ( isset( $_POST['saveForm'] ) ) {
            if ( $form->is_valid( $_POST ) ) {
                $data = Model_Data::parse( $form->get_values() );
                Logic_Data::get()->set_data( $data );
                ?><div class="updated"><p><strong><?php _e( 'Setup saved', 'cyberprojekt_iw' ); ?></strong></p></div><?php
            }
        } else {
            //Normal page display
            $data = Logic_Data::get()->get_data();
            $form->populate( ( array ) $data );
        }
        require_once BASE_DIR . '/libs/class-view-admin.php';
        $va = new View_Admin();
        $p = Logic_Data::get()->checkPaths();
        if ( !empty( $p ) ) {
            $va->create_admin_error_page( $p );
        } else {
            $va->create_admin_page( $form );
        }
    }

    /**
     * action for ajax submit data
     */
    function ajax_submit_action()
    {
        require_once BASE_DIR . '/libs/class-logic-admin-form.php';
        $form = new Logic_Admin_Form();
        if ( $form->is_valid( $_POST ) ) {
            $data = Model_Data::parse( $form->get_values() );
            Logic_Data::get()->set_data( $data );
            $ret = array( "status" => "ok", "data" => __( "Setup saved", 'cyberprojekt_iw' ) );
        } else {
            $ret = array( "status" => "error", "data" => $form->get_errors() );
        }
        echo json_encode( $ret );
        exit();
    }

    /**
     * action for watermark image upload
     */
    function upload_watermark_image_action()
    {
        $this->upload( 'watermark' );
    }

    /**
     * action for font upload
     */
    function upload_font_action()
    {
        $this->upload( 'fonts' );
    }

    /**
     * action generates preview images for watermark
     */
    function generate_image_preview_action()
    {
        require_once BASE_DIR . '/libs/class-logic-admin-form.php';
        $form = new Logic_Admin_Form();
        if ( $form->is_valid( $_POST ) ) {
            $values = ( OBJECT ) $form->get_values();
            $tmp = wp_get_attachment_image_src( $values->preview, 'thumbnail' );
            if ( !$tmp ) {
                echo json_encode( array( "status" => "error", "data" => array( "preview" => __( "Wrong preview image", 'cyberprojekt_iw' ) ) ) );
            } else {
                $tmp = array(
                    'thumbnail' => $tmp,
                    'medium' => wp_get_attachment_image_src( $values->preview, 'medium' ),
                    'large' => wp_get_attachment_image_src( $values->preview, 'large' ),
                    'full' => wp_get_attachment_image_src( $values->preview, 'full' )
                );
                $files = array();
                try {
                    foreach ( $tmp as $key => $value ) {
                        $file = $value[0];
                        $fileToSave = Logic_Data::get()->get_paths()->images_tmp . $key . "_image_tmp.jpg";
                        $imageUrl = Logic_Data::get()->get_paths()->images_tmp_url . $key . '_image_tmp.jpg';
                        require_once BASE_DIR . '/libs/class-image-watermark.php';
                        $ic = new Image_Watermark( $values );
                        header( 'Content-Type: image/jpg' );
                        $ic->save_image( $file, $fileToSave );
                        $files[$key] = $imageUrl . '?date=' . time();
                    }
                    echo json_encode( array( "status" => "ok", "data" => $files ) );
                } catch ( Exception $e ) {
                    echo json_encode( array( "status" => "error", "data" => $e->getMessage() ) );
                }
            }
        } else {
            echo json_encode( array( "status" => "error", "data" => $form->get_errors() ) );
        }
        die();
    }

    /**
     * action generates preview file for fonts
     */
    function font_image_preview_action()
    {
        require_once BASE_DIR . '/libs/class-image-manage.php';
        $tmp = new Image_Manage();
        $tmp->preview_font_manage( $_POST['src'] );
    }

    /**
     * rotates uploaded jpg file if required
     * @param array $file Reference to a single element of $_FILES
     * @return array Reference to a single element of $_FILES
     */
    function upload_filter( $uploadedfile )
    {
        $data = Logic_Data::get()->get_data();
        if ( $data->auto && $uploadedfile['type'] == 'image/jpeg' && $data->image - rotate ) {
            $f = $uploadedfile['file'];
            $exif = @exif_read_data( $f );
            if ( $exif['Orientation'] != 1 ) {
                switch ( $exif['Orientation'] ) {
                    case 8:
                        $rotate = 90;
                        break;
                    case 3:
                        $rotate = 180;
                        break;
                    case 6:
                        $rotate = 270;
                        break;
                    default:
                        $rotate = null;
                        break;
                }
                $image = wp_get_image_editor( $f );
                if ( $rotate != null && !is_wp_error( $image ) ) {
                    $image->rotate( $rotate );
                    $image->save( $f );
                }
            }
        }
        return $uploadedfile;
    }

    /**
     * filter puts watermark on required images
     * @param array $postData
     * @param int $number
     * @return array
     */
    function watermark_filter( $postData, $number )
    {
        $data = Logic_Data::get()->get_data();
        require_once BASE_DIR . '/libs/class-image-watermark.php';
        $ic = new Image_Watermark( $data );
        $ic->parse_images( $postData, $_POST['action'] == 'ciw_ajax_woi' );
        return $postData;
    }

    /**
     * method allows uploading fonts and watermark images to server.
     * @param string $type
     */
    function upload( $type )
    {
        require_once BASE_DIR . '/libs/class-model.php';
        require_once BASE_DIR . '/libs/class-view-ajax-upload.php';
        $model = new Model_Ajax_Upload( $type );
        $tmp = new View_Ajax_Upload( $model );
        $tmp->to_string();
        exit();
    }

    function delete_attachment_filter( $postid )
    {
        if ( is_array( $file = wp_get_attachment_metadata( $postid ) ) ) {
            require_once BASE_DIR . '/libs/class-image-watermark.php';
            $iw = new Image_Watermark( null );
            $wp_upload_dir = wp_upload_dir();
            $upload_dir = $wp_upload_dir["basedir"];
            $filename = $iw->get_image_copy_name( $upload_dir . "/" . $file['file'] );
            if ( file_exists( $filename ) && !unlink( $filename ) ) {
                return new WP_Error( printf( __( "Error during file remove! '%' you will have to remove it manualy", 'cyberprojekt_iw' ), $filename ) );
            }
        }
    }

    function restore_original_image_button()
    {
        global $post;
        $data = $this->get_file_data( $post->ID );
        if ( file_exists( $data['org_file_path'] ) ) {
            wp_enqueue_script( "cyberprojekt_image_watermark", Logic_Data::get()->get_paths()->inc . "cyberprojekt_image_watermark.js" );
            $html = '<div class="misc-pub-section">';
            $html .= '<input type="button" onclick="ciw.restoreOriginalFile(' . $post->ID . ')" accesskey="r" tabindex="5" value="' . __( "Restore original file", 'cyberprojekt_iw' ) . '" class="button-primary" id="custom" name="restore_original_file">';
            $html .= '</div>';
        }
        echo $html;
    }

    public function restore_original_image_action()
    {
        try {
            $data = $this->get_file_data( ( int ) $_POST['id'] );
            if ( file_exists( $data['org_file_path'] ) ) {
                if ( rename( $data['org_file_path'], $data['file_path'] ) ) {
                    wp_generate_attachment_metadata( ( int ) $_POST['id'], $data['file_path'] );
                    $ret = array( "status" => "ok" );
                } else {
                    $ret = array( "status" => "error", "data" => __( "Error during restoring", 'cyberprojekt_iw' ) );
                }
            } else {
                $ret = array( "status" => "error", "data" => __( "Original image does not exists.", 'cyberprojekt_iw' ) );
            }
        } catch ( Exception $e ) {
            $ret = array( "status" => "error", "data" => $e->getMessage() );
        }
        echo json_encode( $ret );
        exit();
    }

    protected function get_file_data( $id )
    {
        $meta = wp_get_attachment_metadata( $id );
        $ext = preg_replace( "{.*\.}", "", $meta['file'] );
        $org_file_name = preg_replace( "{\.$ext$}", "_org.$ext", $meta['file'] );
        if ( $org_file_name == $meta['file'] ) {
            throw new Exception( __( "Original image is the same as modified", 'cyberprojekt_iw' ) );
        }
        $wp_upload_dir = wp_upload_dir();
        $upload_dir = $wp_upload_dir["basedir"];
        return array(
            "org_file_path" => $upload_dir . "/" . $org_file_name,
            "file_path" => $upload_dir . "/" . $meta['file'],
            "upload_dir" => $upload_dir,
            "org_file_name" => $org_file_name,
            "ext" => $ext,
            "meta" => $meta
        );
    }

    public function watermark_one_image_action()
    {
        try {
            $data = $this->get_file_data( ( int ) $_POST['id'] );
            wp_generate_attachment_metadata( ( int ) $_POST['id'], $data['file_path'] );
            $ret = array( "status" => "ok" );
        } catch ( Exception $e ) {
            $ret = array( "status" => "error", "data" => $e->getMessage() );
        }
        echo json_encode( $ret );
        exit();
    }

    function watermark_one_image_button()
    {
        global $post;
        $data = $this->get_file_data( $post->ID );
        wp_enqueue_script( "cyberprojekt_image_watermark", Logic_Data::get()->get_paths()->inc . "cyberprojekt_image_watermark.js" );
        $html = '<div class="misc-pub-section">';
        $html .= '<input type="button" onclick="ciw.watermarkOneImage(' . $post->ID . ')" accesskey="w" tabindex="5" value="' . __( "Watermark image", 'cyberprojekt_iw' ) . '" class="button-primary" id="custom" name="watermark_image">';
        $html .= '</div>';
        echo $html;
    }

    function watermark_massive()
    {
        require_once BASE_DIR . '/libs/class-model.php';
        try {
            $absPath = realpath( ABSPATH . "wp-content/" );
            if ( $_POST['path'] ) {
                $basedir = realpath( $absPath . $_POST['path'] );
                if ( !preg_match( "{^" . $absPath . "/plugins.*}", $basedir ) && !preg_match( "{^" . $absPath . "/uploads.*}", $basedir ) ) {
                    throw new Exception( __( "Wrong path", 'cyberprojekt_iw' ) );
                }
            } else {
                $a = wp_upload_dir();
                $basedir = $a['basedir'];
            }
            if ( $_POST['file'] ) {
                $file = realpath( $basedir . "/" . $_POST['file'] );
                if ( !preg_match( "{^" . $absPath . ".*}", $file ) ) {
                    throw new Exception( __( "Wrong file $file", 'cyberprojekt_iw' ) );
                }
                $data = Logic_Data::get()->get_data();
                require_once BASE_DIR . '/libs/class-image-watermark.php';
                $ic = new Image_Watermark( $data );
                $ic->parse_params();
                $ic->parse_image( $file );
                $ret = array( "status" => "ok", "data" => "ok" );
            } else {
                $showdir = preg_replace( "{^" . $absPath . "}", "", $basedir );
                $baseurl = get_site_url() . "/wp-content" . $showdir."/";
                $d = dir( $basedir );
                $data = array();
                while ( false !== ($entry = $d->read()) ) {
                    if ( !preg_match( "{^\..*}", $entry ) ) {
                        $isDir = is_dir( $basedir . "/" . $entry );
                        if ( !$isDir ) {
                            $isImage = preg_match( "{.*jpg$|.*jpeg$|.*gif$|.*png$}", strtolower( $entry ) );
                        }
                        if ( $isDir || $isImage )
                            $data[] = new Model_Massive_File( $entry, $isDir,$baseurl );
                    }
                }
                $d->close();

                $ret = array( "status" => "ok", "data" => array(
                        'path' => $showdir,
                        'url' => $baseurl,
                        'contents' => $data
                    ) );
            }
        } catch ( Exception $e ) {
            $ret = array( "status" => "error", "data" => $e->getMessage() );
        }
        echo json_encode( $ret );
        exit();
    }

}
