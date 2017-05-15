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

    $('#new_email_address').focusout( function(e) {
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
        if($.trim(new_company_name).length == 0 || $.trim(new_user_name).length == 0 || $.trim(new_email_address).length == 0 || $.trim(new_password).length == 0){
            var prom = ezBSAlert({
                messageText: ajax_object.fillFormMessage,
                alertType: "danger",
                headerText: ajax_object.alertTitle,
                okButtonText: ajax_object.okText
            }).done(function (e) {
                // $("body").append('<div>Callback from alert</div>');
            });
        }
        else{
            var data = {
                request: 'add_new_client',
                action: 'wfd_add_client',
                new_company_name: new_company_name,
                new_user_name: new_user_name,
                new_email_address: new_email_address,
                new_password: new_password
            };
            $.ajax({
                url: ajax_object.ajax_url,
                type: 'POST',
                data: data,
                success: function(response){
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
                            messageText: ajax_object.successMessage,
                            alertType: "success",
                            headerText: response.message,
                            okButtonText: ajax_object.okText
                        }).done(function (e) {
                            $('#add_client').modal('hide');
                            var newRow = $('<tr  data-userid="'+ response.clientId + '">');
                            var cols = "";

                            cols += '<td>' + new_company_name + '</td>';
                            cols += '<td/><td/><td/><td/><td/>';
                            newRow.append(cols);

                            var clientsTable = $('#clients-list');
                            var actionTd = $('<td>', clientsTable)[0];
                            newRow.append(actionTd);
                            clientsTable.append(newRow);

                            $('#add_client_info').data('clientId', response.clientId);
                            $('#add_client_info').modal('show')
                            // $("body").append('<div>Callback from alert</div>');
                        });
                    }
                },
                error: function (response) {
                    ezBSAlert({
                        messageText: response.errorMessage,
                        alertType: "danger",
                        headerText: ajax_object.alertTitle,
                        okButtonText: ajax_object.okText
                    }).done(function (e) {
                        // $("body").append('<div>Callback from alert</div>');
                    });
                },
                dataType:'json'});

        }
    });

    $('#add_clinet_core').click(function (e) {
        var new_street = $('#new_street').val();
        var new_zip = $('#new_zip').val();
        var new_city = $('#new_city').val();
        var new_phone = $('#new_phone').val();
        var new_note = $('#new_note').val();
        if($.trim(new_street).length == 0 || $.trim(new_zip).length == 0 || $.trim(new_city).length == 0 || $.trim(new_phone).length == 0
            || $.trim(new_note).length == 0 ) {
            ezBSAlert({
                messageText: ajax_object.fillFormMessage,
                alertType: "danger",
                headerText: ajax_object.alertTitle,
                okButtonText: ajax_object.okText
            }).done(function (e) {
                // $("body").append('<div>Callback from alert</div>');
            });
        }else{
            var clientId = $('#add_client_info').data('clientId');
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
                function(response){
                    if(response.result == true) {
                        ezBSAlert({
                            messageText: response.message,
                            alertType: "success",
                            headerText: ajax_object.successTitle,
                            okButtonText: ajax_object.okText
                        }).done(function (e) {
                            $('#add_client_info').modal('hide');
                            var updateTarget = $('tr[data-userid="' + clientId + '"]');
                            var td = $('td', updateTarget);
                            td[1].textContent = new_street;
                            td[2].textContent = new_zip;
                            td[3].textContent = new_city;
                            td[4].textContent = new_phone;
                            td[5].textContent = new_note;
                            // $("body").append('<div>Callback from alert</div>');
                        });
                    }
                    else{
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
                .fail(function(response) {
                    alert('Error: ' + response.responseText);
                });

        }
    });

    $('.btn-client-view').click(function (e) {
        setValuesToDialog(this);
        $('#add_clinet_core').prop('disabled', true);
        $('#add_client_info').modal('show');
    });

    $('.btn-client-edit').click(function (e) {
        setValuesToDialog(this);
        $('#add_client_info').modal('show');
    });

    function setValuesToDialog(buttonElem){
        var clientId = $(buttonElem).data('clientId');
        var trElem = $('tr[data-user-id="' + clientId + '"]', $('#clients-list'));
        var tdArray = $('td', trElem);
        $('#new-company').val(tdArray[0].textContent);
        $('#new-street').val(tdArray[1].textContent);
        $('#new-zip').val(tdArray[2].textContent);
        $('#new-city').val(tdArray[3].textContent);
        $('#new-phone').val(tdArray[4].textContent);
        $('#new-note').val(tdArray[5].textContent);
    }

    $('.btn-driver-save').click(function (e) {
        var modalDlg = this.closest('.modal');
        var firstName = $('input[name="fname"]', modalDlg);
        var lastName = $('input[name="lname"]', modalDlg);
        var street = $('input[name="street"]', modalDlg);
        var city = $('input[name="city"]', modalDlg);
        var phone = $('input[name="phone"]', modalDlg);
        var note = $('input[name="note"]', modalDlg);

        if($.trim(firstName.val()).length == 0 || $.trim(lastName.val()).length == 0 || $.trim(street.val()).length == 0 || $.trim(city.val()).length == 0
            || $.trim(phone.val()).length == 0 || $.trim(note.val()).length == 0){
            ezBSAlert({
                messageText: ajax_object.fillFormMessage,
                alertType: "danger",
                headerText: ajax_object.alertTitle,
                okButtonText: ajax_object.okText
            }).done(function (e) {
                // $("body").append('<div>Callback from alert</div>');
            });
        }
        else{
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
                function(response){
                    if(response.result == true) {
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
                    else{
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
                .fail(function(response) {
                alert('Error: ' + response.responseText);
            });

        }

    });
});

function ezBSAlert (options) {
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

    var _show = function(){
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