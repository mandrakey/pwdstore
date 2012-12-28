<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/* *****************************************************************************
 * Main secrets model.
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
 * Provides functions to manage datasets.
 * Data will be retrieved and saved as associative arrays.
 * @package com.bleuelmedia.banking
 * @author mbleuel <mbleuel@bleuelmedia.com>
 */
class Secrets_model extends CI_Model
{
    
    private static $fields = array(
        "id", "category", "tags", "description", "secret", "comment", "date"
    );
    private static $necessaryFields = array(
        "id", "category", "date"
    );
    
    public static function fields()
    {
        return self::$fields;
    }
    
    public static function necessaryFields()
    {
        return self::$necessaryFields;
    }
    
    //==========================================================================
    // SELECT
    
    /**
     * Fetches all existing secrets from database.
     * If set, only secrets for a specified user are retrieved.
     * @param int $userId Retrieve only secrets for this user. Defaults to null.
     * @return array
     */
    public function get($userId = null)
    {
        $this->db->select("secrets.id, category, tags, description, date, categories.name AS category_name");
        $this->db->join("categories", "secrets.category = categories.id");
        if ($userId != null)
            $this->db->where("user_id", intval($userId));
        $this->db->order_by("category_name");
        $res = $this->db->get("secrets");
        
        if (!$res || $res->num_rows() == 0)
            return array();
        
        // Fetch all columns and return
        $secrets = array();
        foreach ($res->result_array() as $secret) {
            ModelHelper::checkNecessaryFields($secret, self::$necessaryFields, self::$fields, false);
            $secrets[] = $secret;
        }
        
        return $secrets;
    }
    
    /**
     * Retrieve all details about one specific secret.
     * @param int $secretId ID of the secret to retrieve.
     * @retval array Secret data
     * @throws Exception
     */
    public function getDetails($secretId)
    {
        if (!isset($secretId) || !is_numeric($secretId))
            throw new Exception("Secrets_model.getDetails: Illegal value '"
                .var_export($secretId, true)."' for field 'secretId'.");
        
        $this->db->select("secrets.*, categories.name AS category_name");
        $this->db->join("categories", "secrets.category = categories.id");
        $res = $this->db->get_where("secrets", array("secrets.id" => $secretId));
        
        if (!$res || $res->num_rows() == 0)
            throw new Exception("Secrets_model.getDetails: The requested secret "
                ."coult not be found.");
        
        return $res->row_array();
    }
    
    //==========================================================================
    // INSERT
    
    //==========================================================================
    // UPDATE
    
    /**
     * Update a given secret with new data.
     * @param int $secretId The ID of the secret to update
     * @param array $secret The data to update
     * @throws Exception
     */
    public function update($secretId, array $secret)
    {
        if (!isset($secretId) || !is_numeric($secretId))
            throw new Exception("Secrets_model.update: Illegal value '"
                .var_export($secretId, true)."' for field 'secretId'");
        
        // Encrypt secret data
        $secret["secret"] = encryptSecret($secret["secret"]);
        
        $this->db->where("id", $secretId);
        $this->db->update("secrets", $secret);
    }
    
    //==========================================================================
    // DELETE
    
}

/* End of file */