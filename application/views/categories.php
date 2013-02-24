<?php
/* *****************************************************************************
 * Categories list page.
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

<h1><?=lang("categories_Categories")?></h1>

<p><input 
        type="button" 
        onclick="document.location.href='<?=site_url("categories/create")?>'"
        value="<?=lang("categories_CreateCategory")?>"></p>

<div>
    <?=lang("common_FilterList")?>: 
    <input type="text"
           onkeyup="filterHtmlTable(event, this, [0])"
           ref="categoryList"
           id="categoryListFilter"> 
    <img src="<?=base_url()?>/tpl/img/edit-clear.png" alt="<?=lang("common_Clear")?>"
         onclick="filterHtmlTable({type: 'keyup', charCode: 0, keyCode: 27}, $('#categoryListFilter'), [0])"
         style="cursor: pointer;"><br>
    &nbsp;
</div>

<table class="list" id="categoryList">
<thead>
<tr>
    <th><?=lang("categories_Name")?></th>
    <th>&nbsp;</th>
</tr>
</thead>
<tbody>
<? foreach ($categories as $category): ?>
<tr onclick="document.location.href='<?=site_url("categories/edit/".$category["id"])?>';">
    <td<?=((!$even) ? " class=\"odd\"" : "")?>><?=$category["name"]?></td>
    <td<?=((!$even) ? " class=\"odd\"" : "")?>>
        [<a href="<?=site_url("categories/delete/".$category["id"])?>"><?=lang("common_Delete")?></a>]
    </td>
</tr>
<? $even = !$even; ?>
<? endforeach; ?>
</tbody>
</table>
