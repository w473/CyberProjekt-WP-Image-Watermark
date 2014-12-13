<?php

/**
 * Description of imageWaterMark
 *
 * @author Jacek Glogosz <jacek@cyberprojekt.pl>
 */
class Image_Watermark
{

    /**
     * watermark params extracted from plugin setup
     * @var array
     */
    protected $water_mark_params;

    /**
     *
     * @var int
     */
    protected $base_font_size = 72;

    /**
     * plugin setup
     * @var Model_Data
     */
    protected $data;

    /**
     * 
     * @param Model_Data $data plugin setup
     */
    public function __construct( $data )
    {
        $this->data = $data;
    }

    /**
     * extracts watermark params from plugin setup
     */
    public function parse_params()
    {
        if ( $this->water_mark_params == null ) {
            $this->water_mark_params = array(
                "global" => array(
                    "type" => $this->data->type,
                    "thumb" => $this->data->thumb,
                    "image_type" => $this->data->image_type,
                    "image_rotate" => $this->data->image_rotate,
                    "quality" => $this->data->quality
                ),
                "text" => $this->parse_text_params( $this->data ),
                "image" => $this->parse_image_params( $this->data ),
            );
        }
    }

    /**
     * parses watermark text data
     * @param Model_Data $data plugin setup
     * @return array
     */
    protected function parse_text_params( $data )
    {
        if ( in_array( $data->type, array( 1, 2 ) ) ) {
            $data->text_color = preg_replace( "{#}", "", $data->text_color );
            $tmp = array(
                "fontFile" => Logic_Data::get()->get_paths()->fonts . $data->text_font,
                "text" => $data->text_text,
                "x" => $data->text_v_pos,
                "y" => $data->text_h_pos,
                "color" => array(
                    "main" => hexdec( $data->text_color ),
                    "r" => hexdec( substr( $data->text_color, 0, 2 ) ),
                    "g" => hexdec( substr( $data->text_color, 2, 2 ) ),
                    "b" => hexdec( substr( $data->text_color, 4, 2 ) )
                ),
                "scale" => $data->text_width / 100,
                "transparency" => ( int ) (($data->text_transparency / 100) * 127),
                "baseFontSize" => $this->base_font_size,
                "angle" => $data->text_rotate
            );
            $box = imagettfbbox( $this->base_font_size, 0, $tmp['fontFile'], $tmp['text'] );
            $tmp["fontBoxDefaultSize"] = array(
                "width" => ($box[4] - $box[0]),
                "height" => ($box[1] - $box[7])
            );
            return $tmp;
        }
    }

    /**
     * parses watermark image data
     * @param Model_Data $data
     * @return array
     * @throws Exception
     */
    protected function parse_image_params( $data )
    {
        if ( in_array( $data->type, array( 1, 3 ) ) ) {
            $tmp = array(
                "url" => Logic_Data::get()->get_paths()->watermark . $data->image_url,
                "width" => $data->image_width / 100,
                "x" => $data->image_v_pos / 100,
                "y" => $data->image_h_pos / 100,
                "opacity" => ( int ) $data->image_transparency,
            );
            $gis = getimagesize( $tmp['url'] );
            list($tmp['image_width'], $tmp['image_height']) = $gis;
            switch ( $gis['mime'] ) {
                case 'image/gif':
                    $tmp['image'] = $this->apply_opacity_to_image( imagecreatefromgif( $tmp['url'] ), $tmp['opacity'] );
                    break;
                case 'image/png':
                    $tmp['image'] = $this->apply_opacity_to_image( imagecreatefrompng( $tmp['url'] ), $tmp['opacity'] );
                    break;
                default:
                    throw new Exception( __( "Wrong mime", 'cyberprojekt_iw' ) );
            }
            return $tmp;
        }
    }

    /**
     * 
     * @param resource $img
     * @param int $opacity
     * @return resource an image resource identifier on success, <b>FALSE</b> on errors.
     */
    protected function apply_opacity_to_image( $img, $opacity )
    {
        if ( !isset( $opacity ) || $opacity == 0 ) {
            return $img;
        }
        $opacity /= 100;

        //get image width and height
        $w = imagesx( $img );
        $h = imagesy( $img );

        //turn alpha blending off
        imagealphablending( $img, false );

        //find the most opaque pixel in the image (the one with the smallest alpha value)
        $minalpha = 127;
        for ( $x = 0; $x < $w; $x++ )
            for ( $y = 0; $y < $h; $y++ ) {
                $alpha = ( imagecolorat( $img, $x, $y ) >> 24 ) & 0xFF;
                if ( $alpha < $minalpha ) {
                    $minalpha = $alpha;
                }
            }
        //loop through image pixels and modify alpha for each
        for ( $x = 0; $x < $w; $x++ ) {
            for ( $y = 0; $y < $h; $y++ ) {
                //get current alpha value (represents the TANSPARENCY!)
                $colorxy = imagecolorat( $img, $x, $y );
                $alpha = ( $colorxy >> 24 ) & 0xFF;
                //calculate new alpha
                if ( $minalpha !== 127 ) {
                    $alpha = 127 + 127 * $opacity * ( $alpha - 127 ) / ( 127 - $minalpha );
                } else {
                    $alpha += 127 * $opacity;
                }
                //get the color index with new alpha
                $alphacolorxy = imagecolorallocatealpha( $img, ( $colorxy >> 16 ) & 0xFF, ( $colorxy >> 8 ) & 0xFF, $colorxy & 0xFF, $alpha );
                //set pixel with the new color + opacity
                if ( !imagesetpixel( $img, $x, $y, $alphacolorxy ) ) {
                    return $img;
                }
            }
        }
        return $img;
    }

    /**
     * checks if filetype is allowed
     * @param array $imagesData
     * @return boolean
     */
    protected function is_file_type_is_allowed( $imagesData )
    {
        $tmp = current( $imagesData['sizes'] );
        return in_array( array_search( $tmp['mime-type'], Logic_Data::get()->get_image_mime_types() ), $this->data->image_type );
    }

    /**
     * return image sizes allowed for watermarking
     * @return array
     */
    protected function get_allowed_file_size()
    {
        $tmp = get_intermediate_image_sizes();
        $tmp[] = 'full_size';
        foreach ( array_keys( $tmp ) as $key ) {
            if ( !in_array( $key, $this->water_mark_params['global']['thumb'] ) ) {
                unset( $tmp[$key] );
            }
        }
        return $tmp;
    }

    /**
     * returns name for copy of file
     * @param string $fileName
     * @return string
     */
    public function get_image_copy_name( $fileName )
    {
        return preg_replace( "/\.[a-z]+$/i", "_org$0", $fileName );
    }

    /**
     * 
     * @param array $images_data
     */
    public function parse_images( $images_data ,$force = false )
    {
        if ( ($force || $this->data->auto) && $this->is_file_type_is_allowed( $images_data ) ) {
            $this->parse_params();
            $tmp = wp_upload_dir();
            $upload_dir = $tmp['basedir'];
            $file = $upload_dir . "/" . $images_data['file'];
            $images_data['sizes']['full_size'] = array( "file" => pathinfo( $file, PATHINFO_BASENAME ) );
            $allowed_file_size = $this->get_allowed_file_size();
            $fileNameCopy = $this->get_image_copy_name($file);
            if ( $this->data->preserve_original && ! file_exists( $fileNameCopy )) {
                if ( !copy( $file, $fileNameCopy ) ) {
                    _e( 'Error during making backup', 'cyberprojekt_iw' );
                }
            }
            foreach ( $images_data['sizes'] as $key => $value ) {
                if ( $this->is_size_proper_for_watermark( $key, $value, $allowed_file_size ,$file) ) {
                    $this->parse_image(pathinfo( $file, PATHINFO_DIRNAME ) . "/" . $value['file']);
                }
            }
            
        }
    }
    
    /**
     * 
     * @param string $filePath
     */
    public function parse_image($filePath){
        $this->get_parser( $filePath, $this->water_mark_params )->save_image( $filePath );
    }
    
    /**
     * checks if size of image is proper for watermarking
     * @param string $size
     * @param array $params
     * @param array $allowed_file_size
     * @return boolean
     */
    protected function is_size_proper_for_watermark($size,$params,$allowed_file_size,$file){
        if($this->data->thumb_size_type==1){
            return in_array( $size, $allowed_file_size );
        }else{
            if($params['height']==null || $params['width']==null){
                $tmp = getimagesize(pathinfo( $file, PATHINFO_DIRNAME ) . "/" . $params['file']);
                $params['height'] = $tmp[1];
                $params['width'] = $tmp[0];
            }
            return ( $params['height'] >= $this->data->thumb_min_h
                    && $params['width'] >= $this->data->thumb_min_w
                    && $params['height'] <= $this->data->thumb_max_h
                    && $params['width'] <= $this->data->thumb_max_w
                    );
        }
    }

    /**
     * saves images with watermark
     * @param string $imageUrl
     * @param string $filename
     */
    public function save_image( $imageUrl, $filename )
    {
        $this->parse_params();
        $this->get_parser( $imageUrl, $this->water_mark_params )->save_image( $filename );
    }

    /**
     * 
     * @return Image_Watermark_Parser
     * @throws Exception
     */
    protected function get_parser( $imageUrl, $waterMarkParams )
    {
        $size = getimagesize( $imageUrl );
        switch ( $size['mime'] ) {
            case "image/gif":
                return new Image_Watermark_Parser_Gif( $imageUrl, $size, $waterMarkParams );
            case "image/jpeg":
                return new Image_Watermark_Parser_Jpg( $imageUrl, $size, $waterMarkParams );
            case "image/png":
                return new Image_Watermark_Parser_Png( $imageUrl, $size, $waterMarkParams );
            default:
                throw new Exception( __( "Wrong image format", 'cyberprojekt_iw' ) );
        }
    }

}

abstract class Image_Watermark_Parser
{

    /**
     *
     * @var resource
     */
    protected $image;

    /**
     * size (0-width,1-height,2-img type,3-wh for img tag,
     * @var array
     */
    protected $image_data;

    /**
     *
     * @var array
     */
    protected $watermark_params;

    public function __construct( $imageUrl, $size, $watermark_params )
    {
        $this->image_data['size'] = $size;
        $this->image = $this->get_image( $imageUrl );
        $this->watermark_params = $watermark_params;
    }

    /**
     * @return resource image
     */
    abstract protected function get_image( $imageUrl );

    /**
     * 
     * @param int $width
     * @param int $height
     * @return float
     */
    protected function get_font_scale_for_image( $width, $height )
    {
        //should check height during scaling - to do later
        return (($width / $this->watermark_params['text']['fontBoxDefaultSize']['width']) * $this->watermark_params['text']['scale']);
    }

    /**
     * returns size of box with text
     * @param int $fontSize
     * @return array
     */
    protected function get_box_size( $fontSize )
    {
        $box = imagettfbbox( $fontSize, 0, $this->watermark_params['text']['fontFile'], $this->watermark_params['text']['text'] );
        return array(
            "width" => abs( $box[4] - $box[0] ),
            "height" => abs( $box[5] - $box[1] )
        );
    }

    /**
     * adds text watermark
     * @return \Image_Watermark_Parser
     */
    protected function add_text_watermark()
    {
        $fontSize = $this->watermark_params['text']['baseFontSize'] * $this->get_font_scale_for_image( $this->image_data['size'][0], $this->image_data['size'][1] );
        $boxSize = $this->get_box_size( $fontSize );
        $x = ($this->image_data['size'][0] * $this->watermark_params['text']['x'] / 100) - ($boxSize['width'] / 2);
        $y = ($this->image_data['size'][1] * $this->watermark_params['text']['y'] / 100 ) + ($boxSize['height'] / 2);
        if ( $x < 0 ) {
            $x = 0;
        } elseif ( $x + $boxSize['width'] > $this->image_data['size'][0] ) {
            $x = $this->image_data['size'][0] - $boxSize['width'];
        }
        if ( $y + $boxSize['height'] > $this->image_data['size'][1] ) {
            $y = $this->image_data['size'][1] - ($boxSize['height'] * (33 / 100));
        } elseif ( $y < $boxSize['height'] ) {
            $y = $boxSize['height'];
        }
        $color = imagecolorallocatealpha( $this->image, $this->watermark_params['text']['color']['r'], $this->watermark_params['text']['color']['g'], $this->watermark_params['text']['color']['b'], $this->watermark_params['text']['transparency'] );
        imagettftext( $this->image, $fontSize, $this->watermark_params['text']['angle'], $x, $y, $color, $this->watermark_params['text']['fontFile'], $this->watermark_params['text']['text'] );
        return $this;
    }

    /**
     * adds image watermark
     * @return \Image_Watermark_Parser
     */
    protected function add_image_watermark()
    {
        $image_ratio = (($this->image_data['size'][0] * $this->watermark_params['image']['width']) / $this->watermark_params['image']['image_width']);
        $w = ($this->watermark_params['image']['image_width'] * $image_ratio);
        $h = ($this->watermark_params['image']['image_height'] * $image_ratio);
        $y = ($this->image_data['size'][1] - $h) * $this->watermark_params['image']['y'];
        $x = ($this->image_data['size'][0] - $w) * $this->watermark_params['image']['x'];
        imagecopyresized( $this->image, $this->watermark_params['image']['image'], $x, $y, 0, 0, $w, $h, $this->watermark_params['image']['image_width'], $this->watermark_params['image']['image_height'] );
        return $this;
    }

    /**
     * saves image with watermark
     * @param string $filename
     * @throws Exception
     */
    public function save_image( $filename )
    {
        switch ( $this->watermark_params["global"]["type"] ) {
            case 1://Text and Image
                $this->add_image_watermark();
                $this->add_text_watermark();
                break;
            case 2://Text Only
                $this->add_text_watermark();
                break;
            case 3://Image Only
                $this->add_image_watermark();
                break;
            default:
                throw new Exception( __( "Wrong type", 'cyberprojekt_iw' ) );
        }
        if ( !$this->save( $filename ) ) {
            throw new Exception( __( "Error while saving image", 'cyberprojekt_iw' ) );
        }
    }

    abstract protected function save( $file );
}

class Image_Watermark_Parser_Jpg extends Image_Watermark_Parser
{

    /**
     * 
     * @param string $imageUrl
     * @return resource
     */
    protected function get_image( $imageUrl )
    {
        return imagecreatefromjpeg( $imageUrl );
    }

    /**
     * 
     * @param string $filename
     * @return bool <b>TRUE</b> on success or <b>FALSE</b> on failure.
     */
    protected function save( $filename )
    {
        return imagejpeg( $this->image, $filename, $this->watermark_params['global']['quality'] );
    }

}

class Image_Watermark_Parser_Png extends Image_Watermark_Parser
{

    /**
     * 
     * @param string $imageUrl
     * @return resource
     */
    protected function get_image( $imageUrl )
    {
        return imagecreatefrompng( $imageUrl );
    }

    /**
     * 
     * @param string $filename
     * @return bool <b>TRUE</b> on success or <b>FALSE</b> on failure.
     */
    protected function save( $filename )
    {
        return imagepng( $this->image, $filename, 9 );
    }

}

class Image_Watermark_Parser_Gif extends Image_Watermark_Parser
{

    /**
     * 
     * @param string $imageUrl
     * @return resource
     */
    protected function get_image( $imageUrl )
    {
        return imagecreatefromgif( $imageUrl );
    }

    /**
     * 
     * @param string $filename
     * @return bool <b>TRUE</b> on success or <b>FALSE</b> on failure.
     */
    protected function save( $filename )
    {
        return imagegif( $this->image, $filename );
    }

}
