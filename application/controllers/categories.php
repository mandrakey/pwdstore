<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/* *****************************************************************************
 * Main categories controller.
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

class Categories extends CI_Controller
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
        
        $this->lang->load("categories");
    }
    
    /**
     * Display all existing categories.
     */
    public function index()
    {        
        try {
            $this->load->model("categories_model");
            $categories = $this->categories_model->getAll();
            $this->tpl->set("categories", $categories);
        } catch (Exception $e) {
            $this->tpl->set("title", lang("error_FailedToLoadData"));
            $this->tpl->set("message", $e->getMessage());
            $this->tpl->display("error/error");
            return;
        }
        
        $this->tpl->display("categories");
    }
    
    /**
     * Show form to create new category.
     */
    public function create()
    {
        $this->tpl->display("categories_create.php");
    }
    
    /**
     * Finally create new category.
     */
    public function doCreate()
    {   
        //----
        // Get data from post
        $category = array(
            "id" => -1,
            "name" => $this->input->post("name")
        );
        
        if (!is_string($category["name"]) || trim($category["name"]) == "") {
            $this->tpl->set("title", lang("error_ParameterError"));
            $this->tpl->set("message", plang("error_IllegalValueForField",
                array("field" => "name", "value" => var_export($category["name"], true))));
            $this->tpl->display("error/error");
            return;
        }
        
        //----
        // Save data
        $this->db->trans_begin();

        try {
            $this->load->model("categories_model");
            $this->categories_model->insert($category);
        } catch (Exception $e) {
            $this->tpl->set("title", lang("error_FailedToCreateRecord"));
            $this->tpl->set("message", $e->getMessage());
            $this->tpl->display("error/error");
            return;
        }
        
        $this->db->trans_commit();
        
        // Success !
        $this->tpl->set("title", lang("categories_CategoryCreated_Title"));
        $this->tpl->set("message", lang("categories_CategoryCreated_Message")
            ."<br><br><b>".lang("dialog_FurtherActions")."</b><br>"
            ."- <a href=\"".site_url("categories/create")."\">"
            .lang("categories_CreateAnotherCategory")."</a><br>"
            ."- <a href=\"".site_url("categories")."\">"
            .lang("dialog_BackTo")." ".lang("categories_Categories")."</a>");
        $this->tpl->display("message");
    }
    
    /**
     * Edit details of a category entry.
     * @param int $categoryId
     */
    public function edit($categoryId)
    {
        //----
        // Check category id
        if (!isset($categoryId) || !is_numeric($categoryId)) {
            $this->tpl->set("title", lang("error_ParameterError"));
            $this->tpl->set("message", plang("error_IllegalValueForField", 
                array("field" => "categoryId", "value" => var_export($categoryId, true))));
            $this->tpl->display("error/error");
            return;
        }
        
        //----
        // Load data
        try {
            $this->load->model("categories_model");
            $category = $this->categories_model->get($categoryId);
            $this->tpl->set("category", $category);
        } catch (Exception $e) {
            $this->tpl->set("title", lang("error_FailedToLoadData"));
            $this->tpl->set("message", $e->getMessage());
            $this->tpl->display("error/error");
            return;
        }
        
        $this->tpl->display("categories_edit.php");
    }
    
    /**
     * Save changed category's data.
     */
    public function doEdit()
    {
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
