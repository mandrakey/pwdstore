<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/* *****************************************************************************
 * Helper function for session/authentication management.
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

class AuthHelper
{
    
    private static $instance;
    
    private $currentUser;
    private $CI;
    
    /**
     * Retrieve current AuthHelper instance or create it.
     * @retval AuthHelper The AuthHelper instance
     */
    public static function getInstance()
    {
        if (self::$instance == null)
            self::$instance = new AuthHelper();
        return self::$instance;
    }
    
    /**
     * Construct a new AuthHelper object.
     * Stores reference to CI instance and tries to load user data from session.
     */
    public function __construct()
    {
        $this->CI = get_instance();
        $this->currentUser = array();
        
        $this->loadUserData();
    }
    
    /**
     * Tells wether a user is logged in or not.
     * @retval bool
     */
    public function isLoggedIn()
    {
        return (count($this->currentUser) > 0) ? true : false;
    }
    
    public function hasLevel($level)
    {
        return (isset($this->currentUser["level"]) 
                && $this->currentUser["level"] === $level) 
            ? true : false;
    }
    
    /**
     * Try to load user data for logged in user.
     */
    private function loadUserData()
    {
        $userId = $this->CI->session->userdata("userId");
        if ($userId == null)
            return;
        
        $this->CI->load->model("users_model");
        $this->currentUser = $this->CI->users_model->getUser($userId);
    }
    
    /**
     * Loads a given user and saves his Id in session.
     * @param int $userId
     * @throws Exception 
     */
    public function loginUser($userId)
    {
        if (!isset($userId) || !is_numeric($userId))
            throw new Exception("AuthHelper.loginUser: Illegal value '".
                var_export($userId, true)."' for field 'userId'");
        
        $this->CI->session->set_userdata("userId", $userId);
        $this->loadUserData();
    }
    
    /**
     * Return the current user array.
     * @retval array
     */
    public function getCurrentUser()
    {
        return $this->currentUser;
    }
    
}

/* End of file */