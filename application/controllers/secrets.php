<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/* *****************************************************************************
 * Main secrets controller.
 * 
 * =============================================================================
 * 
 * Copyright (C) 2013 Maurice Bleuel
 * THIS FILE IS PART OF PWDSTORE
 * 
 * PWDSTORE is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License version 3 as published by the
 * Free Software Foundation.
 * 
 * PWDSTORE is distributed in the hope that it will be useful, but WITHOUT
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
        
        $this->tpl->js("secrets");
        $this->lang->load("common");
        $this->lang->load("secrets");
        
        $this->load->library("PwdstoreSecret");
        $this->load->library("PwdstoreSecretsFile");
    }
    
    public function index()
    {        
        //----
        // Load all secrets for current user and show
        $user = $this->auth->getCurrentUser();
        try {
            $this->load->model("secrets_model");
            $secrets = $this->secrets_model->get($user["id"]);
            $this->tpl->set("secrets", $secrets);
        } catch (Exception $e) {
            $this->tpl->set("title", lang("error_FailedToLoadData"));
            $this->tpl->set("message", $e->getMessage());
            $this->tpl->display("error/error");
            return;
        }
        
        $this->lang->load("secrets");
        $this->tpl->display("secrets");
    }
    
    /**
     * Display all details about one secret.
     * @param int $secretId
     */
    public function view($secretId)
    {
        //----
        // Check secret id
        if (!isset($secretId) || !is_numeric($secretId)) {
            $this->tpl->set("title", lang("error_ParameterError"));
            $this->tpl->set("message", plang("error_IllegalValueForField", 
                array("field" => "secretId", "value" => $secretId)));
            $this->tpl->display("error/error");
            return;
        }
        
        //----
        // Load data
        try {
            $this->load->model("secrets_model");
            $secret = $this->secrets_model->getDetails($secretId);
            $this->tpl->set("secret", $secret);
        } catch (Exception $e) {
            $this->tpl->set("title", lang("error_FailedToLoadData"));
            $this->tpl->set("message", $e->getMessage());
            $this->tpl->display("error/error");
            return;
        }
        
        $this->tpl->display("secrets_view.php");
    }
    
    public function create()
    {
        //----
        // Load data
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
        
        $this->tpl->display("secrets_create.php");
    }
    
    public function doCreate()
    {
        $user = $this->auth->getCurrentUser();
        
        //----
        // Get data from post
        $secret = array(
            "id" => -1,
            "user_id" => intval($user["id"]),
            "category" => $this->input->post("category"),
            "description" => $this->input->post("description"),
            "tags" => $this->input->post("tags"),
            "secret" => $this->input->post("secret"),
            "comment" => $this->input->post("comment"),
            "date" => date("Y-m-d H:i:s")
        );
        
        //----
        // Save data
        $this->db->trans_begin();
        
        $secretId = null;
        try {
            $this->load->model("secrets_model");
            ModelHelper::checkNecessaryFields($secret, $this->secrets_model->necessaryFields(), $this->secrets_model->fields());
            $secretId = $this->secrets_model->insert($secret);
        } catch (Exception $e) {
            $this->tpl->set("title", lang("error_DataUpdateFailed"));
            $this->tpl->set("message", $e->getMessage());
            $this->tpl->display("error/error");
            return;
        }
        
        $this->db->trans_commit();
        
        // Success !
        $this->tpl->set("title", lang("secrets_SecretCreated_Title"));
        $this->tpl->set("message", lang("secrets_SecretCreated_Message")
            ."<br><br><b>".lang("dialog_FurtherActions")."</b><br>"
            ."- <a href=\"".site_url("secrets/create")."\">"
            .lang("secrets_CreateAnotherSecret")."</a>"
            ."- <a href=\"".site_url("secrets/view/".$secretId)."\">"
            .lang("secrets_ViewSecretEntry")."</a><br>"
            ."- <a href=\"".site_url("secrets")."\">"
            .lang("dialog_BackTo")." ".lang("secrets_Secrets")."</a>");
        $this->tpl->display("message");
    }
    
    /**
     * Edit details of a secret entry.
     * @param int $secretId
     */
    public function edit($secretId)
    {
        //----
        // Check secret id
        if (!isset($secretId) || !is_numeric($secretId)) {
            $this->tpl->set("title", lang("error_ParameterError"));
            $this->tpl->set("message", plang("error_IllegalValueForField", 
                array("field" => "secretId", "value" => $secretId)));
            $this->tpl->display("error/error");
            return;
        }
        
        //----
        // Load data
        try {
            $this->load->model("secrets_model");
            $this->load->model("categories_model");
            $secret = $this->secrets_model->getDetails($secretId);
            $categories = $this->categories_model->getAll();
            $this->tpl->set("secret", $secret);
            $this->tpl->set("categories", $categories);
        } catch (Exception $e) {
            $this->tpl->set("title", lang("error_FailedToLoadData"));
            $this->tpl->set("message", $e->getMessage());
            $this->tpl->display("error/error");
            return;
        }
        
        $this->tpl->display("secrets_edit.php");
    }
    
    /**
     * Save changed secret's data.
     * @return type
     */
    public function doEdit()
    {
        //----
        // Get data from post
        $secretId = $this->input->post("secretId");
        $secret = array(
            "id" => $secretId,
            "category" => $this->input->post("category"),
            "description" => $this->input->post("description"),
            "tags" => $this->input->post("tags"),
            "secret" => $this->input->post("secret"),
            "comment" => $this->input->post("comment"),
            "date" => date("Y-m-d H:i:s")
        );
        
        //----
        // Check data
        if (!$secretId || !is_numeric($secretId)) {
            $this->tpl->set("title", lang("error_ParameterError"));
            $this->tpl->set("message", plang("error_IllegalValueForField",
                array("field" => "secretId", "value" => var_export($secretId, true))));
            $this->tpl->display("error/error");
            return;
        }
        
        //----
        // Save data
        try {
            $this->this->load->model("secrets_model");
            ModelHelper::checkNecessaryFields($secret, $this->secrets_model->necessaryFields(), $this->secrets_model->fields());
            $this->this->secrets_model->update(intval($secretId), $secret);
        } catch (Exception $e) {
            $this->tpl->set("title", lang("error_DataUpdateFailed"));
            $this->tpl->set("message", $e->getMessage());
            $this->tpl->display("error/error");
            return;
        }
        
        // Success !
        $this->tpl->set("title", lang("secrets_SecretUpdated_Title"));
        $this->tpl->set("message", lang("secrets_SecretUpdated_Message")
            ."<br><br><b>".lang("dialog_FurtherActions")."</b><br>"
            ."- <a href=\"".site_url("secrets")."\">"
            .lang("dialog_BackTo")." ".lang("secrets_Secrets")."</a>");
        $this->tpl->display("message");
    }
    
    /**
     * Show a confirmation page: Really delete the secret?
     * @param int $secretId The ID of the secret to delete
     * @return type
     */
    public function delete($secretId)
    {
        if (!isset($secretId) || !is_numeric($secretId)) {
            $this->tpl->set("title", lang("error_ParameterError"));
            $this->tpl->set("message", plang("error_IllegalValueForField",
                array("field" => "secretId", "value" => var_export($secretId, true))));
            $this->tpl->display("error/error");
            return;
        }
        
        //----
        // Get secret data
        try {
            $this->load->model("secrets_model");
            $secret = $this->secrets_model->getMinimal(intval($secretId));
            $this->tpl->set("secret", $secret);
        } catch (Exception $e) {
            $this->tpl->set("title", lang("error_FailedToLoadData"));
            $this->tpl->set("message", $e->getMessage());
            $this->tpl->display("error/error");
            return;
        }
        
        $this->tpl->display("secrets_delete");
    }
    
    /**
     * Finally really delete a record.
     */
    public function doDelete()
    {
        $secretId = $this->input->post("secretId");
        if (!$secretId || !is_numeric($secretId)) {
            $this->tpl->set("title", lang("error_ParameterError"));
            $this->tpl->set("message", plang("error_IllegalValueForField", 
                array("field" => "secretId", "value" => var_export($secretId, true))));
            $this->tpl->display("error/error");
            return;
        }
        
        //----
        // Delete the secret
        $this->db->trans_begin();
        
        try {
            $this->load->model("secrets_model");
            $this->secrets_model->delete($secretId);
        } catch (Exception $e) {
            $this->tpl->set("title", lang("error_FailedToDeleteRecord"));
            $this->tpl->set("message", $e->getMessage());
            $this->tpl->display("error/error");
            return;
        }
        
        $this->db->trans_commit();
        
        // Success!
        $this->tpl->set("title", lang("secrets_SecretDeleted_Title"));
        $this->tpl->set("message", lang("secrets_SecretDeleted_Message")
            ."<br><br><b>".lang("dialog_FurtherActions")."</b><br>"
            ."- <a href=\"".site_url("secrets")."\">"
            .lang("dialog_BackTo")." ".lang("secrets_Secrets")."</a>");
        $this->tpl->display("message");
    }
    
    /**
     * Export one or more entries.
     */
    public function export()
    {
        $type = $this->input->post("type");
        $secrets = $this->input->post("secrets");
        
        if (!is_string($type))
            $type = "csv";
        if (!is_string($secrets) || strlen($secrets) == 0) {
            $this->tpl->set("title", lang("error_ParameterError"));
            $this->tpl->set("message", plang("error_IllegalValueForField", 
                array("field" => "secrets", "value" => var_export($secrets, true))));
            $this->tpl->display("error/error");
            return;
        }
        
        $secrets = explode(",", $this->input->post("secrets"));
        for ($i = 0; $i < count($secrets); ++$i)
            $secrets[$i] = intval($secrets[$i]);
        
        $this->load->model("secrets_model");
        $entries = $this->secrets_model->getDetailsIdIn($secrets);
        
        // Create CSV
        if ($type == "csv")
            $data = $this->export_csv($entries);
        else
            $data = $this->export_binary($entries);
        
        foreach ($data["headers"] as $header)
            header($header);
        echo $data["content"];
    }
    
    /**
     * Create file content for binary export.
     * @param array $entries
     * @retval array [headers, content]
     */
    private function export_binary($entries)
    {
        $psfFile = PwdstoreSecretsFile::create("1.0", 1);
        foreach ($entries as $entry) {
            $psfFile->addSecret(PwdstoreSecret::fromArray(array(
                "id" => intval($entry["id"]),
                "userId" => intval($entry["user_id"]),
                "category" => intval($entry["category"]),
                "date" => date("U", strtotime($entry["date"])),
                "tags_length" => strlen($entry["tags"]),
                "description_length" => strlen($entry["description"]),
                "secret_length" => strlen($entry["secret"]),
                "comment_length" => strlen($entry["comment"]),
                "tags" => $entry["tags"],
                "description" => $entry["description"],
                "secret" => $entry["secret"],
                "comment" => $entry["comment"]
            )));
        }
        
        return array(
            "headers" => array(
                "Content-type: application/octet-stream",
                "Content-Disposition: attachment; filename=secrets.pwdstore",
                "Pragma: no-cache",
                "Expires: 0"
            ), 
            "content" => $psfFile->toString());
    }
    
    /**
     * Create file content for CSV export.
     * @param array $entries
     * @retval array [headers, content]
     */
    private function export_csv($entries)
    {
        // Create CSV headings
        $headings = array(
            lang("secrets_Category"),
            lang("secrets_Secret"),
            lang("secrets_Tags"),
            lang("secrets_Description"),
            lang("secrets_Date"),
            lang("secrets_Comment")
        );
        $csv = implode(";", $headings)."\n";
        
        // Add entries to CSV "table"
        foreach ($entries as $entry) {
            $line = array(
                $entry["category_name"],
                decryptSecret($entry["secret"]),
                $entry["tags"],
                $entry["description"],
                date(lang("common_DATE_YMDHI"), strtotime($entry["date"])),
                $entry["comment"]
            );
            $csv .= implode(";", $line)."\n";
        }
        
        // Return everything
        return array(
            "headers" => array(
                "Content-type: text/csv",
                "Content-Disposition: attachment; filename=secrets.csv",
                "Pragma: no-cache",
                "Expires: 0"
            ),
            "content" => $csv
        );
    }
    
}

/* End of file. */
