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
 * Controller for login/logout functionality.
 * @package com.bleuelmedia.banking
 * @author mbleuel <mbleuel@bleuelmedia.com> 
 */
class Login extends CI_Controller 
{
    
    /**
     * Load data for and display login form.
     */
	public function index()
	{
        // Display
        $tpl = Template::getInstance();
        $tpl->display("login");
	}
    
    /**
     * Check supplied login data and log in the user.
     */
    public function doLogin()
    {
        $tpl = Template::getInstance();
        
        //====
        // Get data from form
        
        $login = array(
            "username" => $this->input->post("user"),
            "password" => $this->input->post("pass")
        );
        if (!is_string($login["username"])) {
            $tpl->set("title", "Benutzername");
            $tpl->set("message", "Bitte geben Sie einen Benutzernamen ein");
            $tpl->display("error/error");
            return;
        }
        if (!is_string("password") || trim("password") == "") {
            $tpl->set("title", "Passwort");
            $tpl->set("message", "Bitte geben Sie ein Passwort ein");
            $tpl->display("error/error");
            return;
        }
        
        //====
        // Check login data and log the user in
        
        //----
        // Check data
        try {
            $this->load->model("users_model");
            $auth = AuthHelper::getInstance();
            
            $userId = $this->users_model->checkLogin($login["username"], $login["password"]);
            $auth->loginUser($userId);
        } catch (UserCredentialException $e) {
            $tpl->set("title", "Benutzerdaten inkorrekt");
            $tpl->set(
                "message", 
                ($e->type == "user")
                    ? "Der angegebene Benutzer existiert nicht."
                    : "Das eingegebene Passwort ist falsch."
            );
            $tpl->display("error/error");
            return;
        } catch (Exception $e) {
            $tpl->set("title", "Parameterfehler");
            $tpl->set("message", "Beim prüfen der Logindaten trat ein Fehler auf: "
                .$e->getMessage());
            $tpl->display("error/error");
            return;
        }
        
        redirect("/");
    }
    
    public function logout()
    {
        $this->session->unset_userdata("userId");
        $this->session->sess_destroy();
        redirect("/");
        
    }
}

/* End of file */