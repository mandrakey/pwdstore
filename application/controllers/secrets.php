<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/* *****************************************************************************
 * Main secrets controller.
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

class Secrets extends CI_Controller
{
    
    public function index()
    {
        $auth = AuthHelper::getInstance();
        $tpl = Template::getInstance();
        if (!$auth->isLoggedIn()) {
            redirect("login");
            return;
        }
        
        //----
        // Load all secrets for current user and show
        $user = $auth->getCurrentUser();
        try {
            $this->load->model("secrets_model");
            $secrets = $this->secrets_model->get($user["id"]);
            $tpl->set("secrets", $secrets);
        } catch (Exception $e) {
            $tpl->set("title", lang("error_FailedToLoadData"));
            $tpl->set("message", $e->getMessage());
            $tpl->display("error/error");
            return;
        }
        
        $this->lang->load("secrets");
        $tpl->display("secrets");
    }
    
    /**
     * Display all details about one secret.
     * @param int $secretId
     */
    public function view($secretId)
    {
        $auth = AuthHelper::getInstance();
        $tpl = Template::getInstance();
        if (!$auth->isLoggedIn()) {
            redirect("login");
            return;
        }
        
        $this->lang->load("secrets");
        
        //----
        // Check secret id
        if (!isset($secretId) || !is_numeric($secretId)) {
            $tpl->set("title", lang("error_ParameterError"));
            $tpl->set("message", plang("error_IllegalValueForField", 
                array("field" => "secretId", "value" => $secretId)));
            $tpl->display("error/error");
            return;
        }
        
        //----
        // Load data
        try {
            $this->load->model("secrets_model");
            $secret = $this->secrets_model->getDetails($secretId);
            $tpl->set("secret", $secret);
        } catch (Exception $e) {
            $tpl->set("title", lang("error_FailedToLoadData"));
            $tpl->set("message", $e->getMessage());
            $tpl->display("error/error");
            return;
        }
        
        $tpl->display("secrets_view.php");
    }
    
    /**
     * Edit details of a secret entry.
     * @param int $secretId
     */
    public function edit($secretId)
    {
        $auth = AuthHelper::getInstance();
        $tpl = Template::getInstance();
        if (!$auth->isLoggedIn()) {
            redirect("login");
            return;
        }
        
        $this->lang->load("secrets");
        
        //----
        // Check secret id
        if (!isset($secretId) || !is_numeric($secretId)) {
            $tpl->set("title", lang("error_ParameterError"));
            $tpl->set("message", plang("error_IllegalValueForField", 
                array("field" => "secretId", "value" => $secretId)));
            $tpl->display("error/error");
            return;
        }
        
        //----
        // Load data
        try {
            $this->load->model("secrets_model");
            $this->load->model("categories_model");
            $secret = $this->secrets_model->getDetails($secretId);
            $categories = $this->categories_model->getAll();
            $tpl->set("secret", $secret);
            $tpl->set("categories", $categories);
        } catch (Exception $e) {
            $tpl->set("title", lang("error_FailedToLoadData"));
            $tpl->set("message", $e->getMessage());
            $tpl->display("error/error");
            return;
        }
        
        $tpl->display("secrets_edit.php");
    }
    
    public function doEdit()
    {
        $auth = AuthHelper::getInstance();
        $tpl = Template::getInstance();
        if (!$auth->isLoggedIn()) {
            redirect("login");
            return;
        }
        
        $this->lang->load("secrets");
        
        //----
        // Get data from post
        $secretId = $this->input->post("secretId");
        $secret = array(
            "id" => $secretId,
            "category" => $this->input->post("category"),
            "description" => $this->input->post("description"),
            "secret" => $this->input->post("secret"),
            "comment" => $this->input->post("comment"),
            "date" => date("Y-m-d H:i:s")
        );
        
        //----
        // Check data
        if (!$secretId || !is_numeric($secretId)) {
            $tpl->set("title", lang("error_ParameterError"));
            $tpl->set("message", plang("error_IllegalValueForField",
                array("field" => "secretId", "value" => $secretId)));
            $tpl->display("error/error");
            return;
        }
        
        //----
        // Save data
        try {
            $this->load->model("secrets_model");
            ModelHelper::checkNecessaryFields($secret, $this->secrets_model->necessaryFields(), $this->secrets_model->fields());
            $this->secrets_model->update(intval($secretId), $secret);
        } catch (Exception $e) {
            $tpl->set("title", lang("error_DataUpdateFailed"));
            $tpl->set("message", $e->getMessage());
            $tpl->display("error/error");
            return;
        }
        
        // Success !
        $tpl->set("title", lang("secrets_SecretUpdated_Title"));
        $tpl->set("message", lang("secrets_SecretUpdated_Message")
            ."<br><br><b>".lang("dialog_FurtherActions")."</b><br>"
            ."- <a href=\"".site_url("secrets")."\">"
            .lang("dialog_BackTo")." ".lang("secrets_Secrets")."</a>");
        $tpl->display("message");
    }
    
}

/* End of file. */
