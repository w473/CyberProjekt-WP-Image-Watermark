<?php

/**
 * Description of Image_Manage
 *
 * @author Jacek Glogosz <jacek@cyberprojekt.pl>
 */
class Image_Manage
{

    /**
     * 
     * @param string $src
     * @throws Exception
     */
    public function preview_font_manage( $src )
    {
        try {
            if ( preg_match( "{.*\.\..*|\/}", $src ) ) {
                throw new Exception( __( "Wrong path", 'cyberprojekt_iw' ) );
            }
            $file = Logic_Data::get()->get_paths()->fonts . preg_replace( "{.*\/}", "", preg_replace( "{\.jpg$}", ".ttf", $src ) );
            $this->create_font_preview( $file );
            $msg = array( "status" => "ok" );
        } catch ( Exception $e ) {
            $msg = array( "status" => "error", "message" => $e->getMessage() );
        }
        echo json_encode( $msg );
        exit();
    }

    /**
     * 
     * @param string $fontFile
     * @return boolean
     * @throws Exception
     */
    protected function create_font_preview( $fontFile )
    {
        $preview = preg_replace( "{.ttf$}", ".jpg", $fontFile );
        if ( !file_exists( $preview ) ) {
            if ( ($im = imagecreatetruecolor( 1000, 1000 )) === FALSE )
                throw new Exception( __( "There was an error during creation of image", 'cyberprojekt_iw' ) );
            if ( ($white = imagecolorallocate( $im, 255, 255, 255 )) === FALSE )
                throw new Exception( __( "There was an error during creation of image", 'cyberprojekt_iw' ) );
            if ( (imagefill( $im, 0, 0, $white )) === FALSE )
                throw new Exception( __( "There was an error during creation of image", 'cyberprojekt_iw' ) );
            if ( ($black = imagecolorallocate( $im, 0, 0, 0 )) === FALSE )
                throw new Exception( __( "There was an error during creation of image", 'cyberprojekt_iw' ) );
            if ( (imagettftext( $im, 36, 0, 0, 500, $black, $fontFile, "ABCDEFYGabcdefgy" )) === FALSE )
                throw new Exception( __( "There was an error during creation of image", 'cyberprojekt_iw' ) );
            $im = $this->image_trim( $im, $white );
            $im = $this->resize_image( $im, 200 );
            if ( (imagejpeg( $im, $preview )) === FALSE )
                throw new Exception( __( "There was an error during creation of image", 'cyberprojekt_iw' ) );
        }
        return true;
    }

    /**
     * 
     * @param resource $image
     * @param int $w
     * @return resource
     * @throws Exception
     */
    protected function resize_image( $image, $w )
    {
        if ( ($width = imagesx( $image )) === FALSE )
            throw new Exception( __( "There was an error during image resize", 'cyberprojekt_iw' ) );
        if ( ($height = imagesy( $image )) === FALSE )
            throw new Exception( __( "There was an error during image resize", 'cyberprojekt_iw' ) );
        $scale = $w / $width;
        $newheight = $height * $scale;
        if ( ($dst = imagecreatetruecolor( $w, $newheight )) === FALSE )
            throw new Exception( __( "There was an error during image resize", 'cyberprojekt_iw' ) );
        if ( (imagecopyresampled( $dst, $image, 0, 0, 0, 0, $w, $newheight, $width, $height )) === FALSE )
            throw new Exception( __( "There was an error during image resize", 'cyberprojekt_iw' ) );

        return $dst;
    }

    /**
     * Trims an image then optionally adds padding around it.
     * http://zavaboy.com/2007/10/06/trim_an_image_using_php_and_gd
     * @param resource $im Image link resource
     * @param int $bg The background color to trim from the image
     * @param string $pad Amount of padding to add to the trimmed image: Padding: 1px top, 2px right, 3px bottom, 4px left //imagetrim($im,$bg,'1 2 3 4');
     * @return type
     */
    protected function image_trim( $im, $bg, $pad = null )
    {
// Calculate padding for each side.
        if ( isset( $pad ) ) {
            $pp = explode( ' ', $pad );
            if ( isset( $pp[3] ) ) {
                $p = array( ( int ) $pp[0], ( int ) $pp[1], ( int ) $pp[2], ( int ) $pp[3] );
            } else if ( isset( $pp[2] ) ) {
                $p = array( ( int ) $pp[0], ( int ) $pp[1], ( int ) $pp[2], ( int ) $pp[1] );
            } else if ( isset( $pp[1] ) ) {
                $p = array( ( int ) $pp[0], ( int ) $pp[1], ( int ) $pp[0], ( int ) $pp[1] );
            } else {
                $p = array_fill( 0, 4, ( int ) $pp[0] );
            }
        } else {
            $p = array_fill( 0, 4, 0 );
        }

// Get the image width and height.
        $imw = imagesx( $im );
        $imh = imagesy( $im );

// Set the X variables.
        $xmin = $imw;
        $xmax = 0;

// Start scanning for the edges.
        for ( $iy = 0; $iy < $imh; $iy++ ) {
            $first = true;
            for ( $ix = 0; $ix < $imw; $ix++ ) {
                $ndx = imagecolorat( $im, $ix, $iy );
                if ( $ndx != $bg ) {
                    if ( $xmin > $ix ) {
                        $xmin = $ix;
                    }
                    if ( $xmax < $ix ) {
                        $xmax = $ix;
                    }
                    if ( !isset( $ymin ) ) {
                        $ymin = $iy;
                    }
                    $ymax = $iy;
                    if ( $first ) {
                        $ix = $xmax;
                        $first = false;
                    }
                }
            }
        }

// The new width and height of the image. (not including padding)
        $imw = 1 + $xmax - $xmin; // Image width in pixels
        $imh = 1 + $ymax - $ymin; // Image height in pixels
// Make another image to place the trimmed version in.
        $im2 = imagecreatetruecolor( $imw + $p[1] + $p[3], $imh + $p[0] + $p[2] );

// Make the background of the new image the same as the background of the old one.
        $bg2 = imagecolorallocate( $im2, ($bg >> 16) & 0xFF, ($bg >> 8) & 0xFF, $bg & 0xFF );
        imagefill( $im2, 0, 0, $bg2 );

// Copy it over to the new image.
        imagecopy( $im2, $im, $p[3], $p[0], $xmin, $ymin, $imw, $imh );

// To finish up, we replace the old image which is referenced.
        $im = $im2;
        return $im;
    }

}
