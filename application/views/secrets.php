<?php
/* *****************************************************************************
 * Secrets overview page.
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

<h1><?=lang("secrets_Secrets")?></h1>

<table class="list">
<thead>
<tr>
    <th><?=lang("secrets_Category")?></th>
    <th><?=lang("secrets_Description")?></th>
    <th><?=lang("secrets_Date")?></th>
    <th>&nbsp;</th>
</tr>
</thead>
<tbody>
<? foreach ($secrets as $secret): ?>
<tr onclick="document.location.href='<?=site_url("secrets/view/".$secret["id"])?>';">
    <td<?=((!$even) ? " class=\"odd\"" : "")?>><?=$secret["category_name"]?></td>
    <td<?=((!$even) ? " class=\"odd\"" : "")?>><?=$secret["description"]?></td>
    <td<?=((!$even) ? " class=\"odd\"" : "")?>><?=date(lang("common_DATE_YMD"), strtotime($secret["date"]))?></td>
    <td<?=((!$even) ? " class=\"odd\"" : "")?>>
        [<a href="<?=site_url("secrets/edit/".$secret["id"])?>"><?=lang("common_Edit")?></a>]<br>
        [<a href="<?=site_url("secrets/delete/".$secret["id"])?>"><?=lang("common_Delete")?></a>]
    </td>
</tr>
<? $even = !$even; ?>
<? endforeach; ?>
</tbody>
</table>
