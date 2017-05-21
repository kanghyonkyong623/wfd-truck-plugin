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

    $('.btn-add-truck').click(function (e) {

        $(function () {
            var $checkbox = $('#new_motorcycle');
            $checkbox.prop('checked', false);
        });
        $('#new_truck_id').val('');
        $('#new_brand').val('');
        $('#new_weight').val('');
        $('#new_max_load').val('');
        $('#new_load_height').val('');
        $('#new_truck_type').val('');
        $('#new_status').val('');
        $('#new_pheight').val('');
        $('#new_spec_force').val('');
        $('#new_cable_force').val('');
        $('#new_crane').val('');
        $('#new_plength').val('');
        $('#new_motorcycle').val('');
        $('#new_seats').val('');
        $('#new_under_lift').val('');
        $('#new_out_order').val('');
        $('#profile-pic').attr("src","wp-admin/images/truck_profile_back.jpg");
        $('#truckModalLabel').text('Add New Truck');
        $('#modal_add_truck').data('editMode', false);
        $('#btn_save_truck').prop('disabled', false);
        $('#modal_add_truck').modal('show');
    });

    $('.btn-truck-view').click(function (e) {
        var selId = $(this).data('truckId');
        settruckIdToNavDlg(this, selId);

        $('#modal_add_truck').data('editMode', true);
        $('#modal_add_truck').data('selMode', selId);
        $('#btn_save_truck').prop('disabled', true);
        $('#modal_add_truck').modal('show');
    });

    $('.btn-truck-edit').click(function (e) {
        var selId = $(this).data('truckId');
        settruckIdToNavDlg(this, selId);

        $('#modal_add_truck').data('editMode', true);
        $('#modal_add_truck').data('selMode', selId);
        $('#btn_save_truck').prop('disabled', false);
        $('#modal_add_truck').modal('show');
    });

    $('#btn_save_truck').click(function (e) {

        var new_motorcycle;
        var $checkbox = $('#new_motorcycle');
            if ($checkbox.is(":checked")){
                new_motorcycle='true';
            }else {
                new_motorcycle = 'false';
            }
        var new_truck_id = $('#new_truck_id').val();
        var new_brand = $('#new_brand').val();
        var new_weight = $('#new_weight').val();
        var new_max_load = $('#new_max_load').val();
        var new_load_height = $('#new_load_height').val();
        var new_truck_type = $('#new_truck_type').val();
        var new_status = $('#new_status').val();
        var new_pheight = $('#new_pheight').val();
        var new_spec_force = $('#new_spec_force').val();
        var new_cable_force = $('#new_cable_force').val();
        var new_crane = $('#new_crane').val();
        var new_plength = $('#new_plength').val();
        var new_seats = $('#new_seats').val();
        var new_under_lift = $('#new_under_lift').val();
        var new_out_order = $('#new_out_order').val();
        var new_image=$('image');

        if ($.trim(new_truck_id).length == 0 || $.trim(new_brand).length == 0 || $.trim(new_truck_type).length == 0 || $.trim(new_status).length == 0 ||
            $.trim(new_weight).length == 0 || $.trim(new_max_load).length == 0 || $.trim(new_load_height).length == 0 || $.trim(new_pheight).length == 0 ||
            $.trim(new_spec_force).length == 0 || $.trim(new_cable_force).length == 0 || $.trim(new_crane).length == 0 || $.trim(new_plength).length == 0 ||
            $.trim(new_seats).length == 0 || $.trim(new_under_lift).length == 0)
        {
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
            if ($('#modal_add_truck').data('editMode') == true) {
                var clientId = $('#modal_add_truck').data('clientId');
                var selId= $('#modal_add_truck').data('selMode');
                var data = {
                    action: 'wfd_update_truck',
                    clientId: clientId,
                    selId: selId,
                    new_truck_id: new_truck_id,
                    new_brand: new_brand,
                    new_weight: new_weight,
                    new_max_load: new_max_load,
                    new_load_height: new_load_height,
                    new_truck_type: new_truck_type,
                    new_status: new_status,
                    new_pheight: new_pheight,
                    new_spec_force: new_spec_force,
                    new_cable_force: new_cable_force,
                    new_crane: new_crane,
                    new_plength: new_plength,
                    new_motorcycle: new_motorcycle,
                    new_seats: new_seats,
                    new_under_lift: new_under_lift,
                    new_out_order: new_out_order,
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
                                $('#modal_add_truck').modal('hide');
                                var updateTarget = $('tr[data-truck-id="' + selId + '"]');
                                var td = $('td', updateTarget);
                                td[0].textContent = new_truck_id;
                                td[1].textContent = new_brand;
                                td[2].textContent = new_weight;
                                td[3].textContent = new_max_load;
                                td[4].textContent = new_load_height;
                                td[5].textContent = new_truck_type;
                                td[6].textContent = new_status;
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
            else {
                var clientId = $('#modal_add_truck').data('clientId');
                var data = {
                    action: 'wfd_add_truck',
                    clientId: clientId,
                    new_truck_id: new_truck_id,
                    new_brand: new_brand,
                    new_weight: new_weight,
                    new_max_load: new_max_load,
                    new_load_height: new_load_height,
                    new_truck_type: new_truck_type,
                    new_status: new_status,
                    new_pheight: new_pheight,
                    new_spec_force: new_spec_force,
                    new_cable_force: new_cable_force,
                    new_crane: new_crane,
                    new_plength: new_plength,
                    new_motorcycle: new_motorcycle,
                    new_seats: new_seats,
                    new_under_lift: new_under_lift,
                    new_out_order: new_out_order,
                    new_image: new_image,
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
                                $('#modal_add_truck').modal('hide');
                                var truckTable = $('#truck-list');
                                var newRow = $('<tr data-truck-id="' + response.clientId + '">');
                                var cols = "";

                                cols += '<td>' + new_truck_id + '</td><td>' + new_brand + '</td><td>' + new_weight + '</td><td>' + new_max_load + '</td><td>' + new_load_height + '</td><td>' + new_truck_type + '</td><td>' + new_status + '</td><td><button type="button" class="btn btn-primary btn-truck-view btn-sm"><span class="glyphicon glyphicon-th-list"></button> <button type="button" class="btn btn-primary btn-truck-edit btn-sm"><span class="glyphicon glyphicon-pencil"></button> <button type="button" class="btn btn-primary btn-truck-delete btn-sm"><span class="glyphicon glyphicon-remove"></button> <button type="button" class="btn btn-primary btn-truck-copy btn-sm"><span class="glyphicon glyphicon-save"></button></td>';

                                newRow.append(cols);
                                truckTable.append(newRow);

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
        }
    });

    $('.btn-truck-delete').click(function (e) {
        var selId = $(this).data('truckId');
        ezBSAlert({
            type: "confirm",
            messageText: ajax_object.deleteConformMessage,
            alertType: "info"
        }).done(function (e) {
            if (e == true) {
                $.post(
                    ajax_object.ajax_url,
                    {
                        action: 'wfd_delete_truck',
                        selId: selId,
                    },
                    function (response) {
                        if (response.result == true) {
                            ezBSAlert({
                                messageText: response.message,
                                alertType: "success",
                                headerText: ajax_object.successTitle,
                                okButtonText: ajax_object.okText
                            }).done(function (e) {
                                $('tr[data-truck-id="' + selId + '"]').remove();
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

    $('#btn_save_callnum').click(function (e) {

        var new_name = $('#new_name').val();
        var new_phoneno = $('#new_phoneno').val();
        var new_callnote = $('#new_callnote').val();
        var new_category = $('#new_category').val();
        if ($.trim(new_name).length == 0 || $.trim(new_phoneno).length == 0 || $.trim(new_category).length == 0) {
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
            if ($('#modal_add_callnum').data('editMode') == true) {
                var clientId = $('#modal_add_callnum').data('clientId');
                var selId= $('#modal_add_callnum').data('selMode');
                var data = {
                    action: 'wfd_update_callnum',
                    clientId: clientId,
                    selId: selId,
                    new_name: new_name,
                    new_phoneno: new_phoneno,
                    new_callnote: new_callnote,
                    new_category: new_category,
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
                                $('#modal_add_callnum').modal('hide');
                                var updateTarget = $('tr[data-callnum-id="' + selId + '"]');
                                var td = $('td', updateTarget);
                                td[0].textContent = new_name;
                                td[1].textContent = new_phoneno;
                                td[2].textContent = new_callnote;
                                td[3].textContent = new_category;
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
            else {
                var clientId = $('#modal_add_callnum').data('clientId');
                var data = {
                    action: 'wfd_add_callnum',
                    clientId: clientId,
                    new_name: new_name,
                    new_phoneno: new_phoneno,
                    new_callnote: new_callnote,
                    new_category: new_category,
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
                                $('#modal_add_callnum').modal('hide');
                                var callnumTable = $('#callnum-list');
                                var newRow = $('<tr data-callnum-id="' + response.clientId + '">');
                                var cols = "";

                                cols += '<td>' + new_name + '</td><td>' + new_phoneno + '</td><td>' + new_callnote + '</td><td>' + new_category + '</td><td><button type="button" class="btn btn-primary btn-callnum-edit btn-sm"><span class="glyphicon glyphicon-pencil"></button> <button type="button" class="btn btn-primary btn-callnum-del btn-sm"><span class="glyphicon glyphicon-remove"></button></td>';

                                newRow.append(cols);
                                callnumTable.append(newRow);

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
        }
    });

    $('.btn-add-callnum').click(function (e) {
        $('#new_name').val('');
        $('#new_phoneno').val('');
        $('#new_callnote').val('');
        $('#new_category').val('');
        $('#callnumModalLabel').text('Add New Call Number');
        $('#modal_add_callnum').data('editMode', false);
        $('#modal_add_callnum').modal('show');
    });

    $('.btn-callnum-edit').click(function (e) {
        var selId = $(this).data('callnumId');
        setcallnumIdToNavDlg(this, true);
        $('#modal_add_callnum').data('editMode', true);
        $('#callnumModalLabel').text('Call Number Edit');
        $('#modal_add_callnum').data('selMode', selId);
        $('#modal_add_callnum').modal('show');
    });

    $('.btn-callnum-delete').click(function (e) {
        var selId = $(this).data('callnumId');
        ezBSAlert({
            type: "confirm",
            messageText: ajax_object.deleteConformMessage,
            alertType: "info"
        }).done(function (e) {
            if (e == true) {
                $.post(
                    ajax_object.ajax_url,
                    {
                        action: 'wfd_delete_callnum',
                        selId: selId,
                    },
                    function (response) {
                        if (response.result == true) {
                            ezBSAlert({
                                messageText: response.message,
                                alertType: "success",
                                headerText: ajax_object.successTitle,
                                okButtonText: ajax_object.okText
                            }).done(function (e) {
                                $('tr[data-callnum-id="' + selId + '"]').remove();
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

    $('#btn_save_service').click(function (e) {

        var new_service = $('#new_service').val();
        var new_description = $('#new_description').val();
        var new_price = $('#new_price').val();
        if ($.trim(new_service).length == 0 || $.trim(new_description).length == 0 || $.trim(new_price).length == 0) {
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
            if ($('#modal_add_service').data('editMode') == true) {
                    var clientId = $('#modal_add_service').data('clientId');
                    var selId= $('#modal_add_service').data('selMode');
                    var data = {
                        action: 'wfd_update_service',
                        clientId: clientId,
                        selId: selId,
                        new_service: new_service,
                        new_description: new_description,
                        new_price: new_price,
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
                                    $('#modal_add_service').modal('hide');
                                    var updateTarget = $('tr[data-service-id="' + selId + '"]');
                                    var td = $('td', updateTarget);
                                    td[0].textContent = new_service;
                                    td[1].textContent = new_description;
                                    td[2].textContent = new_price;
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
            else {
                var clientId = $('#modal_add_service').data('clientId');
                var data = {
                    action: 'wfd_add_service',
                    clientId: clientId,
                    new_service: new_service,
                    new_description: new_description,
                    new_price: new_price,
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
                                $('#modal_add_service').modal('hide');
                                var serviceTable = $('#service-list');
                                var newRow = $('<tr  data-service-id="' + response.clientId + '">');
                                var cols = "";

                                cols += '<td>' + new_service + '</td><td>' + new_description + '</td><td>' + new_price + '</td><td><button type="button" class="btn btn-primary btn-service-edit btn-sm"><span class="glyphicon glyphicon-pencil"></button> <button type="button" class="btn btn-primary btn-service-del btn-sm"><span class="glyphicon glyphicon-remove"></button></td>';

                                newRow.append(cols);
                                serviceTable.append(newRow);

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
        }
    });

    $('.btn-add-service').click(function (e) {
        $('#new_service').val('');
        $('#new_description').val('');
        $('#new_price').val('');
        $('#serviceModalLabel').text('Add New Service');
        $('#modal_add_service').data('editMode', false);
        $('#modal_add_service').modal('show');
    });

    $('.btn-service-edit').click(function (e) {
        var selId = $(this).data('serviceId');
        setserviceIdToNavDlg(this, true);
        $('#modal_add_service').data('editMode', true);
        $('#serviceModalLabel').text('Service Edit');
        $('#modal_add_service').data('selMode', selId);
        $('#modal_add_service').modal('show');
    });

    $('.btn-service-delete').click(function (e) {
        var selId = $(this).data('serviceId');
        ezBSAlert({
            type: "confirm",
            messageText: ajax_object.deleteConformMessage,
            alertType: "info"
        }).done(function (e) {
            if (e == true) {
                $.post(
                    ajax_object.ajax_url,
                    {
                        action: 'wfd_delete_service',
                        selId: selId,
                    },
                    function (response) {
                        if (response.result == true) {
                            ezBSAlert({
                                messageText: response.message,
                                alertType: "success",
                                headerText: ajax_object.successTitle,
                                okButtonText: ajax_object.okText
                            }).done(function (e) {
                                $('tr[data-service-id="' + selId + '"]').remove();
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

    function settruckIdToNavDlg(buttonElem, selId) {
        var selId = selId;
        var updateTarget = $(buttonElem).closest('tr[data-truck-id="' + selId + '"]');
        var td = $('td', updateTarget);
        $('#new_truck_id').val(td[0].textContent);
        $('#new_brand').val(td[1].textContent);
        $('#new_weight').val(td[2].textContent);
        $('#new_max_load').val(td[3].textContent);
        $('#new_load_height').val(td[4].textContent);
        $('#new_truck_type').val(td[5].textContent);
        $('#new_status').val(td[6].textContent);
        $('#new_pheight').val(updateTarget.data('truckPheight'));
        $('#new_spec_force').val(updateTarget.data('truckSpecforce'));
        $('#new_plength').val(updateTarget.data('truckPlength'));
        $('#new_cable_force').val(updateTarget.data('truckCableforce'));
        $('#new_crane').val(updateTarget.data('truckCrane'));
        $('#new_seats').val(updateTarget.data('truckSeats'));
        $('#new_under_lift').val(updateTarget.data('truckUnderlift'));
        $('#truckModalLabel').text('Truck: ' + td[0].textContent + '-' + td[1].textContent);
        var motorcheck = updateTarget.data('truckMotorcycle');
        var $checkbox = $('#new_motorcycle');
        if (motorcheck==true){
            $checkbox.prop('checked', true);
        }else {
            $checkbox.prop('checked', false);
        }
    }

    function setcallnumIdToNavDlg(buttonElem, editMode) {
        var selId = $(buttonElem).data('callnumId');
        var updateTarget = $(buttonElem).closest('tr[data-callnum-id="' + selId + '"]');
        var td = $('td', updateTarget);
        $('#new_name').val(td[0].textContent);
        $('#new_phoneno').val(td[1].textContent);
        $('#new_callnote').val(td[2].textContent);
        $('#new_category').val(td[3].textContent);
    }

    function setserviceIdToNavDlg(buttonElem, editMode) {
        var selId = $(buttonElem).data('serviceId');
        var updateTarget = $(buttonElem).closest('tr[data-service-id="' + selId + '"]');
        var td = $('td', updateTarget);
        $('#new_service').val(td[0].textContent);
        $('#new_description').val(td[1].textContent);
        $('#new_price').val(td[2].textContent);
    }

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

    //region Driver
    $('#driver-save').click(function (e) {
        var driverDlg = $('#modal-driver');
        var mode = driverDlg.data('mode');
        var driverId = driverDlg.data('driverId');

        switch (mode){
            case "view":
                enableDriverEdit(false);
                break;
            case "edit":
            case "new":
                var coreDataForm = $('#driver-core-data');
                var coreDataFields = $('input', coreDataForm);
                var coreData = {};
                $.each(coreDataFields, function (i, field) {
                    field = $(field);
                    coreData[field.prop('name')] = field.val();
                });

                var appFields = $('input', $('#driver-application-form'));
                var appData = {};
                $.each(appFields, function (i, field) {
                    field = $(field);
                    appData[field.prop('name')] = field.val();
                });

                var licenseFields = $('input', $('#driver-license-form'));
                var licenseData = {};
                $.each(licenseFields, function (i, field) {
                    field = $(field);
                    if(field.prop('checked') == true){
                        licenseData[field.prop('name')] = 1;
                    }
                    else{
                        licenseData[field.prop('name')] = 0;
                    }
                });

                var qualificationFields = $('input', $('#driver-qualification-form'));
                var qualificationData = {};
                $.each(qualificationFields, function (i, field) {
                    field = $(field);
                    if(field.prop('checked') == true){
                        qualificationData[field.prop('name')] = 1;
                    }
                    else {
                        qualificationData[field.prop('name')] = 0;
                    }
                });

                $.post(
                    ajax_object.ajax_url,
                    {
                        action: 'wfd_driver_save',
                        mode: mode,
                        driverId: driverId,
                        coreData: JSON.stringify(coreData),
                        applicationData: JSON.stringify(appData),
                        licenseData: JSON.stringify(licenseData),
                        qualificationData: JSON.stringify(qualificationData)
                    },
                    function (response) {
                        if (response.result == true) {
                            ezBSAlert({
                                messageText: response.message,
                                alertType: "success",
                                headerText: ajax_object.successTitle,
                                okButtonText: ajax_object.okText
                            }).done(function (e) {
                                driverDlg.modal('hide');
                                if(mode=='new'){
                                    var driversTable = $('#drivers-table');
                                    var newRow = $('<tr  data-driver-id="' + response.driverId + '">');
                                    var cols = "";

                                    cols += '<td>' + coreData['fname'] + '</td>';
                                    cols += '<td>' + coreData['lname'] + '</td><td>' + coreData['street'] + '</td><td>' + coreData['city'] + '</td><td>' + coreData['phone'] + '</td><td>' + coreData['note'] + '</td>';

                                    var actionsContent = '<div class="btn-group-driver"><button type="button" class="btn btn-primary btn-sm btn-driver-view"><span class="glyphicon glyphicon-th-list"></span></button><button type="button" class="btn btn-primary btn-sm btn-driver-edit"><span class="glyphicon glyphicon-pencil"></span></button><button type="button" class="btn btn-primary btn-sm btn-driver-delete"><span class="glyphicon glyphicon-remove"></span></button><button type="button" class="btn btn-primary btn-sm btn-driver-copy"><span class="glyphicon glyphicon-asterisk"></span></button></div>';
                                    cols += '<td>' + actionsContent + '</td>';

                                    newRow.append(cols);
                                    $('button[data-client-id]', newRow).data('clientId', response.clientId);

                                    driversTable.append(newRow);
                                    attachDriverActions();

                                }else{
                                    var updateTarget = $('tr[data-driver-id="' + driverId + '"]');
                                    var td = $('td', updateTarget);
                                    td[0].textContent = coreData['fname'];
                                    td[1].textContent = coreData['lname'];
                                    td[2].textContent = coreData['street'];
                                    td[3].textContent = coreData['city'];
                                    td[4].textContent = coreData['phone'];
                                    td[5].textContent = coreData['note'];
                                }
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
                break;
        }
    });

    $('#btn-add-driver').click(function (e) {
        var driverDlg = $('#modal-driver');
        enableDriverEdit(false);
        driverDlg.data('mode', 'new').modal('show');
    });

    attachDriverActions();

    function attachDriverActions(){
        $('.btn-driver-view').click(function (e) {
            var trElem = $(this).closest('tr');
            var driverId = trElem.data('driverId');

            $.post(ajax_object.ajax_url,
                {
                    action: 'wfd_get_driver_detail',
                    driverId: driverId
                },
                function (response) {
                    setDriverDetailsToDlg(response);
                    $('#driver-save').html('<span class="glyphicon glyphicon-pencil"></span> Edit');

                    enableDriverEdit(true);
                    $('#modal-driver')
                        .data('mode', 'view')
                        .data('driverId', driverId)
                        .modal('show');
                },
                'json'
            );
        });

        $('.btn-driver-edit').click(function (e) {
            var trElem = $(this).closest('tr');
            var driverId = trElem.data('driverId');

            $.post(ajax_object.ajax_url,
                {
                    action: 'wfd_get_driver_detail',
                    driverId: driverId
                },
                function (response) {
                    setDriverDetailsToDlg(response);
                    enableDriverEdit(false);
                    $('#modal-driver')
                        .data('mode', 'edit')
                        .data('driverId', driverId)
                        .modal('show');
                },
                'json'
            );
        });

        $('.btn-driver-delete').click(function (e) {
            var driverId = $(this).closest('tr').data('driverId');
            ezBSAlert({
                type: "confirm",
                messageText: ajax_object.deleteConformMessage,
                alertType: "info"
            }).done(function (e) {
                if (e == true) {
                    $.post(
                        ajax_object.ajax_url,
                        {
                            action: 'wfd_delete_driver',
                            driverId: driverId
                        },
                        function (response) {
                            if (response.result == true) {
                                ezBSAlert({
                                    messageText: response.message,
                                    alertType: "success",
                                    headerText: ajax_object.successTitle,
                                    okButtonText: ajax_object.okText
                                }).done(function (e) {
                                    $('tr[data-driver-id="' + driverId + '"]').remove();
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

    function enableDriverEdit(desabled) {
        if(desabled){
            $('#driver-save').html('<span class="glyphicon glyphicon-pencil"></span> Edit');
        }
        else{
            $('#driver-save').html('<span class="glyphicon glyphicon-floppy-disk"></span> Save');
        }
        $('input', $('#modal-driver')).prop('disabled', desabled);
        $('input', $('#driver-application-form')).rating('create');
        $('#modal-driver').data('mode', 'edit');
    }

    function setDriverDetailsToDlg(driverDetails){
        var targetDlg = $('#modal-driver');
        $('input[name="fname"]', targetDlg).val(driverDetails['fname']);
        $('input[name="lname"]', targetDlg).val(driverDetails['lname']);
        $('input[name="street"]', targetDlg).val(driverDetails['street']);
        $('input[name="city"]', targetDlg).val(driverDetails['city']);
        $('input[name="phone"]', targetDlg).val(driverDetails['phone']);
        $('input[name="note"]', targetDlg).val(driverDetails['note']);
        $('input[name="breakdown"]', targetDlg).val(driverDetails['breakdown_rating']).rating('create');
        $('input[name="drag-cars"]', targetDlg).val(driverDetails['dragcar_rating']).rating('create');
        $('input[name="drag-less-7"]', targetDlg).val(driverDetails['dragless_rating']).rating('create');
        $('input[name="drag-more-7"]', targetDlg).val(driverDetails['dragmore_rating']).rating('create');
        $('input[name="crane"]', targetDlg).val(driverDetails['crane_rating']).rating('create').rating('create');
        $('input[name="truck-service"]', targetDlg).val(driverDetails['truckservice_rating']).rating('create');

        $('input[name="c1"]', targetDlg).prop('checked', driverDetails['c1_license'] == "1");
        $('input[name="c1e"]', targetDlg).prop('checked', driverDetails['c1e_license'] == "1");
        $('input[name="crane-lic"]', targetDlg).prop('checked', driverDetails['crane_license']== "1");
        $('input[name="kennz95"]', targetDlg).prop('checked', driverDetails['kennz_license']== "1");
        $('input[name="club-mobil"]', targetDlg).prop('checked', driverDetails['clubmobile_license']== "1");
        $('input[name="car-opening"]', targetDlg).prop('checked', driverDetails['caropening_license']== "1");
        $('input[name="motor-mechatronics"]', targetDlg).prop('checked', driverDetails['motormech_qual']== "1");
        $('input[name="motor-foreman"]', targetDlg).prop('checked', driverDetails['motorfore_qual']== "1");
        $('input[name="learned"]', targetDlg).prop('checked', driverDetails['learned_qual']== "1");
        $('input[name="unlearned"]', targetDlg).prop('checked', driverDetails['unlearned_qual']== "1");
        $('input[name="commercial"]', targetDlg).prop('checked', driverDetails['commercial_qual']== "1");
    }
    //endregion

    //region Pickup Driver

    $('#btn-add-pickup-driver').click(function (e) {
        var driverDlg = $('#modal-pickup-driver');
        enablePickupDriverEdit(false);
        driverDlg.data('mode', 'new').modal('show');
    });


    attachPickupDriverActions();

    function attachPickupDriverActions(){
        $('.btn-pickup-driver-view').click(function (e) {
            var trElem = $(this).closest('tr');
            var driverId = trElem.data('pickupDriverId');

            $.post(ajax_object.ajax_url,
                {
                    action: 'wfd_get_pickup_driver_detail',
                    pickupDriverId: driverId
                },
                function (response) {
                    setPickupDriverDetailsToDlg(response);
                    $('#pickup-driver-save').html('<span class="glyphicon glyphicon-pencil"></span> Edit');

                    enablePickupDriverEdit(true);
                    $('#modal-pickup-driver')
                        .data('mode', 'view')
                        .data('pickupDriverId', driverId)
                        .modal('show');
                },
                'json'
            );
        });

        $('.btn-pickup-driver-edit').click(function (e) {
            var trElem = $(this).closest('tr');
            var driverId = trElem.data('pickupDriverId');

            $.post(ajax_object.ajax_url,
                {
                    action: 'wfd_get_pickup_driver_detail',
                    pickupDriverId: driverId
                },
                function (response) {
                    setPickupDriverDetailsToDlg(response);
                    enablePickupDriverEdit(false);
                    $('#modal-pickup-driver')
                        .data('mode', 'edit')
                        .data('pickupDriverId', driverId)
                        .modal('show');
                },
                'json'
            );
        });

        $('.btn-pickup-driver-delete').click(function (e) {
            var driverId = $(this).closest('tr').data('pickupDriverId');
            ezBSAlert({
                type: "confirm",
                messageText: ajax_object.deleteConformMessage,
                alertType: "info"
            }).done(function (e) {
                if (e == true) {
                    $.post(
                        ajax_object.ajax_url,
                        {
                            action: 'wfd_delete_pickup_driver',
                            pickupDriverId: driverId
                        },
                        function (response) {
                            if (response.result == true) {
                                ezBSAlert({
                                    messageText: response.message,
                                    alertType: "success",
                                    headerText: ajax_object.successTitle,
                                    okButtonText: ajax_object.okText
                                }).done(function (e) {
                                    $('tr[data-pickup-driver-id="' + driverId + '"]').remove();
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

    function enablePickupDriverEdit(desabled) {
        if(desabled){
            $('#pickup-driver-save').html('<span class="glyphicon glyphicon-pencil"></span> Edit');
        }
        else{
            $('#pickup-driver-save').html('<span class="glyphicon glyphicon-floppy-disk"></span> Save');
        }
        $('input', $('#modal-pickup-driver')).prop('disabled', desabled);
        $('input', $('#pickup-driver-application-form')).rating('create');
        $('#modal-pickup-driver').data('mode', desabled? 'view':'edit');
    }

    function setPickupDriverDetailsToDlg(driverDetails){
        var targetDlg = $('#modal-pickup-driver');
        $('input[name="fname"]', targetDlg).val(driverDetails['fname']);
        $('input[name="lname"]', targetDlg).val(driverDetails['lname']);
        $('input[name="street"]', targetDlg).val(driverDetails['street']);
        $('input[name="city"]', targetDlg).val(driverDetails['city']);
        $('input[name="phone"]', targetDlg).val(driverDetails['phone']);
        $('input[name="note"]', targetDlg).val(driverDetails['note']);
        $('input[name="pickups_less_250"]', targetDlg).val(driverDetails['pickups_less_250']).rating('create');
        $('input[name="pickups_less_500"]', targetDlg).val(driverDetails['pickups_less_500']).rating('create');
        $('input[name="pickups_more_500"]', targetDlg).val(driverDetails['pickups_more_500']).rating('create');
        $('input[name="cars"]', targetDlg).val(driverDetails['cars']).rating('create');
        $('input[name="truck_less_3"]', targetDlg).val(driverDetails['truck_less_3']).rating('create');
        $('input[name="truck_less_7"]', targetDlg).val(driverDetails['truck_less_7']).rating('create');

        $('input[name="c1"]', targetDlg).prop('checked', driverDetails['c1_license'] == "1");
        $('input[name="c1e"]', targetDlg).prop('checked', driverDetails['c1e_license'] == "1");
        $('input[name="crane_lic"]', targetDlg).prop('checked', driverDetails['crane_lic']== "1");
        $('input[name="kennz95"]', targetDlg).prop('checked', driverDetails['kennz95']== "1");
        $('input[name="motor_mechatronics"]', targetDlg).prop('checked', driverDetails['motor_mechatronics']== "1");
        $('input[name="motor_foreman"]', targetDlg).prop('checked', driverDetails['motor_foreman']== "1");
        $('input[name="learned"]', targetDlg).prop('checked', driverDetails['learned']== "1");
        $('input[name="unlearned"]', targetDlg).prop('checked', driverDetails['unlearned']== "1");
        $('input[name="commercial"]', targetDlg).prop('checked', driverDetails['commercial']== "1");
    }

    $('#pickup-driver-save').click(function (e) {
        var driverDlg = $('#modal-pickup-driver');
        var mode = driverDlg.data('mode');
        var driverId = driverDlg.data('pickupDriverId');

        switch (mode){
            case "view":
                enableDriverEdit(false);
                break;
            case "edit":
            case "new":
                var coreDataForm = $('#pickup-driver-core-data');
                var coreDataFields = $('input', coreDataForm);
                var coreData = {};
                $.each(coreDataFields, function (i, field) {
                    field = $(field);
                    coreData[field.prop('name')] = field.val();
                });

                var appFields = $('input', $('#pickup-driver-application-form'));
                var appData = {};
                $.each(appFields, function (i, field) {
                    field = $(field);
                    appData[field.prop('name')] = field.val();
                });

                var licenseFields = $('input', $('#pickup-driver-license-form'));
                var licenseData = {};
                $.each(licenseFields, function (i, field) {
                    field = $(field);
                    if(field.prop('checked') == true){
                        licenseData[field.prop('name')] = 1;
                    }
                    else{
                        licenseData[field.prop('name')] = 0;
                    }
                });

                var qualificationFields = $('input', $('#pickup-driver-qualification-form'));
                var qualificationData = {};
                $.each(qualificationFields, function (i, field) {
                    field = $(field);
                    if(field.prop('checked') == true){
                        qualificationData[field.prop('name')] = 1;
                    }
                    else {
                        qualificationData[field.prop('name')] = 0;
                    }
                });

                $.post(
                    ajax_object.ajax_url,
                    {
                        action: 'wfd_pickup_driver_save',
                        mode: mode,
                        pickupDriverId: driverId,
                        coreData: JSON.stringify(coreData),
                        applicationData: JSON.stringify(appData),
                        licenseData: JSON.stringify(licenseData),
                        qualificationData: JSON.stringify(qualificationData)
                    },
                    function (response) {
                        if (response.result == true) {
                            ezBSAlert({
                                messageText: response.message,
                                alertType: "success",
                                headerText: ajax_object.successTitle,
                                okButtonText: ajax_object.okText
                            }).done(function (e) {
                                driverDlg.modal('hide');
                                if(mode=='new'){
                                    var driversTable = $('#pickup-drivers-table');
                                    var newRow = $('<tr  data-pickup-driver-id="' + response.driverId + '">');
                                    var cols = "";

                                    cols += '<td>' + coreData['fname'] + '</td>';
                                    cols += '<td>' + coreData['lname'] + '</td><td>' + coreData['street'] + '</td><td>' + coreData['city'] + '</td><td>' + coreData['phone'] + '</td><td>' + coreData['note'] + '</td>';

                                    var actionsContent = '<div class="btn-group-pickup-driver"><button type="button" class="btn btn-primary btn-sm btn-pickup-driver-view"><span class="glyphicon glyphicon-th-list"></span></button><button type="button" class="btn btn-primary btn-sm btn-pickup-driver-edit"><span class="glyphicon glyphicon-pencil"></span></button><button type="button" class="btn btn-primary btn-sm btn-pickup-driver-delete"><span class="glyphicon glyphicon-remove"></span></button><button type="button" class="btn btn-primary btn-sm btn-pickup-driver-copy"><span class="glyphicon glyphicon-asterisk"></span></button></div>';
                                    cols += '<td>' + actionsContent + '</td>';

                                    newRow.append(cols);
                                    $('button[data-client-id]', newRow).data('clientId', response.clientId);

                                    driversTable.append(newRow);
                                    attachPickupDriverActions();

                                }else{
                                    var updateTarget = $('tr[data-pickup-driver-id="' + driverId + '"]');
                                    var td = $('td', updateTarget);
                                    td[0].textContent = coreData['fname'];
                                    td[1].textContent = coreData['lname'];
                                    td[2].textContent = coreData['street'];
                                    td[3].textContent = coreData['city'];
                                    td[4].textContent = coreData['phone'];
                                    td[5].textContent = coreData['note'];
                                }
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
                break;
        }
    });

    //endregion

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

$(document).ready(function() {


    var readURL = function(input) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();

            reader.onload = function (e) {
                $('#profile-pic').attr('src', e.target.result);
            }

            reader.readAsDataURL(input.files[0]);
        }
    }

    $("#file-upload").on('change', function(){
        readURL(this);

    });

    $("#profile-pic").on('dblclick', function(){
        $("#file-upload").click();
    });
});
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