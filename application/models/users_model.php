<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/* *****************************************************************************
 * Main user model.
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
class Users_model extends CI_Model
{
    
    private static $fields = array(
        "id", "name", "password", "email", "active", "level", "language"
    );
    private static $necessaryFields = array(
        "id", "name", "email", "language"
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
     * Retrieves all existing users from database.
     * @param bool $all If true, even inactive users will be retrieved
     * @retval array List of users
     */
    public function getUsers($all = true)
    {
        if ($all)
            $res = $this->db->get("users");
        else
            $res = $this->db->get_where("users", array("active" => "1"));
        
        if (!$res || $res->num_rows() == 0)
            return array();
        
        // Fetch all columns and return
        $users = array();
        foreach ($res->result_array() as $user) {
            ModelHelper::checkNecessaryFields($user, self::$necessaryFields, self::$fields);
            $users[] = $user;
        }
        
        return $users;
    }
    
    /**
     * Retrieve a specific user from database.
     * @param int $id
     * @retval array User data
     * @throws Exception
     */
    public function getUser($id)
    {
        if (!isset($id) || !is_numeric($id))
            throw new Exception("User_model.getUser: Illegal value '"
                .var_export($id, true)."' for field 'id'");
        
        $this->db->select("id, name, email, active, level, language");
        $res = $this->db->get_where("users", array("id" => $id));
        if (!$res || $res->num_rows() == 0)
            return array();
        
        $user = array();
        try {
            $user = $res->row_array();
            ModelHelper::checkNecessaryFields($user, self::$necessaryFields, self::$fields);
        } catch (Exception $e) {
            throw $e;
        }
        
        return $user;
    }
    
    /**
     * Checks given user login data for correctness.
     * @param string $name
     * @param string $password
     * @throws Exception
     * @return int The user's ID
     */
    public function checkLogin($name, $password)
    {
        if (!isset($name) || !is_string($name))
            throw new Exception("User_model.checkLogin: Illegal value '"
                .var_export($name, true)."' for field 'name'");
        if (!isset($password) || !is_string($password))
            throw new Exception("User_model.checkLogin: Illegal value '"
                .var_export($password, true)."' for field 'password'");
        
        //----
        // Check if the user exists
        $this->db->select("id,password")->from("users");
        $this->db->where(array("name" => $name));
        $res = $this->db->get();
        if ($res->num_rows() == 0)
            throw new UserCredentialException("user", "The selected user does not "
                ."exist.");
        
        //----
        // Check the password
        $row = $res->row_array();
        
        if ($row["password"] != sha1($password))
            throw new UserCredentialException("password", "The entered password was "
                ."not correct.");
        
        //----
        // All correct, return the user's ID
        return $row["id"];
    }
    
    //==========================================================================
    // INSERT
    
    /**
     * Insert a new user into the database.
     * @param array $user
     * @return ID of created user
     * @throws Exception
     */
    public function insertUser(array $user)
    {
        if (!isset($user) || !is_array($user))
            throw new Exception("User_model.insertUser: Illegal value '"
                .var_export($user, true)."' for field 'user'");
        
        $userId = null;
        try {
            ModelHelper::checkNecessaryFields($user, self::$necessaryFields, self::$fields);
            
            //----
            // Prepare data
            array_shift($user);
            $this->db->insert("users", $user);
            
            //----
            // Retrieve new ID
            $res = $this->db->get_where(
                "users", 
                array("name" => $user["name"]),
                1);
            
            if ($res->num_rows() == 0) {
                $this->db->trans_rollback();
                throw new Exception("Failed to retrieve new user's ID");
            }
            
            $row = $res->row_array();
            $userId = $row["id"];
        } catch (Exception $e) {
            throw $e;
        }
        
        return $userId;
    }
    
    //==========================================================================
    // UPDATE
    
    /**
     * Save userdata.
     * @param int $user The user to update.
     * @param array $data Data to use.
     */
    public function updateUser($user, $data)
    {
        if (!isset($user) || !is_numeric($user))
            throw new Exception("User_model.insertUser: Illegal value '"
                .var_export($user, true)."' for field 'user'");
        if (!isset($user) || !is_array($data))
            throw new Exception("User_model.insertUser: Illegal value '"
                .var_export($data, true)."' for field 'data'");
        
        $this->db->where("id", $user);
        $this->db->update("users", $data);
    }
    
    //==========================================================================
    // DELETE
    
    /**
     * Delete a given user from the database.
     * @param int $user ID of the user to delete
     * @throws Exception
     */
    public function deleteUser($user)
    {
        if (!isset($user) || !is_numeric($user))
            throw new Exception("User_model.insertUser: Illegal value '"
                .var_export($user, true)."' for field 'user'");
        
        $this->db->delete("users", array("id" => intval($user)));
    }
    
}

/* End of file */