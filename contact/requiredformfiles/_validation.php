<?php
class FCFvalidate {
    var $error = false; // public
    var $error_string; // publc
    var $error_tmp = "data not valid";

    function validate($ar, $post) // __construct()
    {
        if (!is_array($ar)) {
            /* no validation required */
        } else {
            foreach($ar as $a_name => $a_type) {
            	
            	$a_name = str_replace("[]","",$a_name);
            	
                /* if no validation specified, make not_empty */
                if (trim($a_type) == "") {
                    $a_type = "NOT_EMPTY";
                } 
                /* make sure $name is in $post */
                $found = false;
                foreach($post as $p_name => $p_value) {
                	
                	$p_name = str_replace("[]","",$p_name);
                	                	
                    if ($p_name == $a_name) {
                        $found = true;
                        break;
                    } 
                } 
                if (!$found) {
                    $this->error("The \'".$a_name."\' field needs to have a value.<br />");
                } else {

                    if (is_array($p_value)) {
                        foreach($p_value as $pv) {
                            if (!$this->checkit($pv, $a_type)) {
                                $this->error("The '".$a_name."' field ".$this->error_tmp.".<br />");
                            } 
                        } 
                    } else {
                        if (!$this->checkit($p_value, $a_type)) {
                        	$this->error("The '".$a_name."' field ".$this->error_tmp.".<br />");
                        } 
                    } 
                } 
            } 
        } 
    } 
    /* ERRORS */
    function error($str) // private
    {
        $this->error = true;
        $this->error_string .= $str;
    } 
    /* VALIDATE FIELD AGAINST TYPE */
    function checkit($value, $type) // private
    {
        $length = "";
        if (preg_match('/^MIN[0-9]+$/', $type)) {
            $tmp = explode(":", $type);
            $length = $tmp[1];
            $type = "MINLENGTH";
        } 
        if (preg_match('/^MAX[0-9]+$/', $type)) {
            $tmp = explode(":", $type);
            $length = $tmp[1];
            $type = "MAXLENGTH";
        } 

        switch ($type) {
        	
        	case "RADIO_GROUP":
		    case "CHECKBOX_GROUP":
            case "NOT_EMPTY":
                $this->error_tmp = "cannot be empty";
                return $this->not_empty($value);
                break;

            case "MINLENGTH":
                if (strlen($value) < $length) {
                    $this->error_tmp = "is to short";
                    return false;
                } else {
                    return true;
                } 
                break;

            case "MAXLENGTH":
                if (strlen($value) > $length) {
                    $this->error_tmp = "is to long";
                    return false;
                } else {
                    return true;
                } 
                break;

            case "ALPHA":
                $exp = '/^[a-z]+$/';
                if ($this->not_empty($value) && preg_match($exp, $value)) {
                    return true;
                } else {
                    $this->error_tmp = "is not alpha";
                    return false;
                } 
                break;

            case "ALPHASPACE":
                $exp = '/^[a-z ]+$/';
                if ($this->not_empty($value) && preg_match($exp, $value)) {
                    return true;
                } else {
                    $this->error_tmp = "is not alphaspace";
                    return false;
                } 
                break;

            case "ALPHANUM":
                $exp = '/^[a-z0-9]+$/';
                if ($this->not_empty($value) && preg_match($exp, $value)) {
                    return true;
                } else {
                    $this->error_tmp = "is not alphanumeric";
                    return false;
                } 
                break;

            case "ALPHANUMSPACE":
                $exp = '/^[a-z0-9 ]+$/';
                if ($this->not_empty($value) && preg_match($exp, $value)) {
                    return true;
                } else {
                    $this->error_tmp = "is not alphanumeric with spaces";
                    return false;
                } 
                break;

            case "NUMERIC":
                $exp = '/^[0-9]+$/';
                if ($this->not_empty($value) && preg_match($exp, $value)) {
                    return true;
                } else {
                    $this->error_tmp = "is not numeric";
                    return false;
                } 
                break;

            case "NUMERICPLUS":
                $exp = '/^[0-9+-.]+$/';
                if ($this->not_empty($value) && preg_match($exp, $value)) {
                    return true;
                } else {
                    $this->error_tmp = "is not numeric";
                    return false;
                } 
                break;

            case "EMAIL":
                $exp = '/^[A-Z0-9._%-]+@[A-Z0-9.-]+\.[A-Z]{2,4}$/i';
                if ($this->not_empty($value) && preg_match($exp, $value)) {
                    return true;
                } else {
                    $this->error_tmp = "is not a valid email";
                    return false;
                } 
                break;

            case "YYYYMMDD":
                $exp = '/^(19|20)[0-9][0-9][- /.](0[1-9]|1[012])[- /.](0[1-9]|[12][0-9]|3[01])$/';
                if ($this->not_empty($value) && preg_match($exp, $value)) {
                    return true;
                } else {
                    $this->error_tmp = "is not in YYYYMMDD format";
                    return false;
                } 
                break;

            case "DDMMYYYY":
                $exp = '/^(0[1-9]|[12][0-9]|3[01])[- /.](0[1-9]|1[012])[- /.](19|20)[0-9][0-9]$/';
                if ($this->not_empty($value) && preg_match($exp, $value)) {
                    return true;
                } else {
                    $this->error_tmp = "is not in DDMMYYYY format";
                    return false;
                } 
                break;

            case "MMDDYYYY":
                $exp = '/^(0[1-9]|1[012])[- /.](0[1-9]|[12][0-9]|3[01])[- /.](19|20)[0-9][0-9]$/';
                if ($this->not_empty($value) && preg_match($exp, $value)) {
                    return true;
                } else {
                    $this->error_tmp = "is not in MMDDYYYY format";
                    return false;
                } 
                break;

            default:
                if ($this->not_empty($value) && $this->regex($type, $value)) {
                    return true;
                } else {
                    $this->error_tmp = "is not valid";
                    return false;
                } 
        } 
    } 
    /* NOT_EMPTY */
    function not_empty($value) // private
    {
        if (trim($value) == "") {
            return false;
        } else {
            return true;
        } 
    } 

    /* REGULAR EXPRESSION */
    function regex($regex, $value) // private
    {
        $the_regex = 'preg_match("/' . $regex . '/i", "' . $value . '")';
        $the_code = '<?php if(' . $the_regex . ') { return true; } else { return false; } ?>';
        if (!eval('?>' . $the_code . '<?php ')) {
            return false;
        } else {
            return true;
        } 
    } 
}
?>