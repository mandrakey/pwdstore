<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/* *****************************************************************************
 * Main settings controller.
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
 * Controller for user settings.
 * @package com.bleuelmedia.banking
 * @author mbleuel <mbleuel@bleuelmedia.com>
 */
class Settings extends CI_Controller
{
    
    private $auth;
    private $tpl;
    
    public function __construct()
    {
        parent::__construct();
        
        $this->auth = AuthHelper::getInstance();
        $this->tpl = Template::getInstance();
        if (!$this->auth->isLoggedIn()) {
            redirect("login");
            return;
        }
        
        $this->lang->load("settings");
    }
    
    /**
     * Display current profile settings.
     */
    public function index()
    {
        $this->tpl->js("settings");
        $this->tpl->display("settings");
    }
    
    public function save()
    {   
        //----
        // Get user dataset
        $this->load->model("users_model");
        
        $userId = $this->input->post("userId");
        $data = array(
            "email" => $this->input->post("email")
        );
        
        //----
        // New password?
        $newPassword = $this->input->post("newPassword");
        $newPasswordConfirmation = $this->input->post("newPasswordConfirmation");
        if ($newPassword) {
            if (!$newPasswordConfirmation || $newPassword !== $newPasswordConfirmation) {
                $this->tpl->set("title", lang("settings_NewPassword"));
                $this->tpl->set("message", lang("settings_NewPasswordDoesNotMatch"));
                $this->tpl->display("error/error");
                return;
            }
            
            $data["password"] = sha1($newPassword);
        }
        
        //----
        // Update data
        $this->db->trans_begin();
        
        try {
            $this->users_model->updateUser($userId, $data);
        } catch (Exception $e) {
            $this->tpl->set("title", lang("error_DataUpdateFailed"));
            $this->tpl->set("message", $e->getMessage());
            $this->tpl->display("error/error");
            return;
        }
        
        $this->db->trans_commit();
        
        //----
        // Show success message
        $this->tpl->set("title", lang("settings_SettingsUpdated_Title"));
        $this->tpl->set("message", lang("settings_SettingsUpdated_Message")
            ."<br><br><b>".lang("dialog_FurtherActions")."</b><br>"
            ."- <a href=\"".site_url("settings")."\">"
            .lang("dialog_BackTo")." ".lang("settings_Settings")."</a><br>"
            ."- <a href=\"".site_url("")."\">".lang("dialog_BackTo")." ".lang("navigation_Home")."</a>");
        $this->tpl->display("message");
    }
    
}

/* End of file. */
