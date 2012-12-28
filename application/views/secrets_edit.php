<?php
/* *****************************************************************************
 * Secrets detail view page.
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

<?php $even = true; ?>

<?=form_open("secrets/doEdit")?>
<input type="hidden" name="secretId" value="<?=$secret["id"]?>">
<div class="form">
<div class="header">
    <?=lang("secrets_EditSecretEntry")?>
</div>
<div class="body">
    <table style="width: 100%;">
    <tr>
        <td><?=lang("secrets_Category")?>:</td>
        <td>
            <select name="category">
                <? foreach ($categories as $category): ?>
                <option 
                    value="<?=$category["id"].(($category["id"] == $secret["category"]) ? " selected" : "")?>">
                    <?=$category["name"]?></option>
                <? endforeach; ?>
            </select></td>
    </tr>
    <tr>
        <td><?=lang("secrets_Description")?>:</td>
        <td><input type="text" name="description" value="<?=$secret["description"]?>"></td>
    </tr>
    <tr>
        <td><?=lang("secrets_Secret")?>:</td>
        <td><textarea name="secret"><?=decryptSecret($secret["secret"])?></textarea></td>
    </tr>
    <tr>
        <td><?=lang("secrets_Comment")?>:</td>
        <td><textarea name="comment"><?=$secret["comment"]?></textarea></td>
    </tr>
    <tr>
        <td>&nbsp;</td>
        <td>
            <input type="submit" value="Speichern">
            <input type="button" value="Zurück" onclick="document.location.href='<?=site_url("secrets")?>';">
        </td>
    </tr>
    </table>
</div></div>
</form>
