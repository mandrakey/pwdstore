<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/* *****************************************************************************
 * Helper function for model classes.
 * Contains mainly functionality for automatically checking database 
 * results against necessary field lists.
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
 * Functionality to make model handling easier.
 * @package com.bleuelmedia.cas
 * @author mbleuel <mbleuel@bleuelmedia.com> 
 */
class ModelHelper
{
    
    /**
     * Checks a given dataset for matching an array with possible fields.
     * Non-existing fields will be added with null value, existing but unallowed 
     * fields will be removed completely.
     * @param array $dataset Dataset from database or user input
     * @param array $fields Names of fields that may exist
     * @return bool
     */
    public static function checkFieldset(&$dataset, $fields)
    {
        if (!isset($dataset) || !is_array($dataset))
            return false;
        if (!isset($fields) || !is_array($fields))
            return false;
        
        // First, delete unwanted fields from dataset
        foreach ($dataset as $key => $value) {
            if (!in_array($key, $fields))
                unset($dataset[$key]);
        }
        
        // Add missing fields
        foreach ($fields as $key) {
            if (!array_key_exists($key, $dataset))
                $dataset[$key] = null;
        }
        
        return true;
    }
    
    /**
     * Checks a given dataset for necessary fields.
     * Necessary fields must exist and contain a value other than 
     * null. If at least one field is missing, this method throws an exception 
     * containing a list of missing fields as text.
     * @param type $dataset
     * @param type $fields
     * @throws Exception
     */
    public static function checkNecessaryFields(&$dataset, $necessaryFields, $fields = null)
    {
        if (!isset($dataset) || !is_array($dataset))
            throw new Exception("ModelHelper.checkNecessaryFields: Illegal value '"
                .var_export($dataset, true)."' for field 'dataset'.");
        if ((!isset($necessaryFields) || !is_array($necessaryFields)) && !is_null($necessaryFields))
            throw new Exception("ModelHelper.checkNecessaryFields: Illegal value '"
                .var_export($necessaryFields, true)."' for field 'necessaryFields'.");
        
        // We may get empty field list: No necessary fields.
        if ($necessaryFields == null)
            return;
        
        // First make sure no unwanted fields exist / all possible fields 
        // are present
        if (is_array($fields) && !self::checkFieldset($dataset, $fields))
            throw new Exception("ModelHelper.checkNecessaryFields: Failed to "
                ."check existing fields in data model.");
        
        // Now check every necessary field for non-null value
        $missing = array();
        foreach ($necessaryFields as $key)
            if ($dataset[$key] === null)
                $missing[] = "- ".$key;
        
        if (count($missing) > 0)
            throw new Exception("Bitte prüfen Sie folgende Angaben:<br>"
                .implode("<br>", $missing));
    }
    
}

/* End of file */