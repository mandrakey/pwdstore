<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/* *****************************************************************************
 * Main login controller.
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
    
    /**
     * Display current profile settings.
     */
    public function index()
    {
        if (!AuthHelper::getInstance()->isLoggedIn()) {
            redirect(site_url("login"));
            return;
        }
        
        $tpl = Template::getInstance();
        $tpl->js("settings");
        $tpl->display("settings");
    }
    
    public function save()
    {
        if (!AuthHelper::getInstance()->isLoggedIn()) {
            redirect(site_url("login"));
            return;
        }
        
        $tpl = Template::getInstance();
        $tpl->js("settings");
        
        //----
        // Get user dataset
        $this->load->model("users_model");
        
        $user = $this->input->post("userid");
        $data = array(
            "firstname" => $this->input->post("firstname"),
            "lastname" => $this->input->post("lastname"),
            "email" => $this->input->post("email")
        );
        
        //----
        // New password?
        $newPassword = $this->input->post("newPassword");
        $newPasswordConfirmation = $this->input->post("newPasswordConfirmation");
        if ($newPassword) {
            if (!$newPasswordConfirmation || $newPassword !== $newPasswordConfirmation) {
                $tpl->set("title", "Neues Passwort");
                $tpl->set("message", "Die neuen Passwörter stimmen nicht überein.");
                $tpl->display("error/error");
                return;
            }
            
            $data["password"] = sha1($newPassword);
        }
        
        //----
        // Update data
        try {
            $this->users_model->updateUser($user, $data);
        } catch (Exception $e) {
            $tpl->set("title", "Beim Speichern trat ein Fehler auf");
            $tpl->set("message", $e->getMessage());
            $tpl->display("error/error");
            return;
        }
        
        //----
        // Show success message
        $tpl->set("title", "Gespeichert!");
        $tpl->set("message", "Die geänderten Daten wurden übernommen.<br><br>"
            ."<b>Weiteres vorgehen:</b><br>"
            ."<a href=\"".site_url("settings")."\">Zurück zu Einstellungen</a><br>"
            ."<a href=\"".site_url("")."\">Zurück zur Startseite</a>");
        $tpl->display("message");
    }
    
}

/* End of file. */
