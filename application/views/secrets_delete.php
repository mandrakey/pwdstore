<?php
/* *****************************************************************************
 * Delete a secret confirmation page.
 * 
 * =============================================================================
 * 
 * THIS FILE IS PART OF BLEUELMEDIA SIMPLE BANKING
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
?>

<div class="box">
    <div class="body center">
        <strong><?=plang("secrets_ReallyDeleteSecret", array("secretId" => $secret["id"], "description" => $secret["description"], "category" => $secret["category_name"]))?></strong>
        <p>
            <?=form_open("secrets/doDelete")?>
            <input type="hidden" name="secretId" value="<?=$secret["id"]?>">
            
                <input type="submit" value="<?=lang("common_Yes")?>">
                <input type="button" value="<?=lang("common_No")?>" 
                       onclick="document.location.href='<?=site_url("secrets")?>';">
            </form>
        </p>
    </div>
</div>
