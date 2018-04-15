<?php

namespace Cartbeforehorse\Validation;

/*********************************************************************************************************************
 *
 * ValidationSys provides a space where standard system-wide verification functions can be placed.
 * Errors can be caught and managed in a standard manner, allowing for tidier coding in the application logic
 *
 ********************************************************************************************************************/

class ValidationSys {

    /***
     * No instantiation of this class allowed
     */
    private function __construct() {
    }

    /***
     * Error helper for raising error, or simply returning true/false
     */
    private static function ManageErrorHandling ($msg, $valid, $err) {
        if ($err && !$valid)
            CodingError::RaiseCodingError ($msg);
        elseif ($err && $valid)
            return null;
        return $valid;
    }

    /***
     * The following functions are wrappers to standard PHP checking functions. The $err
     * parameter is a bool with the following meaning:
     *    -> true  - throw an error if proposed value is of wrong type
     *               Should be used to catch design-time errors
     *    -> false - return true/false according to type of $proposed_value
     *               Mainly used to catch runtime (user) errors
     */
    public static function VarIsSet ($var, $err =false) {
        $valid = isset ($var);
        return self::ManageErrorHandling ('Variable not set in ' . __METHOD__, $valid, $err);
    }

    //
    // The following for checking types ara valid
    //
    public static function IsNull ($proposed_value, $err =false) {
        $valid = is_null ($proposed_value);
        return self::ManageErrorHandling ('Value not null in ' . __METHOD__, $valid, $err);
    }
    public static function IsNullOrEmptyString ($proposed_value, $err =false) {
        $valid = is_null ($proposed_value) || ( is_string($proposed_value) && empty($proposed_value) );
        return self::ManageErrorHandling ('Value not null in ' . __METHOD__, $valid, $err);
    }
    public static function IsNonEmptyString ($proposed_value, $err =false) {
        $valid = ! self::IsNullOrEmptyString ($proposed_value);
        return self::ManageErrorHandling ('Value not empty in ' . __METHOD__, $valid, $err);
    }
    public static function IsNullOrZero ($proposed_value, $err =false) {
        $valid = is_null ($proposed_value) || $proposed_value == 0;
        return self::ManageErrorHandling ('Value not null or Zero in ' . __METHOD__, $valid, $err);
    }
    public static function IsBool ($proposed_value, $err =false) {
        $valid = is_bool ($proposed_value);
        return self::ManageErrorHandling ('Value not boolean in ' . __METHOD__, $valid, $err);
    }
    public static function IsString ($proposed_value, $err =false) {
        $valid = is_string ($proposed_value);
        return self::ManageErrorHandling ('Value not a string in ' . __METHOD__, $valid, $err);
    }
    public static function IsNumber ($proposed_value, $err =false) {
        $valid = is_numeric ($proposed_value);
        return self::ManageErrorHandling ('Value not a number in ' . __METHOD__, $valid, $err);
    }
    public static function IsArray ($proposed_arr, $err =false) {
        $valid = is_array ($proposed_arr);
        return self::ManageErrorHandling ('Value not an array in ' . __METHOD__, $valid, $err);
    }
    public static function IsObject ($proposed_obj, $err =false) {
        $valid = is_object ($proposed_obj);
        return self::ManageErrorHandling ('Value not an object in ' . __METHOD__, $valid, $err);
    }
    public static function IsObjectType ($proposed_obj, $obj_name, $err =false) {
        $is_obj = self::IsObject ($proposed_obj, $err);
        $valid = get_class($proposed_obj)==$obj_name;
        return self::ManageErrorHandling ("Object is not $obj_name in " . __METHOD__, $valid, $err);
    }
    public static function IsSubclassOf ($proposed_obj, $obj_name, $err =false) {
        $is_obj = self::IsObject ($proposed_obj, $err);
        $valid = is_subclass_of ($proposed_obj, $obj_name);
        return self::ManageErrorHandling ("Object not a subclass of $obj_name in " . __METHOD__, $valid, $err);
    }
    public static function IsClassOrSubclassOf ($proposed_obj, $obj_name, $err =false) {
        $is_obj = self::IsObject ($proposed_obj, $err);
        $valid = is_a ($proposed_obj, $obj_name);
        return self::ManageErrorHandling ("Object is not an instance of, nor a subclass of $obj_name in " . __METHOD__, $valid, $err);
    }

    /***
     * The following functions check that STRINGS comply to specific rules or formats
     * Try to ensure that all functions start with "String*"
     */
    public static function StringContains ($proposed_value, $search_str, $err =false) {
        $valid = strpos ($search_str, $proposed_value) !== false;
        return self::ManageErrorHandling ("$proposed_value not found in string, in " . __METHOD__, $valid, $err);
    }

    /***
     * The following functions check that ARRAYS comply to specific rules or formats
     * Try to ensure that all functions start with "Array*"
     */
    public static function InArray ($proposed_value, $validation_array, $err =false) {
        $valid = in_array ($proposed_value, $validation_array);
        return self::ManageErrorHandling ('Value not in given array in ' . __METHOD__, $valid, $err);
    }
    public static function ArrayContains ($proposed_value, $validation_array, $err =false) {
        return self::InArray ($proposed_value, $validation_array, $err);
    }
    public static function ArraysEqual ($arr1, $arr2, $err =false) {
        $equal = $arr1 == $arr2;
        return self::ManageErrorHandling ('Arrays are not equal in ' . __METHOD__, $equal, $err);
    }
    public static function ArrayKeysEqual ($arr1, $arr2, $err =false) {
        $equal = !array_diff_key ($arr1, $arr2) && !array_diff_key ($arr2, $arr1);
        return self::ManageErrorHandling ('Arrays are not of the same template in ' . __METHOD__, $equal, $err);
    }
    public static function ArrayValuesDuplicate (array $arr1, array $arr2 =array(), array $arr3 =array(), array $arr4 =array(), $err =false) {
        $merged_arr = array_merge($arr1, $arr2, $arr3, $arr4);
        $duplicate = (count(array_flip($merged_arr)) != count($merged_arr));
        return self::ManageErrorHandling ('Array values duplicate in ' . __METHOD__, $duplicate, $err);
    }
    public static function ArrayValsNull (array $chk_array, $err =false) {
        $all_values_null = true;
        foreach ($chk_array as $val) {
            $all_values_null = $all_values_null && $val=="";
        }
        return self::ManageErrorHandling ('All values in the array are empty in ' . __METHOD__, $all_values_null, $err);
    }
    public static function ArrayKeyValsNotNull (array $control_array, array $proposed_data, $err =false) {
        $valid = true;
        foreach ( $control_array as $key ) {
            $valid = $valid && isset($proposed_data[$key]) && self::IsNullOrEmptyString ($proposed_data[$key]);
            $valid = self::ManageErrorHandling ("Missing or null key value: <strong>$key</strong>, in " . __METHOD__, $valid, $err);
        }
        return $valid;
    }
    public static function ArrayKeysExclusive (array $arr1, array $arr2, array $arr3 =array(), array $arr4 =array(), $err =false) {
        $total_length  = count($arr1) + count($arr2) + count($arr3) + count($arr4);
        $merged_length = count ( array_merge($arr1,$arr2,$arr3,$arr4) );
        $keys_are_exclusive = ($total_length == $merged_length);
        return self::ManageErrorHandling ('Arrays keys are not exclusive in ' . __METHOD__, $keys_are_exclusive, $err);
    }
    public static function ImplodeIgnoringNulls (string $glue, array $pieces) {
        return implode ($glue, array_filter ($pieces, function($v){return!($v===''||$v===null);}));
    }

    ///////////////////////////////////////////////////////////////////////////////////
    // Functions to check validity of $_GET[] parameters in URL
    //
    public static function IsValidChannel ($channel) {
        if ( !in_array ($channel, array('xl','oo','pd')) )
            CodingError::RaiseCodingError ('Invalid channel detected in ' . __METHOD__);
    }

}
