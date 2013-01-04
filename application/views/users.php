<?php
/* *****************************************************************************
 * Users list page.
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

<h1><?=lang("users_Users")?></h1>

<p><input 
        type="button" 
        onclick="document.location.href='<?=site_url("users/create")?>'"
        value="<?=lang("users_CreateUser")?>"></p>

<table class="list">
<thead>
<tr>
    <th><?=lang("users_Name")?></th>
    <th><?=lang("users_EMail")?></th>
    <th><?=lang("users_Active")?></th>
    <th><?=lang("users_Level")?></th>
    <th>&nbsp;</th>
</tr>
</thead>
<tbody>
<? foreach ($users as $user): ?>
<tr onclick="document.location.href='<?=site_url("users/edit/".$user["id"])?>';">
    <td<?=((!$even) ? " class=\"odd\"" : "")?>><?=$user["name"]?></td>
    <td<?=((!$even) ? " class=\"odd\"" : "")?>><?=$user["email"]?></td>
    <td<?=((!$even) ? " class=\"odd\"" : "")?>><?=$user["active"]?></td>
    <td<?=((!$even) ? " class=\"odd\"" : "")?>><?=$user["level"]?></td>
    <td<?=((!$even) ? " class=\"odd\"" : "")?>>
        [<a href="<?=site_url("users/delete/".$user["id"])?>"><?=lang("common_Delete")?></a>]
    </td>
</tr>
<? $even = !$even; ?>
<? endforeach; ?>
</tbody>
</table>
