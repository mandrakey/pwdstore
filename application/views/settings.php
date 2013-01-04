<?php
/* *****************************************************************************
 * Settings change page.
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

<h1><?=plang("settings_SettingsFor", array("user_name" => $user["name"]))?></h1>

<?=form_open("settings/save")?>
<input type="hidden" name="userId" value="<?=$user["id"]?>">

<div class="form"><div class="body">
    <table style="width: 100%;">
    <tr>
        <td><?=lang("settings_Username")?>:</td>
        <td><input type="text" name="name" value="<?=$user["name"]?>" disabled></td>
    </tr>
    <tr>
        <td><?=lang("settings_EMail")?>:</td>
        <td><input type="text" name="email" value="<?=$user["email"]?>"></td>
    </tr>
    <tr>
        <td><?=lang("settings_Password")?>:</td>
        <td>
            <span id="link_newPassword">
                <a href="javascript:void(0);" onclick="showEditPassword();"><?=lang("settings_ChangePassword")?></a><br>
                &nbsp;</span>
            <span id="settings_newPassword" style="display: none;">
                <input type="password" name="newPassword" placeholder="<?=lang("settings_NewPassword")?>"><br>
                <input type="password" name="newPasswordConfirmation" placeholder="<?=lang("settings_ConfirmNewPassword")?>">
            </span>
        </td>
    </tr>
    <tr>
        <td>&nbsp;</td>
        <td>
            <input type="submit" value="<?=lang("common_Save")?>">
            <input type="button" value="<?=lang("common_Abort")?>" onclick="history.go(-1);">
        </td>
    </tr>
    </table>
</div></div>
</form>
