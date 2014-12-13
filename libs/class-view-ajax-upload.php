<?php

/**
 * Description of View_Ajax_Upload
 *
 * @author Jacek Glogosz <jacek@cyberprojekt.pl>
 */
class View_Ajax_Upload
{

    /**
     *
     * @var Model_Ajax_Upload
     */
    protected $model;

    /**
     * 
     * @param Model_Ajax_Upload $model
     */
    public function __construct( Model_Ajax_Upload $model )
    {
        $this->model = $model;
    }

    /**
     * deletes file
     */
    protected function delete()
    {
        $file = realpath( $this->model->dir . $this->model->post['delete'] );
        if ( $this->model->type == 'fonts' ) {
            $fileMini = realpath( $this->model->dir . preg_replace( "{ttf$}", "jpg", $this->model->post['delete'] ) );
            unlink( $fileMini );
        }
        if ( preg_match( "{^" . $this->model->dir . ".*}", $file ) && unlink( $file ) ) {
            $ret = array( "status" => "ok", "file" => $this->model->post['delete'] );
        } else {
            $ret = array( "status" => "error", "file" => $this->model->post['delete'], "message" => __( "There was an error during file delete", 'cyberprojekt_iw' ) );
        }
        echo json_encode( $ret );
        exit();
    }

    /**
     * shows list of files
     */
    protected function show()
    {
        ?><div style="background-color: white;border:1px solid black;">
            <div>
                <table>
                    <tr><th><?php _e( 'Name', 'cyberprojekt_iw' ) ?></th><th><?php _e( 'Preview', 'cyberprojekt_iw' ) ?></th><th><?php _e( 'Options', 'cyberprojekt_iw' ) ?></th></tr>
                    <?php
                    $images = Logic_Data::get()->get_file_from_folder_with_extension( $this->model->dir, $this->model->ext, true );
                    foreach ( $images as $key => $value ) {
                        ?>
                        <tr>
                            <td><?php echo $value['element']; ?></td>
                            <td><img src="<?php echo $this->model->wed_dir . $value['preview']; ?>" onerror="ciw.iwImageManagePreviewCreate(this);" style="width:200px"/></td>
                            <td><a href="javascript:ciw.iwRemove('<?php echo $key; ?>','<?php echo $this->model->action; ?>','<?php echo $this->model->id; ?>','<?php echo $this->model->type; ?>')"><?php _e( 'Delete', 'cyberprojekt_iw' ) ?></a></td>
                        </tr>
                        <?php
                    }
                    ?>
                </table>
            </div>
        </div><?php
    }

    public function to_string()
    {
        $type = isset($this->model->post['type']) ? $this->model->post['type'] : null;
        switch ( $type ) {
            case 'delete':
                $this->delete();
                break;
            case 'upload':
                $this->upload();
                break;
            default:
                $this->show();
                break;
        }
    }

    /**
     * uploads file
     */
    function upload()
    {
        $id = $this->model->id . "-fileupload";
        //sprawdzicz uprawnienia do zpaisu
        $urls = array();
        $errors = array();
        if ( sizeof( $this->model->files[$id] ) > 0 ) {
            foreach ( $this->model->files[$id]['error'] as $key => $error ) {
                if ( $error == UPLOAD_ERR_OK ) {
                    $uploadedUrl = $this->model->dir . $this->model->files[$id]['name'][$key];
                    if ( !in_array( $this->model->files[$id]['type'][$key], $this->model->allowed_types ) ) {
                        $errors[] = sprintf( __( 'Wrong file type %1$s %2$s allowed %3$s!', 'cyberprojekt_iw' ), $this->model->files[$id]['name'][$key], $this->model->files[$id]['type'][$key], implode( ",", $this->model->allowedTypes ) );
                    } elseif ( move_uploaded_file( $this->model->files[$id]['tmp_name'][$key], $uploadedUrl ) ) {
                        $urls[] = array(
                            "url" => plugins_url( $this->model->web_dir . $this->model->files[$id]['name'][$key], __FILE__ ),
                            "fullName" => $this->model->files[$id]['name'][$key],
                            "name" => preg_replace( "{\..*}", "", $this->model->files[$id]['name'][$key] ),
                        );
                    } else {
                        $errors[] = sprintf( __( 'Failed to save %1$s', 'cyberprojekt_iw' ), $this->model->files[$id]['name'][$key] );
                    }
                } else {
                    $errors[] = print_r( $error, true );
                }
            }
        } else {
            $errors[] = __( 'No files', 'cyberprojekt_iw' );
        }
        $ret = array( "files" => $urls, "errors" => implode( ",<br>", $errors ) );
        $ret['message'] = (sizeof( $errors ) == 0) ? __( 'Successfully uploaded file', 'cyberprojekt_iw' ) : __( 'Error during upload', 'cyberprojekt_iw' );
        echo json_encode( $ret );
        exit();
    }

}
