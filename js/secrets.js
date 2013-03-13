/* *****************************************************************************
 * JavaScript functions for secrets controller.
 * 
 * =============================================================================
 * 
 * THIS FILE IS PART OF BLEUELMEDIA PWDSTORE
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

/** Indicates wether clicks on table rows trigger an action or not. */
TR_ONCLICK_EXECUTE = true;

/** Last export checkbox having been clicked. */
LAST_CLICKED_EXPORT = -1;

/**
 * Enable or disable the onClick-event on secret TR elements.
 * @param enable If TRUE, onClick event will be enabled, if FALSE disable.
 * Default is TRUE.
 */
function enableTrClick(enable)
{
    if (typeof(enable) == "undefined" || enable != false)
        enable = true;
    
    TR_ONCLICK_EXECUTE = (enable) ? true : false;
}

/**
 * Handles click on a secretsList TR element.
 * @param obj The clicked HTML element object
 */
function secretsList_trClick(obj)
{
    if (!TR_ONCLICK_EXECUTE)
        return;
    if (typeof(obj) == "undefined" || obj == null)
        return;
    
    var url = $(obj).attr("ref");
    document.location.href=url;
}

/**
 * Handles click on one export checkbox.
 * Can also handle shift+click selecting a range of entries.
 * @param obj The HTML checkbox object being clicked
 * @param e Event data for click-event
 * @throws string
 */
function secretsList_exportClick(obj, e)
{
    if (typeof(obj) == "undefined" || obj == null)
        throw "obj value is not valid";
    if (typeof(e) == "undefined" || e == null)
        throw "e value is not valid";
    
    var exportId = parseInt($(obj)[0].name.replace("export_", ""));
    
    if (e.shiftKey && LAST_CLICKED_EXPORT > -1) {
        var begin = -1;
        var end = -1;
        
        if (exportId > LAST_CLICKED_EXPORT) {
            begin = LAST_CLICKED_EXPORT;
            end = exportId;
        } else {
            begin = exportId;
            end = LAST_CLICKED_EXPORT;
        }
        
        // Mark all entries between exportId and LAST_CLICKED_EXPORT
        $("input[name^=export_]").each(function(nr, obj)
        {
            id = parseInt($(obj)[0].name.replace("export_", ""));
            if (id > begin && id < end) {
                $(obj)[0].checked = !$(obj)[0].checked;
            }
        });
    } else {
        LAST_CLICKED_EXPORT = exportId;
    }
    
    secretsList_displayExportBox();
}

/**
 * Handles click on "select all" checkbox.
 * Either selects or deselects all available entries.
 * @param obj The checkbox "select all"
 * @param e Click event data
 */
function secretsList_selectallClick(obj, e)
{
    if (typeof(obj) == "undefined" || obj == null)
        return;
    
    var doCheck = $(obj)[0].checked;
    $("input[name^=export_]").attr("checked", doCheck);
    
    EXPORT_COUNT = (doCheck) ? $("input[name^=export_]").length : 0;
    secretsList_displayExportBox();
}

/**
 * Displays or hides the entry export box depending on EXPORT_COUNT.
 * The box is only visible if at least one entry is selected.
 */
function secretsList_displayExportBox()
{
    var checkCount = 0;
    $("input[name^=export_]").each(function(nr, obj)
    {
        if (obj.checked)
            checkCount++;
    });
    
    console.log(checkCount);
    if (checkCount > 0)
        $('#secretsList_export').show();
    else
        $('#secretsList_export').hide();
}

function secretsList_startExport(type)
{
    if (typeof(type) == "undefined" || type == null)
        return;
    
    var entryList = [];
    $("input[name^=export_]").each(function(nr, obj)
    {
        if (obj.checked)
            entryList.push($(obj).attr("ref"));
    });
    
    $("input[ref=export][name=type]")[0].value = type;
    $("input[ref=export][name=secrets]")[0].value = entryList.join(",");
    $("form[name=secretsExportForm]")[0].submit();
}

//==============================================================================
// AUTO EXECUTE

$(document).ready(function() {
    secretsList_displayExportBox();
});

