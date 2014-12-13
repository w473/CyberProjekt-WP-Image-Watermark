<?php

/**
 * Description of Logic_Admin_Form
 *
 * @author Jacek Glogosz <jacek@cyberprojekt.pl>
 */
class Logic_Admin_Form
{

    protected $form_elements = array();
    protected $errors = array();

    public function __construct()
    {
        require_once BASE_DIR . '/libs/class-view-form-elements.php';
        $this->init();
    }

    protected function init()
    {
        $formElements = array(
            new Checkbox( "auto", array( "value" => "auto", "label" => __( "Automatic watermark", 'cyberprojekt_iw' ) ) ),
            new Checkbox( "preserve_original", array( "value" => "preserve_original", "label" => __( "Preserve Original Image", 'cyberprojekt_iw' ) ) ),
            new Radio( "type", array( "options" => array(
                    1 => __( "Text and image", 'cyberprojekt_iw' ),
                    2 => __( "Text only", 'cyberprojekt_iw' ),
                    3 => __( "Image only", 'cyberprojekt_iw' ) )
                , "required" => true, "label" => __( "Watermark type", 'cyberprojekt_iw' ), "description" => __( "Select a watermark type", 'cyberprojekt_iw' ) ) ),
            new Radio( "thumb_size_type", array( "options" => Logic_Data::get()->get_sizes_type(), "label" => __( "Image sizes type", 'cyberprojekt_iw' ), "description" => __( "Set type of size measure to enable watermarks", 'cyberprojekt_iw' ) ) ),
            new Checkbox( "thumb", array( "options" => Logic_Data::get()->get_image_sizes(), "multi" => true, "label" => __( "Image sizes", 'cyberprojekt_iw' ), "description" => __( "Enable watermarks for the selected image sizes", 'cyberprojekt_iw' ) ) ),
            new Text( "thumb_min_w", array( "label" => __( "Images min width", 'cyberprojekt_iw' ),  "validators" => array( "int" => array( "min" => 0) ) ) ),
            new Text( "thumb_min_h", array( "label" => __( "Images min height", 'cyberprojekt_iw' ),  "validators" => array( "int" => array( "min" => 0) ) ) ),
            new Text( "thumb_max_w", array( "label" => __( "Images max width", 'cyberprojekt_iw' ),  "validators" => array( "int" => array( "min" => 0) ) ) ),
            new Text( "thumb_max_h", array( "label" => __( "Images max height", 'cyberprojekt_iw' ),  "validators" => array( "int" => array( "min" => 0) ) ) ),
            new Checkbox( "image_type", array( "options" => Logic_Data::get()->get_image_types(), "multi" => true, "label" => __( "Image types", 'cyberprojekt_iw' ) ) ),
            new Checkbox( "image_rotate", array( "value" => 'rotate', "label" => __( "Enable automatic rotate of images by exif data (only for JPG!)", 'cyberprojekt_iw' ) ) ),
            new Text( "quality", array( "label" => __( "Jpg image quality", 'cyberprojekt_iw' ), "description" => __( "Jpg image quality", 'cyberprojekt_iw' ), "validators" => array( "int" => array( "min" => 0, "max" => 100 ) ) ) ),
            new Select( "text_font", array( "options" => Logic_Data::get()->get_fonts(), "label" => __( "Text font", 'cyberprojekt_iw' ) ) ),
            new Text( "text_text", array( "label" => __( "Watermark text", 'cyberprojekt_iw' ), "description" => __( "Configure the text", 'cyberprojekt_iw' ), "validators" => array( "length" => array( "min" => 1, "max" => 100 ) ) ) ),
            new Text( "text_width", array( "label" => __( "Text width", 'cyberprojekt_iw' ), "description" => __( "Configure the watermark text width (percentage)", 'cyberprojekt_iw' ), "validators" => array( "int" => array( "min" => 1, "max" => 100 ) ) ) ),
            new Text( "text_color", array( "label" => __( "Text color", 'cyberprojekt_iw' ), "description" => __( "Configure the watermark text color (FFFFFF is white)", 'cyberprojekt_iw' ), "validators" => array( "color" ) ) ),
            new Text( "text_transparency", array( "label" => __( "Text transparency", 'cyberprojekt_iw' ), "description" => __( "Configure the watermark text transparency (percentage)", 'cyberprojekt_iw' ), "validators" => array( "int" => array( "min" => 0, "max" => 100 ) ) ) ),
            new Text( "text_v_pos", array( "label" => __( "Text vertical position", 'cyberprojekt_iw' ), "description" => __( "Vertical position adjustment", 'cyberprojekt_iw' ), "validators" => array( "int" => array( "min" => 1, "max" => 100 ) ) ) ),
            new Text( "text_h_pos", array( "label" => __( "Text horizontal position", 'cyberprojekt_iw' ), "description" => __( "Horizontal position adjustment.", 'cyberprojekt_iw' ), "validators" => array( "int" => array( "min" => 1, "max" => 100 ) ) ) ),
            new Text( "text_rotate", array( "label" => __( "Text rotate angle", 'cyberprojekt_iw' ), "description" => __( "Text rotate angle.", 'cyberprojekt_iw' ), "validators" => array( "int" => array( "min" => 0, "max" => 359 ) ) ) ),
            new Select( "image_url", array( "options" => Logic_Data::get()->get_watermark_images(), "label" => __( "Image URL", 'cyberprojekt_iw' ), "description" => __( "Choose the watermark image.", 'cyberprojekt_iw' ), "validators" => array(
                    "fileExists" => array( "dir" => Logic_Data::get()->get_paths()->watermark ) ) ) ),
            new Text( "image_width", array( "label" => __( "Image width", 'cyberprojekt_iw' ), "description" => __( "Configure the watermark image width (percentage)", 'cyberprojekt_iw' ), "validators" => array( "int" => array( "min" => 1, "max" => 100 ) ) ) ),
            new Text( "image_v_pos", array( "label" => __( "Image vertical position", 'cyberprojekt_iw' ), "description" => __( "Vertical position adjustment", 'cyberprojekt_iw' ), "validators" => array( "int" => array( "min" => 1, "max" => 100 ) ) ) ),
            new Text( "image_h_pos", array( "label" => __( "Image horizontal position", 'cyberprojekt_iw' ), "description" => __( "Horizontal position adjustment.", 'cyberprojekt_iw' ), "validators" => array( "int" => array( "min" => 1, "max" => 100 ) ) ) ),
            new Hidden( "preview", array() ),
//        new Text("image_transparency", array("label" => __("Image transparency (VERY SLOW!),'cyberprojekt_iw')", "validators" => array("int" => array("min" => 0, "max" => 100)))),
        );
        $this->add_form_elements( $formElements );
    }

    protected function add_form_elements( $array )
    {
        foreach ( $array as $key => $value ) {
            if ( $value instanceof Abstract_Element ) {
                $this->form_elements[$value->get_name()] = $value;
            } else {
                throw new Exception( printf( __( "Element %s is not subclass of AbstractElement", 'cyberprojekt_iw' ), $key ) );
            }
        }
    }

    public function is_valid( $data )
    {
        $valid = true;
        if ( in_array( $data['type'], array( 1, 3 ) ) ) {
            $this->get_field( "image_url" )->set_required( true );
        }
        if ( in_array( $data['type'], array( 1, 2 ) ) ) {
            $this->get_field( "text_text" )->set_required( true );
        }
        foreach ( $this->form_elements as $value ) {
            $val = isset( $data[$value->get_name()] ) ? $data[$value->get_name()] : null;
            $v = $value->is_valid( $val );
            $valid = $v && $valid;
            if ( !$v ) {
                $this->errors[$value->get_name()] = $value->get_errors();
            }
        }
        return $valid;
    }

    public function populate( $data )
    {
        foreach ( $data as $key => $value ) {
            if ( array_key_exists( $key, $this->form_elements ) ) {
                $this->form_elements[$key]->set_value( $value );
            }
        }
    }

    public function get_values()
    {
        $ret = array();
        foreach ( $this->form_elements as $value ) {
            $ret[$value->get_name()] = $value->get_value();
        }
        return $ret;
    }

    public function get_errors()
    {
        return $this->errors;
    }

    /**
     * 
     * @param string $name
     * @return Abstract_Element
     */
    public function get_field( $name )
    {
        if ( array_key_exists( $name, $this->form_elements ) ) {
            return $this->form_elements[$name];
        }
        throw new Exception( printf( __( "Form element by name %s does not exists!", 'cyberprojekt_iw' ), $name ) );
    }

}
