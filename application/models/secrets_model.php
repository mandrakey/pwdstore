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
        "id", "category", "tags", "host", "secret", "comment", "date"
    );
    private static $necessaryFields = array(
        "id", "category", "secret", "date"
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
    
    public function getSecrets()
    {
        $this->db->select("id, category, tags, host, date");
        $res = $this->db->get("secrets");
        
        if (!$res || $res->num_rows() == 0)
            return array();
        
        // Fetch all columns and return
        $secrets = array();
        foreach ($res->result_array() as $secret) {
            ModelHelper::checkNecessaryFields($secret, self::$necessaryFields, self::$fields);
            $secrets[] = $secret;
        }
    }
    
    //==========================================================================
    // INSERT
    
    //==========================================================================
    // UPDATE
    
    //==========================================================================
    // DELETE
    
}

/* End of file */