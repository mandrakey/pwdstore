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

<div>
    <?=lang("common_FilterList")?>: 
    <input type="text"
           onkeyup="filterHtmlTable(event, this, [0,1])"
           ref="secretsList"
           id="secretsListFilter"> 
    <img src="<?=base_url()?>/tpl/img/edit-clear.png" alt="<?=lang("common_Clear")?>"
         onclick="filterHtmlTable({type: 'keyup', charCode: 0, keyCode: 27}, $('#secretsListFilter'), [0,1])"
         style="cursor: pointer;"><br>
    &nbsp;
</div>

<table class="list" id="secretsList">
<thead>
<tr>
    <th class="unimportant"><?=lang("secrets_Category")?></th>
    <th><?=lang("secrets_Description")?></th>
    <th class="unimportant"><?=lang("secrets_Date")?></th>
    <th>&nbsp;</th>
</tr>
</thead>
<tbody>
<? foreach ($secrets as $secret): ?>
<tr onclick="document.location.href='<?=site_url("secrets/view/".$secret["id"])?>';">
    <td class="unimportant<?=((!$even) ? " odd" : "")?>"><?=$secret["category_name"]?></td>
    <td class="<?=((!$even) ? " odd" : "")?>"><?=$secret["description"]?></td>
    <td class="unimportant<?=((!$even) ? " odd" : "")?>"><?=date(lang("common_DATE_YMD"), strtotime($secret["date"]))?></td>
    <td class="<?=((!$even) ? " odd" : "")?>">
        <span class="action">[<a href="<?=site_url("secrets/edit/".$secret["id"])?>"><?=lang("common_Edit")?></a>]</span>
        <span class="action">[<a href="<?=site_url("secrets/delete/".$secret["id"])?>"><?=lang("common_Delete")?></a>]</span>
    </td>
</tr>
<? $even = !$even; ?>
<? endforeach; ?>
</tbody>
</table>
