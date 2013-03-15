<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/* *****************************************************************************
 * Pwdstore Secrets File Secret class
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
 
 class PwdstoreSecret
 {

    // Secret header
    private $id; // unsigned int
    private $userId; // unsigned int
    private $category; // unsigned int
    private $date; // unsigned int (timestamp)

    private $tags_length; // unsigned int
    private $description_length; // unsigned int
    private $secret_length; // unsigned int
    private $comment_length; // unsigned int

    private $tags; // string
    private $description; // string
    private $secret; // string
    private $comment; // string
    
    //==========================================================================
    // FACTORY STATICS + PRIVATE CONSTRUCTOR
    
    /**
    * Construct a new secret from an array.
    * @param array $data The data to construct secret from as associative array
    * @return PwdstoreSecret
    * @throws InvalidArgumentException
    */
    public static function fromArray(array $data)
    {
        if (!is_array($data) || count($data) != 12)
            throw new InvalidArgumentException("Illegal value '"
                .var_export($data, true)."' for argument 'data'");

        return new PwdstoreSecret($data);
    }
    
    /**
     * Construct a new secret from a given file.
     * @param resource $f Resource pointing to open file to be read. Cursor is 
     * expected to be at beginning of a secret entry.
     * @return PwdstoreSecret
     * @throws InvalidArgumentException
     */
    public static function fromFile($f)
    {
        if (!is_resource($f))
            throw new InvalidArgumentException("Illegal value '"
                .var_export($f, true)."' for argument 'f'");
        
        $data = array();
        
        $data["id"] = intval(implode("", unpack("N", fread($f, 4))));
        $data["userId"] = intval(implode("", unpack("N", fread($f, 4))));
        $data["category"] = intval(implode("", unpack("N", fread($f, 4))));
        $data["date"] = intval(implode("", unpack("N", fread($f, 4))));
        
        $data["tags_length"] = intval(implode("", unpack("N", fread($f, 4))));
        $data["description_length"] = intval(implode("", unpack("N", fread($f, 4))));
        $data["secret_length"] = intval(implode("", unpack("N", fread($f, 4))));
        $data["comment_length"] = intval(implode("", unpack("N", fread($f, 4))));
        
        $data["tags"] = (intval($data["tags_length"]) > 0) 
            ? fread($f, $data["tags_length"]) 
            : "";
        $data["description"] = (intval($data["description_length"]) > 0) 
            ? fread($f, $data["description_length"]) 
            : "";
        $data["secret"] = (intval($data["secret_length"]) > 0) 
            ? fread($f, $data["secret_length"]) 
            : "";
        $data["comment"] = (intval($data["comment_length"]) > 0) 
            ? fread($f, $data["comment_length"]) 
            : "";
        
        return new PwdstoreSecret($data);
    }
    
    /**
    * Construct a new PwdstoreSecret from an prepared values array.
    * @param array $data
    */
    public function __construct($data = null)
    {
        if ($data == null)
            return;
        
        $this->id = $data["id"];
        $this->userId = $data["userId"];
        $this->category = $data["category"];
        $this->date = $data["date"];

        $this->tags_length = $data["tags_length"];
        $this->description_length = $data["description_length"];
        $this->secret_length = $data["secret_length"];
        $this->comment_length = $data["comment_length"];

        $this->tags = $data["tags"];
        $this->description = $data["description"];
        $this->secret = $data["secret"];
        $this->comment = $data["comment"];
    }
    
    //==========================================================================
    // PUBLIC INTERFACE
    
    /**
     * Print contents of this secret to a writable file resource.
     * @param resource $f The file to write to
     * @throws InvalisArgumentException
     */
    public function toFile($f)
    {
        if (!is_resource($f))
            throw new InvalidArgumentException("Illegal value '"
                .var_export($f, true)."' for argument 'f'");
        
        // Write data
        fwrite($f, pack("N", $this->id), 4);
        fwrite($f, pack("N", $this->userId), 4);
        fwrite($f, pack("N", $this->category), 4);
        fwrite($f, pack("N", $this->date), 4);
        
        fwrite($f, pack("N", $this->tags_length), 4);
        fwrite($f, pack("N", $this->description_length), 4);
        fwrite($f, pack("N", $this->secret_length), 4);
        fwrite($f, pack("N", $this->comment_length), 4);
        
        fwrite($f, $this->tags, $this->tags_length);
        fwrite($f, $this->description, $this->description_length);
        fwrite($f, $this->secret, $this->secret_length);
        fwrite($f, $this->comment, $this->comment_length);
    }
    
    /**
     * Print contents of this secret to standard output (echo).
     */
    public function toStdout()
    {
        echo pack("N", $this->id);
        echo pack("N", $this->userId);
        echo pack("N", $this->category);
        echo pack("N", $this->date);
        
        echo pack("N", $this->tags_length);
        echo pack("N", $this->description_length);
        echo pack("N", $this->secret_length);
        echo pack("N", $this->comment_length);
        
        echo substr($this->tags, 0, $this->tags_length);
        echo substr($this->description, 0, $this->description_length);
        echo substr($this->secret, 0, $this->secret_length);
        echo substr($this->comment, 0, $this->comment_length);
    }
    
    /**
     * Return contents of this secret as binary string.
     * @return string
     */
    public function toString()
    {
        $res = pack("N", $this->id);
        $res .= pack("N", $this->userId);
        $res .= pack("N", $this->category);
        $res .= pack("N", $this->date);
        
        $res .= pack("N", $this->tags_length);
        $res .= pack("N", $this->description_length);
        $res .= pack("N", $this->secret_length);
        $res .= pack("N", $this->comment_length);
        
        $res .= substr($this->tags, 0, $this->tags_length);
        $res .= substr($this->description, 0, $this->description_length);
        $res .= substr($this->secret, 0, $this->secret_length);
        $res .= substr($this->comment, 0, $this->comment_length);
        
        return $res;
    }

}
 
