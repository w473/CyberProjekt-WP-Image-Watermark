<?php

/**
 * Description of Logic_Data
 *
 * @author jacek
 */
class Logic_Data
{

    /**
     *
     * @var Logic_Data
     */
    private static $instance;
    private $image_sizes;
    private $image_types;
    private $image_mime_types;
    private $fonts;
    private $watermark_images;

    private function __construct()
    {
        ;
    }

    /**
     * 
     * @return Logic_Data
     */
    public static function get()
    {
        if ( !self::$instance ) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * returns previously saved setup
     * @return Model_Data
     */
    public function get_data()
    {
        $data = json_decode( get_option( 'cyberprojekt-iw' ), false );
        $ret = new Model_Data();
        if ( !($data === NULL || (!is_object( $data ))) ) {
            foreach ($data AS $key => $value){
                $ret->{$key} = $value;
            }
        }
        return $ret;
    }

    /**
     * saves setup in db
     * @param Model_Data $data
     */
    public function set_data( Model_Data $data )
    {
        update_option( 'cyberprojekt-iw', json_encode( $data ) );
    }

    /**
     *
     * @var Paths
     */
    private $paths;

    public function get_image_sizes()
    {
        if ( !$this->image_sizes ) {
            $this->image_sizes = get_intermediate_image_sizes();
            $this->image_sizes[] = 'Full';
        }
        return $this->image_sizes;
    }

    public function get_sizes_type()
    {
        if ( !$this->image_sizes_type ) {
            $this->image_sizes_type = array(
                1 => "type of image",
                2 => "size of image"
            );
        }
        return $this->image_sizes_type;
    }
    
    public function get_image_mime_types()
    {
        if ( !$this->image_mime_types ) {
            $this->image_mime_types = array(
                1 => "image/jpeg",
                2 => "image/png",
                3 => "image/gif",
            );
        }
        return $this->image_mime_types;
    }

    public function get_image_types()
    {
        if ( !$this->image_types ) {
            $this->image_types = array(
                1 => ".JPG/.JPEG",
                2 => ".PNG",
                3 => ".GIF"
            );
        }
        return $this->image_types;
    }

    public function get_fonts()
    {
        if ( !$this->fonts ) {
            $this->fonts = $this->get_file_from_folder_with_extension( $this->get_paths()->fonts, "ttf" );
        }
        return $this->fonts;
    }

    public function get_watermark_images()
    {
        if ( !$this->watermark_images ) {
            $this->watermark_images = $this->get_file_from_folder_with_extension( $this->get_paths()->watermark, "png|gif" );
        }
        return $this->watermark_images;
    }

    /**
     * 
     * @return Paths
     */
    public function get_paths()
    {
        if ( !$this->paths ) {
            $this->paths = new Paths();
        }
        return $this->paths;
    }

    public function get_file_from_folder_with_extension( $dir, $ext, $withPreview = false )
    {
        $dirContents = scandir( $dir );
        $files = array();
        $match = "{.*\." . preg_replace( "{\|}", "|.*\.", $ext ) . "}";
        $replace = "{\." . preg_replace( "{\|}", "|\.", $ext ) . "}";
        foreach ( $dirContents as $value ) {
            $file = $dir . "/" . $value;
            if ( is_file( $file ) && preg_match( $match, $value ) && is_readable( $file ) ) {
                if ( $withPreview ) {
                    $files[$value] = array(
                        "element" => preg_replace( $replace, "", preg_replace( "{_}", " ", $value ) ),
                        "preview" => $this->is_image( $ext ) ? $value : preg_replace( "{\.$ext}", ".jpg", $value )
                    );
                } else {
                    $files[$value] = preg_replace( $replace, "", preg_replace( "{_}", " ", $value ) );
                }
            }
        }
        return $files;
    }

    public function is_image( $ext )
    {
//do rozbudowy kiedyś...
        if ( $ext == 'ttf' ) {
            return false;
        }
        return true;
    }

    /**
     * returns non writable path.
     * @return array
     */
    public function checkPaths()
    {
        $path = $this->get_paths();
        $pathWritAble = array(
            $path->images_tmp,
            $path->watermark,
            $path->fonts
        );
        foreach ( $pathWritAble as $key => $value ) {
            if ( is_writable( $value ) ) {
                unset( $pathWritAble[$key] );
            }
        }
        return $pathWritAble;
    }

}

class Paths
{

    /**
     * include path
     * @var string
     */
    public $inc;

    /**
     * path to preview images
     * @var string
     */
    public $images_preview;

    /**
     * path to tempoaraty images
     * @var string 
     */
    public $images_tmp;

    /**
     * url of temporary images 
     * @var string
     */
    public $images_tmp_url;

    /**
     * path to url folder
     * @var string
     */
    public $fonts;

    /**
     * fonts url
     * @var string
     */
    public $fonts_url;

    /**
     * path to watermark images
     * @var string
     */
    public $watermark;

    /**
     * url of watermark images
     * @var string
     */
    public $watermark_url;

    public function __construct()
    {
        $this->inc = BASE_URL . "inc/";
        $this->images_preview = BASE_DIR . "/images/preview/";
        $this->images_tmp = BASE_DIR . "/tmp/";
        $this->fonts = BASE_DIR . "/fonts/";
        $this->images_tmp_url = BASE_URL . "tmp/";
        $this->fonts_url = BASE_URL . "fonts/";
        $this->watermark_url = BASE_URL . "images/";
        $this->watermark = BASE_DIR . "/images/";
    }

}
