<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/* *****************************************************************************
 * Helper function for templating.
 * Automatically integrates some information, header and footer into template.
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

class Template
{
    
    private static $instance;
    
    private $data;
    
    /**
     * Get the template instance.
     * @retval Template The template object. 
     */
    public static function getInstance()
    {
        if (self::$instance == null)
            self::$instance = new Template();
        return self::$instance;
    }
    
    /**
     * Construct a new Template object. 
     */
    private function __construct()
    {
        $this->data = array();
    }
    
    /**
     * Set a template key/value pair.
     * @param string $key
     * @param mixed $value
     * @throws Exception 
     */
    public function set($key, $value)
    {
        if (!isset($key) || !is_string($key))
            throw new Exception("Template.set: Illegal value '"
                .var_export($key, true)."' for field 'key'");
        
        $this->data[$key] = $value;
    }
    
    /**
     * Display a given template using header, footer and necessary data.
     * @param string $tpl Template file to display, without file ending.
     * @param bool $plain If true, header and footer will NOT be displayed.
     * @throws Exception 
     */
    public function display($tpl, $plain = false)
    {
        if (!isset($tpl) || !is_string($tpl))
            throw new Exception("Template.display: Illegal value '"
                .var_export($tpl, true)."' for field 'tpl'");
        
        //----
        // Set necessary data
        $this->data["auth"] = AuthHelper::getInstance();
        $this->data["user"] = $this->data["auth"]->getCurrentUser();
        
        //----
        // Add standard JS files
        $this->js("common");
        
        try {
            $CI = get_instance();
            
            if (!$plain)
                $CI->load->view("inc/header.php", $this->data);
            $CI->load->view($tpl, $this->data);
            if (!$plain)
                $CI->load->view("inc/footer.php", $this->data);
        
            
        } catch (Exception $e) {
            throw $e;
        }
    }
    
    /**
     * Wrapper for displaying simple error message.
     * Automatically sets the template $title and $message attributes and 
     * loads/displays necessary error message template.
     * @param string $title
     * @param string $message
     * @param string $type 
     */
    public function displayError($title, $message, $type = "error")
    {
        $this->set("title", $title);
        $this->set("message", $message);
        $this->display("error/".$type);
    }
    
    /**
     * Add a new javascript file to be loaded with template.
     * @param string $jsFile
     */
    public function js($jsFile)
    {
        if (!isset($jsFile) || !is_string($jsFile))
            return;
        
        // Add file to jsFiles array in data
        if (!isset($this->data["jsFiles"]))
            $this->data["jsFiles"] = array();
        $this->data["jsFiles"][] = $jsFile;
    }
}

/* End of file */