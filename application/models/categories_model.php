<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/* *****************************************************************************
 * Main categories model.
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
class Categories_model extends CI_Model
{
    
    private static $fields = array(
        "id", "name"
    );
    private static $necessaryFields = array(
        "id", "name"
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
     * Fetches all existing categories from database.
     * @return List of categories
     */
    public function getAll()
    {
        $this->db->order_by("name");
        $res = $this->db->get("categories");
        
        if (!$res || $res->num_rows() == 0)
            return array();
        
        // Fetch all columns and return
        $categories = array();
        foreach ($res->result_array() as $category) {
            ModelHelper::checkNecessaryFields($category, self::$necessaryFields, self::$fields);
            $categories[] = $category;
        }
        
        return $categories;
    }
    
    /**
     * Retrieve one specified category from database.
     * @param int $categoryId
     * @retval array The category data
     * @throws Exception
     */
    public function get($categoryId)
    {
        if (!isset($categoryId) || !is_numeric($categoryId))
            throw new Exception("Categories_model.get: Illegal value '"
                .$categoryId."' for field 'categoryId'");
        
        $res = $this->db->get_where("categories", array("id" => $categoryId));
        if (!$res || $res->num_rows() == 0)
            throw new Exception("Categories_model.get: The requested category "
                ."could not be found");
        
        return $res->row_array();
    }
    
    //==========================================================================
    // INSERT
    
    /**
     * Insert a new category record into database.
     * @param array $category Category data to insert
     * @throws Exception
     */
    public function insert(array $category)
    {
        try {
            ModelHelper::checkNecessaryFields($category, self::$necessaryFields, self::$fields);
            array_shift($category);
            $this->db->insert("categories", $category);
        } catch (Exception $e) { throw $e; }
    }
    
    //==========================================================================
    // UPDATE
    
    /**
     * Update an existing category record.
     * @param int $categoryId
     * @param array $category
     */
    public function update($categoryId, array $category)
    {
        ModelHelper::checkNecessaryFields($category, self::$necessaryFields, self::$fields);
        array_shift($category);
        
        $this->db->where("id", $categoryId);
        $this->db->update("categories", $category);
    }
    
    //==========================================================================
    // DELETE
    
    /**
     * Delete a specified category from database.
     * @param int $categoryId
     * @throws Exception
     */
    public function delete($categoryId)
    {
        if (!isset($categoryId) || !is_numeric($categoryId))
            throw new Exception("Categories_model.delete: Illegal value '"
                .var_export($categoryId, true)."' for field 'categoryId'");
        
        $this->db->delete("categories", array("id" => $categoryId));
    }
    
}

/* End of file */