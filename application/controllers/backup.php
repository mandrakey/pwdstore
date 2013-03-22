<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

/* *****************************************************************************
 * Backup controller.
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

/**
 * Handles export and import of datasets.
 * The following actions are available:
 * - index: Allows to export data (options form)
 * - secretsToFile: Exports chosen secrets to a file
 * - secretsFromFile: Imports secrets from a given file
 */ 
class Backup extends CI_Controller
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
        $this->lang->load("backup");
        
        /*$this->load->library("PwdstoreSecret");
        $this->load->library("PwdstoreSecretsFile");*/
    }
    
    public function index()
    {        
        throw new Exception("Not implemented");
    }
	
	public function secretsToFile($entries, $format = "csv")
	{
		throw new Exception("Not implemented");
	}
	
	public function secretsFromFile($file, $format = "csv")
	{
		throw new Exception("Not implemented");
	}
}

/* End of file. */
