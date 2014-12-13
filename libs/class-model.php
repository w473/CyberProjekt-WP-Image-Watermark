<?php

class Model_Data
{

    /**
     * automatic watermarking
     * @var boolean
     */
    public $auto = true;

    /**
     * flag indicating if you want to preserve (copy) orginal full size image
     * @var boolean
     */
    public $preserve_original = false;

    /**
     * type of watermarking 1- both, 2 - text, 3 - image
     * @var int
     */
    public $type = 2;

    /**
     * 1 - by type of image
     * 2 - by size of image
     * @var int
     */
    public $thumb_size_type = 1;

    /**
     * sizes to watermark
     * @var array
     */
    public $thumb = array( 2, 3, 4 );

    /**
     * minimal width of file to put watermark
     * @var int
     */
    public $thumb_min_w = 0;

    /**
     * minimal height of file to put watermark
     * @var int
     */
    public $thumb_min_h = 0;

    /**
     * maximal width of file to put watermark
     * @var int 
     */
    public $thumb_max_w = 0;

    /**
     * maximal height of file to put watermark
     * @var int
     */
    public $thumb_max_h = 0;

    /**
     * images types to put watermark on 1 - JPG/.JPEG, 2-.PNG, 3-.GIF
     * @var array
     */
    public $image_type = array( 1, 2, 3 );

    /**
     * force rotate jpg
     * @var boolean
     */
    public $image_rotate = false;

    /**
     * used font
     * @var string
     */
    public $text_font = "Arial.ttf";

    /**
     * text for watermark
     * @var string
     */
    public $text_text = 'watermark';

    /**
     * text width in %
     * @var int
     */
    public $text_width = 20;

    /**
     * text color
     * @var string
     */
    public $text_color = '#000000';

    /**
     * text transparency
     * @var int
     */
    public $text_transparency = 60;

    /**
     * text rotate angle
     * @var int
     */
    public $text_rotate = 0;

    /**
     * text vertival position in %
     * @var int
     */
    public $text_v_pos = 100;

    /**
     * text horizontal position in %
     * @var int
     */
    public $text_h_pos = 100;

    /**
     * url of watermark image
     * @var string
     */
    public $image_url = null;

    /**
     * width of watermark image in %
     * @var int
     */
    public $image_width = 20;

    /**
     * watermark image vertical posion in %
     * @var int
     */
    public $image_v_pos = 100;

    /**
     * watermark image horizontal position in %
     * @var int
     */
    public $image_h_pos = 100;

    /**
     * id of image in media library to preview watermark
     * @var int
     */
    public $preview = null;

    /**
     * quality of saved image
     * @var int
     */
    public $quality = 75;

    /**
     * 
     * @param array $data
     * @return \Model_Data
     */
    public static function parse( $data )
    {
        $tmp = new self();
        $objVars = array_keys( $data );
        foreach ( $objVars as $key ) {
            $tmp->$key = $data[$key];
        }
        return $tmp;
    }

}

/**
 * @author Jacek Glogosz <jacek@cyberprojekt.pl>
 */
class Model_Ajax_Upload
{

    /**
     * url to folder with images
     * @var string
     */
    public $wed_dir;

    /**
     * path to dir with images
     * @var string
     */
    public $dir;

    /**
     * extensions
     * @var string
     */
    public $ext;

    /**
     * html main id
     * @var string
     */
    public $id;

    /**
     * action to execute
     * @var string
     */
    public $action;

    /**
     * allowed file types 
     * @var string
     */
    public $allowed_types;

    /**
     * post data
     * @var array
     */
    public $post;

    /**
     * post file data
     * @var array
     */
    public $files;

    /**
     * type of model 
     * @var string
     */
    public $type;

    /**
     * creates instance of Model object with prepared required params
     * @param string $type
     * @throws Exception
     */
    public function __construct( $type )
    {
        $this->type = $type;
        switch ( $type ) {
            case "watermark":
//                $this->divId = "ciw_ajax_upload_watermark";
                $this->wed_dir = Logic_Data::get()->get_paths()->watermark_url;
                $this->dir = Logic_Data::get()->get_paths()->watermark;
                $this->ext = "png|gif";
                $this->id = "image_url";
                $this->action = "ciw_ajax_uwi";
                $this->allowed_types = array( 'image/png', 'image/gif' );
                break;
            case "fonts":
//                $this->divId = "ciw_ajax_upload_fonts";
                $this->wed_dir = Logic_Data::get()->get_paths()->fonts_url;
                $this->dir = Logic_Data::get()->get_paths()->fonts;
                $this->ext = "ttf";
                $this->id = "text_font";
                $this->action = "ciw_ajax_uwf";
                $this->allowed_types = array( 'application/x-font-ttf' );
                break;
            default:
                throw new Exception( __( "Wrong execution!", 'cyberprojekt_iw' ) );
        }
        $this->post = $_POST;
        $this->files = $_FILES;
    }

}

class Model_Massive_File
{

    public $is_dir = false;
    public $name = "";
    public $url = "";

    public function __construct( $name, $is_dir,$baseurl )
    {
        $this->name = $name;
        $this->is_dir = $is_dir;
        $this->url = $baseurl.$this->name;
    }

}
