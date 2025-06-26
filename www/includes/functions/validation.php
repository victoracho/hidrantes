<?php
/**
 * This class encapsulates various validation functionality.
 *
 *
 * @version 1
 * @author Gorka Mendez
 */
class valid
{    
	public static function isAlpha($value, $allow = '')
    {
		if ( !preg_match('/^[a-zA-Z'.$allow.']+$/', $value) ) 
    	return false;
    	else return true;		 			
    }
    
	public static function isAlpha_Wspace($value, $allow = '')
    {
		if ( !preg_match('/^[a-zA-Z\s'.$allow.']+$/', $value) ) 
    	return false;
    	else return true;		 			
    }        
    
	public static function isAlphaNum($value, $allow = '')
    {
		if ( !preg_match('/^[a-zA-Z0-9'.$allow.']+$/', $value) ) 
    	return false;
    	else return true;		 			
    } 
    
	public static function isAlphaNum_Wspace($value, $allow = '')
    {
		if ( !preg_match('/^[a-zA-Z0-9\s'.$allow.']+$/', $value) ) 
    	return false;
    	else return true;		 			
    }     
    public static function isEmail($email)
    {
        $pattern = "/^([a-zA-Z0-9])+([\.a-zA-Z0-9_-])*@([a-zA-Z0-9_-])+(\.[a-zA-Z0-9_-]+)+/";

        if (preg_match($pattern, $email))
        {
            return true;
        } else {
            return false;
        }
    }
    
    public static function checkLength($value, $maxLength, $minLength = 0)
    {
        if (!(strlen($value) > $maxLength) && !(strlen($value) < $minLength)) {
            return true;
        } else {
            return false;
        }
    } 
   
    
    public static function isNumber($number)
    {
        if (preg_match("/^\-?\+?[0-9e1-9]+$/", $number))
        {
            return true;
        } else {
            return false;
        }
    } 

	public static function isDecimalNumber($number) {
		  return (string)(float)$n === (string)$n;
		}    
       
}
?>