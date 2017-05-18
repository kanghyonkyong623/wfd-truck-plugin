/**
 * Created by kkuk6 on 5/14/2017.
 */

$(document).ready(function ($) {

    function validateEmail(sEmail) {
        var filter = /^[\w\-\.\+]+\@[a-zA-Z0-9\.\-]+\.[a-zA-z0-9]{2,4}$/;
        if (filter.test(sEmail)) {
            return true;
        }
        else {
            return false;
        }
    }

    $('#new_email_address').focusout(function (e) {
        var emailElem = $('#new_email_address');
        var sEmail = emailElem.val();
        var groupElem = emailElem.closest('.form-group');
        if ($.trim(sEmail).length == 0) {
            groupElem.removeClass('has-success');
            groupElem.addClass('has-error');
            $('#add_new_clinet').prop('disabled', true);
        }
        if (validateEmail(sEmail)) {
            groupElem.addClass('has-success');
            groupElem.removeClass('has-error');
            $('#add_new_clinet').prop('disabled', false);
        }
        else {
            groupElem.removeClass('has-success');
            groupElem.addClass('has-error');
            $('#add_new_clinet').prop('disabled', true);
        }
    });


    $('#generate_pw').click(function (e) {
        $('#new_password').val(randomPassword(12));
    });

    function randomPassword(length) {
        var chars = "abcdefghijklmnopqrstuvwxyz!@#$%^&*()-+<>ABCDEFGHIJKLMNOP1234567890";
        var pass = "";
        for (var x = 0; x < length; x++) {
            var i = Math.floor(Math.random() * chars.length);
            pass += chars.charAt(i);
        }
        return pass;
    }

    $('#add_new_clinet').click(function (e) {

        var new_company_name = $('#new_company_name').val();
        var new_user_name = $('#new_user_name').val();
        var new_email_address = $('#new_email_address').val();
        var new_password = $('#new_password').val();
        if ($.trim(new_company_name).length == 0 || $.trim(new_user_name).length == 0 || $.trim(new_email_address).length == 0 || $.trim(new_password).length == 0) {
            var prom = ezBSAlert({
                messageText: ajax_object.fillFormMessage,
                alertType: "danger",
                headerText: ajax_object.alertTitle,
                okButtonText: ajax_object.okText
            }).done(function (e) {
                // $("body").append('<div>Callback from alert</div>');
            });
        }
        else {
            var dlg = $('#add_client');
            var action = dlg.data('clientId') == undefined ? 'wfd_add_client' : 'wfd_edit_client';
            var data = {
                action: action,
                new_company_name: new_company_name,
                new_user_name: new_user_name,
                new_email_address: new_email_address,
                new_password: new_password,
                client_id: dlg.data('clientId')
            };
            $.post(ajax_object.ajax_url,
                data,
                function (response) {
                    if (response.result != true) {
                        ezBSAlert({
                            messageText: response.errorMessage,
                            alertType: "danger",
                            headerText: ajax_object.alertTitle,
                            okButtonText: ajax_object.okText
                        }).done(function (e) {
                            // $("body").append('<div>Callback from alert</div>');
                        });
                    } else {
                        ezBSAlert({
                            messageText: response.message,
                            alertType: "success",
                            headerText: ajax_object.successTitle,
                            okButtonText: ajax_object.okText
                        }).done(function (e) {
                            if (dlg.modal('hide').data('clientId') == undefined) {
                                var clientsTable = $('#clients-list');
                                var newRow = $('<tr  data-user-id="' + response.clientId + '">');
                                var cols = "";

                                cols += '<td>' + new_company_name + '</td>';
                                cols += '<td/><td/><td/><td/><td/>';

                                var actionsContent = $('.btn-group-client').html();
                                cols += '<td>' + actionsContent + '</td>';

                                newRow.append(cols);
                                $('button[data-client-id]', newRow).data('clientId', response.clientId);

                                clientsTable.append(newRow);

                                $('#new_company').val(data.new_company_name);
                                attachClientActions();
                            }
                            $('input[name="client_id"]', $('#modal_nav_client')).val(response.clientId);
                            $('#form-navigate-client-view').submit();
                            // $("body").append('<div>Callback from alert</div>');
                        });
                    }
                },
                'json').fail(function (response) {
                    ezBSAlert({
                        messageText: response.errorMessage,
                        alertType: "danger",
                        headerText: ajax_object.alertTitle,
                        okButtonText: ajax_object.okText
                    }).done(function (e) {
                        // $("body").append('<div>Callback from alert</div>');
                    });
                });
        }
    });

    $('#add_clinet_core').click(function (e) {
        var new_street = $('#new_street').val();
        var new_zip = $('#new_zip').val();
        var new_city = $('#new_city').val();
        var new_phone = $('#new_phone').val();
        var new_note = $('#new_note').val();
        if ($.trim(new_street).length == 0 || $.trim(new_zip).length == 0 || $.trim(new_city).length == 0 || $.trim(new_phone).length == 0
            || $.trim(new_note).length == 0) {
            ezBSAlert({
                messageText: ajax_object.fillFormMessage,
                alertType: "danger",
                headerText: ajax_object.alertTitle,
                okButtonText: ajax_object.okText
            }).done(function (e) {
                // $("body").append('<div>Callback from alert</div>');
            });
        } else {
            var clientId = $('#modal_nav_client').data('clientId');
            var data = {
                action: 'wfd_update_client',
                clientId: clientId,
                new_street: new_street,
                new_zip: new_zip,
                new_city: new_city,
                new_phone: new_phone,
                new_note: new_note
            };
            $.post(
                ajax_object.ajax_url,
                data,
                function (response) {
                    if (response.result == true) {
                        ezBSAlert({
                            messageText: response.message,
                            alertType: "success",
                            headerText: ajax_object.successTitle,
                            okButtonText: ajax_object.okText
                        }).done(function (e) {
                            $('#modal_nav_client').modal('hide');
                            var updateTarget = $('tr[data-user-id="' + clientId + '"]');
                            var td = $('td', updateTarget);
                            td[1].textContent = new_street;
                            td[2].textContent = new_zip;
                            td[3].textContent = new_city;
                            td[4].textContent = new_phone;
                            td[5].textContent = new_note;
                            // $("body").append('<div>Callback from alert</div>');
                        });
                    }
                    else {
                        ezBSAlert({
                            messageText: response.errorMessage,
                            alertType: "danger",
                            headerText: ajax_object.alertTitle,
                            okButtonText: ajax_object.okText
                        }).done(function (e) {
                            // $("body").append('<div>Callback from alert</div>');
                        });
                    }
                },
                'json'
            )
                .fail(function (response) {
                    alert('Error: ' + response.responseText);
                });

        }
    });

    $('#select-client-page').change(function (e) {
        $('#form-navigate-client-view').prop('action', this.options[this.selectedIndex].value);
        $('#navigate_client_view').prop('disabled', false);
    });

    attachClientActions();

    function attachClientActions(){
        $('.btn-client-view').click(function (e) {
            setClientIdToNavDlg(this);
            $('#form-navigate-client-view').submit();
        });

        $('.btn-client-edit').click(function (e) {
            var trElem = $(this).closest('tr');
            var tdElems = $('td', trElem);
            $('#new_company_name').val(tdElems[0].textContent);
            $('#new_user_name').val(trElem.data('userName'));
            $('#new_email_address').val(trElem.data('emailAddress'));
            $('#new_password').val(trElem.data('word'));
            $('#add_client').data('clientId', trElem.data('userId')).modal('show');
        });

        $('.btn-client-delete').click(function (e) {
            var clientId = $(this).data('clientId');
            ezBSAlert({
                type: "confirm",
                messageText: ajax_object.deleteConformMessage,
                alertType: "info"
            }).done(function (e) {
                if (e == true) {
                    $.post(
                        ajax_object.ajax_url,
                        {
                            action: 'wfd_delete_client',
                            clientId: clientId
                        },
                        function (response) {
                            if (response.result == true) {
                                ezBSAlert({
                                    messageText: response.message,
                                    alertType: "success",
                                    headerText: ajax_object.successTitle,
                                    okButtonText: ajax_object.okText
                                }).done(function (e) {
                                    $('tr[data-user-id="' + clientId + '"]').remove();
                                });
                            }
                            else {
                                ezBSAlert({
                                    messageText: response.errorMessage,
                                    alertType: "danger",
                                    headerText: ajax_object.alertTitle,
                                    okButtonText: ajax_object.okText
                                }).done(function (e) {
                                    // $("body").append('<div>Callback from alert</div>');
                                });
                            }
                        },
                        'json'
                    )
                        .fail(function (response) {
                            alert('Error: ' + response.responseText);
                        });
                }
            });
        });
    }

    function setClientIdToNavDlg(buttonElem, editMode) {
        var clientId = $(buttonElem).data('clientId');
        var formElem = $('#modal_nav_client');
        var inputElem = $('input[name="client_id"]', formElem);
        inputElem.val(clientId);
        if(editMode == true){
            $('input[name="edit_mode"]', formElem).val(true);
        }
    }

    $('.btn-driver-save').click(function (e) {
        var modalDlg = this.closest('.modal');
        var firstName = $('input[name="fname"]', modalDlg);
        var lastName = $('input[name="lname"]', modalDlg);
        var street = $('input[name="street"]', modalDlg);
        var city = $('input[name="city"]', modalDlg);
        var phone = $('input[name="phone"]', modalDlg);
        var note = $('input[name="note"]', modalDlg);

        if ($.trim(firstName.val()).length == 0 || $.trim(lastName.val()).length == 0 || $.trim(street.val()).length == 0 || $.trim(city.val()).length == 0
            || $.trim(phone.val()).length == 0 || $.trim(note.val()).length == 0) {
            ezBSAlert({
                messageText: ajax_object.fillFormMessage,
                alertType: "danger",
                headerText: ajax_object.alertTitle,
                okButtonText: ajax_object.okText
            }).done(function (e) {
                // $("body").append('<div>Callback from alert</div>');
            });
        }
        else {
            var driverId = $(modalDlg).data('driverId');
            var data = {
                action: 'wfd_update_driver',
                driverId: driverId,
                firstName: firstName.val(),
                lastName: lastName.val(),
                street: street.val(),
                city: city.val(),
                phone: phone.val(),
                note: note.val()
            };
            $.post(
                ajax_object.ajax_url,
                data,
                function (response) {
                    if (response.result == true) {
                        ezBSAlert({
                            messageText: response.message,
                            alertType: "success",
                            headerText: ajax_object.successTitle,
                            okButtonText: ajax_object.okText
                        }).done(function (e) {
                            $(modalDlg).modal('hide');
                            var updateTarget = $('tr[driver-id="' + driverId + '"]');
                            var td = $('td', updateTarget);
                            td[1].textContent = data.firstName;
                            td[2].textContent = data.lastName;
                            td[3].textContent = data.street;
                            td[4].textContent = data.city;
                            td[5].textContent = data.phone;
                            td[6].textContent = data.note;
                            // $("body").append('<div>Callback from alert</div>');
                        });
                    }
                    else {
                        ezBSAlert({
                            messageText: response.errorMessage,
                            alertType: "danger",
                            headerText: ajax_object.alertTitle,
                            okButtonText: ajax_object.okText
                        }).done(function (e) {
                            // $("body").append('<div>Callback from alert</div>');
                        });
                    }
                },
                'json'
            )
                .fail(function (response) {
                    alert('Error: ' + response.responseText);
                });

        }

    });

    $('#filter-company').on('keyup', function (selector) {
        var selected = $(this).val();
        $('#filter-zip').val('');
        $('#filter-city').val('');
        filterByText(selected, $('#clients-list'), 0);
    });

    $('#filter-zip').on('keyup', function (selector) {
        var selected = $(this).val();
        $('#filter-company').val('');
        $('#filter-city').val('');
        filterByText(selected, $('#clients-list'), 2);
    });

    $('#filter-city').on('keyup', function (selector) {
        var selected = $(this).val();
        $('#filter-zip').val('');
        $('#filter-company').val('');
        filterByText(selected, $('#clients-list'), 3);
    });

    $('#edit-core-data-toggle').click(function (e) {
        if($(this).attr("class").includes("active") == true) {
            ezBSAlert({
                type: "confirm",
                messageText: ajax_object.saveConformMessage,
                alertType: "info"
            }).done(function (e) {
                if(e != ""){
                    if (e == true) {
                        saveCoreData();
                    }
                    else{

                    }
                }
            });
            $(this).addClass('btn-primary').removeClass('btn-save').html('<span class="glyphicon glyphicon-pencil"></span> Edit');
            activateCoreDataEdit(false);
        }
        else {
            $(this).addClass('btn-save').removeClass('btn-primary').html('<span class="glyphicon glyphicon-save"></span> End Edit');
            activateCoreDataEdit(true);
        }
    });

    function saveCoreData(){
        var coreInfo = getCoreInfo();
        var data = {
            action: 'wfd_truck_save_core',
            coreInfo: JSON.stringify(coreInfo),
            openHours: JSON.stringify(getOpenhours()),
            payment: JSON.stringify(getPayment()),
            partner: JSON.stringify(getPartner()),
            assistance: JSON.stringify(getAssistance()),
            mobi: JSON.stringify(getMobiservice())
        };
        $.post(ajax_object.ajax_url,
            data,
            function (response) {
                if (response.result == true) {
                    ezBSAlert({
                        messageText: response.message,
                        alertType: "success",
                        headerText: ajax_object.successTitle,
                        okButtonText: ajax_object.okText
                    });
                }
                else {
                    ezBSAlert({
                        messageText: response.errorMessage,
                        alertType: "danger",
                        headerText: ajax_object.alertTitle,
                        okButtonText: ajax_object.okText
                    });
                    $('#edit-core-data-toggle').click();
                }
                },
            'json'
        );
    }

    function getCoreInfo(){
        var coreInfoInputs = $('input', $('#core-data-container'));
        var retValues = {};
        $.each(coreInfoInputs, function (index, inputElem) {
            inputElem = $(inputElem);
            retValues[inputElem.prop('name')] = inputElem.val();
        });
        return retValues;
    }

    function getOpenhours(){
        var openingHoursInputs = $('input', $('#opening-hours'));
        var retValues = {};
        $.each(openingHoursInputs, function (index, inputElem) {
            inputElem = $(inputElem);
            retValues[inputElem.prop('name')] = inputElem.val();
        });
        return retValues;
    }
    
    function getPayment() {
        var paymentsInputs = $('input', $('#payment-container'));
        var retValues = [];
        $.each(paymentsInputs, function (index, inputElem) {
            inputElem = $(inputElem);
            if(inputElem.prop('checked') == true){
                retValues.push(inputElem.prop('name'));
            }
        });
        return retValues;
    }
    
    function getPartner() {
        var partnerInputs = $('input', $('#partner-container'));
        var retValues = [];
        $.each(partnerInputs, function (index, inputElem) {
            inputElem = $(inputElem);
            if(inputElem.prop('checked') == true){
                retValues.push(inputElem.prop('name'));
            }
        });
        return retValues;
    }
    
    function getAssistance() {
        var assistanceInputs = $('input', $('#assistance-container'));
        var retValues = [];
        $.each(assistanceInputs, function (index, inputElem) {
            inputElem = $(inputElem);
            if(inputElem.prop('checked') == true){
                retValues.push(inputElem.prop('name'));
            }
        });
        return retValues;
    }

    function getMobiservice(){
        var mobiInputs = $('input', $('#mobi-service-container'));
        var retValues = [];
        $.each(mobiInputs, function (index, inputElem) {
            inputElem = $(inputElem);
            if($.trim(inputElem.val()).length > 0){
                retValues.push(inputElem.val());
            }
        });
        return retValues;
    }

    if(ajax_object.coreDataEditMode == "true"){
        $('#edit-core-data-toggle').click();
    }
    else{
        activateCoreDataEdit(false);
    }

    function activateCoreDataEdit(active){
            $('input', $('#core')).prop('disabled', !active);
            $('#add-assistance').prop('disabled', !active);
            $('#add-mobi-service').prop('disabled', !active);
    }

    $('#add-assistance').click(function (e) {
        ezBSAlert({
            type: "prompt",
            messageText: ajax_object.enterNewAssistanceMessage,
            alertType: "primary"
        }).done(function (e) {
            $('#assistance-container').append('<div class="checkbox"><label><input type="checkbox" name="' + e + '"><span class="cr"><i class="cr-icon glyphicon glyphicon-ok"></i></span>' +
                e + '</label></div>');
        });

    });

    $('#add-mobi-service').click(function (e) {
        var num = 3;
        var placeHolder = "Car dealer";
        var mobiContainer = $('#mobi-service-container');
        var placeHolderTemp = $('input:last', mobiContainer).prop('placeholder');
        var lastSpace = placeHolderTemp.lastIndexOf(' ');
        placeHolder = placeHolderTemp.substr(0, lastSpace);
        num = placeHolderTemp.substr(lastSpace, placeHolderTemp.length-lastSpace);
        num = parseInt(num) + 1;
        $('#mobi-service-container').append('<div class="row form-group"><label class="control-label col-sm-4" style="padding-left: 40px;line-height: 30px;">' +
            num + '.</label><div class="col-sm-8"><input type="text" class="form-control" name="fname" value="" placeholder="' +
            placeHolder + ' ' + num + '"></div></div>')
    });

    function filterByText(text, table, col) {
        var trArray = $('tr', table);
        if (text.toUpperCase() == "ALL") {
            $.each(trArray, function (i, tr) {
                tr.style.display = '';
            });
        }
        else {
            $.each(trArray, function (i, tr) {
                var td = $('td', tr);
                if (td.length == 0) {
                    return;
                }
                if (td[col].textContent.indexOf(text) > -1) {
                    tr.style.display = '';
                }
                else {
                    tr.style.display = 'none';
                }
            });

        }
    }
});

function ezBSAlert(options) {
    var deferredObject = $.Deferred();
    var defaults = {
        type: "alert", //alert, prompt,confirm
        modalSize: 'modal-sm', //modal-sm, modal-lg
        okButtonText: 'Ok',
        cancelButtonText: 'Cancel',
        yesButtonText: 'Yes',
        noButtonText: 'No',
        headerText: 'Attention',
        messageText: 'Message',
        alertType: 'default', //default, primary, success, info, warning, danger
        inputFieldType: 'text' //could ask for number,email,etc
    };
    $.extend(defaults, options);

    var _show = function () {
        var headClass = "navbar-default";
        switch (defaults.alertType) {
            case "primary":
                headClass = "alert-primary";
                break;
            case "success":
                headClass = "alert-success";
                break;
            case "info":
                headClass = "alert-info";
                break;
            case "warning":
                headClass = "alert-warning";
                break;
            case "danger":
                headClass = "alert-danger";
                break;
        }
        $('BODY').append(
            '<div id="ezAlerts" class="modal fade" >' +
            '<div class="modal-dialog ' + defaults.modalSize + '">' +
            '<div class="modal-content">' +
            '<div id="ezAlerts-header" class="modal-header ' + headClass + '">' +
            '<button id="close-button" type="button" class="close" data-dismiss="modal"><span aria-hidden="true">×</span><span class="sr-only">Close</span></button>' +
            '<h4 id="ezAlerts-title" class="modal-title">Modal title</h4>' +
            '</div>' +
            '<div id="ezAlerts-body" class="modal-body">' +
            '<div id="ezAlerts-message" ></div>' +
            '</div>' +
            '<div id="ezAlerts-footer" class="modal-footer">' +
            '</div>' +
            '</div>' +
            '</div>' +
            '</div>'
        );

        $('.modal-header').css({
            'padding': '15px 15px',
            '-webkit-border-top-left-radius': '5px',
            '-webkit-border-top-right-radius': '5px',
            '-moz-border-radius-topleft': '5px',
            '-moz-border-radius-topright': '5px',
            'border-top-left-radius': '5px',
            'border-top-right-radius': '5px'
        });

        $('#ezAlerts-title').text(defaults.headerText);
        $('#ezAlerts-message').html(defaults.messageText);

        var keyb = "false", backd = "static";
        var calbackParam = "";
        switch (defaults.type) {
            case 'alert':
                keyb = "true";
                backd = "true";
                $('#ezAlerts-footer').html('<button class="btn btn-' + defaults.alertType + '">' + defaults.okButtonText + '</button>').on('click', ".btn", function () {
                    calbackParam = true;
                    $('#ezAlerts').modal('hide');
                });
                break;
            case 'confirm':
                var btnhtml = '<button id="ezok-btn" class="btn btn-primary">' + defaults.yesButtonText + '</button>';
                if (defaults.noButtonText && defaults.noButtonText.length > 0) {
                    btnhtml += '<button id="ezclose-btn" class="btn btn-default">' + defaults.noButtonText + '</button>';
                }
                $('#ezAlerts-footer').html(btnhtml).on('click', 'button', function (e) {
                    if (e.target.id === 'ezok-btn') {
                        calbackParam = true;
                        $('#ezAlerts').modal('hide');
                    } else if (e.target.id === 'ezclose-btn') {
                        calbackParam = false;
                        $('#ezAlerts').modal('hide');
                    }
                });
                break;
            case 'prompt':
                $('#ezAlerts-message').html(defaults.messageText + '<br /><br /><div class="form-group"><input type="' + defaults.inputFieldType + '" class="form-control" id="prompt" /></div>');
                $('#ezAlerts-footer').html('<button class="btn btn-primary">' + defaults.okButtonText + '</button>').on('click', ".btn", function () {
                    calbackParam = $('#prompt').val();
                    $('#ezAlerts').modal('hide');
                });
                break;
        }

        $('#ezAlerts').modal({
            show: false,
            backdrop: backd,
            keyboard: keyb
        }).on('hidden.bs.modal', function (e) {
            $('#ezAlerts').remove();
            deferredObject.resolve(calbackParam);
        }).on('shown.bs.modal', function (e) {
            if ($('#prompt').length > 0) {
                $('#prompt').focus();
            }
        }).modal('show');
    };

    _show();
    return deferredObject.promise();
}


// $(document).ready(function(){
//     $("#btnAlert").on("click", function(){
//         var prom = ezBSAlert({
//             messageText: "hello world",
//             alertType: "danger"
//         }).done(function (e) {
//             $("body").append('<div>Callback from alert</div>');
//         });
//     });
//
//     $("#btnConfirm").on("click", function(){
//         ezBSAlert({
//             type: "confirm",
//             messageText: "hello world",
//             alertType: "info"
//         }).done(function (e) {
//             $("body").append('<div>Callback from confirm ' + e + '</div>');
//         });
//     });
//
//     $("#btnPrompt").on("click", function(){
//         ezBSAlert({
//             type: "prompt",
//             messageText: "Enter Something",
//             alertType: "primary"
//         }).done(function (e) {
//             ezBSAlert({
//                 messageText: "You entered: " + e,
//                 alertType: "success"
//             });
//         });
//     });
//
// });