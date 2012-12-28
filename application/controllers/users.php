<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/* *****************************************************************************
 * Main users controller.
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

class Users extends CI_Controller
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
        
        $this->lang->load("users");
    }
    
    /**
     * Display all existing users.
     */
    public function index()
    {        
        try {
            $this->load->model("users_model");
            $users = $this->users_model->getUsers(true);
            $this->tpl->set("users", $users);
        } catch (Exception $e) {
            $this->tpl->set("title", lang("error_FailedToLoadData"));
            $this->tpl->set("message", $e->getMessage());
            $this->tpl->display("error/error");
            return;
        }
        
        $this->tpl->display("users");
    }
    
    /**
     * Show form to create new user.
     */
    public function create()
    {
        $this->tpl->display("users_create");
    }
    
    /**
     * Finally create new category.
     */
    public function doCreate()
    {
        //----
        // Get data from post
        $user = array(
            "id" => -1,
            "active" => $this->input->post("active"),
            "level" => $this->input->post("level"),
            "name" => $this->input->post("name"),
            "email" => $this->input->post("email"),
            "language" => $this->input->post("language")
        );
        
        //----
        // Check entered password
        $password = $this->input->post("password");
        $passwordConfirmation = $this->input->post("passwordConfirmation");
        if (!$password || !$passwordConfirmation || trim($password) == "" || trim($passwordConfirmation) == "") {
            $this->tpl->set("title", lang("error_ParameterError"));
            $this->tpl->set("message", lang("users_PleaseEnterPasswordAndConfirmation"));
            $this->tpl->display("error/error");
            return;
        } elseif ($password !== $passwordConfirmation) {
            $this->tpl->set("title", lang("error_ParameterError"));
            $this->tpl->set("message", lang("users_PasswordDoesNotMatchConfirmation"));
            $this->tpl->display("error/error");
            return;
        }
        
        $user["password"] = sha1($password);
        
        //----
        // Save data
        $this->db->trans_begin();

        $userId = null;
        try {
            $this->load->model("users_model");
            $userId = $this->users_model->insertUser($user);
        } catch (Exception $e) {
            $this->tpl->set("title", lang("error_FailedToCreateRecord"));
            $this->tpl->set("message", $e->getMessage());
            $this->tpl->display("error/error");
            return;
        }
        
        $this->db->trans_commit();
        
        // Success !
        $this->tpl->set("title", lang("users_UserCreated_Title"));
        $this->tpl->set("message", lang("users_UserCreated_Message")
            ."<br><br><b>".lang("dialog_FurtherActions")."</b><br>"
            ."- <a href=\"".site_url("users")."\">"
            .lang("dialog_BackTo")." ".lang("users_Users")."</a>");
        $this->tpl->display("message");
    }
    
    /**
     * Edit details of a user entry.
     * @param int $userId
     */
    public function edit($userId)
    {
        //----
        // Check user id
        if (!isset($userId) || !is_numeric($userId)) {
            $this->tpl->set("title", lang("error_ParameterError"));
            $this->tpl->set("message", plang("error_IllegalValueForField", 
                array("field" => "userId", "value" => var_export($userId, true))));
            $this->tpl->display("error/error");
            return;
        }
        
        //----
        // Load data
        try {
            $this->load->model("users_model");
            $user = $this->users_model->getUser($userId);
            $this->tpl->set("user", $user);
        } catch (Exception $e) {
            $this->tpl->set("title", lang("error_FailedToLoadData"));
            $this->tpl->set("message", $e->getMessage());
            $this->tpl->display("error/error");
            return;
        }
        
        $this->tpl->js("users");
        $this->tpl->display("users_edit.php");
    }
    
    /**
     * Save changed category's data.
     */
    public function doEdit()
    {
        return;
        //----
        // Get data from post
        $categoryId = $this->input->post("categoryId");
        $category = array(
            "id" => $categoryId,
            "name" => $this->input->post("name")
        );
        
        //----
        // Check data
        if (!$categoryId || !is_numeric($categoryId)) {
            $this->tpl->set("title", lang("error_ParameterError"));
            $this->tpl->set("message", plang("error_IllegalValueForField",
                array("field" => "categoryId", "value" => var_export($categoryId, true))));
            $this->tpl->display("error/error");
            return;
        }
        
        //----
        // Save data
        $this->db->trans_begin();
        
        try {
            $this->load->model("categories_model");
            $this->categories_model->update(intval($categoryId), $category);
        } catch (Exception $e) {
            $this->tpl->set("title", lang("error_DataUpdateFailed"));
            $this->tpl->set("message", $e->getMessage());
            $this->tpl->display("error/error");
            return;
        }
        
        $this->db->trans_commit();
        
        // Success !
        $this->tpl->set("title", lang("categories_CategoryUpdated_Title"));
        $this->tpl->set("message", lang("categories_CategoryUpdated_Message")
            ."<br><br><b>".lang("dialog_FurtherActions")."</b><br>"
            ."- <a href=\"".site_url("categories")."\">"
            .lang("dialog_BackTo")." ".lang("categories_Categories")."</a>");
        $this->tpl->display("message");
    }
    
    /**
     * Show a confirmation page: Really delete the category?
     * @param int $categoryId The ID of the category to delete
     * @return type
     */
    public function delete($categoryId)
    {
        return;
        if (!isset($categoryId) || !is_numeric($categoryId)) {
            $this->tpl->set("title", lang("error_ParameterError"));
            $this->tpl->set("message", plang("error_IllegalValueForField",
                array("field" => "categoryId", "value" => var_export($categoryId, true))));
            $this->tpl->display("error/error");
            return;
        }
        
        //----
        // Get category data
        try {
            $this->load->model("categories_model");
            $category = $this->categories_model->get(intval($categoryId));
            $this->tpl->set("category", $category);
        } catch (Exception $e) {
            $this->tpl->set("title", lang("error_FailedToLoadData"));
            $this->tpl->set("message", $e->getMessage());
            $this->tpl->display("error/error");
            return;
        }
        
        $this->tpl->display("categories_delete");
    }
    
    /**
     * Finally really delete a record.
     */
    public function doDelete()
    {
        return;
        $categoryId = $this->input->post("categoryId");
        if (!$categoryId || !is_numeric($categoryId)) {
            $this->tpl->set("title", lang("error_ParameterError"));
            $this->tpl->set("message", plang("error_IllegalValueForField", 
                array("field" => "categoryId", "value" => var_export($categoryId, true))));
            $this->tpl->display("error/error");
            return;
        }
        
        //----
        // Delete the secret
        $this->db->trans_begin();
        
        try {
            $this->load->model("categories_model");
            $this->categories_model->delete($categoryId);
        } catch (Exception $e) {
            $this->tpl->set("title", lang("error_FailedToDeleteRecord"));
            $this->tpl->set("message", $e->getMessage());
            $this->tpl->display("error/error");
            return;
        }
        
        $this->db->trans_commit();
        
        // Success!
        $this->tpl->set("title", lang("categories_CategoryDeleted_Title"));
        $this->tpl->set("message", lang("categories_CategoryDeleted_Message")
            ."<br><br><b>".lang("dialog_FurtherActions")."</b><br>"
            ."- <a href=\"".site_url("categories")."\">"
            .lang("dialog_BackTo")." ".lang("categories_Categories")."</a>");
        $this->tpl->display("message");
    }
    
}

/* End of file. */
