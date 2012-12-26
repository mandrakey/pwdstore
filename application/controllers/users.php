<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/* *****************************************************************************
 * Main user management controller.
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
 * Controller for user management.
 * @package com.bleuelmedia.banking
 * @author mbleuel <mbleuel@bleuelmedia.com>
 */
class Users extends CI_Controller
{
    
    /**
     * Checks wether current user has correct access rights or not.
     * @retval boolean TRUE if the user may access the page, FALSE if not.
     */
    private function checkAuth() {
        $auth = AuthHelper::getInstance();
        if (!$auth->isLoggedIn() || !$auth->hasLevel("0")) {
            $tpl = Template::getInstance();
            $tpl->set("title", "Fehlende Berechtigung");
            $tpl->set("message", "Sie sind nicht berechtigt, diesen Bereich zu "
                ."betreten.");
            $tpl->display("error/error");
            return false;
        }
        
        return true;
    }
    
    /**
     * Display a list of all users.
     */
    public function index()
    {
        if (!$this->checkAuth())
            return;
        
        $tpl = Template::getInstance();
        
        //----
        // Load all users
        try {
            $this->load->model("users_model");
            $users = $this->users_model->getUsers();
            $tpl->set("users", $users);
        } catch (Exception $e) {
            $tpl->set("title", "Benutzerdaten konnten nicht abgerufen werden");
            $tpl->set("message", $e->getMessage());
            $tpl->display("error/error");
            return;
        }
        
        $tpl->display("users");
    }
    
    public function create()
    {
        if (!$this->checkAuth())
            return;
        
        $tpl = Template::getInstance();
        $tpl->display("users_new");
    }
    
    public function create_save()
    {
        if (!$this->checkAuth())
            return;
        
        $tpl = Template::getInstance();
        
        //====
        // Get and check user data
        
        $this->load->model("users_model");
        $user = array(
            "id" => -1,
            "name" => $this->input->post("name"),
            "email" => $this->input->post("email"),
            "firstname" => $this->input->post("firstname"),
            "lastname" => $this->input->post("lastname"),
            "active" => $this->input->post("active"),
            "level" => $this->input->post("level")
        );
        
        //----
        // Check for updated password
        $newPassword = $this->input->post("newPassword");
        $newPasswordConfirmation = $this->input->post("newPasswordConfirmation");
        if ($newPassword !== "") {
            if ($newPassword !== $newPasswordConfirmation) {
                $tpl->set("title", "Passwort");
                $tpl->set("message", "Das Passwort und seine Bestätigung "
                    ."stimmen nicht überein!");
                $tpl->display("error/error");
                return;
            }
            
            $user["password"] = sha1($newPassword);
        } else {
            $user["password"] = sha1("password");
        }
        
        //====
        // Save data
        
        $userid = -1;
        try {
            $this->db->trans_begin();
            $userid = $this->users_model->insertUser($user);
            $this->db->trans_commit();
        } catch (Exception $e) {
            $tpl->set("title", "Fehler beim Speichern der Daten");
            $tpl->set("message", $e->getMessage());
            $tpl->display("error/error");
            return;
        }
        
        //----
        // Display message
        $tpl->set("title", "Benutzer angelegt");
        $tpl->set("message", "Der Benutzer ".$user["name"]." wurde "
            ."erstellt.<br><br>"
            ."<b>Weiteres Vorgehen:</b><br>"
            ."<a href=\"".site_url("users/edit/".$userid)."\">".$user["name"]." bearbeiten</a><br>"
            ."<a href=\"".site_url("users")."\">Zurück zur Übersicht</a><br>");
        $tpl->display("message");
    }
    
    /**
     * Edit a specific user.
     * @param int $userId Id of the user to edit.
     */
    public function edit($userId)
    {
        if (!$this->checkAuth())
            return;
        
        $tpl = Template::getInstance();
        
        if (!isset($userId) || !is_numeric($userId)) {
            $tpl->set("title", "Parameterfehler");
            $tpl->set("message", "Illegaler Wert '"
                .var_export($userId, true)."' für Feld 'userId'");
            $tpl->display("error/error");
            return;
        }
        
        //====
        // Get user data
        
        $user = array();
        try {
            $this->load->model("users_model");
            $user = $this->users_model->getUser($userId);
        } catch (Exception $e) {
            $tpl->set("title", "Fehler beim Abrufen der Daten");
            $tpl->set("message", $e->getMessage());
            $tpl->display("error/error");
            return;
        }
        
        if (count($user) == 0) {
            $tpl->set("title", "Der gewählte Benutzer wurde nicht gefunden");
            $tpl->set("message", "");
            $tpl->display("error/error");
            return;
        }
        
        $tpl->set("currentUser", $user);
        $tpl->js("users");
        $tpl->display("users_edit");
    }
    
    /**
     * Save changed user data.
     */
    public function save()
    {
        if (!$this->checkAuth())
            return;
        
        $tpl = Template::getInstance();
        
        //====
        // Get and check user data
        
        $userid = $this->input->post("userid");
        if (!$userid || !is_numeric($userid)) {
            $tpl->set("title", "Parameterfehler");
            $tpl->set("message", "Illegaler Wert '".var_export($userid, true)."' "
                ."für Feld 'userid'");
            $tpl->display("error/error");
            return;
        }
        
        $this->load->model("users_model");
        $user = array(
            "name" => $this->input->post("name"),
            "email" => $this->input->post("email"),
            "firstname" => $this->input->post("firstname"),
            "lastname" => $this->input->post("lastname"),
            "active" => $this->input->post("active"),
            "level" => $this->input->post("level")
        );
        
        //----
        // Check for updated password
        $newPassword = $this->input->post("newPassword");
        $newPasswordConfirmation = $this->input->post("newPasswordConfirmation");
        if ($newPassword !== "") {
            if ($newPassword !== $newPasswordConfirmation) {
                $tpl->set("title", "Geändertes Passwort");
                $tpl->set("message", "Das geänderte Passwort und seine Bestätigung "
                    ."stimmen nicht überein!");
                $tpl->display("error/error");
                return;
            }
            
            $user["password"] = sha1($newPassword);
        }
        
        //====
        // Save data
        
        try {
            $this->users_model->updateUser($userid, $user);
        } catch (Exception $e) {
            $tpl->set("title", "Fehler beim Abrufen der Daten");
            $tpl->set("message", $e->getMessage());
            $tpl->display("error/error");
            return;
        }
        
        //----
        // Display message
        $tpl->set("title", "Benutzer gespeichert");
        $tpl->set("message", "Die Benutzerdaten für ".$user["name"]." wurden "
            ."übernommen.<br><br>"
            ."<b>Weiteres Vorgehen:</b><br>"
            ."<a href=\"".site_url("users/edit/".$userid)."\">Zurück zu ".$user["name"]."</a><br>"
            ."<a href=\"".site_url("users")."\">Zurück zur Übersicht</a><br>");
        $tpl->display("message");
        
    }
    
    /**
     * Ask confirmation for deleting a given user.
     * @param int $userId
     */
    public function delete($userId)
    {
        if (!$this->checkAuth())
            return;
        
        $tpl = Template::getInstance();
        
        if (!isset($userId) || !is_numeric($userId)) {
            $tpl->set("title", "Parameterfehler");
            $tpl->set("message", "Illegaler Wert '"
                .var_export($userId, true)."' für Feld 'userId'");
            $tpl->display("error/error");
            return;
        }
        
        //====
        // Get user data
        
        $user = array();
        try {
            $this->load->model("users_model");
            $user = $this->users_model->getUser($userId);
        } catch (Exception $e) {
            $tpl->set("title", "Fehler beim Abrufen der Daten");
            $tpl->set("message", $e->getMessage());
            $tpl->display("error/error");
            return;
        }
        
        if (count($user) == 0) {
            $tpl->set("title", "Der gewählte Benutzer wurde nicht gefunden");
            $tpl->set("message", "");
            $tpl->display("error/error");
            return;
        }
        
        $tpl->set("currentUser", $user);
        $tpl->display("users_delete");
    }
    
    public function delete_ok()
    {
        if (!$this->checkAuth())
            return;
        
        $tpl = Template::getInstance();
        
        $userId = $this->input->post("userId");
        if (!isset($userId) || !is_numeric($userId)) {
            $tpl->set("title", "Parameterfehler");
            $tpl->set("message", "Illegaler Wert '"
                .var_export($userId, true)."' für Feld 'userId'");
            $tpl->display("error/error");
            return;
        }
        
        // Delete the user
        $this->load->model("users_model");
        $this->users_model->deleteUser(intval($userId));
        
        $tpl->set("title", "Der Benutzer wurde erfolgreich gelöscht");
        $tpl->set("message", 
            "<a href=\"".site_url("users")."\">Zur Benutzerübersicht</a>");
        $tpl->display("message");
    }
    
}

/* End of file. */
