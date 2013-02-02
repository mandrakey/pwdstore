/* *****************************************************************************
 * Common JavaScript functionality.
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

/**
 * Filter some HTML table for a given value.
 * @param event Event object (keyup or click)
 * @param obj HTML Input element containing filter text and defining table 
 * to search by it's <i>ref</i> attribute.
 * @param searchColumns Column numbers to include when checking for matches.
 * From left to right: 0,1,...,n
 */
function filterHtmlTable(event, obj, searchColumns)
{
    if (typeof(searchColumns) == 'undefined' || searchColumns == null || searchColumns.length == 0)
        return;
    if (typeof(obj) == "undefined" || obj == null || $(obj).length == 0)
        return;
    if ($("#"+$(obj).attr("ref")).length == 0)
        return;
    
    if (typeof(event) == "undefined" || event == null)
        event = {type: 'keyup', charCode: 0, keyCode: 13};
    
    //--------------------------------------------------------------------------
    // Execute filtering
    
    if (event.type == 'click')
    {
        var ev = {type: 'keyup', keyCode: 13};
        filterHtmlTable(ev, $('input[name='+$(obj).attr('ref')+'_filter]'), searchColumns);
        return;
    }
    
    //Handle escape key
    if (event.keyCode == 27)
        $(obj)[0].value = '';
    
    var searchedNameUmlauts = $(obj)[0].value;
    var searchedName = escapeString($(obj)[0].value, false);
    var listDomId = $(obj).attr('ref');
    
    //Do we have a field for "only active items"?
    var onlyActive = false;
    if ($('input[name=onlyActive][ref='+listDomId+']').length > 0)
        onlyActive = $('input[name=onlyActive][ref='+listDomId+']')[0].checked;
    
    $('#'+listDomId+' > tbody > tr').each(function(nr, obj)
    {
        
        //isActive exists?
        var isActiveField = $(obj).find('input[ref=isActive]');
        var isActive = (isActiveField != null && isActiveField.length > 0) 
            ? $(isActiveField)[0].checked 
            : true;
        
        var columns = $(obj).children();
        var exp = new RegExp('.*'+searchedName+'.*','ig');
        var expUmlauts = new RegExp('.*'+searchedNameUmlauts+'.*','ig');
        var display = false;
        
        //Check this line against all rules
        for (var i = 0; i < searchColumns.length; ++i)
        {
            if ((searchedName == '' 
                    || exp.test(columns[searchColumns[i]].innerHTML)
                    || expUmlauts.test(columns[searchColumns[i]].innerHTML)) 
                    && (!onlyActive || (onlyActive && isActive)))
            {
                display = true;
                break;
            }
        }
        
        //If no rule matched, hide it and return
        if (!display)
            $(obj).hide();
        else
            $(obj).show();
    });
    
    recreateTableEvenOddMarking($('#'+listDomId+' > tbody > tr'));
}

/**
 * Recreates a tables even/odd marking for TR elements.
 * @param objects An array of TR elements to apply marking
 */
function recreateTableEvenOddMarking(objects)
{
    var rowType = 'odd';
    for (var i = 0; i < objects.length; ++i)
    {
        if ($(objects[i])[0].style.display == 'none')
            continue;
        
        $(objects[i]).children().each(function(nr, td)
        {
            td.style.background = "";
        });
        
        
        if (rowType == 'even')
        {
            $(objects[i]).children().removeClass('odd');
            $(objects[i]).children().addClass('even');
        } else {
            $(objects[i]).children().removeClass('even');
            $(objects[i]).children().addClass('odd');
        }
        
        rowType = (rowType == 'even') ? 'odd' : 'even';
    };
}

/**
 * Escapes special characters in strings like quotes or umlauts.
 * \param text
 * \param escapeBlanks If true, blanks will be changed to underscores. Default: true.
 * \return string
 */
function escapeString(text, escapeBlanks)
{
    if (typeof(escapeBlanks) == "undefined" || escapeBlanks != false)
        escapeBlanks = true;
    
    if (escapeBlanks)
        text = text.replace(/ /g, '_');
    
    text = text.replace(/ä/g, 'ae');
    text = text.replace(/ö/g, 'oe');
    text = text.replace(/ü/g, 'ue');
    text = text.replace(/Ä/g, 'Ae');
    text = text.replace(/Ö/g, 'Oe');
    text = text.replace(/Ü/g, 'Ue');
    return text;
}

