<?php

/**
 * @author Jacek Glogosz <jacek@cyberprojekt.pl>
 */
abstract class Abstract_Element
{

    /**
     *
     * @var string
     */
    protected $name;

    /**
     *
     * @var mixed
     */
    protected $value;

    /**
     *
     * @var array
     */
    protected $attribs;

    /**
     *
     * @var boolean
     */
    protected $required = false;

    /**
     *
     * @var array
     */
    protected $errors = array();

    abstract public function __toString();

    /**
     * 
     * @return string
     */
    protected function to_string_validation()
    {
        $ret = "";
        foreach ( $this->errors as $value ) {
            $ret .= "<span class='error'>$value</span>";
        }
        return $ret;
    }

    /**
     * @return boolean Description
     */
    abstract public function is_valid( $data );

    /**
     * 
     * @return string
     */
    protected function get_label()
    {
        $label = $this->attribs['label'];
        if ( $label ) {
//            $label = "<span>".$label."</span>";
            $label = "<label for=\"" . $this->get_name() . "\">" . $label . "</label>";
        }
        return $label;
    }

    /**
     * 
     * @return string
     */
    protected function get_description()
    {
        $description = isset( $this->attribs['description'] ) ? $this->attribs['description'] : null;
        if ( $description ) {
            $description = "<span class=\"description\">" . $description . "</span>";
        }
        return $description;
    }

    /**
     * 
     * @return string
     */
    protected function to_string_end()
    {
        return $this->to_string_validation();
    }

    /**
     * 
     * @param boolean $required
     */
    public function set_required( $required = true )
    {
        $this->attribs['required'] = $required;
    }

    /**
     * 
     * @return boolean
     */
    protected function is_required()
    {
        return ( boolean ) (isset( $this->attribs['required'] ) ? $this->attribs['required'] : false);
    }

    /**
     * 
     * @param mixed $value
     */
    public function set_value( $value )
    {
        $this->value = $value;
    }

    /**
     * 
     * @return string
     */
    public function get_name()
    {
        return $this->name;
    }

    /**
     * 
     * @param string $name
     * @param array $attribs
     */
    public function __construct( $name, $attribs = array() )
    {
        $this->name = $name;
        $this->label = isset( $attribs['label'] ) ? $attribs['label'] : null;
        $this->value = isset( $attribs['value'] ) ? $attribs['value'] : null;
        $this->required = ( boolean ) ( isset( $attribs['required'] ) ? $attribs['required'] : false );
        $this->attribs = $attribs;
    }

    /**
     * 
     * @return mixed
     */
    public function get_value()
    {
        return $this->value;
    }

    /**
     * 
     * @param array $validatorData validator data
     * @param mixed $value value to validate
     */
    protected function is_valid_with_validator( $k, $v, $value )
    {
        $name = is_int( $k ) ? $v : $k;
        switch ( $name ) {
            case "length":
                if ( !(strlen( $value ) >= ( int ) $v['min'] && strlen( $value ) <= ( int ) $v['max']) ) {
                    $this->errors[] = printf( __( "String length is not valid %s", 'cyberprojekt_iw' ), $this->get_name() );
                    return false;
                }
                break;
            case "int":
                $tmp = ( int ) $value;
                if ( !is_int( $tmp ) || ( string ) $tmp != ( string ) $value ) {
                    $this->errors[] = printf( __( "%s is not integer", 'cyberprojekt_iw' ), $tmp );
                    return false;
                } elseif ( ($v['min']!=null && !(( int ) $tmp >= ( int ) $v['min']) && ( $v['max']!=null && ( int ) $tmp <= ( int ) $v['max'])) ) {
                    $this->errors[] = printf( __( "%s is outside borders", 'cyberprojekt_iw' ), $tmp );
                    return false;
                }
                break;
            case 'color':
                //'/^#[a-f0-9]{6}$/i'
                if ( !preg_match( "{^#([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$}", $value ) ) {
                    $this->errors[] = printf( __( "%s is not valid color", 'cyberprojekt_iw' ), $tmp );
                    return false;
                }
                break;
            case 'fileExists':
                if ( !file_exists( $v['dir'] . "/" . $value ) ) {
                    $this->errors[] = __( "File does not exists", 'cyberprojekt_iw' );
                    return false;
                }
                break;
            default:
                $this->errors[] = printf( __( "Validator %s does not exists", 'cyberprojekt_iw' ), $name );
                return false;
        }
        return true;
    }

    public function get_errors()
    {
        return $this->errors;
    }

}

abstract class Multi extends Abstract_Element
{

    /**
     *
     * @var boolean
     */
    protected $is_multi_select = false;

    /**
     *
     * @var array
     */
    protected $options;

    /**
     * 
     * @return boolean
     */
    protected function is_multi()
    {
        return (substr( $this->name, -2 ) == '[]') || ( boolean ) (isset( $this->attribs['multi'] ) ? $this->attribs['multi'] : false);
    }

    /**
     * 
     * @param string $name
     * @param array $attribs
     */
    public function __construct( $name, $attribs = array() )
    {
        parent::__construct( $name, $attribs );
        $this->options = isset( $attribs['options'] ) ? $attribs['options'] : array();
    }

    /**
     * 
     * @param array $data
     * @return boolean
     */
    public function is_valid( $data )
    {
        $this->set_value( $data );
        $valid = true;
        if ( !is_array( $data ) )
            $data = array( $data );
        $validators = isset( $this->attribs['validators'] ) ? $this->attribs['validators'] : array();
//        if($this->isRequired() && $data)
        if ( $this->is_required() && (sizeof( $data ) == 0 || (sizeof( $data ) == 1 && $data[0] == '')) ) {
            $valid = false;
            $this->errors[] = __( "Value is required!", 'cyberprojekt_iw' );
        }
        foreach ( $data as $value ) {
            if ( strlen( $value ) > 0 && sizeof( $this->options ) > 0 ) {
                if ( !array_key_exists( $value, $this->options ) ) {
                    $valid = false;
                    $this->errors[] = __( "Not found in haystack", 'cyberprojekt_iw' );
                } elseif ( $validators ) {
                    foreach ( $validators as $k => $v ) {
                        $valid = $this->is_valid_with_validator( $k, $v, $value ) && $valid;
                    }
                }
            }
        }
        return $valid;
    }

}

class Text extends Abstract_Element
{

    /**
     * 
     * @return string
     */
    public function __toString()
    {
        $ret = "<div>" . $this->get_label() . "<input type='text' name='" . $this->get_name() . "' id='" . $this->get_name() . "' value='" . $this->value . "'>" . $this->get_description() . $this->to_string_end() . "</div>";
        return $ret;
    }

    /**
     * 
     * @param array $data
     * @return boolean
     */
    public function is_valid( $data )
    {
        $this->set_value( $data );
        $valid = true;
        if ( strlen( $data ) > 0 || $this->is_required() ) {
            foreach ( $this->attribs['validators'] as $key => $params ) {
                $valid = $this->is_valid_with_validator( $key, $params, $data ) && $valid;
            }
        }
        return $valid;
    }

}

class Hidden extends Abstract_Element
{

    /**
     * 
     * @return string
     */
    public function __toString()
    {
        $ret = "<input type='hidden' name='" . $this->get_name() . "' id='" . $this->get_name() . "' value='" . $this->value . "'/>";
        return $ret;
    }

    /**
     * 
     * @param array $data
     * @return boolean
     */
    public function is_valid( $data )
    {
        $this->set_value( $data );
        return true;
    }

}

class Select extends Multi
{

    /**
     * 
     * @return string
     */
    public function __toString()
    {
        $options = $this->options;
        $ret = "<div>" . $this->get_label() . '<select id="' . $this->name . '" name="' . $this->name . '"' . $this->getMulti() . '>';
        if ( !$this->getMulti() ) {
            $options = array_merge( array( "" => "-- " . __( 'Choose', 'cyberprojekt_iw' ). " --" ), $options );
        }
        foreach ( $options as $key => $value ) {
            $selectedHtml = ((is_array( $this->value ) && key_exists( $key, $this->value )) || $key == $this->value) ? ' selected="selected"' : "";
            $ret.='<option value="' . $key . '"' . $selectedHtml . '>' . $value . '</option>';
        }
        $ret .= '</select>' . $this->get_description() . $this->to_string_end() . "</div>";
        return $ret;
    }

    /**
     * 
     * @return string
     */
    protected function getMulti()
    {
        return $this->is_multi() ? ' multiple="multiple"' : "";
    }

}

abstract class Radio_Checkbox extends Multi
{

    /**
     * @return string radio or checkbox
     */
    abstract protected function get_type();
    
    /**
     * 
     * @return string
     */
    protected function get_label()
    {
        $label = $this->attribs['label'];
        if ( $label ) {
//            $label = "<span>".$label."</span>";
            $label = "<label>" . $label . "</label>";
        }
        return $label;
    }

    /**
     * 
     * @return string
     */
    public function __toString()
    {
        $ret = "<div>" . $this->get_label();
        if ( !is_array( $this->options ) || sizeof( $this->options ) == 0 ) {
            $this->options = array( 1 => "" );
        }
        foreach ( $this->options as $key => $value ) {
            $checked = ((is_array( $this->value ) && in_array( $key, $this->value )) || $key == $this->value) ? ' checked="checked"' : "";
            $name = $this->is_multi() ? $this->name . "[]" : $this->name;
            if(sizeof($this->options)>1) $ret .= "<br />";
            $ret.='<input name="' . $name . '" value="' . $key . '" type="' . $this->get_type() . '"' . $checked . '><label> ' . $value . '</label>';
        }
        $tmp = $this->get_description() . $this->to_string_end();
        if( strlen( $tmp)>0 ){
            $ret.= "<br />".$tmp;
        }
        $ret .= "</div>";
        return $ret;
    }

}

class Radio extends Radio_Checkbox
{

    /**
     * 
     * @return string
     */
    protected function get_type()
    {
        return "radio";
    }

}

class Checkbox extends Radio_Checkbox
{

    /**
     * 
     * @return string
     */
    protected function get_type()
    {
        return "checkbox";
    }

}
