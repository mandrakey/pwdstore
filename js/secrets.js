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
            console.log("b: " + begin + ", e: " + end);
            if (id > begin && id < end) {
                console.log("match " + id);
                $(obj)[0].checked = !$(obj)[0].checked;
            }
        });        
    } else {
        LAST_CLICKED_EXPORT = exportId;
    }
}

function secretsList_selectallClick(obj, e)
{
    if (typeof(obj) == "undefined" || obj == null)
        return;
    
    var doCheck = $(obj)[0].checked;
    $("input[name^=export_]").attr("checked", doCheck);
}

