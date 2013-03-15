<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/* *****************************************************************************
 * Pwdstore Secrets File access library
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
 
 class PwdstoreSecretsFile
 {

    // File header
    private $_DUMMY;
    private $version;
    private $secretsCount;
    private $encrypted;

    private $secrets;
    
    /** Holds the current opened file as resource. */
    private $file;
    
    //==========================================================================
    // FACTORY STATICS + PRIVATE CONSTRUCTOR
    
    /**
     * Read a given pwdstore secrets file.
     * @param string $file Path to the file to be read
     * @return PwdstoreSecretsFile
     * @throws InvalidArgumentException
     */
    public static function fromFile($file)
    {
        if (!is_string($file))
            throw new InvalidArgumentException("Illegal value '"
                .var_export($file, true)."' for argument 'file'");
        
        if (!is_readable($file))
            throw new InvalisArgumentException("The file '".$file."' does "
                ."not exist or could not be opened for reading. Please check "
                ."the access rights");
        
        // Create object
        $psfFile = new PwdstoreSecretsFile(1);
        $psfFile->file = $file;
        $psfFile->read();
        return $psfFile;
    }
    
    /**
     * Create an empty PwdstoreSecretsFile object to be filled for later output.
     * @param string $version File format version
     * @param int $encrypted If 1, the "contains encrypted entries" flag inside 
     * output file/text will be set to 1.
     * @return PwdstoreSecretsFile
     */
    public static function create($version, $encrypted = 0)
    {
        if (!is_string($version) || trim($version) == "")
            throw new InvalidArgumentExcpetion("Illegal value '"
                .var_export($version, true)."' for argument 'version'");
        
        $psfFile = new PwdstoreSecretsFile(1);
        $psfFile->version = $version;
        $psfFile->encrypted = $encrypted;
        return $psfFile;
    }
    
    /**
     * Construct an empty PwdstoreSecretsFile.
     * Should not be called directly.
     * @param int $indirectCall If this is null, the constructor was called by 
     * CodeIgniter autoloader and shall do nothing. When constructing an 
     * object, give it any value other than "null".
     */
    public function __construct($indirectCall = null)
    {
        if ($indirectCall == null)
            return;
        
        $this->_DUMMY = null;
        $this->version = null;
        $this->secretsCount = 0;
        $this->encrypted = 0;
        $this->secrets = array();
    }
    
    //==========================================================================
    // PUBLIC INTERFACE
    
    /**
     * Add a new secret to the secrets list and increase secretsCount.
     * @param PwdstoreSecret $secret The secret to add to the list
     * @throws InvalidArgumentException
     */
    public function addSecret($secret)
    {
        if (!is_object($secret))
            throw new InvalidArgumentException("Illegal value '"
                .var_export($secret, true)."' for argument 'secret'");
        
        $this->secrets[] = $secret;
        $this->secretsCount++;
    }
    
    /**
     * Print contents of this secret file to a writable file resource.
     * @param resource $f The file to write to
     * @throws InvalisArgumentException, RuntimeException
     */
    public function toFile($file)
    {
        if (!is_string($file) || trim($file) === "")
            throw new InvalidArgumentException("Illegal value "
                .var_export($file, true)."' for argument 'file'");
        if (!is_writable($file))
            throw new InvalidArgumentException("The path '".$file."' is not "
                ."writable");
        
        $f = fopen($file, "w+b");
        if (!is_resource($f) || !$f)
            throw new RuntimeException("Failed to open file '".$file."' for "
                ."writing");
        
        fwrite($f, "PWDSTORE", 8);
        fwrite($f, $this->version, 8);
        fwrite($f, pack("N", $this->secretsCount), 4);
        fwrite($f, pack("n", $this->encrypted), 2);
        
        for ($i = 0; $i < $this->secretsCount; ++$i) {
            $this->secrets[$i]->toFile($f);
        }
        
        fclose($f);
    }
    
    /**
     * Print contents of this secret to standard output (echo).
     */
    public function toStdout()
    {
        echo "PWDSTORE";
        echo sprintf("%8s", $this->version);
        echo pack("N", $this->secretsCount);
        echo pack("n", $this->encrypted);
        
        for ($i = 0; $i < $this->secretsCount; ++$i)
            $this->secrets[$i]->toStdout();
    }
    
    /**
     * Return contents of this secret as binary string.
     * @return string
     */
    public function toString()
    {
        $res = "PWDSTORE";
        $res .= sprintf("%8s", $this->version);
        $res .= pack("N", $this->secretsCount);
        $res .= pack("n", $this->encrypted);
        
        for ($i = 0; $i < $this->secretsCount; ++$i)
            $res .= $this->secrets[$i]->toString();
        
        return $res;
    }
    
    //==========================================================================
    // PRIVATE METHODS
    
    /** Reads a previous opened file and gets it's content.
     * @throws RuntimeException
     */
    private function read()
    {
        $f = fopen($this->file, "rb");
        if (!is_resource($f) || !$f)
            throw new RuntimeException("Failed to open file '".$this->file."' for "
                ."reading");
        
        fseek($f, 0);
        
        $this->_DUMMY = fread($f, 8);
        if ($this->_DUMMY !== "PWDSTORE")
            throw new RuntimeException("This file does not seem to be a "
                ."pwdstore secrets file");
        
        // Read file header
        $this->version = fread($f, 8);
        $this->secretsCount = intval(implode("", unpack("N", fread($f, 4))));
        $this->encrypted = intval(implode("", unpack("n", fread($f, 2))));
        
        // Read secrets
        for ($i = 0; $i < $this->secretsCount; ++$i) {
            $this->secrets[] = PwdstoreSecret::fromFile($f);
        }
        
        fclose($f);
    }
 }
 
