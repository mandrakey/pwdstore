<?php
/* *****************************************************************************
 * Edit user page.
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

<?=form_open("users/doEdit")?>
<div class="form">
<div class="header">
    <?=lang("users_EditUser")?>
</div>
<div class="body">
    <table style="width: 100%;">
    <tr>
        <td>&nbsp;</td>
        <td>
            <input type="checkbox" name="active" value="1"<?=((intval($user["active"]) == 1) ? " checked" : "")?>> 
            <?=lang("users_UserIsActive")?>
        </td>
    </tr>
    <tr>
        <td><?=lang("users_Level")?>:</td>
        <td>
            <select name="level">
                <option value="0"<?=((intval($user["level"]) == 0) ? " selected" : "")?>><?=lang("users_Administrator")?></option>
                <option value="1"<?=((intval($user["level"]) == 1) ? " selected" : "")?>><?=lang("users_User")?></option>
            </select>
        </td>
    </tr>
    <tr>
        <td><?=lang("users_Name")?>:</td>
        <td><input type="text" name="name" value="<?=$user["name"]?>" disabled></td>
    </tr>
    <tr>
        <td><?=lang("users_EMail")?>:</td>
        <td><input type="text" name="email" value="<?=$user["email"]?>" required></td>
    </tr>
    <tr>
        <td><?=lang("users_Password")?>:</td>
        <td>
            <span id="link_newPassword">
                <a href="javascript:void(0);" onclick="showEditPassword();"><?=lang("users_ChangePassword")?></a><br>
                &nbsp;</span>
            <span id="users_newPassword" style="display: none;">
                <input type="password" name="newPassword" placeholder="<?=lang("users_NewPassword")?>"><br>
                <input type="password" name="newPasswordConfirmation" placeholder="<?=lang("users_ConfirmNewPassword")?>">
            </span>
        </td>
    </tr>
    <tr>
        <td><?=lang("users_Language")?>:</td>
        <td>
            <select name="language">
                <option value="english"<?=(($user["language"] == "english") ? " selected" : "")?>>English</option>
                <option value="german"<?=(($user["language"] == "german") ? " selected" : "")?>>Deutsch</option>
            </select>
        </td>
    </tr>
    <tr>
        <td>&nbsp;</td>
        <td>
            <input type="submit" value="<?=lang("common_Save")?>">
            <input type="button" value="<?=lang("common_Back")?>" onclick="document.location.href='<?=site_url("users")?>';">
        </td>
    </tr>
    </table>
</div></div>
</form>
