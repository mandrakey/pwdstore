<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/* *****************************************************************************
 * Common functions library.
 * 
 * =============================================================================
 * 
 * THIS FILE IS PART OF BLEUELMEDIA PWDSTORE
 * (C)2012 bleuelmedia.com
 * 
 * This program is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License version 3 as published by the
 * Free Software Foundation.
 * 
 * This program is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE.  See the GNU General Public License for more
 * details.
 * 
 * You should have received a copy of the GNU General Public License along with
 * this program; if not, see http://www.gnu.org/licenses or write to the Free
 * Software Foundation, Inc., 51 Franklin Street, Fifth Floor, Boston, MA
 * 02110-1301 USA.
 * ****************************************************************************/

/**
 * Check wether a given value is a valid number for account balance etc...
 * Correct values means something like
 * - greater or equal to 0.01
 * - MAY contain up to two digits after comma
 * @param float v Value to check
 * @retval bool If the value is valid or not
 */
function isValidMoneyValue($v)
{
    $correctAmount = "/^([1-9]{1}[0-9]*([\.,]?[0-9]{1,2})?$)|(^0[\.,][0-9]{1,2}$)/";
    return (!preg_match($correctAmount, $v) || floatval($v) == 0) ? false : true;
}

/**
 * Display a language string with parameters to be replaced.
 * @param string $languageKey Key in language file
 * @param array $data Parameters to replace like array('parameter' => 'value'). 
 * In the message text {parameter} will be replaced with value.
 * @return The new text
 */
function plang($languageKey, array $data = array())
{
    $text = lang($languageKey);
    foreach ($data as $key => $value)
        $text = str_replace("{".$key."}", $value, $text);
    return $text;
}

function encryptSecret($secret)
{
    $auth = AuthHelper::getInstance();
    $user = $auth->getCurrentUser();
    $key = md5($user["id"]."_".$user["name"]);
    
    $CI = get_instance();
    $CI->load->library("encrypt");
    return $CI->encrypt->encode(base64_encode($secret), $key);
}

function decryptSecret($secret)
{
    $auth = AuthHelper::getInstance();
    $user = $auth->getCurrentUser();
    $key = md5($user["id"]."_".$user["name"]);
    
    $CI = get_instance();
    $CI->load->library("encrypt");
    return base64_decode($CI->encrypt->decode($secret, $key));
}

/* End of file */