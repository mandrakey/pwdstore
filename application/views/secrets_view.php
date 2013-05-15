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

<?php $even = true; $i = 0; ?>

<div class="form">
<div class="header">
    <? if (count($secrets) == 1): ?>
    <?=lang("secrets_ViewSecretEntry")?>
    <? else: ?>
    <?=lang("secrets_ViewSecretEntries")?>
    <? endif; ?>
</div>
<div class="body">
    <table style="width: 100%; border-collapse: collapse;">
    <? foreach($secrets as $secret): ?>
    <tr>
        <td><?=lang("secrets_Category")?>:</td>
        <td><?=$secret["category_name"]?></td>
    </tr>
    <tr>
        <td><?=lang("secrets_Description")?>:</td>
        <td><?=$secret["description"]?></td>
    </tr>
    <tr>
        <td><?=lang("secrets_Secret")?>:</td>
        <td><?=decryptSecret($secret["secret"])?></td>
    </tr>
    <tr>
        <td><?=lang("secrets_Comment")?>:</td>
        <td><?=$secret["comment"]?></td>
    </tr>
    <? if ($i < count($secrets) - 1): ?>
    <tr>
        <td colspan="2"><hr></td>
    </tr>
    <? else: ?>
    <tr>
        <td>&nbsp;</td>
        <td>
            <input 
                type="button" 
                value="<?=lang("common_Back")?>" 
                onclick="document.location.href='<?=site_url("secrets")?>';">
        </td>
    </tr>
    <? endif; ?>
    <? $i++; ?>
    <? endforeach; ?>
    </table>
</div></div>
