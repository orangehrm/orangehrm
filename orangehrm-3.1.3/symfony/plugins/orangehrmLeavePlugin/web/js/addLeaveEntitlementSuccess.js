    var abortEmployeeLoad = false;
    var matchingCount = false;
    
    function toggleFilters(show) {
        if (show) {
           $('ol#filter li:not(:first)').show();                
        } else {
            $('ol#filter li:not(:first)').hide();
        }        
    }
    
    
    function updateFilterMatches() {
        
        matchingCount = false;
        
        var params = '';
        
        $('ol#filter li:not(:first)').find('input,select').each(function(index, element) {
            var name = $(this).attr('name');
            name = name.replace('entitlements[filters][', '');
            name = name.replace(']', '');
            var value = $(this).val();

            params = params + '&' + name + '=' + value;
        });
        
        $.ajax({
            type: 'GET',
            url: getCountUrl,
            data: params,
            dataType: 'json',
            success: function(data) {
                filterMatchingEmployees = data;
                
                $('span#ajax_count').remove();
                var text = lang_matchesMany.replace('%count%', data);
                if (data == 1) {
                    text = lang_matchesOne;
                } else if (data == 0) {
                    text = lang_matchesNone;
                }

                matchingCount = data;
                $('ol#filter li:first').append('<span id="ajax_count">(' + text + ')</span>');
            }
        });
    }
    
    function fetchEmployees(offset) {
        var params = '';
        
        if (offset == 0) {
            abortEmployeeLoad = false;
            $('div#employee_list').html(''); 
            
            var progress = matchingCount ? '(0 / ' + matchingCount + ')' : '0';
            $('div#employee_loading').html(lang_Loading + '... ' + progress + ' ').show();
        }
        
        $('ol#filter li:not(:first)').find('input,select').each(function(index, element) {
            var name = $(this).attr('name');
            name = name.replace('entitlements[filters][', '');
            name = name.replace(']', '');
            var value = $(this).val();

            params = params + '&' + name + '=' + value;
        });
        params = params + '&offset=' + offset + '&lt=' + $('#entitlements_leave_type').val() + '&fd='+$('#date_from').val()+ '&td='+ $('#date_to').val()+'&ent='+$('#entitlements_entitlement').val();
        $.ajax({
            type: 'GET',
            url: getEmployeeUrl,
            data: params,
            dataType: 'json',
            success: function(results) {                
                var offset = parseInt(results.offset, 10);
                var pageSize = parseInt(results.pageSize, 10);
                var data = results.data;
                var count = data.length;
                var finishedLoading = true;
                
                if (offset == 0) {
                    if (count == 0) {
                        $('div#employee_list').html(lang_NoResultsFound);
                    } else {
                        $('div#employee_list').html("<table class='table'><tr><th>"+lang_employee+"</th><th>"+lang_old_entitlement+"</th><th>"+lang_new_entitlement+"</th></tr></table>");                    
                    }
                }                
                
                if (count > 0) {
                    var rows = $('div#employee_list table tr').length - 1;

                    var html = '';
                    for (var i = 0; i < count; i++) {
                        rows++;                        
                        var css = rows % 2 ? "even" : "odd";

                        var decodedName = $("<div/>").text(data[i][0]).html();
                        var oldValue = parseFloat(data[i][1]);
                        var newValue = parseFloat(data[i][2]);
                        
                        html = html + '<tr class="' + css + '"><td>'+decodedName+'</td><td>'+oldValue.toFixed(2)+'</td><td>'+newValue.toFixed(2)+'</td></tr>';
                    }

                    $('div#employee_list table.table').append(html);
                    
                    if ((count == pageSize)) {
                        finishedLoading = false;
                        
                        if (!abortEmployeeLoad) {                            
                            fetchEmployees(offset + pageSize);
                        }
                    }
                }
                
                if (finishedLoading) {
                    $('div#employee_loading').html('').hide();
                } else {
                    var progress = matchingCount ? '(' + rows + ' / ' + matchingCount + ') ' : rows;
                    $('div#employee_loading').html(lang_Loading + '... ' + progress + ' ');
                }
            }
        });        
    }

    function updateEmployeeList() {        
        fetchEmployees(0);
    }

    function showProgressDialog(message) {
        $('#buildAssignWait').text(message);
        $('#bulkAssignWaitDlg').modal();        
    }
    
    $(document).ready(function() {               
        
        if(mode == 'update'){
            $('#filter').hide();
        }
        
        if ($('#entitlements_filters_bulk_assign').is(':checked')) {
            toggleFilters(true);    
            $('#entitlements_employee_empName').parent('li').hide();
        } else {
            toggleFilters(false);
        }
        
                
        $('#btnSave').click(function() {
            if ($('#entitlements_filters_bulk_assign').is(':checked')) {
                
                if (filterMatchingEmployees == 0) {
                    $('#noselection').modal();
                } else {
                    var valid = $('#frmLeaveEntitlementAdd').valid();
                    if (valid) {                      
                        updateEmployeeList();
                        
                        $('#preview').modal();
                    }
                }
            } else {
                if(!($('#entitlements_id').val() > 0)){
                    var valid = $('#frmLeaveEntitlementAdd').valid();
                        if (valid) {   
                            var params = '';

                            params = 'empId='+$('#entitlements_employee_empId').val()+'&lt=' + $('#entitlements_leave_type').val() + '&fd='+$('#date_from').val()+ '&td='+ $('#date_to').val()+'&ent='+$('#entitlements_entitlement').val();

                            $.ajax({
                                type: 'GET',
                                url: getEmployeeEntitlementUrl,
                                data: params,
                                dataType: 'json',
                                success: function(data) {                
                                    if( !isNaN(data[0]) && parseFloat(data[0])!=0 ){
                                        
                                        var oldValue = parseFloat(data[0]);
                                        var newValue = parseFloat(data[1]);
                        
                                        $('ol#employee_entitlement_update').html(''); 
                                        var html = '<span>Existing Entitlement value '+ oldValue.toFixed(2)+' will be updated to '+ newValue.toFixed(2) +'</span>'
                                        $('ol#employee_entitlement_update').append(html);
                                        $('#employeeEntitlement').modal();
                                    }else{
                                        var loadingMsg = lang_PleaseWait + '...';
                                        showProgressDialog(loadingMsg);                                            
                                        $('#frmLeaveEntitlementAdd').submit();
                                    }

                                }
                            });

                        }

                    }else{
                        $('#frmLeaveEntitlementAdd').submit();
                    }
            }
        });        
        
        $('#dialogConfirmBtn').click(function() {
            var loadingMsg = lang_BulkAssignPleaseWait.replace('%count%', matchingCount) + '...';
            showProgressDialog(loadingMsg);
            $('#frmLeaveEntitlementAdd').submit();
        });        
        
        $('#bulkAssignCancelBtn').click(function() {
            abortEmployeeLoad = true;
        });
        
        $('#dialogUpdateEntitlementConfirmBtn').click(function() {
            var loadingMsg = lang_PleaseWait + '...';
            showProgressDialog(loadingMsg);            
            $('#frmLeaveEntitlementAdd').submit();
        });

        $('#btnCancel').click(function() {
            window.location.href = listUrl;
        });        
 
        $('#entitlements_filters_bulk_assign').click(function(){     
            
            if ($('span#ajax_count').length == 0) {
                updateFilterMatches();
            }
            
            var checked = $(this).is(':checked');
            toggleFilters(checked);
            if (checked) {
                $('#entitlements_employee_empName').parent('li').hide();
            } else {
                $('#entitlements_employee_empName').parent('li').show();
                $('span#ajax_count').remove();
            }
        });
        
        $('ol#filter li:not(:first)').find('input,select').change(function(){
           updateFilterMatches(); 
        });
        
    $.validator.addMethod("twoDecimals", function(value, element, params) {
        
        var isValid = false;

        var match = value.match(/^\$?([0-9]{1,3},([0-9]{3},)*[0-9]{3}|[0-9]+)(.[0-9][0-9])?$/);
        if(match) {
            isValid = true;
        }
        if (value == ""){
            isValid = true;
        }
        return isValid;
    });        
    
    $.validator.addMethod("checkEmployeeNameNotChanged", function(value, element, params) {
        
        var isValid = true;

        var idField = $('#entitlements_employee_empId');
        if (idField.val() !== '') {
            var inputFieldName = $('#entitlements_employee_empName').val();
            var lastSelectedName = idField.data('item.name');
            
            isValid = ($.trim(inputFieldName) === $.trim(lastSelectedName));
        }

        return isValid;
    });        
 
     $.validator.addMethod("triggerEmpIdValidation", function(value, element, params) {        
        $('#entitlements_employee_empId').valid();
        return true;
    }); 
    
    $('#entitlements_employee_empName').result(function(event, item) {
        $('#entitlements_employee_empId').val(item.id)
            .data('item.name', item.name)
            .valid();
    });
    
        $('#frmLeaveEntitlementAdd').validate({
                ignore: [],
                rules: {
                    'entitlements[employee][empName]': {
                        triggerEmpIdValidation: true,
                        onkeyup: 'if_invalid'
                    },
                    'entitlements[employee][empId]': {
                        required: function(element) {
                            return !$('#entitlements_filters_bulk_assign').is(':checked');
                        },
                        checkEmployeeNameNotChanged: true
                    },
                    'entitlements[leave_type]':{required: true },
                    'entitlements[date][from]': {
                        required: true,
                        valid_date: function() {
                            return {
                                required: true,                                
                                format:datepickerDateFormat,
                                displayFormat:displayDateFormat
                            }
                        }
                    },
                    'entitlements[date][to]': {
                        required: true,
                        valid_date: function() {
                            return {
                                required: true,
                                format:datepickerDateFormat,
                                displayFormat:displayDateFormat
                            }
                        },
                        date_range: function() {
                            return {
                                format:datepickerDateFormat,
                                displayFormat:displayDateFormat,
                                fromDate:$("#date_from").val()
                            }
                        }
                    },
                    'entitlements[entitlement]': {
                        required: true,
                        number: true,
                        twoDecimals: true,
                        remote: {
                            url: validEntitlemnetUrl,
                            data: {
                                id: $('#entitlements_id').val()
                            }
                        }
                    }
                    
                },
                messages: {
                    'entitlements[employee][empId]':{
                        required:lang_required,
                        checkEmployeeNameNotChanged:lang_invalid
                    },
                    'entitlements[leave_type]':{
                        required:lang_required
                    },
                    'entitlements[date_from]':{
                        required:lang_invalidDate,
                        valid_date: lang_invalidDate
                    },
                    'entitlements[date_to]':{
                        required:lang_invalidDate,
                        valid_date: lang_invalidDate ,
                        date_range: lang_dateError
                    },
                    'entitlements[entitlement]': {
                        required: lang_required,
                        number: lang_number,
                        remote : lang_valid_entitlement,
                        twoDecimals: lang_number
                    }                    
            }

        });
        
    });


