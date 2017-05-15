<?php
/*
  Plugin Name: WFD Truck Plugins
  Plugin URI: http://wordpress.org/
  Description: Driver & Delivery Management System
  Version: 1.0
  Author URI: http://wordpress.org/
  Text Domain: wfd-truck
 */

add_action('admin_menu', 'wfd_truck_ltd_admin_menu_fn');

function wfd_truck_ltd_admin_menu_fn()
{
    add_menu_page('WFD Truck Management', 'Company Info', 'manage_options', 'wfd_truck_management', 'wfd_truck_management_fn');
    add_submenu_page('wfd_truck_management', 'Settings', 'Settings', 'manage_options', 'wfd_truck_settings', 'wfd_truck_settings_fn');
}

add_action('plugins_loaded', 'plugin_init');

function plugin_init()
{
    load_plugin_textdomain('wfd_truck', false, dirname(plugin_basename(__FILE__)) . '/languages/');
}

//add_action( 'plugins_loaded', 'my_enqueue' );
function my_enqueue() {

    wp_enqueue_style('bootstrap-style', 'http://netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css', null);
    wp_enqueue_style('bootstrap-theme', 'http://netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap-theme.min.css', null);
    wp_enqueue_style('bootstrap-select', 'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.4/css/bootstrap-select.min.css', null);
    wp_enqueue_style('bootstrap-responsive', 'https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/2.3.2/css/bootstrap-responsive.css', null);
    wp_enqueue_style('bootstrap-table-css', 'https://rawgit.com/wenzhixin/bootstrap-table/master/src/bootstrap-table.css', null);
    wp_enqueue_style('font-awesome-css', 'http://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css', null);

    wp_enqueue_script('jquery-js', 'http://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.3/jquery.min.js', null);
    wp_enqueue_script('bootstrap-main-js', 'http://netdna.bootstrapcdn.com/bootstrap/3.1.1/js/bootstrap.min.js');
    wp_enqueue_script('bootstrap-select-js', 'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.4/js/bootstrap-select.min.js', null);
    wp_enqueue_script('bootstrap-table-js', 'https://rawgit.com/wenzhixin/bootstrap-table/master/src/bootstrap-table.js', null);

    wp_register_style('truck-main-style', plugins_url('css/truck-main.css', __FILE__));
    wp_register_style('star-rating-css', plugins_url('css/star-rating.min.css', __FILE__));
    wp_enqueue_style('truck-main-style');
    wp_enqueue_style('star-rating-css');

    wp_enqueue_script('ajax-script', plugins_url('/js/truck-main.js', __FILE__), array('jquery-js'));
    wp_enqueue_script('star-rating-script', plugins_url('/js/star-rating.min.js', __FILE__), array('jquery-js'));

    // in JavaScript, object properties are accessed as ajax_object.ajax_url, ajax_object.we_value
    wp_localize_script('ajax-script', 'ajax_object',
        array('ajax_url' => admin_url('admin-ajax.php'), 'fillFormMessage' => __('Please fill all of the fields!', 'wfd_truck'),
            'alertTitle' => __('Alert', 'wfd_truck'), 'okText' => __('OK', 'wfd_truck'),
            'successTitle' => __('Succeed', 'wdf_truck')));
}

add_action('wp_ajax_wfd_add_client', 'wfd_add_client');
add_action('wp_ajax_nopriv_wfd_add_client', 'wfd_add_client');
add_action('wp_ajax_wfd_update_driver', 'wfd_update_driver');
add_action('wp_ajax_nopriv_wfd_update_driver', 'wfd_update_driver');
add_action('wp_ajax_wfd_update_client', 'wfd_update_client');
add_action('wp_ajax_nopriv_wfd_update_client', 'wfd_update_client');

function wfd_add_client()
{
    global $wpdb;
    $new_user_name = $_POST['new_user_name'];
    $new_email_address = $_POST['new_email_address'];
    $new_company_name = $_POST['new_company_name'];
    $new_password = $_POST['new_password'];

    $result_array = array();
    $tbl_wp_users = $wpdb->users;
    $existing_users = $wpdb->get_results("SELECT * FROM $tbl_wp_users WHERE `display_name`='$new_user_name' OR `user_email`='$new_email_address'", OBJECT);
    if (count($existing_users) > 0) {
        $result_array['result'] = false;
        $result_array['errorMessage'] = __('Your name or email address is already using on this site!', 'wfd_truck');
    } else {
        $tbl_clients = $wpdb->prefix . "wfd_truck_client_info";
        $sql_add_client = "INSERT INTO $tbl_clients (`username`, `email`, `company`, `password`) values ('$new_user_name', '$new_email_address', '$new_company_name', '$new_password')";
        if ($wpdb->query($sql_add_client) != false) {
            $result_array['result'] = true;
            $result_array['message'] =  __('Congratulate! Your account successfully created!', 'wdf_truck');
            $result_array['clientId'] = $wpdb->insert_id;
        } else {
            $result_array['result'] = false;
            $result_array['errorMessage'] = $wpdb->last_error;
        }
    }
    echo json_encode($result_array);
    wp_die();
}
function wfd_update_client()
{
    global $wpdb;
    $clientId = $_POST['clientId'];
    $zip = $_POST['new_zip'];
    $street = $_POST['new_street'];
    $city = $_POST['new_city'];
    $phone = $_POST['new_phone'];
    $note = $_POST['new_note'];

    $result_array = array();

    $tbl_clients = $wpdb->prefix . "wfd_truck_client_info";
    $sql_update_driver = "UPDATE $tbl_clients SET zip='$zip', street='$street', city='$city', phone='$phone', note='$note' WHERE id='$clientId'";
    if ($wpdb->query($sql_update_driver) != false) {
        $result_array['result'] = true;
        $result_array['message'] = __('Client Core information successfully updated!', 'wfd_truck');
    } else {
        $result_array['result'] = false;
        $result_array['errorMessage'] = $wpdb->last_error;
    }

    echo json_encode($result_array);
    wp_die();
}

function wfd_update_driver()
{
    global $wpdb;
    $driverId = $_POST['driverId'];
    $firstName = $_POST['firstName'];
    $lastName = $_POST['lastName'];
    $street = $_POST['street'];
    $city = $_POST['city'];
    $phone = $_POST['phone'];
    $note = $_POST['note'];

    $result_array = array();
    $tbl_wp_users = $wpdb->users;

    $tbl_drivers = $wpdb->prefix . "wfd_truck_driver_info";
    $sql_update_driver = "UPDATE $tbl_drivers SET fname='$firstName', lname='$lastName', street='$street', city='$city', phone='$phone', note='$note' WHERE id='$driverId'";
    if ($wpdb->query($sql_update_driver) != false) {
        $result_array['result'] = true;
        $result_array['message'] = __('Driver information successfully updated!', 'wfd_truck');
    } else {
        $result_array['result'] = false;
        $result_array['errorMessage'] = $wpdb->last_error;
    }

    echo json_encode($result_array);
    wp_die();
}


function wfd_truck_settings_fn()
{
    global $wpdb;
    $tbl_licences = $wpdb->prefix . "wfd_truck_driver_licences";
    $res_licences = $wpdb->get_results("select * from $tbl_licences", OBJECT);

    $tbl_qualification = $wpdb->prefix . "wfd_truck_driver_qualification";
    $res_qualification = $wpdb->get_results("select * from $tbl_qualification", OBJECT);

    $tbl_ranking = $wpdb->prefix . "wfd_truck_driver_ranking";
    $res_ranking = $wpdb->get_results("select * from $tbl_ranking", OBJECT);

//  print_r($res_licences);
//  print_r($res_qualification);
    ?>

    <link rel="stylesheet" href="<?php echo plugin_dir_url(__FILE__) ?>/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo plugin_dir_url(__FILE__) ?>/css/bootstrap-theme.min.css">
    <link rel="stylesheet" href="<?php echo plugin_dir_url(__FILE__) ?>/css/jquery.dataTables.min.css">

    <script src="<?php echo plugin_dir_url(__FILE__) ?>/js/jquery.dataTables.min.js"></script>
    <script src="<?php echo plugin_dir_url(__FILE__) ?>/js/bootstrap.min.js"></script>
    <style>#wpbody-content {
            width: 98.5%;
        }</style>
    <h2><?php _e('Settings', 'wfd_truck'); ?></h2>

    <script>
        jQuery(document).ready(function () {
            //   jQuery('table').DataTable();
        });
    </script>

    <div class="row">
        <div class="col-sm-6">
            <h3><?php _e('Licenses', 'wfd_truck'); ?></h3>

            <table class="table dataTable table-striped">
                <thead>
                <tr>
                    <th><?php _e('SL#', 'wfd_truck'); ?></th>
                    <th><?php _e('Licenses Name', 'wfd_truck'); ?></th>
                    <th><?php _e('Action', 'wfd_truck'); ?></th>
                </tr>
                </thead>
                <tbody>
                <?php
                $i = 1;
                foreach ($res_licences as $rl) {
                    ?>
                    <tr>
                        <td><?php echo $i;
                            $i++ ?></td>
                        <td><?php echo $rl->licences ?></td>
                        <td>
                            <a href="?page=wfd_truck_settings&act=licedit&id=<?php echo $rl->id ?>"><?php _e('Edit', 'wfd_truck'); ?></a>
                            <a href="?page=wfd_truck_settings&act=licdel&id=<?php echo $rl->id ?>"
                               onClick="return confirm(<?php _e('Are you sure?', 'wfd_truck'); ?>)"><?php _e('Delete', 'wfd_truck'); ?></a>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>

            <a class="btn btn-primary" role="button" data-toggle="collapse" href="#colLicenses" aria-expanded="false"
               aria-controls="collapseExample">
                <?php _e('New Licenses', 'wfd_truck'); ?>
            </a>

            <div class="collapse" id="colLicenses">
                <div class="well">
                    <form method="POST">
                        <div class="form-group">
                            <label><?php _e('License Name', 'wfd_truck'); ?></label>
                            <input type="text" name="licenses" class="form-control">
                        </div>
                        <div class="form-group">
                            <input type="submit" class="btn btn-primary" value="Save License" name="save_license">
                        </div>
                    </form>
                </div>
            </div>

            <?php if ($_GET['act'] == 'licedit') {
                $id = $_GET['id'];
                $res_licences_id = $wpdb->get_results("SELECT * FROM $tbl_licences where id=$id");

                ?>
                <h3><?php _e('Update Qualification', 'wfd_truck'); ?></h3>
                <form method="POST">
                    <div class="form-group">
                        <label><?php _e('Qualification Name', 'wfd_truck'); ?></label>
                        <input type="text" name="licenses" class="form-control"
                               value="<?php echo $res_licences_id[0]->licences ?>">
                        <input type='hidden' name="id" value="<?php echo $res_licences_id[0]->id ?>"/>
                    </div>
                    <div class="form-group">
                        <input type="submit" class="btn btn-primary"
                               value="<?php _e('Update Licenses', 'wfd_truck'); ?>" name="update_licenses">
                    </div>
                </form>
            <?php }

            if ($_GET['act'] == 'licdel') {
                $id = $_GET['id'];
                $wpdb->query("DELETE FROM $tbl_licences where id=$id", OBJECT);
                ?>


                <h2><?php _e('Data Deleted...', 'wfd_truck'); ?></h2>
                <script>
                    setTimeout(function () {
                        window.location.href = "<?php echo site_url(); ?>/<?php echo admin_url();?>/admin.php?page=wfd_truck_settings";
                    }, 3000);
                </script>

                <?php

            }
            ?>


        </div>
        <div class="col-sm-6">
            <h3><?php _e('Qualification', 'wfd_truck'); ?></h3>
            <table class="table dataTable table-striped">
                <thead>
                <tr>
                    <th><?php _e('SL#', 'wfd_truck'); ?></th>
                    <th><?php _e('Qualification Name', 'wfd_truck'); ?></th>
                    <th><?php _e('Action', 'wfd_truck'); ?></th>
                </tr>
                </thead>
                <tbody>
                <?php
                $i = 1;
                foreach ($res_qualification as $rq) {
                    ?>
                    <tr>
                        <td><?php echo $i;
                            $i++ ?></td>
                        <td><?php echo $rq->qualification ?></td>
                        <td>
                            <a href="?page=wfd_truck_settings&act=quaedit&id=<?php echo $rq->id ?>"><?php _e('Edit', 'wfd_truck'); ?></a>
                            <a href="?page=wfd_truck_settings&act=quadel&id=<?php echo $rq->id ?>"
                               onClick="return confirm(<?php _e('Are you sure?', 'wfd_truck'); ?>)"><?php _e('Delete', 'wfd_truck'); ?></a>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>


            <a class="btn btn-primary" role="button" data-toggle="collapse" href="#colQualification"
               aria-expanded="false" aria-controls="collapseExample">
                <?php _e('New Qualification', 'wfd_truck'); ?>
            </a>

            <div class="collapse" id="colQualification">
                <div class="well">
                    <form method="POST">
                        <div class="form-group">
                            <label><?php _e('Qualification Name', 'wfd_truck'); ?></label>
                            <input type="text" name="qualification" class="form-control">
                        </div>
                        <div class="form-group">
                            <input type="submit" class="btn btn-primary"
                                   value="<?php _e('Save Qualification', 'wfd_truck'); ?>" name="save_qualification">
                        </div>
                    </form>
                </div>
            </div>
            <?php if ($_GET['act'] == 'quaedit') {
                $id = $_GET['id'];
                $res_quaedit = $wpdb->get_results("SELECT * FROM $tbl_qualification where id=$id");

                ?>
                <h3><?php _e('Update Qualification', 'wfd_truck'); ?></h3>
                <form method="POST">
                    <div class="form-group">
                        <label><?php _e('Qualification Name', 'wfd_truck'); ?></label>
                        <input type="text" name="qualification" class="form-control"
                               value="<?php echo $res_quaedit[0]->qualification ?>">
                        <input type='hidden' name="id" value="<?php echo $res_quaedit[0]->id ?>"/>
                    </div>
                    <div class="form-group">
                        <input type="submit" class="btn btn-primary"
                               value="<?php _e('Update Qualification', 'wfd_truck'); ?>" name="update_qualification">
                    </div>
                </form>
            <?php }

            if ($_GET['act'] == 'quadel') {
                $id = $_GET['id'];
                $wpdb->query("DELETE FROM $tbl_qualification where id=$id", OBJECT);
                ?>


                <h2><?php _e('Data Deleted...', 'wfd_truck'); ?></h2>
                <script>
                    setTimeout(function () {
                        window.location.href = "<?php echo site_url(); ?>/<?php echo admin_url();?>/admin.php?page=wfd_truck_settings";
                    }, 3000);
                </script>

                <?php

            }
            ?>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-6">
            <h3><?php _e('Ranking', 'wfd_truck'); ?></h3>
            <table class="table dataTable table-striped">
                <thead>
                <tr>
                    <th><?php _e('SL#', 'wfd_truck'); ?></th>
                    <th><?php _e('Ranking Item Name', 'wfd_truck'); ?></th>
                    <th><?php _e('Action', 'wfd_truck'); ?></th>
                </tr>
                </thead>
                <tbody>
                <?php
                $i = 1;
                foreach ($res_ranking as $rk) {
                    ?>
                    <tr>
                        <td><?php echo $i;
                            $i++ ?></td>
                        <td><?php echo $rk->rank_item ?></td>
                        <td>
                            <a href="?page=wfd_truck_settings&act=rankedit&id=<?php echo $rk->id ?>"><?php _e('Edit', 'wfd_truck'); ?></a>
                            <a href="?page=wfd_truck_settings&act=rankdel&id=<?php echo $rk->id ?>"
                               onClick="return confirm(<?php _e('Are you sure?', 'wfd_truck'); ?>)"><?php _e('Delete', 'wfd_truck'); ?></a>
                        </td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>


            <a class="btn btn-primary" role="button" data-toggle="collapse" href="#colRank" aria-expanded="false"
               aria-controls="collapseExample">
                <?php _e('New Rank Item', 'wfd_truck'); ?>
            </a>

            <div class="collapse" id="colRank">
                <div class="well">
                    <form method="POST">
                        <div class="form-group">
                            <label><?php _e('Rank Item Name', 'wfd_truck'); ?></label>
                            <input type="text" name="rank_item" class="form-control">
                        </div>
                        <div class="form-group">
                            <input type="submit" class="btn btn-primary"
                                   value="<?php _e('Save Qualification', 'wfd_truck'); ?>" name="save_ranking">
                        </div>
                    </form>
                </div>
            </div>
            <?php if ($_GET['act'] == 'rankedit') {
                $id = $_GET['id'];
                $res_rankedit = $wpdb->get_results("SELECT * FROM $tbl_ranking where id=$id");

                ?>
                <h3><?php _e('Update Qualification', 'wfd_truck'); ?></h3>
                <form method="POST">
                    <div class="form-group">
                        <label><?php _e('Rank Item Name', 'wfd_truck'); ?></label>
                        <input type="text" name="rank_item" class="form-control"
                               value="<?php echo $res_rankedit[0]->rank_item ?>">
                        <input type='hidden' name="id" value="<?php echo $id ?>"/>
                    </div>
                    <div class="form-group">
                        <input type="submit" class="btn btn-primary"
                               value="<?php _e('Update Qualification', 'wfd_truck'); ?>" name="update_ranking">
                    </div>
                </form>
            <?php }

            if ($_GET['act'] == 'rankdel') {
                $id = $_GET['id'];
                $wpdb->query("DELETE FROM $tbl_ranking where id=$id", OBJECT);
                ?>


                <h2><?php _e('Data Deleted...', 'wfd_truck'); ?></h2>
                <script>
                    setTimeout(function () {
                        window.location.href = "<?php echo site_url(); ?>/<?php echo admin_url();?>/admin.php?page=wfd_truck_settings";
                    }, 3000);
                </script>

                <?php

            }
            ?>
        </div>
    </div>
    <?php

}

function wfd_truck_management_fn()
{
    global $wpdb;
    $tbl_client_info = $wpdb->prefix . "wfd_truck_client_info";
    $res_client_info = $wpdb->get_results("select * from $tbl_client_info", OBJECT);

    ?>


    <link rel="stylesheet" href="<?php echo plugin_dir_url(__FILE__) ?>/css/bootstrap.min.css">
    <link rel="stylesheet" href="<?php echo plugin_dir_url(__FILE__) ?>/css/bootstrap-theme.min.css">
    <link rel="stylesheet" href="<?php echo plugin_dir_url(__FILE__) ?>/css/jquery.dataTables.min.css">

    <script src="<?php echo plugin_dir_url(__FILE__) ?>/js/jquery.dataTables.min.js"></script>
    <script src="<?php echo plugin_dir_url(__FILE__) ?>/js/bootstrap.min.js"></script>
    <style>#wpbody-content {
            width: 98.5%;
        }</style>

    <script>
        jQuery(document).ready(function () {
            jQuery('table').DataTable();
        });
    </script>
    <h2><?php _e('Client Info', 'wfd_truck'); ?></h2>
    <table class="table dataTable table-striped">
        <thead>
        <tr>
            <th><?php _e('sl#', 'wfd_truck'); ?></th>
            <th><?php _e('client_type', 'wfd_truck'); ?></th>
            <th><?php _e('company', 'wfd_truck'); ?></th>
            <th><?php _e('email', 'wfd_truck'); ?></th>
            <th><?php _e('username', 'wfd_truck'); ?></th>
            <th><?php _e('street', 'wfd_truck'); ?></th>
            <th><?php _e('zip', 'wfd_truck'); ?></th>
            <th><?php _e('city', 'wfd_truck'); ?></th>
            <th><?php _e('phone', 'wfd_truck'); ?></th>
            <th><?php _e('emergency phone', 'wfd_truck'); ?></th>
            <th><?php _e('fax', 'wfd_truck'); ?></th>
            <th style="width: 220px;"><?php _e('website', 'wfd_truck'); ?></th>
            <th><?php _e('note', 'wfd_truck'); ?></th>
            <th><?php _e('Action', 'wfd_truck'); ?></th>
        </tr>
        </thead>
        <tbody>
        <?php
        $i = 1;
        foreach ($res_client_info as $r1) { ?>
        <tr>
            <td><?php echo $i ?></td>
            <td><?php echo $r1->type ?></td>
            <td><?php echo $r1->company ?></td>
            <td><?php echo $r1->email ?></td>
            <td><?php echo $r1->username ?></td>

            <td><?php echo $r1->street ?></td>
            <td><?php echo $r1->zip ?></td>
            <td><?php echo $r1->city ?></td>
            <td><?php echo $r1->phone ?></td>
            <td><?php echo $r1->emergency_phone ?></td>
            <td><?php echo $r1->fax ?></td>
            <td style="word-break: break-all;"><?php echo $r1->website ?></td>
            <td><?php echo $r1->note ?></td>
            <td>
                <a href="?page=wfd_truck_management&act=edit&id=<?php echo $r1->id ?>"><?php _e('Edit', 'wfd_truck'); ?></a>
                <a href="?page=wfd_truck_management&act=del&id=<?php echo $r1->id ?>"
                   onClick="return confirm(<?php _e('Are you sure?', 'wfd_truck'); ?>)"><?php _e('Delete', 'wfd_truck'); ?></a>
            </td>
            <?php
            $i++;
            }
            ?>
        </tr>
        </tbody>
    </table>

    <br>
    <hr>

    <?php if ($_GET['act'] == 'edit') {
    $id = $_GET['id'];
    $res_client_info_id = $wpdb->get_results("select * from $tbl_client_info where id=$id limit 1", OBJECT);

    //print_r($res_client_info_id);
    ?>


    <div class="well col-sm-6">

        <h2><?php _e('Edit New Client', 'wfd_truck'); ?></h2>

        <form method="POST">
            <div class="form-group"><label><?php _e('client_type', 'wfd_truck'); ?></label>
                <input type="text" name="type" class="form-control" placeholder="Client Type"
                       value="<?php echo $res_client_info_id[0]->type ?>">
            </div>
            <div class="form-group"><label><?php _e('Company', 'wfd_truck'); ?></label>
                <input type="text" name="company" class="form-control" placeholder="Company Name"
                       value="<?php echo $res_client_info_id[0]->company ?>">
            </div>
            <div class="form-group"><label><?php _e('Email', 'wfd_truck'); ?></label>
                <?php echo $res_client_info_id[0]->email ?>

            </div>
            <div class="form-group"><label><?php _e('Username', 'wfd_truck'); ?></label>
                <?php echo $res_client_info_id[0]->username ?>
            </div>

            <div class="form-group"><label><?php _e('Street', 'wfd_truck'); ?></label>
                <input type="text" name="street" class="form-control" placeholder="street"
                       value="<?php echo $res_client_info_id[0]->street ?>">
            </div>
            <div class="form-group"><label><?php _e('Zip', 'wfd_truck'); ?></label>
                <input type="text" name="zip" class="form-control" placeholder="zip"
                       value="<?php echo $res_client_info_id[0]->zip ?>">
            </div>
            <div class="form-group"><label><?php _e('City', 'wfd_truck'); ?></label>
                <input type="text" name="city" class="form-control" placeholder="city"
                       value="<?php echo $res_client_info_id[0]->city ?>">
            </div>
            <div class="form-group"><label><?php _e('Phone', 'wfd_truck'); ?></label>
                <input type="text" name="phone" class="form-control" placeholder="phone"
                       value="<?php echo $res_client_info_id[0]->phone ?>">
            </div>
            <div class="form-group"><label><?php _e('Fax', 'wfd_truck'); ?></label>
                <input type="text" name="fax" class="form-control" placeholder="fax"
                       value="<?php echo $res_client_info_id[0]->fax ?>">
            </div>
            <div class="form-group"><label><?php _e('Website', 'wfd_truck'); ?></label>
                <input type="text" name="website" class="form-control" placeholder="website"
                       value="<?php echo $res_client_info_id[0]->website ?>">
            </div>
            <div class="form-group"><label><?php _e('Emergency Phone', 'wfd_truck'); ?></label>
                <input type="text" name="emergency_phone" class="form-control" placeholder="emergency_phone"
                       value="<?php echo $res_client_info_id[0]->emergency_phone ?>">
                <input type="hidden" name="id" value="<?php echo $res_client_info_id[0]->id ?>">
            </div>
            <div class="form-group"><label><?php _e('Note', 'wfd_truck'); ?></label>
                <textarea name="note" class="form-control"
                          placeholder="Note here"><?php echo $res_client_info_id[0]->note ?></textarea>

            </div>

            <div class="form-group">
                <input type="submit" class="btn btn-default" name="edit_new_clinet"
                       value="<?php _e('Edit Client', 'wfd_truck'); ?>"/>
            </div>
        </form>

    </div>
<?php } elseif ($_GET['act'] == 'del') {
    $id = $_GET['id'];
    $wpdb->query("DELETE FROM $tbl_client_info where id=$id", OBJECT);
    ?>


    <h2><?php _e('Data Deleted...', 'wfd_truck'); ?></h2>
    <script>
        setTimeout(function () {
            window.location.href = "<?php echo admin_url();?>admin.php?page=wfd_truck_management";
        }, 3000);
    </script>

    <?php

} else { ?>

    <button class="btn btn-primary" type="button" data-toggle="collapse" data-target="#collapseExample"
            aria-expanded="false" aria-controls="collapseExample">
        <?php _e('Add New Client', 'wfd_truck'); ?>
    </button>
<?php } ?>
    <div class="collapse" id="collapseExample">
        <div class="well col-sm-6">

            <h2><?php _e('Add New Client', 'wfd_truck'); ?></h2>

            <form method="POST">
                <div class="form-group"><label><?php _e('Company', 'wfd_truck'); ?></label>
                    <input type="text" name="company" class="form-control" placeholder="Company Name">
                </div>
                <div class="form-group"><label><?php _e('Email', 'wfd_truck'); ?></label>
                    <input type="email" name="email" id="email" class="form-control" placeholder="email">
                    <div id="email_available" style="color:red"></div>
                </div>
                <div class="form-group"><label><?php _e('Usernane', 'wfd_truck'); ?></label>
                    <input type="text" name="username" id="username" class="form-control" placeholder="username">
                    <div id="user_available" style="color:red;"></div>
                </div>
                <div class="form-group"><label><?php _e('Password', 'wfd_truck'); ?></label>
                    <input type="password" name="password" class="form-control" placeholder="password">
                </div>
                <div class="form-group"><label><?php _e('Street', 'wfd_truck'); ?></label>
                    <input type="text" name="street" class="form-control" placeholder="street">
                </div>
                <div class="form-group"><label><?php _e('Zip', 'wfd_truck'); ?></label>
                    <input type="text" name="zip" class="form-control" placeholder="zip">
                </div>
                <div class="form-group"><label><?php _e('City', 'wfd_truck'); ?></label>
                    <input type="text" name="city" class="form-control" placeholder="city">
                </div>
                <div class="form-group"><label><?php _e('Phone', 'wfd_truck'); ?></label>
                    <input type="text" name="phone" class="form-control" placeholder="phone">
                </div>
                <div class="form-group"><label><?php _e('Fax', 'wfd_truck'); ?></label>
                    <input type="text" name="fax" class="form-control" placeholder="fax">
                </div>
                <div class="form-group"><label><?php _e('Website', 'wfd_truck'); ?></label>
                    <input type="text" name="website" class="form-control" placeholder="website">
                </div>
                <div class="form-group"><label><?php _e('Emergency Phone', 'wfd_truck'); ?></label>
                    <input type="text" name="emergency_phone" class="form-control" placeholder="emergency_phone">
                </div>
                <div class="form-group"><label><?php _e('Note', 'wfd_truck'); ?></label>
                    <textarea name="note" class="form-control" placeholder="Note here"></textarea>

                </div>

                <div class="form-group">
                    <input type="submit" class="btn btn-default" name="add_new_clinet"
                           value="<?php _e('Save Client', 'wfd_truck'); ?>"/>
                </div>
            </form>

        </div>
    </div>

<?php }


add_action('init', 'wfd_truck_init_fn');

function wfd_truck_init_fn()
{
    session_start();
    global $wpdb;


    if (isset($_POST['save_ranking'])) {
        $rank_item = $_POST['rank_item'];
        $tbl_ranking = $wpdb->prefix . "wfd_truck_driver_ranking";
        $sql_ranking = "INSERT INTO $tbl_ranking (rank_item) values ('$rank_item')";
        $wpdb->query($sql_ranking);
    }
    if (isset($_POST['update_ranking'])) {
        $rank_item = $_POST['rank_item'];
        $id = $_POST['id'];
        $tbl_ranking = $wpdb->prefix . "wfd_truck_driver_ranking";
        $sql = "UPDATE $tbl_ranking set rank_item='$rank_item' where id=$id";
        $wpdb->query($sql);

        ?>

        <script>
            setTimeout(function () {
                window.location.href = "<?php echo admin_url();?>admin.php?page=wfd_truck_settings";
            }, 3000);
        </script>
        <?php
    }

    if (isset($_POST['save_license'])) {
        $licenses = $_POST['licenses'];
        $tbl_driver_licences = $wpdb->prefix . "wfd_truck_driver_licences";
        $sql = "INSERT INTO $tbl_driver_licences (licences) values ('$licenses')";
        $wpdb->query($sql);
    }
    if (isset($_POST['save_qualification'])) {
        $qualification = $_POST['qualification'];
        $tbl_driver_qualification = $wpdb->prefix . "wfd_truck_driver_qualification";
        $sql = "INSERT INTO $tbl_driver_qualification (qualification) values ('$qualification')";
        $wpdb->query($sql);
    }
    if (isset($_POST['update_licenses'])) {
        $licenses = $_POST['licenses'];
        $id = $_POST['id'];
        $tbl_driver_licences = $wpdb->prefix . "wfd_truck_driver_licences";
        $sql = "UPDATE $tbl_driver_licences set licences='$licenses' where id=$id";
        $wpdb->query($sql);
        ?>
        <script>
            setTimeout(function () {
                window.location.href = "<?php echo admin_url();?>admin.php?page=wfd_truck_settings";
            }, 3000);
        </script>
        <?php
    }
    if (isset($_POST['update_qualification'])) {
        $id = $_POST['id'];
        $qualification = $_POST['qualification'];
        $tbl_driver_qualification = $wpdb->prefix . "wfd_truck_driver_qualification";
        $sql = "UPDATE $tbl_driver_qualification set qualification='$qualification' where id=$id";
        $wpdb->query($sql);
        ?>
        <script>
            setTimeout(function () {
                window.location.href = "<?php echo admin_url();?>admin.php?page=wfd_truck_settings";
            }, 3000);
        </script>
        <?php
    }

    if (isset($_POST['add_new_clinet'])) {
        $company = $_POST['company'];
        $email = $_POST['email'];
        $username = $_POST['username'];
        $password = md5($_POST['password']);
        $street = $_POST['street'];
        $zip = $_POST['zip'];
        $city = $_POST['city'];
        $phone = $_POST['phone'];
        $fax = $_POST['fax'];
        $website = $_POST['website'];
        $emergency_phone = $_POST['emergency_phone'];
        $note = $_POST['note'];

        $tbl_client_info = $wpdb->prefix . "wfd_truck_client_info";
        $sql_ins_client_info = "INSERT INTO $tbl_client_info (`company`, `email`, `username`, `password`, `street`, `zip`, `city`, `phone`, `fax`,  `website`, `emergency_phone`, `note`) "
            . "values ('$company','$email', '$username', '$password', '$street','$zip', '$city', '$phone', '$fax', '$website', '$emergency_phone', '$note');";
        //echo $sql_ins_client_info;
        $wpdb->query($sql_ins_client_info);
    }


    if (isset($_POST['chang_pass'])) {
        $id = $_POST['id'];
        $old_pass = md5($_POST['old_pass']);
        $new_pass = md5($_POST['new_pass']);
        $tbl_client_info = $wpdb->prefix . "wfd_truck_client_info";
        $sql_update_client_pass = "UPDATE $tbl_client_info SET `password`='$new_pass' where id=$id;";
        //echo $sql_update_client_pass;
        $wpdb->query($sql_update_client_pass);
        $_SESSION['client_login'] = 'false';
        $_SESSION['client_username'] = '';
        $_SESSION['client_id'] = '';
        ?>
        <script>
            setTimeout(function () {
                window.location.href = "<?php echo site_url()?>/user-dashboard/";
            }, 3000);
        </script>
        <?php
    }


    if (isset($_POST['edit_new_clinet'])) {
        $company = $_POST['company'];


        $id = $_POST['id'];
        $street = $_POST['street'];
        $zip = $_POST['zip'];
        $city = $_POST['city'];
        $phone = $_POST['phone'];
        $fax = $_POST['fax'];
        $website = $_POST['website'];
        $emergency_phone = $_POST['emergency_phone'];
        $note = $_POST['note'];

        $tbl_client_info = $wpdb->prefix . "wfd_truck_client_info";
        $sql_update_client_info = "UPDATE $tbl_client_info SET `company`='$company',  `street`='$street', `zip`='$zip', `city`='$city', `phone`='$phone', `fax`='$fax',  `website`='$website', `emergency_phone`='$emergency_phone',`note`='$note' where id=$id;";
        $wpdb->query($sql_update_client_info);
        ?>
        <script>
            setTimeout(function () {
                window.location.href = "<?php echo admin_url();?>admin.php?page=wfd_truck_management";
            }, 3000);
        </script>
        <?php
    }
    if (isset($_POST['edit_new_clinet_fn'])) {
        $company = $_POST['company'];


        $id = $_POST['id'];
        $street = $_POST['street'];
        $zip = $_POST['zip'];
        $city = $_POST['city'];
        $phone = $_POST['phone'];
        $fax = $_POST['fax'];
        $website = $_POST['website'];
        $emergency_phone = $_POST['emergency_phone'];
        $note = $_POST['note'];

        $tbl_client_info = $wpdb->prefix . "wfd_truck_client_info";
        $sql_update_client_info = "UPDATE $tbl_client_info SET `company`='$company',  `street`='$street', `zip`='$zip', `city`='$city', `phone`='$phone', `fax`='$fax',  `website`='$website', `emergency_phone`='$emergency_phone',`note`='$note' where id=$id;";
        $wpdb->query($sql_update_client_info);
    }
    if (isset($_POST['client_login'])) {
        $username = $_POST['username'];
        $password = md5($_POST['password']);
        $tbl_client_info = $wpdb->prefix . "wfd_truck_client_info";
        $res_client_info = $wpdb->get_results("select * from $tbl_client_info where username='$username' and password='$password' limit 1", OBJECT);

        if (count($res_client_info) != 0) {
            $_SESSION['client_login'] = 'true';
            $_SESSION['client_username'] = $res_client_info[0]->company;
            $_SESSION['client_id'] = $res_client_info[0]->id;
            $_GET['action'] = 'profile';
        }
    }
    if (isset($_POST['drive_save'])) {
        $tbl_ranking = $wpdb->prefix . "wfd_truck_driver_ranking";
        $res_ranking = $wpdb->get_results("select * from $tbl_ranking order by id");
        foreach ($res_ranking as $rk) {
            $rankingarr["$rk->rank_item"] = $_POST['ranking_' . $rk->id];
        }

        $licences = json_encode($_POST['licences']);
        $qualification = json_encode($_POST['qualification']);
        $rating = json_encode($rankingarr);
        $fname = $_POST['fname'];
        $lname = $_POST['lname'];
        $street = $_POST['street'];
        $city = $_POST['city'];
        $phone = $_POST['phone'];
        $note = $_POST['note'];
        $type = $_POST['type'];
        $cid = $_POST['cid'];

        $tbl_driver_info = $wpdb->prefix . "wfd_truck_driver_info";
        $sql_driver = "INSERT INTO $tbl_driver_info (cid, type, fname, lname, street, city, phone, note) values ('$cid', '$type', '$fname', '$lname', '$street', '$city', '$phone', '$note')";

        $wpdb->query($sql_driver);
    }
    if (isset($_POST['drive_edit_save'])) {

        $id = $_POST['id'];
        $fname = $_POST['fname'];
        $lname = $_POST['lname'];
        $street = $_POST['street'];
        $city = $_POST['city'];
        $phone = $_POST['phone'];
        $note = $_POST['note'];
        $cid = $_POST['cid'];

        $tbl_driver_info = $wpdb->prefix . "wfd_truck_driver_info";
        $sql_update_driver_info = "UPDATE $tbl_driver_info SET fname='$fname',  lname='$lname', street='$street', city='$city', phone='$phone', note='note' where id=$id";
        $wpdb->query($sql_update_driver_info);
        ?>
        <script>
            setTimeout(function () {
                window.location.href = "<?php echo admin_url();?>admin.php?page=wfd_truck_management";
            }, 3000);
        </script>
        <?php
    }

    if (isset($_POST['tpool_save'])) {
        $tbl_ranking = $wpdb->prefix . "wfd_truck_driver_ranking";
        $res_ranking = $wpdb->get_results("select * from $tbl_ranking order by id");
        foreach ($res_ranking as $rk) {
            $rankingarr["$rk->rank_item"] = $_POST['ranking_' . $rk->id];
        }

        $licences = json_encode($_POST['licences']);
        $qualification = json_encode($_POST['qualification']);
        $rating = json_encode($rankingarr);
        $id = $_POST['id'];
        $brand = $_POST['brand'];
        $weight = $_POST['weight'];
        $max_load = $_POST['max_load'];
        $load_height = $_POST['load_height'];
        $type = $_POST['type'];
        $status = $_POST['status'];
        $cid = $_POST['cid'];

        $tbl_tpool_info = $wpdb->prefix . "wfd_truck_truck_truck_info";
        $sql_tpool = "INSERT INTO $tbl_tpool_info (id, cid, brand, weight, max_load, load_height, type, status) values ('$id', '$cid', '$brand', '$weight', '$max_load', '$load_height', '$type', '$status')";

        $wpdb->query($sql_tpool);
    }
    if (isset($_POST['no_save'])) {
        $tbl_ranking = $wpdb->prefix . "wfd_truck_driver_ranking";
        $res_ranking = $wpdb->get_results("select * from $tbl_ranking order by id");
        foreach ($res_ranking as $rk) {
            $rankingarr["$rk->rank_item"] = $_POST['ranking_' . $rk->id];
        }

        $licences = json_encode($_POST['licences']);
        $qualification = json_encode($_POST['qualification']);
        $rating = json_encode($rankingarr);
        $name = $_POST['name'];
        $phone = $_POST['phone'];
        $note = $_POST['note'];
        $category = $_POST['category'];
        $cid = $_POST['cid'];

        $tbl_callno_info = $wpdb->prefix . "wfd_truck_call_num";
        $sql_callno = "INSERT INTO $tbl_callno_info (cid, name, phone, note, category) values ('$cid', '$name', '$phone', '$note', '$category')";

        $wpdb->query($sql_callno);
    }
    if (isset($_POST['service_save'])) {
        $tbl_ranking = $wpdb->prefix . "wfd_truck_driver_ranking";
        $res_ranking = $wpdb->get_results("select * from $tbl_ranking order by id");
        foreach ($res_ranking as $rk) {
            $rankingarr["$rk->rank_item"] = $_POST['ranking_' . $rk->id];
        }

        $licences = json_encode($_POST['licences']);
        $qualification = json_encode($_POST['qualification']);
        $rating = json_encode($rankingarr);
        $service = $_POST['service'];
        $desc = $_POST['description'];
        $price = $_POST['price'];
        $cid = $_POST['cid'];

        $tbl_price_info = $wpdb->prefix . "wfd_truck_truck_prices";
        $sql_add_service = "INSERT INTO $tbl_price_info (cid, service, description, price) values ('$cid', '$service', '$desc', '$price')";

        $wpdb->query($sql_add_service);
    }
}


function wfd_ref_truck_plugin_activation()
{
    require_once(ABSPATH . '/wp-admin/includes/upgrade.php');

    global $wpdb;
    $charset_collate = $wpdb->get_charset_collate();

    $tbl_pay_m = $wpdb->prefix . "wfd_truck_pay_m";
    $sql_pay_m = "CREATE TABLE IF NOT EXISTS $tbl_pay_m (
    `id` int(9) NOT NULL AUTO_INCREMENT,
    `method` varchar(50) NOT NULL,
      PRIMARY KEY (id)
    ) $charset_collate";
    dbDelta($sql_pay_m);


    $tbl_assistance = $wpdb->prefix . "wfd_truck_assistance";
    $sql_assistance = "CREATE TABLE IF NOT EXISTS $tbl_assistance (
    `id` int(9) NOT NULL AUTO_INCREMENT,
    `assistance` varchar(150) NOT NULL,
      PRIMARY KEY (id)
    ) $charset_collate";
    dbDelta($sql_assistance);


    $tbl_assistance_u = $wpdb->prefix . "wfd_truck_assistance_u";
    $sql_assistance_u = "CREATE TABLE IF NOT EXISTS $tbl_assistance_u (
    `id` int(9) NOT NULL AUTO_INCREMENT,
      `aid` int(9) NOT NULL,
      `cid` int(9) NOT NULL,
      PRIMARY KEY (id)
    ) $charset_collate";
    dbDelta($sql_assistance_u);

    $tbl_call_num = $wpdb->prefix . "wfd_truck_call_num";
    $sql_call_num = "CREATE TABLE IF NOT EXISTS $tbl_call_num (
    `id` int(9) NOT NULL AUTO_INCREMENT,
    `cid` int(9) NOT NULL,
    `name` varchar(100) NOT NULL,
    `phone` varchar(20) NOT NULL,
    `note` varchar(500) NOT NULL,
    `category` varchar(100) NOT NULL,
      PRIMARY KEY (id)
    ) $charset_collate";
    dbDelta($sql_call_num);


    $tbl_client_info = $wpdb->prefix . "wfd_truck_client_info";
    $sql_client_info = "CREATE TABLE IF NOT EXISTS $tbl_client_info (
    `id` int(9) NOT NULL AUTO_INCREMENT,
    `type` int(9) NOT NULL DEFAULT '1',
    `company` varchar(250) NOT NULL,
    `email` varchar(100) NOT NULL,
    `username` varchar(50) NOT NULL,
    `password` varchar(100) NOT NULL,
    `street` varchar(250) NOT NULL,
    `zip` varchar(10) NOT NULL,
    `city` varchar(100) NOT NULL,
    `phone` varchar(25) NOT NULL,
    `fax` varchar(25) NOT NULL,
    `website` varchar(100) NOT NULL,
    `emergency_phone` varchar(25) NOT NULL,
    `note` longtext NOT NULL,
      PRIMARY KEY (id)
    ) $charset_collate";
    dbDelta($sql_client_info);

    $tbl_driver_info = $wpdb->prefix . "wfd_truck_driver_info";
    $sql_driver_info = "CREATE TABLE IF NOT EXISTS $tbl_driver_info (
    `id` int(9) NOT NULL AUTO_INCREMENT,
    `cid` int(9) NOT NULL,
    `type` varchar(25) NOT NULL,
    `fname` varchar(100) NOT NULL,
    `lname` varchar(100) NOT NULL,
    `street` varchar(100) NOT NULL,
    `city` varchar(100) NOT NULL,
    `phone` varchar(20) NOT NULL,
    `note` varchar(500) NOT NULL,
    `picture` varchar(100) NOT NULL,
      PRIMARY KEY (id)
    ) $charset_collate";
    dbDelta($sql_driver_info);

    $tbl_driver_licences = $wpdb->prefix . "wfd_truck_driver_licences";
    $sql_driver_licences = "CREATE TABLE IF NOT EXISTS $tbl_driver_licences (
    `id` int(9) NOT NULL AUTO_INCREMENT,
    
    `licences` varchar(50) NOT NULL,
      PRIMARY KEY (id)
    ) $charset_collate";
    dbDelta($sql_driver_licences);

    $tbl_driver_qualification = $wpdb->prefix . "wfd_truck_driver_qualification";
    $sql_driver_qualification = "CREATE TABLE IF NOT EXISTS $tbl_driver_qualification (
    `id` int(9) NOT NULL AUTO_INCREMENT,
    
    `qualification` varchar(50) NOT NULL,
      PRIMARY KEY (id)
    ) $charset_collate";
    dbDelta($sql_driver_qualification);

    $tbl_driver_ranking = $wpdb->prefix . "wfd_truck_driver_ranking";
    $sql_driver_ranking = "CREATE TABLE IF NOT EXISTS $tbl_driver_ranking (
    `id` int(9) NOT NULL AUTO_INCREMENT,
    `rank_item` varchar(50) NOT NULL,
      PRIMARY KEY (id)
    ) $charset_collate";
    dbDelta($sql_driver_ranking);


    $tbl_mobi_service = $wpdb->prefix . "wfd_truck_mobi_service";
    $sql_mobi_service = "CREATE TABLE IF NOT EXISTS $tbl_mobi_service (
    `id` int(9) NOT NULL AUTO_INCREMENT,
    `cid` int(9) NOT NULL,
    `mobi_service` varchar(150) NOT NULL,
      PRIMARY KEY (id)
    ) $charset_collate";
    dbDelta($sql_mobi_service);

    $tbl_operating_hours = $wpdb->prefix . "wfd_truck_operating_hours";
    $sql_operating_hours = "CREATE TABLE IF NOT EXISTS $tbl_operating_hours (
    `id` int(9) NOT NULL AUTO_INCREMENT,
    `cid` int(9) NOT NULL,
    `type` varchar(50) NOT NULL,
    `rdays_start` varchar(25) NOT NULL,
    `rdays_end` varchar(25) NOT NULL,
    `weday_start` varchar(25) NOT NULL,
    `weday_end` varchar(25) NOT NULL,
    `wday_start` varchar(25) NOT NULL,
    `wday_end` varchar(25) NOT NULL,
      PRIMARY KEY (id)
    ) $charset_collate";
    dbDelta($sql_operating_hours);


    $tbl_truck_partner = $wpdb->prefix . "wfd_truck_truck_partner";
    $sql_truck_partner = "CREATE TABLE IF NOT EXISTS $tbl_truck_partner (
    `id` int(9) NOT NULL AUTO_INCREMENT,
    `partner` varchar(100) NOT NULL,
      PRIMARY KEY (id)
    ) $charset_collate";
    dbDelta($sql_truck_partner);

    $tbl_truck_partner_u = $wpdb->prefix . "wfd_truck_truck_partner_u";
    $sql_truck_partner_u = "CREATE TABLE IF NOT EXISTS $tbl_truck_partner_u (
    `id` int(9) NOT NULL AUTO_INCREMENT,
    `pid` int(9) NOT NULL,
    `cid` int(9) NOT NULL,
      PRIMARY KEY (id)
    ) $charset_collate";
    dbDelta($sql_truck_partner_u);

    $tbl_truck_pay_m_u = $wpdb->prefix . "wfd_truck_truck_pay_m_u";
    $sql_truck_pay_m_u = "CREATE TABLE IF NOT EXISTS $tbl_truck_pay_m_u (
    `id` int(9) NOT NULL AUTO_INCREMENT,
    `payid` int(9) NOT NULL,
  `cid` int(9) NOT NULL,
      PRIMARY KEY (id)
    ) $charset_collate";
    dbDelta($sql_truck_pay_m_u);

    $tbl_truck_prices = $wpdb->prefix . "wfd_truck_truck_prices";
    $sql_truck_prices = "CREATE TABLE IF NOT EXISTS $tbl_truck_prices (
    `id` int(9) NOT NULL AUTO_INCREMENT,
    `cid` int(9) NOT NULL,
  `service` varchar(200) NOT NULL,
  `description` varchar(300) NOT NULL,
  `price` varchar(50) NOT NULL,
      PRIMARY KEY (id)
    ) $charset_collate";
    dbDelta($sql_truck_prices);


    $tbl_truck_truck_info = $wpdb->prefix . "wfd_truck_truck_truck_info";
    $sql_truck_truck_info = "CREATE TABLE IF NOT EXISTS $tbl_truck_truck_info (
    `id` int(9) NOT NULL AUTO_INCREMENT,
    `cid` int(9) NOT NULL,
  `brand` varchar(100) NOT NULL,
  `weight` varchar(100) NOT NULL,
  `max_load` varchar(100) NOT NULL,
  `load_height` varchar(100) NOT NULL,
  `type` varchar(100) NOT NULL,
  `status` varchar(100) NOT NULL,
  `plateau_height` varchar(50) NOT NULL,
  `plateau_lengh` varchar(50) NOT NULL,
  `spectacle_force` varchar(50) NOT NULL,
  `cable_winch_force` varchar(50) NOT NULL,
  `crane` varchar(50) NOT NULL,
  `motorcycle` varchar(50) NOT NULL,
  `seats` varchar(50) NOT NULL,
  `uder_lift` varchar(50) NOT NULL,
  `picture` varchar(500) NOT NULL,
  `license` VARCHAR( 50 ) NOT NULL ,
  `qualification` VARCHAR( 500 ) NOT NULL ,
  `rating` VARCHAR( 500 ) NOT NULL,
      PRIMARY KEY (id)
    ) $charset_collate";
    dbDelta($sql_truck_truck_info);
}

register_activation_hook(__FILE__, 'wfd_ref_truck_plugin_activation');


add_action('wp_ajax_nopriv_check_user', 'wfd_check_user_fn');
add_action('wp_ajax_check_user', 'wfd_check_user_fn');

function wfd_check_user_fn()
{
    global $wpdb;
    $username = $_REQUEST['username'];
    $tbl_client_info = $wpdb->prefix . "wfd_truck_client_info";
    $res_client_info = $wpdb->get_results("select * from $tbl_client_info where username='$username'", OBJECT);
    if ($res_client_info[0]->username) {
        echo "Username: " . $res_client_info[0]->username . ' already taken. Use different one.';
    }
    die();
}

add_action('wp_ajax_nopriv_check_email', 'wfd_check_email_fn');
add_action('wp_ajax_check_email', 'wfd_check_email_fn');

function wfd_check_email_fn()
{
    global $wpdb;
    $email = $_REQUEST['email'];
    $tbl_client_info = $wpdb->prefix . "wfd_truck_client_info";
    $res_client_info = $wpdb->get_results("select * from $tbl_client_info where email='$email'", OBJECT);
    if ($res_client_info[0]->email) {
        echo "Eamil: " . $res_client_info[0]->email . ' already in use. Use different one.';
    }
    die();
}

add_shortcode('wfd_truck_user_dashboard', 'wfd_truck_user_dashboard_fn');

function wfd_admin_view()
{
    global $wpdb;
    $tbl_client_info = $wpdb->prefix . "wfd_truck_client_info";
    $id = $_SESSION['client_id'];
    $res_client_list = $wpdb->get_results("select * from $tbl_client_info", OBJECT);
    $res_client_info = $wpdb->get_results("select * from $tbl_client_info where id=$id", OBJECT);
    $res_company_list = $wpdb->get_results("SELECT DISTINCT `company` FROM $tbl_client_info ORDER BY 'company'", OBJECT);
    $res_zip_list = $wpdb->get_results("SELECT DISTINCT `zip` FROM $tbl_client_info ORDER BY 'zip'", OBJECT);
    $res_city_list = $wpdb->get_results("SELECT DISTINCT `city` FROM $tbl_client_info ORDER BY 'city'", OBJECT);
    if (count($res_client_list) == 0 || $res_client_info[0]->type == 1) {
        ?>
        <div role="tabpanel" class="tab-pane" id="client_list">
            <div class="col-sm-9">
                <h2><?php _e('Clients List', 'wfd_truck'); ?></h2>
                <div class="row" style="margin-left: 0px;">
                    <div class="col-sm-3" style="padding-left: 0px;"><select
                                class="selectpicker form-control" id="filter-company">
                            <option value="" selected disabled
                                    hidden><?php _e('filter company', 'wfd_truck') ?></option>
                            <?php
                            foreach ($res_company_list as $company) {
                                echo "<option>" . $company->company . "</option>";
                            } ?>
                        </select></div>

                    <div class="col-sm-2"><select class="selectpicker form-control"
                                                  id="filter-zip">
                            <option value="" selected disabled
                                    hidden><?php _e('filter ZIP', 'wfd_truck') ?></option>
                            <?php
                            foreach ($res_zip_list as $zip) {
                                echo "<option>$zip->zip</option>";
                            } ?>
                        </select></div>
                    <div class="col-sm-3"><select class="selectpicker form-control"
                                                  id="filter-city">
                            <option value="" selected disabled
                                    hidden><?php _e('filter city', 'wfd_truck') ?></option>
                            <?php
                            foreach ($res_city_list as $city) {
                                echo "<option>$city->city</option>";
                            } ?>
                        </select></div>
                </div>
                <table class="table table-striped" data-toggle="table" id="clients-list">
                    <thead>
                    <tr>
                        <th data-field="company"
                            data-sortable="true"><?php _e('Company', 'wfd_truck'); ?></th>
                        <th data-field="street"><?php _e('Street', 'wfd_truck'); ?></th>
                        <th data-field="zip"
                            data-sortable="true"><?php _e('Zip', 'wfd_truck'); ?></th>
                        <th data-field="city"
                            data-sortable="true"><?php _e('City', 'wfd_truck'); ?></th>
                        <th><?php _e('Phone', 'wfd_truck'); ?></th>
                        <th><?php _e('Note', 'wfd_truck'); ?></th>
                        <th><?php _e('Action', 'wfd_truck'); ?></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php

                    foreach ($res_client_list as $client) { ?>
                        <tr data-userid="<?php echo $client->id ?>">
                            <td><?php echo $client->company ?></td>
                            <td><?php echo $client->street ?></td>
                            <td><?php echo $client->zip ?></td>
                            <td><?php echo $client->city ?></td>
                            <td><?php echo $client->phone ?></td>
                            <td><?php echo $client->note ?></td>
                            <td>
                                <button type="button" class="btn btn-link" data-toggle="modal"
                                        data-target="#mdvide_<?php echo $client->id ?>"><?php _e('View', 'wfd_truck'); ?></button>
                                ||
                                <button type="button" class="btn btn-link" data-toggle="modal"
                                        data-target="#mdvide_<?php echo $client->id ?>"><?php _e('Edit', 'wfd_truck'); ?></button>
                                ||
                                <button type="button" class="btn btn-link" data-toggle="modal"
                                        data-target="#mdvide_<?php echo $client->id ?>"><?php _e('Delete', 'wfd_truck'); ?></button>


                                <!-- Modal -->
                                <div class="modal fade" id="mdvide_<?php echo $client->id ?>"
                                     tabindex="-1"
                                     role="dialog" aria-labelledby="myModalLabel">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <button type="button" class="close"
                                                        data-dismiss="modal"
                                                        aria-label="Close"><span
                                                            aria-hidden="true">&times;</span>
                                                </button>
                                                <h4 class="modal-title"
                                                    id="myModalLabel"><?php _e('Details', 'wfd_truck'); ?></h4>
                                            </div>
                                            <div class="modal-body">


                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-default"
                                                        data-dismiss="modal"><?php _e('Close', 'wfd_truck'); ?></button>
                                                <button type="button"
                                                        class="btn btn-primary"><?php _e('Save changes', 'wfd_truck'); ?></button>
                                            </div>
                                        </div>
                                    </div>
                                </div>


                            </td>
                        </tr>
                    <?php } ?>
                    </tbody>
                </table>
                <button type="button"
                        class="btn btn-primary" data-toggle="modal" data-target="#add_client">
                    <span class="glyphicon glyphicon-user"></span><span
                            class="glyphicon glyphicon-plus"></span><?php _e('add Client', 'wfd_truck'); ?>
                </button>
            </div>
            <div class="modal fade" id="add_client"
                 tabindex="-1"
                 role="dialog" aria-labelledby="myModalLabel">
                <div class="modal-dialog" role="document">

                    <form method="post">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close"
                                        data-dismiss="modal"
                                        aria-label="Close"><span
                                            aria-hidden="true">&times;</span>
                                </button>
                                <h4 class="modal-title"><?php _e('New Client', 'wfd_truck'); ?></h4>
                            </div>
                            <div class="well">
                                <div class="container-fluid">
                                    <div class="row form-group">
                                        <div class="col-sm-4">
                                            <label><?php _e('Company Name', 'wfd_truck') ?></label>
                                        </div>
                                        <div class="col-sm-4"><input class="form-control"
                                                                     id="new_company_name">
                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <div class="col-sm-4">
                                            <label><?php _e('User Name', 'wfd_truck') ?></label>
                                        </div>
                                        <div class="col-sm-4"><input class="form-control"
                                                                     id="new_user_name">
                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <div class="col-sm-4">
                                            <label for="new_email_address"><?php _e('Email', 'wfd_truck') ?></label>
                                        </div>
                                        <div class="col-sm-4"><input class="form-control" type="email"
                                                                     id="new_email_address" name="email_name">
                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <div class="col-sm-4">
                                            <label><?php _e('Password', 'wfd_truck') ?></label>
                                        </div>
                                        <div class="col-sm-4"><input class="form-control"
                                                                     id="new_password">
                                        </div>
                                        <div class="col-sm-4">
                                            <button type="button" class="btn btn-primary" id="generate_pw">
                                                <span class="glyphicon glyphicon-pencil"></span>
                                                <?php _e('Generate', 'wfd_truck'); ?>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                            </div>
                            <div class="modal-footer form-group">
                                <button type="button" class="btn btn-primary"
                                        id="add_new_clinet"><span
                                            class="glyphicon glyphicon-save-file"></span> <?php _e('Save', 'wfd_truck'); ?>
                                </button>
                            </div>
                        </div>
                    </form>

                </div>
            </div>
            <div class="modal fade" id="add_client_info" tabindex="-1"
                 role="dialog" aria-labelledby="myModalLabel">
                <div class="modal-dialog" role="document">

                    <form method="post">
                        <div class="modal-content">
                            <div class="modal-header">
                                <button type="button" class="close"
                                        data-dismiss="modal"
                                        aria-label="Close"><span
                                            aria-hidden="true">&times;</span>
                                </button>
                                <h4 class="modal-title"><?php _e('Client Core Data', 'wfd_truck'); ?></h4>
                            </div>
                            <div class="well">
                                <div class="container-fluid">
                                    <div class="row form-group">
                                        <div class="col-sm-4">
                                            <label><?php _e('Street', 'wfd_truck') ?></label>
                                        </div>
                                        <div class="col-sm-4"><input class="form-control"
                                                                     id="new_street">
                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <div class="col-sm-4">
                                            <label><?php _e('ZIP', 'wfd_truck') ?></label>
                                        </div>
                                        <div class="col-sm-4"><input class="form-control"
                                                                     id="new_zip">
                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <div class="col-sm-4">
                                            <label for="new_email_address"><?php _e('City', 'wfd_truck') ?></label>
                                        </div>
                                        <div class="col-sm-4"><input class="form-control" type="email"
                                                                     id="new_city" name="email_name">
                                        </div>
                                    </div>
                                    <div class="row form-group">
                                        <div class="col-sm-4">
                                            <label><?php _e('Phone', 'wfd_truck') ?></label>
                                        </div>
                                        <div class="col-sm-4"><input class="form-control"
                                                                     id="new_phone">
                                        </div>
                                    </div>
                                </div>
                                <div class="row form-group">
                                    <div class="col-sm-4">
                                        <label><?php _e('Note', 'wfd_truck') ?></label>
                                    </div>
                                    <div class="col-sm-4"><input class="form-control"
                                                                 id="new_note">
                                    </div>
                                </div>
                            </div>

                            </div>
                            <div class="modal-footer form-group">
                                <button type="button" class="btn btn-primary"
                                        id="add_clinet_core"><span
                                            class="glyphicon glyphicon-save-file"></span> <?php _e('Save', 'wfd_truck'); ?>
                                </button>
                            </div>
                        </div>
                    </form>

                </div>
    <?php }

}

function wfd_truck_user_dashboard_fn()
{
    my_enqueue();
    if (!isset($_GET['action']))
        $_GET['action'] = 'profile';

    ?>
    <div class="container">

        <?php

        // print_r($_SESSION);

        if (isset($_GET['act']) && $_GET['act'] == 'logout') {
            $_SESSION['client_login'] = 'false';
            $_SESSION['client_username'] = '';
            $_SESSION['client_id'] = '';
            ?>
            <h2><?php _e('Logedout', 'wfd_truck'); ?> </h2>
            <script>
                setTimeout(function () {
                    window.location.href = "<?php echo site_url(); ?>";
                }, 1000);
            </script>
            <?php
        }

        if ($_SESSION['client_login'] != 'true') {
            ?>

            <div class="row">
                <div class="col-sm-4 col-sm-offset-4">
                    <form method="POST">
                        <label><b><?php _e('Username', 'wfd_truck'); ?></b></label>
                        <input type="text" placeholder="Enter Username" name="username" required class="form-control">

                        <label><b><?php _e('Password', 'wfd_truck'); ?></b></label>
                        <input type="password" placeholder="Enter Password" name="password" required
                               class="form-control">

                        <input type="submit" class="btn btn-lg btn-default" value="<?php _e('Login', 'wfd_truck'); ?>"
                               name="client_login">
                    </form>
                </div>
            </div>

            <style>
                .btn.btn-lg.btn-default {
                    background: navajowhite;
                    height: 55px;
                    margin-top: 10px;
                }

                input[type='text'], input[type='password'] {
                    font-size: 14px !important;
                    border: 1px solid #777 !important;
                }
            </style>
            <?php
        } else {
            ?>
            <div class="row">
                <div class="col-sm-12 nav navbar-inverse">
                    <div class="col-sm-3"></div>
                    <div class="col-sm-9">
                        <ul class="pull-right nav  navbar-nav">
                            <li>
                                <a href=""><?php _e('Welcome', 'wfd_truck'); ?><?php echo $_SESSION['client_username'] ?></a>
                            </li>
                            <li><a href="?act=logout"><?php _e('Logout', 'wfd_truck'); ?></a></li>
                        </ul>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col-sm-3 hidden">
                    <div class="sidebar-nav">
                        <div class="navbar navbar-default" role="navigation">
                            <div class="navbar-header">
                                <button type="button" class="navbar-toggle" data-toggle="collapse"
                                        data-target=".sidebar-navbar-collapse">
                                    <span class="sr-only">Toggle navigation</span>
                                    <span class="icon-bar"></span>
                                    <span class="icon-bar"></span>
                                    <span class="icon-bar"></span>
                                </button>
                                <span class="visible-xs navbar-brand"><?php _e('Profile', 'wfd_truck'); ?></span>
                            </div>
                            <div class="navbar-collapse collapse sidebar-navbar-collapse">
                                <ul class="nav navbar-nav">
                                    <li class="active"><a
                                                href="?action=profile"><?php _e('Profile', 'wfd_truck'); ?></a></li>
                                    <!--                                                <li><a href="?action=edit-profile">Edit Profile</a></li>-->
                                    <li><a href="?action=review"><?php _e('Reviews', 'wfd_truck'); ?></a></li>
                                </ul>

                                <ul class="nav navbar-nav">
                                    <li><a href="?act=logout"><?php _e('Logout', 'wfd_truck'); ?></a></li>
                                </ul>
                            </div><!--/.nav-collapse -->
                        </div>
                    </div>
                </div>
                <div class="col-sm-12">
                    <?php if ($_GET['action'] == 'profile') {
                        global $wpdb;
                        $id = $_SESSION['client_id'];

                        $tbl_client_info = $wpdb->prefix . "wfd_truck_client_info";
                        $res_client_list = $wpdb->get_results("select * from $tbl_client_info", OBJECT);
                        $res_client_info = $wpdb->get_results("select * from $tbl_client_info where id=$id", OBJECT);

                        $tbl_operating_hours = $wpdb->prefix . "wfd_truck_operating_hours";
                        $res_operating_hours_off = $wpdb->get_results("select * from $tbl_operating_hours where cid=$id and type='Office'", OBJECT);
                        $res_operating_hours_gar = $wpdb->get_results("select * from $tbl_operating_hours where cid=$id and type='Garage'", OBJECT);
                        $res_operating_hours_carrent = $wpdb->get_results("select * from $tbl_operating_hours where cid=$id and type='Car Rental'", OBJECT);
                        $res_operating_hours_oncall = $wpdb->get_results("select * from $tbl_operating_hours where cid=$id and type='On call duty'", OBJECT);

                        $tbl_pay_m = $wpdb->prefix . "wfd_truck_pay_m";
                        $res_pay_m = $wpdb->get_results("select * from $tbl_pay_m", OBJECT);

                        $tbl_pay_m_u = $wpdb->prefix . "wfd_truck_truck_pay_m_u";
                        $res_pay_m_u = $wpdb->get_results("select * from $tbl_pay_m_u where cid=$id", OBJECT);


                        $tbl_partner = $wpdb->prefix . "wfd_truck_truck_partner";
                        $res_partner = $wpdb->get_results("select * from $tbl_partner", OBJECT);

                        $tbl_partner_u = $wpdb->prefix . "wfd_truck_truck_partner_u";
                        $res_partner_u = $wpdb->get_results("select * from $tbl_partner_u where cid=$id", OBJECT);


                        $tbl_assistance = $wpdb->prefix . "wfd_truck_assistance";
                        $res_assistance = $wpdb->get_results("select * from $tbl_assistance", OBJECT);


                        $tbl_assistance_u = $wpdb->prefix . "wfd_truck_assistance_u";
                        $res_assistance_u = $wpdb->get_results("select * from $tbl_assistance_u where cid=$id", OBJECT);

                        $tbl_mobi_service = $wpdb->prefix . "wfd_truck_mobi_service";
                        $res_mobi_service = $wpdb->get_results("select * from $tbl_mobi_service where cid=$id", OBJECT);

                        $tbl_driver_info = $wpdb->prefix . "wfd_truck_driver_info";
                        $res_driver = $wpdb->get_results("select * from $tbl_driver_info where cid=$id and type='Driver'", OBJECT);
                        $res_driver_truck = $wpdb->get_results("select * from $tbl_driver_info where cid=$id and type='Pickup Driver'", OBJECT);

                        $tbl_call_num = $wpdb->prefix . "wfd_truck_call_num";
                        $res_call_num = $wpdb->get_results("select * from $tbl_call_num where cid=$id", OBJECT);


                        $tbl_truck_info = $wpdb->prefix . "wfd_truck_truck_truck_info";
                        $res_truck_info = $wpdb->get_results("select * from $tbl_truck_info where cid=$id", OBJECT);


                        $tbl_prices = $wpdb->prefix . "wfd_truck_truck_prices";
                        $res_prices = $wpdb->get_results("select * from $tbl_prices where cid=$id", OBJECT);


// echo "select * from $tbl_driver_info where cid=$id and type='Driver'";
                        //wp_wfd_truck_truck_truck_info
                        //echo "select * from $tbl_call_num where cid=$id";


                        ?>

                        <link rel="stylesheet" href="<?php echo plugin_dir_url(__FILE__) ?>css/bootstrap.min.css">
                        <link rel="stylesheet" href="<?php echo plugin_dir_url(__FILE__) ?>css/bootstrap-theme.min.css">
                        <!--<link rel="stylesheet" href="<?php echo plugin_dir_url(__FILE__) ?>css/jquery.dataTables.min.css">-->

                        <!--<script src="<?php echo plugin_dir_url(__FILE__) ?>js/jquery.dataTables.min.js"></script>-->
                        <!--<script src="<?php echo plugin_dir_url(__FILE__) ?>js/bootstrap.min.js"></script>-->

                        <br>
                        <ul class="nav nav-tabs" role="tablist">
                            <?php
                            if (count($res_client_list) == 0 || $res_client_info[0]->type == 1) {
                                ?>
                                <li role="presentation"><a href="#client_list" aria-controls="Clients List" role="tab"
                                                           data-toggle="tab"><?php _e('Clients List', 'wfd_truck'); ?></a>
                                </li>
                            <?php } ?>
                            <li role="presentation" class="active"><a href="#core" aria-controls="CoreData" role="tab"
                                                                      data-toggle="tab"><?php _e('Core Data', 'wfd_truck'); ?></a>
                            </li>
                            <li role="presentation"><a href="#driver" aria-controls="Driver" role="tab"
                                                       data-toggle="tab"><?php _e('Driver', 'wfd_truck'); ?></a></li>
                            <li role="presentation"><a href="#pdriver" aria-controls="PickupDriver" role="tab"
                                                       data-toggle="tab"><?php _e('Pickup Driver', 'wfd_truck'); ?></a>
                            </li>
                            <li role="presentation"><a href="#tpool" aria-controls="TruckPool" role="tab"
                                                       data-toggle="tab"><?php _e('Truck Pool', 'wfd_truck'); ?></a>
                            </li>
                            <li role="presentation"><a href="#callNum" aria-controls="CallNumber" role="tab"
                                                       data-toggle="tab"><?php _e('Call Numbers', 'wfd_truck'); ?></a>
                            </li>
                            <li role="presentation"><a href="#prices" aria-controls="Prices" role="tab"
                                                       data-toggle="tab"><?php _e('Prices', 'wfd_truck'); ?></a></li>
                            <li role="presentation"><a href="#handover" aria-controls="Handover" role="tab"
                                                       data-toggle="tab"><?php _e('Handover', 'wfd_truck'); ?></a></li>
                        </ul>
                        <div class="tab-content">
                            <?php wfd_admin_view(); ?>
                            <div role="tabpanel" class="tab-pane active" id="core">

                                <div class="col-sm-3">
                                    <h2><?php _e('Core Info', 'wfd_truck'); ?></h2>
                                    <?php

                                    foreach ($res_client_info as $r1) { ?>

                                        <div><strong><?php _e('Company', 'wfd_truck'); ?>
                                                : </strong><?php echo $r1->company ?></div>
                                        <div><strong><?php _e('Email', 'wfd_truck'); ?>
                                                :</strong><?php echo $r1->email ?></div>
                                        <div><strong><?php _e('Username', 'wfd_truck'); ?>
                                                :</strong><?php echo $r1->username ?></div>
                                        <div><strong><?php _e('Street', 'wfd_truck'); ?>
                                                :</strong><?php echo $r1->street ?></div>
                                        <div><strong><?php _e('Zip', 'wfd_truck'); ?>:</strong><?php echo $r1->zip ?>
                                        </div>
                                        <div><strong><?php _e('City', 'wfd_truck'); ?>:</strong><?php echo $r1->city ?>
                                        </div>
                                        <div><strong><?php _e('Phone', 'wfd_truck'); ?>
                                                :</strong><?php echo $r1->phone ?></div>
                                        <div><strong><?php _e('Fax', 'wfd_truck'); ?>:</strong><?php echo $r1->fax ?>
                                        </div>
                                        <div><strong><?php _e('Emergency Phone', 'wfd_truck'); ?>
                                                :</strong><?php echo $r1->emergency_phone ?></div>
                                        <div><strong><?php _e('Website', 'wfd_truck'); ?>
                                                :</strong><?php echo $r1->website ?></div>
                                        <div><strong><?php _e('Note', 'wfd_truck'); ?>:</strong>
                                            <p><?php echo $r1->note ?></p></div>

                                        <button class="btn btn-primary" type="button" data-toggle="collapse"
                                                data-target="#collapseExample"
                                                aria-expanded="false" aria-controls="collapseExample"><span
                                                    class="glyphicon glyphicon-pencil"></span><?php _e('   Edit', 'wfd_truck'); ?>
                                        </button>
                                        <a href="#changePass"
                                           data-toggle="collapse"><?php _e('Change Password', 'wfd_truck'); ?></a>

                                        <div class="collapse" id="changePass">
                                            <div class="well">
                                                <h3><?php _e('Change Password', 'wfd_truck'); ?></h3>
                                                <form method="POST" id="change_pass">

                                                    <div class="form-group">
                                                        <label><?php _e('New Password', 'wfd_truck'); ?></label>
                                                        <input type="password" class="form-control" name="new_pass"
                                                               id="new_pass" required="required">
                                                    </div>
                                                    <div class="form-group">
                                                        <label><?php _e('Re-type Password', 'wfd_truck'); ?></label>
                                                        <input type="password" class="form-control" name="retyp_pass"
                                                               id="retyp_pass" required="required">
                                                        <p class="alert-danger" style="display:none"
                                                           id="retyp_error"><?php _e('Password and retype password not match', 'wfd_truck'); ?></p>
                                                        <input type="hidden" name="id"
                                                               value="<?php echo $res_client_info[0]->id ?>">
                                                    </div>
                                                    <input type="submit" class="btn btn-primary"
                                                           value="<?php _e('Update Password', 'wfd_truck'); ?>"
                                                           name="chang_pass">
                                                </form>
                                            </div>
                                        </div>

                                        <script>

                                            jQuery('#change_pass').submit(function () {

                                                // Get the Login Name value and trim it

                                                var new_pass = jQuery('#new_pass').val();
                                                var retyp_pass = jQuery('#retyp_pass').val();

                                                // Check if empty of not
                                                if (new_pass != retyp_pass) {
                                                    jQuery('#retyp_error').show();
                                                    return false;
                                                }
                                            });
                                        </script>

                                        <div class="collapse" id="collapseExample">

                                            <?php
                                            $id = $_SESSION['client_id'];
                                            $res_client_info_id = $wpdb->get_results("select * from $tbl_client_info where id=$id limit 1", OBJECT);

                                            if (count($res_client_info_id) > 0) {
                                                //print_r($res_client_info_id);
                                                ?>


                                                <div class="well">

                                                    <h2><?php _e('Edit New Client', 'wfd_truck'); ?></h2>

                                                    <form method="POST">
                                                        <div class="form-group">
                                                            <label><?php _e('Company', 'wfd_truck'); ?></label>
                                                            <input type="text" name="company" class="form-control"
                                                                   placeholder="Company Name"
                                                                   value="<?php echo $res_client_info_id[0]->company ?>">
                                                        </div>
                                                        <div class="form-group">
                                                            <label><?php _e('Email', 'wfd_truck'); ?></label>
                                                            <?php echo $res_client_info_id[0]->email ?>

                                                        </div>
                                                        <div class="form-group">
                                                            <label><?php _e('Username', 'wfd_truck'); ?></label>
                                                            <?php echo $res_client_info_id[0]->username ?>
                                                        </div>

                                                        <div class="form-group">
                                                            <label><?php _e('Street', 'wfd_truck'); ?></label>
                                                            <input type="text" name="street" class="form-control"
                                                                   placeholder="street"
                                                                   value="<?php echo $res_client_info_id[0]->street ?>">
                                                        </div>
                                                        <div class="form-group">
                                                            <label><?php _e('Zip', 'wfd_truck'); ?></label>
                                                            <input type="text" name="zip" class="form-control"
                                                                   placeholder="zip"
                                                                   value="<?php echo $res_client_info_id[0]->zip ?>">
                                                        </div>
                                                        <div class="form-group">
                                                            <label><?php _e('City', 'wfd_truck'); ?></label>
                                                            <input type="text" name="city" class="form-control"
                                                                   placeholder="city"
                                                                   value="<?php echo $res_client_info_id[0]->city ?>">
                                                        </div>
                                                        <div class="form-group">
                                                            <label><?php _e('Phone', 'wfd_truck'); ?></label>
                                                            <input type="text" name="phone" class="form-control"
                                                                   placeholder="phone"
                                                                   value="<?php echo $res_client_info_id[0]->phone ?>">
                                                        </div>
                                                        <div class="form-group">
                                                            <label><?php _e('Fax', 'wfd_truck'); ?></label>
                                                            <input type="text" name="fax" class="form-control"
                                                                   placeholder="fax"
                                                                   value="<?php echo $res_client_info_id[0]->fax ?>">
                                                        </div>
                                                        <div class="form-group">
                                                            <label><?php _e('Emergency Phone', 'wfd_truck'); ?></label>
                                                            <input type="text" name="emergency_phone"
                                                                   class="form-control"
                                                                   placeholder="emergency_phone"
                                                                   value="<?php echo $res_client_info_id[0]->emergency_phone ?>">
                                                            <input type="hidden" name="id"
                                                                   value="<?php echo $res_client_info_id[0]->id ?>">
                                                        </div>
                                                        <div class="form-group">
                                                            <label><?php _e('Website', 'wfd_truck'); ?></label>
                                                            <input type="text" name="website" class="form-control"
                                                                   placeholder="website"
                                                                   value="<?php echo $res_client_info_id[0]->website ?>">
                                                        </div>

                                                        <div class="form-group">
                                                            <label><?php _e('Note', 'wfd_truck'); ?></label>
                                                            <textarea name="note" class="form-control"
                                                                      placeholder="Note here"><?php echo $res_client_info_id[0]->note ?></textarea>

                                                        </div>

                                                        <div class="form-group">
                                                            <input type="submit" class="btn btn-default"
                                                                   name="edit_new_clinet_fn"
                                                                   value="<?php _e('Edit Client', 'wfd_truck'); ?>"/>
                                                        </div>
                                                    </form>

                                                </div>
                                            <?php } ?>

                                            <div class="clearfix"></div>
                                        </div>


                                        <?php

                                    }
                                    ?>
                                </div>
                                <div class="col-sm-9">
                                    <div class="row">
                                        <div class="col-sm-12">
                                            <h2><?php _e('Opening Hours', 'wfd_truck'); ?></h2>
                                            <table class="table table-striped">
                                                <thead>
                                                <tr>
                                                    <th><?php _e('Location', 'wfd_truck'); ?></th>
                                                    <th><?php _e('Mon-Fri', 'wfd_truck'); ?></th>
                                                    <th><?php _e('Sat', 'wfd_truck'); ?></th>
                                                    <th><?php _e('Sun', 'wfd_truck'); ?></th>
                                                </tr>
                                                </thead>
                                                <tbody>
                                                <tr>
                                                    <td><?php echo $res_operating_hours_off[0]->type ?></td>
                                                    <td><?php echo $res_operating_hours_off[0]->rdays_start . ' - ' . $res_operating_hours_off[0]->rdays_end ?></td>
                                                    <td><?php echo $res_operating_hours_off[0]->weday_start . ' - ' . $res_operating_hours_off[0]->weday_end ?></td>
                                                    <td><?php echo $res_operating_hours_off[0]->wday_start . ' - ' . $res_operating_hours_off[0]->wday_end ?></td>
                                                </tr>
                                                <tr>
                                                    <td><?php echo $res_operating_hours_gar[0]->type ?></td>
                                                    <td><?php echo $res_operating_hours_gar[0]->rdays_start . ' - ' . $res_operating_hours_gar[0]->rdays_end ?></td>
                                                    <td><?php echo $res_operating_hours_gar[0]->weday_start . ' - ' . $res_operating_hours_gar[0]->weday_end ?></td>
                                                    <td><?php echo $res_operating_hours_gar[0]->wday_start . ' - ' . $res_operating_hours_gar[0]->wday_end ?></td>
                                                </tr>
                                                <tr>
                                                    <td><?php echo $res_operating_hours_carrent[0]->type ?></td>
                                                    <td><?php echo $res_operating_hours_carrent[0]->rdays_start . ' - ' . $res_operating_hours_carrent[0]->rdays_end ?></td>
                                                    <td><?php echo $res_operating_hours_carrent[0]->weday_start . ' - ' . $res_operating_hours_carrent[0]->weday_end ?></td>
                                                    <td><?php echo $res_operating_hours_carrent[0]->wday_start . ' - ' . $res_operating_hours_carrent[0]->wday_end ?></td>
                                                </tr>
                                                <tr>
                                                    <td><?php echo $res_operating_hours_oncall[0]->type ?></td>
                                                    <td><?php echo $res_operating_hours_oncall[0]->rdays_start . ' - ' . $res_operating_hours_oncall[0]->rdays_end ?></td>
                                                    <td><?php echo $res_operating_hours_oncall[0]->weday_start . ' - ' . $res_operating_hours_oncall[0]->weday_end ?></td>
                                                    <td><?php echo $res_operating_hours_oncall[0]->wday_start . ' - ' . $res_operating_hours_oncall[0]->wday_end ?></td>
                                                </tr>

                                                </tbody>
                                            </table>
                                        </div>

                                    </div>
                                    <div class="row">
                                        <div class="col-sm-3">
                                            <h3><?php _e('Payment', 'wfd_truck'); ?></h3>
                                            <ul class="nav" style="padding-left: 0; margin-left: 0">
                                                <?php

                                                foreach ($res_pay_m as $pm) { ?>
                                                    <li>
                                                        <?php
                                                        foreach ($res_pay_m_u as $pmu) {
                                                            if ($pm->id == $pmu->payid) {
                                                                ?>
                                                                <i class="fa fa-check" aria-hidden="true"></i>
                                                                <?php
                                                            } else {
                                                                echo "<span style='width: 18px; height: 15px; float: left;'></span>";
                                                            }
                                                        }
                                                        ?>
                                                        <?php echo $pm->method ?></li>
                                                <?php } ?>
                                            </ul>
                                        </div>
                                        <div class="col-sm-3">
                                            <h3><?php _e('Partner', 'wfd_truck'); ?></h3>
                                            <ul class="nav" style="padding-left: 0; margin-left: 0">
                                                <?php

                                                foreach ($res_partner as $p) { ?>
                                                    <li>
                                                        <?php
                                                        foreach ($res_partner_u as $pu) {
                                                            if ($p->id == $pu->pid) {
                                                                ?>
                                                                <i class="fa fa-check" aria-hidden="true"></i>
                                                                <?php
                                                            } else {
                                                                echo "<span style='width: 18px; height: 15px; float: left;'></span>";
                                                            }
                                                        }
                                                        ?>
                                                        <?php echo $p->partner ?></li>
                                                <?php } ?>
                                            </ul>
                                        </div>
                                        <div class="col-sm-3">
                                            <h3><?php _e('Assistance', 'wfd_truck'); ?></h3>
                                            <ul class="nav" style="padding-left: 0; margin-left: 0">
                                                <?php

                                                foreach ($res_assistance as $pa) { ?>
                                                    <li>
                                                        <?php
                                                        foreach ($res_assistance_u as $pau) {
                                                            if ($pa->id == $pau->aid) {
                                                                ?>
                                                                <i class="fa fa-check" aria-hidden="true"></i>
                                                                <?php
                                                            } else {
                                                                echo "<span style='width: 18px; height: 15px; float: left;'></span>";
                                                            }
                                                        }
                                                        ?>
                                                        <?php echo $pa->assistance ?></li>
                                                <?php } ?>
                                            </ul>
                                        </div>
                                        <div class="col-sm-3">
                                            <h3><?php _e('Mobi Services', 'wfd_truck'); ?></h3>
                                            <ul class="nav" style="padding-left: 0; margin-left: 0">
                                                <?php

                                                foreach ($res_mobi_service as $ms) { ?>
                                                    <li>

                                                        <?php echo $ms->mobi_service ?></li>
                                                <?php } ?>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div role="tabpanel" class="tab-pane" id="driver">
                                <h2><?php _e('Driver', 'wfd_truck'); ?></h2>
                                <table class="table table-striped dataTable">
                                    <thead>
                                    <tr>
                                        <th><?php _e('Sl#', 'wfd_truck'); ?></th>
                                        <th><?php _e('First Name', 'wfd_truck'); ?></th>
                                        <th><?php _e('Last Name', 'wfd_truck'); ?></th>
                                        <th><?php _e('Street', 'wfd_truck'); ?></th>
                                        <th><?php _e('City', 'wfd_truck'); ?></th>
                                        <th><?php _e('Phone', 'wfd_truck'); ?></th>
                                        <th><?php _e('Note', 'wfd_truck'); ?></th>
                                        <th><?php _e('Action', 'wfd_truck'); ?></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    $r = 1;
                                    foreach ($res_driver as $rd) {
                                        ?>
                                        <tr driver-id="<?php echo $rd->id ?>">
                                            <td><?php echo $r;
                                                $r++; ?></td>
                                            <td><?php echo $rd->fname ?></td>
                                            <td><?php echo $rd->lname ?></td>
                                            <td><?php echo $rd->street ?></td>
                                            <td><?php echo $rd->city ?></td>
                                            <td><?php echo $rd->phone ?></td>
                                            <td><?php echo $rd->note ?></td>
                                            <td>
                                                <button type="button" class="btn btn-link" data-toggle="modal"
                                                        data-target="#mdview_<?php echo $rd->id ?>"><?php _e('', 'wfd_truck'); ?>
                                                    <?php _e('View', 'wfd_truck'); ?>
                                                </button>
                                                ||
                                                <button type="button" class="btn btn-link" data-toggle="modal"
                                                        data-target="#mdedit_<?php echo $rd->id ?>"><?php _e('', 'wfd_truck'); ?>
                                                    <?php _e('Edit', 'wfd_truck'); ?>
                                                </button>
                                                ||
                                                <button type="button" class="btn btn-link" data-toggle="modal"
                                                        data-target="#mdnew_<?php echo $rd->id ?>"><?php _e('', 'wfd_truck'); ?>
                                                    <?php _e('New', 'wfd_truck'); ?>
                                                </button>
                                                ||
                                                <button type="button" class="btn btn-link" data-toggle="modal"
                                                        data-target="#mdcopy_<?php echo $rd->id ?>"><?php _e('', 'wfd_truck'); ?>
                                                    <?php _e('Copy', 'wfd_truck'); ?>
                                                </button>

                                                <!-- Modal View-->
                                                <div class="modal fade" id="mdview_<?php echo $rd->id ?>"
                                                     data-driver-id="<?php echo $rd->id ?>" tabindex="-1" role="dialog"
                                                     aria-labelledby="myModalLabel">
                                                    <div style="width: 60%" class="modal-dialog" role="document"
                                                         data-driver-id="<?php echo $rd->id ?>">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <button type="button" class="close" data-dismiss="modal"
                                                                        aria-label="Close"><span
                                                                            aria-hidden="true">&times;</span></button>
                                                                <h4 class="modal-title" id="myModalLabel">
                                                                    <?php _e('Driver:', 'wfd_truck'); ?><?php echo $rd->fname ?><?php echo $rd->lname ?></h4>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="row">
                                                                    <div class="col-sm-5">
                                                                        <h2><?php _e('Core Data', 'wfd_truck'); ?></h2>
                                                                        <form class="form-horizontal" action="#">
                                                                            <div class="form-group">
                                                                                <label class="control-label col-sm-5"><?php _e('First Name', 'wfd_truck'); ?>
                                                                                    :</label>
                                                                                <div class="col-sm-7">
                                                                                    <input type="text"
                                                                                           class="form-control"
                                                                                           name="fname"
                                                                                           value=<?php echo $rd->fname ?>>
                                                                                </div>
                                                                            </div>
                                                                            <div class="form-group">
                                                                                <label class="control-label col-sm-5"><?php _e('Last Name', 'wfd_truck'); ?>
                                                                                    :</label>
                                                                                <div class="col-sm-7">
                                                                                    <input type="text"
                                                                                           class="form-control"
                                                                                           name="lname"
                                                                                           value=<?php echo $rd->lname ?>>
                                                                                </div>
                                                                            </div>
                                                                            <div class="form-group">
                                                                                <label class="control-label col-sm-5"><?php _e('Street', 'wfd_truck'); ?>
                                                                                    :</label>
                                                                                <div class="col-sm-7">
                                                                                    <input type="text"
                                                                                           class="form-control"
                                                                                           name="street"
                                                                                           value=<?php echo $rd->street ?>>
                                                                                </div>
                                                                            </div>
                                                                            <div class="form-group">
                                                                                <label class="control-label col-sm-5"><?php _e('City', 'wfd_truck'); ?>
                                                                                    :</label>
                                                                                <div class="col-sm-7">
                                                                                    <input type="text"
                                                                                           class="form-control"
                                                                                           name="city"
                                                                                           value=<?php echo $rd->city ?>>
                                                                                </div>
                                                                            </div>
                                                                            <div class="form-group">
                                                                                <label class="control-label col-sm-5"><?php _e('Phone', 'wfd_truck'); ?>
                                                                                    :</label>
                                                                                <div class="col-sm-7">
                                                                                    <input type="text"
                                                                                           class="form-control"
                                                                                           name="phone"
                                                                                           value=<?php echo $rd->phone ?>>
                                                                                </div>
                                                                            </div>
                                                                            <div class="form-group">
                                                                                <label class="control-label col-sm-5"><?php _e('Note', 'wfd_truck'); ?>
                                                                                    :</label>
                                                                                <div class="col-sm-7">
                                                                                    <input type="text"
                                                                                           class="form-control"
                                                                                           name="note"
                                                                                           value=<?php echo $rd->note ?>>
                                                                                </div>
                                                                            </div>
                                                                        </form>
                                                                    </div>
                                                                    <div class="col-sm-1"></div>
                                                                    <div class="col-sm-4">
                                                                        <h2><?php _e('Applications', 'wfd_truck'); ?></h2>
                                                                        <form class="form-horizontal" action="#">
                                                                            <div class="form-group">
                                                                                <label class="col-sm-5"><?php _e('breakdown service', 'wfd_truck'); ?></label>
                                                                                <div class="col-sm-7"><input
                                                                                            id="input-21d" value="2"
                                                                                            type="text"
                                                                                            class="rating col-sm-6"
                                                                                            data-min=0 data-max=5
                                                                                            data-step=0.5 data-size="xs"
                                                                                            data-show-caption=false
                                                                                            title=""></div>
                                                                            </div>
                                                                            <div class="form-group">
                                                                                <label class="col-sm-5"><?php _e('drag cars', 'wfd_truck'); ?></label>
                                                                                <input id="input-21d" value="2"
                                                                                       type="text"
                                                                                       class="rating col-sm-6"
                                                                                       data-min=0 data-max=5
                                                                                       data-step=0.5 data-size="xs"
                                                                                       data-show-caption=false
                                                                                       title="">
                                                                            </div>
                                                                            <div class="form-group">
                                                                                <label class="col-sm-5"><?php _e('drag < 7.5to', 'wfd_truck'); ?></label>
                                                                                <div class="col-sm-7"><input
                                                                                            id="input-21d" value="2"
                                                                                            type="text"
                                                                                            class="rating col-sm-6"
                                                                                            data-min=0 data-max=5
                                                                                            data-step=0.5 data-size="xs"
                                                                                            data-show-caption=false
                                                                                            title=""></div>
                                                                            </div>
                                                                            <div class="form-group">
                                                                                <label class="col-sm-5"><?php _e('drag > 7.5to', 'wfd_truck'); ?></label>
                                                                                <div class="col-sm-7"><input
                                                                                            id="input-21d" value="2"
                                                                                            type="text"
                                                                                            class="rating col-sm-6"
                                                                                            data-min=0 data-max=5
                                                                                            data-step=0.5 data-size="xs"
                                                                                            data-show-caption=false
                                                                                            title=""></div>
                                                                            </div>
                                                                            <div class="form-group">
                                                                                <label class="col-sm-5"><?php _e('crane', 'wfd_truck'); ?></label>
                                                                                <div class="col-sm-7"><input
                                                                                            id="input-21d" value="2"
                                                                                            type="text"
                                                                                            class="rating col-sm-6"
                                                                                            data-min=0 data-max=5
                                                                                            data-step=0.5 data-size="xs"
                                                                                            data-show-caption=false
                                                                                            title=""></div>
                                                                            </div>
                                                                            <div class="form-group">
                                                                                <label class="col-sm-5"><?php _e('truck service', 'wfd_truck'); ?></label>
                                                                                <div class="col-sm-7"><input
                                                                                            id="input-21d" value="2"
                                                                                            type="text"
                                                                                            class="rating col-sm-6"
                                                                                            data-min=0 data-max=5
                                                                                            data-step=0.5 data-size="xs"
                                                                                            data-show-caption=false
                                                                                            title=""></div>
                                                                            </div>
                                                                        </form>
                                                                    </div>
                                                                    <div class="col-sm-2">
                                                                        <img src="wp-admin/images/4.jpg"
                                                                             class="img-thumbnail" alt="Cinque Terre"
                                                                             width="200" height="150">
                                                                        <p class="col-sm-12"><?php _e('driver photo', 'wfd_truck'); ?></p>
                                                                    </div>
                                                                </div>
                                                                <div cla="row">
                                                                    <div class="col-sm-6">
                                                                        <h2><?php _e('Driving Licences', 'wfd_truck'); ?></h2>
                                                                        <form>
                                                                            <div class="col-sm-4">
                                                                                <div class="checkbox">
                                                                                    <label><input type="checkbox"
                                                                                                  value=""
                                                                                                  checked><?php _e('C1', 'wfd_truck'); ?>
                                                                                    </label>
                                                                                </div>
                                                                                <div class="checkbox">
                                                                                    <label><input type="checkbox"
                                                                                                  value=""
                                                                                                  checked><?php _e('C1E', 'wfd_truck'); ?>
                                                                                    </label>
                                                                                </div>
                                                                                <div class="checkbox disabled">
                                                                                    <label><input type="checkbox"
                                                                                                  value=""><?php _e('crane', 'wfd_truck'); ?>
                                                                                    </label>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-sm-4">
                                                                                <div class="checkbox">
                                                                                    <label><input type="checkbox"
                                                                                                  value=""><?php _e('Kennz 95', 'wfd_truck'); ?>
                                                                                    </label>
                                                                                </div>
                                                                                <div class="checkbox">
                                                                                    <label><input type="checkbox"
                                                                                                  value=""
                                                                                                  checked><?php _e('Clubmobil', 'wfd_truck'); ?>
                                                                                    </label>
                                                                                </div>
                                                                                <div class="checkbox disabled">
                                                                                    <label><input type="checkbox"
                                                                                                  value=""><?php _e('car opening', 'wfd_truck'); ?>
                                                                                    </label>
                                                                                </div>
                                                                            </div>
                                                                        </form>
                                                                    </div>
                                                                    <div class="col-sm-6">
                                                                        <h2><?php _e('Qualification', 'wfd_truck'); ?></h2>
                                                                        <form>
                                                                            <div class="checkbox">
                                                                                <label><input type="checkbox"
                                                                                              value=""><?php _e('motor mechatronics', 'wfd_truck'); ?>
                                                                                </label>
                                                                            </div>
                                                                            <div class="checkbox">
                                                                                <label><input type="checkbox" value=""
                                                                                              checked><?php _e('motor foreman', 'wfd_truck'); ?>
                                                                                </label>
                                                                            </div>
                                                                            <div class="checkbox disabled">
                                                                                <label><input type="checkbox"
                                                                                              value=""><?php _e('learned', 'wfd_truck'); ?>
                                                                                </label>
                                                                            </div>
                                                                            <div class="checkbox disabled">
                                                                                <label><input type="checkbox"
                                                                                              value=""><?php _e('unlearned', 'wfd_truck'); ?>
                                                                                </label>
                                                                            </div>
                                                                            <div class="checkbox disabled">
                                                                                <label><input type="checkbox" value=""
                                                                                              checked><?php _e('commerical vehicle technology', 'wfd_truck'); ?>
                                                                                </label>
                                                                            </div>
                                                                        </form>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="button" class="btn btn-primary">
                                                                    <span class="glyphicon glyphicon-pencil"></span> <?php _e('Edit', 'wfd_truck'); ?>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- Modal Edit-->
                                                <div class="modal fade" id="mdedit_<?php echo $rd->id ?>" tabindex="-1"
                                                     role="dialog" aria-labelledby="myModalLabel"
                                                     data-driver-id="<?php echo $rd->id ?>">
                                                    <div style="width: 60%" class="modal-dialog" role="document">
                                                        <div class="modal-content">
                                                            <div style="background-color: #5cb85c; color: white !important;"
                                                                 class="modal-header">
                                                                <button type="button" class="close" data-dismiss="modal"
                                                                        aria-label="Close"><span
                                                                            aria-hidden="true">&times;</span></button>
                                                                <h4 class="modal-title" id="myModalLabel">
                                                                    <?php _e('Driver:', 'wfd_truck'); ?><?php echo $rd->fname ?><?php echo $rd->lname ?></h4>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="row">
                                                                    <div class="col-sm-5">
                                                                        <h2><?php _e('Core Data', 'wfd_truck'); ?></h2>
                                                                        <form class="form-horizontal" action="#">
                                                                            <div class="form-group">
                                                                                <label class="control-label col-sm-5"><?php _e('First Name', 'wfd_truck'); ?>
                                                                                    :</label>
                                                                                <div class="col-sm-7">
                                                                                    <input type="text"
                                                                                           class="form-control"
                                                                                           name="fname"
                                                                                           value=<?php echo $rd->fname ?>>
                                                                                </div>
                                                                            </div>
                                                                            <div class="form-group">
                                                                                <label class="control-label col-sm-5"><?php _e('Last Name', 'wfd_truck'); ?>
                                                                                    :</label>
                                                                                <div class="col-sm-7">
                                                                                    <input type="text"
                                                                                           class="form-control"
                                                                                           name="lname"
                                                                                           value=<?php echo $rd->lname ?>>
                                                                                </div>
                                                                            </div>
                                                                            <div class="form-group">
                                                                                <label class="control-label col-sm-5"><?php _e('Street', 'wfd_truck'); ?>
                                                                                    :</label>
                                                                                <div class="col-sm-7">
                                                                                    <input type="text"
                                                                                           class="form-control"
                                                                                           name="street"
                                                                                           value=<?php echo $rd->street ?>>
                                                                                </div>
                                                                            </div>
                                                                            <div class="form-group">
                                                                                <label class="control-label col-sm-5"><?php _e('City', 'wfd_truck'); ?>
                                                                                    :</label>
                                                                                <div class="col-sm-7">
                                                                                    <input type="text"
                                                                                           class="form-control"
                                                                                           name="city"
                                                                                           value=<?php echo $rd->city ?>>
                                                                                </div>
                                                                            </div>
                                                                            <div class="form-group">
                                                                                <label class="control-label col-sm-5"><?php _e('Phone', 'wfd_truck'); ?>
                                                                                    :</label>
                                                                                <div class="col-sm-7">
                                                                                    <input type="text"
                                                                                           class="form-control"
                                                                                           name="phone"
                                                                                           value=<?php echo $rd->phone ?>>
                                                                                </div>
                                                                            </div>
                                                                            <div class="form-group">
                                                                                <label class="control-label col-sm-5"><?php _e('Note', 'wfd_truck'); ?>
                                                                                    :</label>
                                                                                <div class="col-sm-7">
                                                                                    <input type="text"
                                                                                           class="form-control"
                                                                                           name="note"
                                                                                           value=<?php echo $rd->note ?>>
                                                                                </div>
                                                                            </div>
                                                                        </form>
                                                                    </div>
                                                                    <div class="col-sm-1"></div>
                                                                    <div class="col-sm-4">
                                                                        <h2><?php _e('Applications', 'wfd_truck'); ?></h2>
                                                                        <form class="form-horizontal" action="#">
                                                                            <div class="form-group">
                                                                                <label><?php _e('breakdown service', 'wfd_truck'); ?></label>
                                                                            </div>
                                                                            <div class="form-group">
                                                                                <label><?php _e('drag cars', 'wfd_truck'); ?></label>

                                                                            </div>
                                                                            <div class="form-group">
                                                                                <label><?php _e('drag < 7.5to', 'wfd_truck'); ?></label>

                                                                            </div>
                                                                            <div class="form-group">
                                                                                <label><?php _e('drag > 7.5to', 'wfd_truck'); ?></label>

                                                                            </div>
                                                                            <div class="form-group">
                                                                                <label><?php _e('crane', 'wfd_truck'); ?></label>

                                                                            </div>
                                                                            <div class="form-group">
                                                                                <label><?php _e('truck service', 'wfd_truck'); ?></label>

                                                                            </div>
                                                                        </form>
                                                                    </div>
                                                                    <div class="col-sm-2">
                                                                        <img src="wp-admin/images/4.jpg"
                                                                             class="img-thumbnail" alt="Cinque Terre"
                                                                             width="200" height="150">
                                                                        <p class="col-sm-12"><?php _e('driver photo', 'wfd_truck'); ?></p>
                                                                    </div>
                                                                </div>
                                                                <div cla="row">
                                                                    <div class="col-sm-6">
                                                                        <h2><?php _e('Driving Licences', 'wfd_truck'); ?></h2>
                                                                        <form>
                                                                            <div class="col-sm-4">
                                                                                <div class="checkbox">
                                                                                    <label><input type="checkbox"
                                                                                                  value=""
                                                                                                  checked><?php _e('C1', 'wfd_truck'); ?>
                                                                                    </label>
                                                                                </div>
                                                                                <div class="checkbox">
                                                                                    <label><input type="checkbox"
                                                                                                  value=""
                                                                                                  checked><?php _e('C1E', 'wfd_truck'); ?>
                                                                                    </label>
                                                                                </div>
                                                                                <div class="checkbox disabled">
                                                                                    <label><input type="checkbox"
                                                                                                  value=""><?php _e('crane', 'wfd_truck'); ?>
                                                                                    </label>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-sm-4">
                                                                                <div class="checkbox">
                                                                                    <label><input type="checkbox"
                                                                                                  value=""><?php _e('Kennz 95', 'wfd_truck'); ?>
                                                                                    </label>
                                                                                </div>
                                                                                <div class="checkbox">
                                                                                    <label><input type="checkbox"
                                                                                                  value=""
                                                                                                  checked><?php _e('Clubmobil', 'wfd_truck'); ?>
                                                                                    </label>
                                                                                </div>
                                                                                <div class="checkbox disabled">
                                                                                    <label><input type="checkbox"
                                                                                                  value=""><?php _e('car opening', 'wfd_truck'); ?>
                                                                                    </label>
                                                                                </div>
                                                                            </div>
                                                                        </form>
                                                                    </div>
                                                                    <div class="col-sm-6">
                                                                        <h2><?php _e('Qualification', 'wfd_truck'); ?></h2>
                                                                        <form>
                                                                            <div class="checkbox">
                                                                                <label><input type="checkbox"
                                                                                              value=""><?php _e('motor mechatronics', 'wfd_truck'); ?>
                                                                                </label>
                                                                            </div>
                                                                            <div class="checkbox">
                                                                                <label><input type="checkbox" value=""
                                                                                              checked><?php _e('motor foreman', 'wfd_truck'); ?>
                                                                                </label>
                                                                            </div>
                                                                            <div class="checkbox disabled">
                                                                                <label><input type="checkbox"
                                                                                              value=""><?php _e('learned', 'wfd_truck'); ?>
                                                                                </label>
                                                                            </div>
                                                                            <div class="checkbox disabled">
                                                                                <label><input type="checkbox"
                                                                                              value=""><?php _e('unlearned', 'wfd_truck'); ?>
                                                                                </label>
                                                                            </div>
                                                                            <div class="checkbox disabled">
                                                                                <label><input type="checkbox" value=""
                                                                                              checked><?php _e('commerical vehicle technology', 'wfd_truck'); ?>
                                                                                </label>
                                                                            </div>
                                                                        </form>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="submit"
                                                                        class="btn btn-primary btn-driver-save">
                                                                    <span class="glyphicon glyphicon-floppy-disk"></span> <?php _e('Save', 'wfd_truck'); ?>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- Modal New-->
                                                <div class="modal fade" id="mdnew_<?php echo $rd->id ?>"
                                                     data-driver-id="<?php echo $rd->id ?>" tabindex="-1" role="dialog"
                                                     aria-labelledby="myModalLabel">
                                                    <div style="width: 60%" class="modal-dialog" role="document"
                                                         data-driver-id="<?php echo $rd->id ?>">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <button type="button" class="close" data-dismiss="modal"
                                                                        aria-label="Close"><span
                                                                            aria-hidden="true">&times;</span></button>
                                                                <h4 class="modal-title" id="myModalLabel">
                                                                    <?php _e('Driver:', 'wfd_truck'); ?><?php echo $rd->fname ?><?php echo $rd->lname ?></h4>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="row">
                                                                    <div class="col-sm-5">
                                                                        <h2><?php _e('Core Data', 'wfd_truck'); ?></h2>
                                                                        <form class="form-horizontal" action="#">
                                                                            <div class="form-group">
                                                                                <label class="control-label col-sm-5"><?php _e('First Name', 'wfd_truck'); ?>
                                                                                    :</label>
                                                                                <div class="col-sm-7">
                                                                                    <input type="text"
                                                                                           class="form-control"
                                                                                           name="fname" value="">
                                                                                </div>
                                                                            </div>
                                                                            <div class="form-group">
                                                                                <label class="control-label col-sm-5"><?php _e('Last Name', 'wfd_truck'); ?>
                                                                                    :</label>
                                                                                <div class="col-sm-7">
                                                                                    <input type="text"
                                                                                           class="form-control"
                                                                                           name="lname" value="">
                                                                                </div>
                                                                            </div>
                                                                            <div class="form-group">
                                                                                <label class="control-label col-sm-5"><?php _e('Street', 'wfd_truck'); ?>
                                                                                    :</label>
                                                                                <div class="col-sm-7">
                                                                                    <input type="text"
                                                                                           class="form-control"
                                                                                           name="street" value="">
                                                                                </div>
                                                                            </div>
                                                                            <div class="form-group">
                                                                                <label class="control-label col-sm-5"><?php _e('City', 'wfd_truck'); ?>
                                                                                    :</label>
                                                                                <div class="col-sm-7">
                                                                                    <input type="text"
                                                                                           class="form-control"
                                                                                           name="city" value="">
                                                                                </div>
                                                                            </div>
                                                                            <div class="form-group">
                                                                                <label class="control-label col-sm-5"><?php _e('Phone', 'wfd_truck'); ?>
                                                                                    :</label>
                                                                                <div class="col-sm-7">
                                                                                    <input type="text"
                                                                                           class="form-control"
                                                                                           name="phone" value="">
                                                                                </div>
                                                                            </div>
                                                                            <div class="form-group">
                                                                                <label class="control-label col-sm-5"><?php _e('Note', 'wfd_truck'); ?>
                                                                                    :</label>
                                                                                <div class="col-sm-7">
                                                                                    <input type="text"
                                                                                           class="form-control"
                                                                                           name="note" value="">
                                                                                </div>
                                                                            </div>
                                                                        </form>
                                                                    </div>
                                                                    <div class="col-sm-1"></div>
                                                                    <div class="col-sm-4">
                                                                        <h2><?php _e('Applications', 'wfd_truck'); ?></h2>
                                                                        <form class="form-horizontal" action="#">
                                                                            <div class="form-group">
                                                                                <label><?php _e('breakdown service', 'wfd_truck'); ?></label>
                                                                            </div>
                                                                            <div class="form-group">
                                                                                <label><?php _e('drag cars', 'wfd_truck'); ?></label>

                                                                            </div>
                                                                            <div class="form-group">
                                                                                <label><?php _e('drag < 7.5to', 'wfd_truck'); ?></label>

                                                                            </div>
                                                                            <div class="form-group">
                                                                                <label><?php _e('drag > 7.5to', 'wfd_truck'); ?></label>

                                                                            </div>
                                                                            <div class="form-group">
                                                                                <label><?php _e('crane', 'wfd_truck'); ?></label>

                                                                            </div>
                                                                            <div class="form-group">
                                                                                <label><?php _e('truck service', 'wfd_truck'); ?></label>

                                                                            </div>
                                                                        </form>
                                                                    </div>
                                                                    <div class="col-sm-2">
                                                                        <img src="wp-admin/images/4.jpg"
                                                                             class="img-thumbnail" alt="Cinque Terre"
                                                                             width="200" height="150">
                                                                        <p class="col-sm-12"><?php _e('driver photo', 'wfd_truck'); ?></p>
                                                                    </div>
                                                                </div>
                                                                <div cla="row">
                                                                    <div class="col-sm-6">
                                                                        <h2><?php _e('Driving Licences', 'wfd_truck'); ?></h2>
                                                                        <form>
                                                                            <div class="col-sm-4">
                                                                                <div class="checkbox">
                                                                                    <label><input type="checkbox"
                                                                                                  value=""><?php _e('C1', 'wfd_truck'); ?>
                                                                                    </label>
                                                                                </div>
                                                                                <div class="checkbox">
                                                                                    <label><input type="checkbox"
                                                                                                  value=""><?php _e('C1E', 'wfd_truck'); ?>
                                                                                    </label>
                                                                                </div>
                                                                                <div class="checkbox disabled">
                                                                                    <label><input type="checkbox"
                                                                                                  value=""><?php _e('crane', 'wfd_truck'); ?>
                                                                                    </label>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-sm-4">
                                                                                <div class="checkbox">
                                                                                    <label><input type="checkbox"
                                                                                                  value=""><?php _e('Kennz 95', 'wfd_truck'); ?>
                                                                                    </label>
                                                                                </div>
                                                                                <div class="checkbox">
                                                                                    <label><input type="checkbox"
                                                                                                  value=""><?php _e('Clubmobil', 'wfd_truck'); ?>
                                                                                    </label>
                                                                                </div>
                                                                                <div class="checkbox disabled">
                                                                                    <label><input type="checkbox"
                                                                                                  value=""><?php _e('car opening', 'wfd_truck'); ?>
                                                                                    </label>
                                                                                </div>
                                                                            </div>
                                                                        </form>
                                                                    </div>
                                                                    <div class="col-sm-6">
                                                                        <h2><?php _e('Qualification', 'wfd_truck'); ?></h2>
                                                                        <form>
                                                                            <div class="checkbox">
                                                                                <label><input type="checkbox"
                                                                                              value=""><?php _e('motor mechatronics', 'wfd_truck'); ?>
                                                                                </label>
                                                                            </div>
                                                                            <div class="checkbox">
                                                                                <label><input type="checkbox"
                                                                                              value=""><?php _e('motor foreman', 'wfd_truck'); ?>
                                                                                </label>
                                                                            </div>
                                                                            <div class="checkbox disabled">
                                                                                <label><input type="checkbox"
                                                                                              value=""><?php _e('learned', 'wfd_truck'); ?>
                                                                                </label>
                                                                            </div>
                                                                            <div class="checkbox disabled">
                                                                                <label><input type="checkbox"
                                                                                              value=""><?php _e('unlearned', 'wfd_truck'); ?>
                                                                                </label>
                                                                            </div>
                                                                            <div class="checkbox disabled">
                                                                                <label><input type="checkbox"
                                                                                              value=""><?php _e('commerical vehicle technology', 'wfd_truck'); ?>
                                                                                </label>
                                                                            </div>
                                                                        </form>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="submit" class="btn btn-primary">
                                                                    <span class="glyphicon glyphicon-floppy-disk"></span> <?php _e('Save', 'wfd_truck'); ?>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- Modal Copy-->
                                                <div class="modal fade" id="mdcopy_<?php echo $rd->id ?>"
                                                     data-driver-id="<?php echo $rd->id ?>" tabindex="-1" role="dialog"
                                                     aria-labelledby="myModalLabel">
                                                    <div style="width: 60%" class="modal-dialog" role="document"
                                                         data-driver-id="<?php echo $rd->id ?>">
                                                        <div class="modal-content">
                                                            <div class="modal-header">
                                                                <button type="button" class="close" data-dismiss="modal"
                                                                        aria-label="Close"><span
                                                                            aria-hidden="true">&times;</span></button>
                                                                <h4 class="modal-title" id="myModalLabel">
                                                                    <?php _e('Driver:', 'wfd_truck'); ?><?php echo $rd->fname ?><?php echo $rd->lname ?></h4>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="row">
                                                                    <div class="col-sm-5">
                                                                        <h2><?php _e('Core Data', 'wfd_truck'); ?></h2>
                                                                        <form class="form-horizontal" action="#">
                                                                            <div class="form-group">
                                                                                <label class="control-label col-sm-5"><?php _e('First Name', 'wfd_truck'); ?>
                                                                                    :</label>
                                                                                <div class="col-sm-7">
                                                                                    <input type="text"
                                                                                           class="form-control"
                                                                                           name="fname" value="">
                                                                                </div>
                                                                            </div>
                                                                            <div class="form-group">
                                                                                <label class="control-label col-sm-5"><?php _e('Last Name', 'wfd_truck'); ?>
                                                                                    :</label>
                                                                                <div class="col-sm-7">
                                                                                    <input type="text"
                                                                                           class="form-control"
                                                                                           name="lname" value="">
                                                                                </div>
                                                                            </div>
                                                                            <div class="form-group">
                                                                                <label class="control-label col-sm-5"><?php _e('Street', 'wfd_truck'); ?>
                                                                                    :</label>
                                                                                <div class="col-sm-7">
                                                                                    <input type="text"
                                                                                           class="form-control"
                                                                                           name="street" value="">
                                                                                </div>
                                                                            </div>
                                                                            <div class="form-group">
                                                                                <label class="control-label col-sm-5"><?php _e('City', 'wfd_truck'); ?>
                                                                                    :</label>
                                                                                <div class="col-sm-7">
                                                                                    <input type="text"
                                                                                           class="form-control"
                                                                                           name="city" value="">
                                                                                </div>
                                                                            </div>
                                                                            <div class="form-group">
                                                                                <label class="control-label col-sm-5"><?php _e('Phone', 'wfd_truck'); ?>
                                                                                    :</label>
                                                                                <div class="col-sm-7">
                                                                                    <input type="text"
                                                                                           class="form-control"
                                                                                           name="phone" value="">
                                                                                </div>
                                                                            </div>
                                                                            <div class="form-group">
                                                                                <label class="control-label col-sm-5"><?php _e('Note', 'wfd_truck'); ?>
                                                                                    :</label>
                                                                                <div class="col-sm-7">
                                                                                    <input type="text"
                                                                                           class="form-control"
                                                                                           name="note" value="">
                                                                                </div>
                                                                            </div>
                                                                        </form>
                                                                    </div>
                                                                    <div class="col-sm-1"></div>
                                                                    <div class="col-sm-4">
                                                                        <h2><?php _e('Applications', 'wfd_truck'); ?></h2>
                                                                        <form class="form-horizontal" action="#">
                                                                            <div class="form-group">
                                                                                <label><?php _e('breakdown service', 'wfd_truck'); ?></label>
                                                                            </div>
                                                                            <div class="form-group">
                                                                                <label><?php _e('drag cars', 'wfd_truck'); ?></label>

                                                                            </div>
                                                                            <div class="form-group">
                                                                                <label><?php _e('drag < 7.5to', 'wfd_truck'); ?></label>

                                                                            </div>
                                                                            <div class="form-group">
                                                                                <label><?php _e('drag > 7.5to', 'wfd_truck'); ?></label>

                                                                            </div>
                                                                            <div class="form-group">
                                                                                <label><?php _e('crane', 'wfd_truck'); ?></label>

                                                                            </div>
                                                                            <div class="form-group">
                                                                                <label><?php _e('truck service', 'wfd_truck'); ?></label>

                                                                            </div>
                                                                        </form>
                                                                    </div>
                                                                    <div class="col-sm-2">
                                                                        <img src="wp-admin/images/4.jpg"
                                                                             class="img-thumbnail" alt="Cinque Terre"
                                                                             width="200" height="150">
                                                                        <p class="col-sm-12"><?php _e('driver photo', 'wfd_truck'); ?></p>
                                                                    </div>
                                                                </div>
                                                                <div cla="row">
                                                                    <div class="col-sm-6">
                                                                        <h2><?php _e('Driving Licences', 'wfd_truck'); ?></h2>
                                                                        <form>
                                                                            <div class="col-sm-4">
                                                                                <div class="checkbox">
                                                                                    <label><input type="checkbox"
                                                                                                  value=""><?php _e('C1', 'wfd_truck'); ?>
                                                                                    </label>
                                                                                </div>
                                                                                <div class="checkbox">
                                                                                    <label><input type="checkbox"
                                                                                                  value=""><?php _e('C1E', 'wfd_truck'); ?>
                                                                                    </label>
                                                                                </div>
                                                                                <div class="checkbox disabled">
                                                                                    <label><input type="checkbox"
                                                                                                  value=""><?php _e('crane', 'wfd_truck'); ?>
                                                                                    </label>
                                                                                </div>
                                                                            </div>
                                                                            <div class="col-sm-4">
                                                                                <div class="checkbox">
                                                                                    <label><input type="checkbox"
                                                                                                  value=""><?php _e('Kennz 95', 'wfd_truck'); ?>
                                                                                    </label>
                                                                                </div>
                                                                                <div class="checkbox">
                                                                                    <label><input type="checkbox"
                                                                                                  value=""><?php _e('Clubmobil', 'wfd_truck'); ?>
                                                                                    </label>
                                                                                </div>
                                                                                <div class="checkbox disabled">
                                                                                    <label><input type="checkbox"
                                                                                                  value=""><?php _e('car opening', 'wfd_truck'); ?>
                                                                                    </label>
                                                                                </div>
                                                                            </div>
                                                                        </form>
                                                                    </div>
                                                                    <div class="col-sm-6">
                                                                        <h2><?php _e('Qualification', 'wfd_truck'); ?></h2>
                                                                        <form>
                                                                            <div class="checkbox">
                                                                                <label><input type="checkbox"
                                                                                              value=""><?php _e('motor mechatronics', 'wfd_truck'); ?>
                                                                                </label>
                                                                            </div>
                                                                            <div class="checkbox">
                                                                                <label><input type="checkbox"
                                                                                              value=""><?php _e('motor foreman', 'wfd_truck'); ?>
                                                                                </label>
                                                                            </div>
                                                                            <div class="checkbox disabled">
                                                                                <label><input type="checkbox"
                                                                                              value=""><?php _e('learned', 'wfd_truck'); ?>
                                                                                </label>
                                                                            </div>
                                                                            <div class="checkbox disabled">
                                                                                <label><input type="checkbox"
                                                                                              value=""><?php _e('unlearned', 'wfd_truck'); ?>
                                                                                </label>
                                                                            </div>
                                                                            <div class="checkbox disabled">
                                                                                <label><input type="checkbox"
                                                                                              value=""><?php _e('commerical vehicle technology', 'wfd_truck'); ?>
                                                                                </label>
                                                                            </div>
                                                                        </form>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="submit" class="btn btn-primary">
                                                                    <span class="glyphicon glyphicon-floppy-disk"></span> <?php _e('Save', 'wfd_truck'); ?>
                                                                </button>
                                                                <button type="button" class="btn btn-primary">
                                                                    <span class="glyphicon glyphicon-download-alt"></span> <?php _e('Paste', 'wfd_truck'); ?>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php } ?>
                                    </tbody>

                                </table>


                                <button class="btn btn-primary" type="button" data-toggle="collapse"
                                        data-target="#newDriver" aria-expanded="false" aria-controls="newDriver">
                                    <span class="glyphicon glyphicon-user"></span><span
                                            class="glyphicon glyphicon-plus"></span><?php _e('   Add Driver', 'wfd_truck'); ?>
                                </button>

                                <div class="collapse" id="newDriver">

                                    <?php
                                    $tbl_qualification = $wpdb->prefix . "wfd_truck_driver_qualification";
                                    $tbl_licences = $wpdb->prefix . "wfd_truck_driver_licences";
                                    $tbl_ranking = $wpdb->prefix . "wfd_truck_driver_ranking";
                                    $res_ranking = $wpdb->get_results("select * from $tbl_ranking order by id");
                                    $res_licenses = $wpdb->get_results("select * from $tbl_licences order by id");
                                    $res_qualification = $wpdb->get_results("select * from $tbl_qualification order by id");


                                    ?>
                                    <div class="well">
                                        <form method="POST">
                                            <div class="col-sm-6">
                                                <div class="form-group">
                                                    <label><?php _e('First Name', 'wfd_truck'); ?></label>
                                                    <input type="text" name="fname" class="form-control"
                                                           placeholder="Frist Name">
                                                </div>
                                                <div class="form-group">
                                                    <label><?php _e('Last Name', 'wfd_truck'); ?></label>
                                                    <input type="text" name="lname" class="form-control"
                                                           placeholder="Last Name">
                                                </div>
                                                <div class="form-group">
                                                    <label><?php _e('Address', 'wfd_truck'); ?></label>
                                                    <input type="text" name="street" class="form-control"
                                                           placeholder="Street Address">
                                                </div>
                                                <div class="form-group">
                                                    <label><?php _e('City', 'wfd_truck'); ?></label>
                                                    <input type="text" name="city" class="form-control"
                                                           placeholder="City">
                                                </div>
                                                <div class="form-group">
                                                    <label><?php _e('Phone', 'wfd_truck'); ?></label>
                                                    <input type="text" name="phone" class="form-control"
                                                           placeholder="Phone Number">
                                                </div>
                                                <div class="form-group">
                                                    <label><?php _e('Note', 'wfd_truck'); ?></label>
                                                    <textarea name="note" class="form-control"
                                                              placeholder="Note"></textarea>
                                                </div>
                                                <div class="form-group">
                                                    <input type="hidden" name="type" value="Driver">
                                                    <input type="hidden" name="cid" value="<?php echo $id ?>">
                                                    <input type="submit" name="drive_save" class="form-controll"
                                                           value="Save">
                                                </div>
                                            </div>
                                            <div class="col-sm-6">
                                                <div class="col-sm-8">
                                                    <h3><?php _e('Rating', 'wfd_truck'); ?></h3>
                                                    <?php foreach ($res_ranking as $rk) { ?>
                                                        <label><?php echo $rk->rank_item ?></label>
                                                        <select name="<?php echo 'ranking_' . $rk->id ?>"
                                                                class="form-control">
                                                            <option>5</option>
                                                            <option>4</option>
                                                            <option>3</option>
                                                            <option>2</option>
                                                            <option>1</option>
                                                        </select>
                                                        <br/>

                                                    <?php } ?>
                                                </div>
                                                <div class="col-sm-4">
                                                    <?php _e('Driver Picture', 'wfd_truck'); ?>
                                                </div>
                                                <div class="clearfix"></div>
                                                <div class="col-sm-6">
                                                    <h3><?php _e('License', 'wfd_truck'); ?></h3>
                                                    <?php foreach ($res_licenses as $ri) { ?>
                                                        <input type="checkbox" name="licences[]"
                                                               value="<?php echo $ri->licences ?>"> <?php echo $ri->licences ?>
                                                        <br>
                                                    <?php } ?>
                                                </div>
                                                <div class="col-sm-6">
                                                    <h3><?php _e('Qualification', 'wfd_truck'); ?></h3>
                                                    <?php foreach ($res_qualification as $rq) { ?>
                                                        <input type="checkbox" name="qualification[]"
                                                               value="<?php echo $rq->qualification ?>"> <?php echo $rq->qualification ?>
                                                        <br>
                                                    <?php } ?>
                                                </div>
                                            </div>

                                        </form>

                                        <div class="clearfix"></div>
                                    </div>
                                </div>


                            </div>
                            <div role="tabpanel" class="tab-pane" id="pdriver">
                                <h2><?php _e('Pickup  Driver', 'wfd_truck'); ?></h2>
                                <table class="table table-striped dataTable">
                                    <thead>
                                    <tr>
                                        <th><?php _e('Sl#', 'wfd_truck'); ?></th>
                                        <th><?php _e('First Name', 'wfd_truck'); ?></th>
                                        <th><?php _e('Last Name', 'wfd_truck'); ?></th>
                                        <th><?php _e('Street', 'wfd_truck'); ?></th>
                                        <th><?php _e('City', 'wfd_truck'); ?></th>
                                        <th><?php _e('Phone', 'wfd_truck'); ?></th>
                                        <th><?php _e('Note', 'wfd_truck'); ?></th>
                                        <th><?php _e('Action', 'wfd_truck'); ?></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php
                                    $r = 1;
                                    foreach ($res_driver_truck as $rdt) {
                                        ?>
                                        <tr>
                                            <td><?php echo $r;
                                                $r++; ?></td>
                                            <td><?php echo $rdt->fname ?></td>
                                            <td><?php echo $rdt->lname ?></td>
                                            <td><?php echo $rdt->street ?></td>
                                            <td><?php echo $rdt->city ?></td>
                                            <td><?php echo $rdt->phone ?></td>
                                            <td><?php echo $rdt->note ?></td>
                                            <td>
                                                <button type="button" class="btn btn-link" data-toggle="modal"
                                                        data-target="#mdtview_<?php echo $rdt->id ?>"><?php _e('', 'wfd_truck'); ?>
                                                    <?php _e('View', 'wfd_truck'); ?>
                                                </button>
                                                ||
                                                <button type="button" class="btn btn-link" data-toggle="modal"
                                                        data-target="#mdtedit_<?php echo $rdt->id ?>"><?php _e('', 'wfd_truck'); ?>
                                                    <?php _e('Edit', 'wfd_truck'); ?>
                                                </button>
                                                ||
                                                <button type="button" class="btn btn-link" data-toggle="modal"
                                                        data-target="#mdtnew_<?php echo $rdt->id ?>"><?php _e('', 'wfd_truck'); ?>
                                                    <?php _e('New', 'wfd_truck'); ?>
                                                </button>
                                                ||
                                                <button type="button" class="btn btn-link" data-toggle="modal"
                                                        data-target="#mdtcopy_<?php echo $rdt->id ?>"><?php _e('', 'wfd_truck'); ?>
                                                    <?php _e('Copy', 'wfd_truck'); ?>
                                                </button>
                                                <!-- Modal TView-->
                                                <div class="modal fade" id="mdtview_<?php echo $rdt->id ?>"
                                                     tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                                                    <div style="width: 60%" class="modal-dialog" role="document">
                                                        <div class="modal-content">
                                                            <div style="background-color: #5cb85c; color: white !important;"
                                                                 class="modal-header">
                                                                <button type="button" class="close" data-dismiss="modal"
                                                                        aria-label="Close"><span
                                                                            aria-hidden="true">&times;</span></button>
                                                                <h4 class="modal-title" id="myModalLabel">
                                                                    <?php _e('Truck Driver:', 'wfd_truck'); ?><?php echo $rdt->fname ?><?php echo $rdt->lname ?></h4>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="row">
                                                                    <div class="col-sm-5">
                                                                        <h2><?php _e('Core Data', 'wfd_truck'); ?></h2>
                                                                        <form class="form-horizontal" action="#">
                                                                            <div class="form-group">
                                                                                <label class="control-label col-sm-5"><?php _e('First Name', 'wfd_truck'); ?>
                                                                                    :</label>
                                                                                <div class="col-sm-7">
                                                                                    <input type="text"
                                                                                           class="form-control"
                                                                                           name="fname"
                                                                                           value=<?php echo $rdt->fname ?>>
                                                                                </div>
                                                                            </div>
                                                                            <div class="form-group">
                                                                                <label class="control-label col-sm-5"><?php _e('Last Name', 'wfd_truck'); ?>
                                                                                    :</label>
                                                                                <div class="col-sm-7">
                                                                                    <input type="text"
                                                                                           class="form-control"
                                                                                           name="lname"
                                                                                           value=<?php echo $rdt->lname ?>>
                                                                                </div>
                                                                            </div>
                                                                            <div class="form-group">
                                                                                <label class="control-label col-sm-5"><?php _e('Street', 'wfd_truck'); ?>
                                                                                    :</label>
                                                                                <div class="col-sm-7">
                                                                                    <input type="text"
                                                                                           class="form-control"
                                                                                           name="street"
                                                                                           value=<?php echo $rdt->street ?>>
                                                                                </div>
                                                                            </div>
                                                                            <div class="form-group">
                                                                                <label class="control-label col-sm-5"><?php _e('City', 'wfd_truck'); ?>
                                                                                    :</label>
                                                                                <div class="col-sm-7">
                                                                                    <input type="text"
                                                                                           class="form-control"
                                                                                           name="city"
                                                                                           value=<?php echo $rdt->city ?>>
                                                                                </div>
                                                                            </div>
                                                                            <div class="form-group">
                                                                                <label class="control-label col-sm-5"><?php _e('Phone', 'wfd_truck'); ?>
                                                                                    :</label>
                                                                                <div class="col-sm-7">
                                                                                    <input type="text"
                                                                                           class="form-control"
                                                                                           name="phone"
                                                                                           value=<?php echo $rdt->phone ?>>
                                                                                </div>
                                                                            </div>
                                                                            <div class="form-group">
                                                                                <label class="control-label col-sm-5"><?php _e('Note', 'wfd_truck'); ?>
                                                                                    :</label>
                                                                                <div class="col-sm-7">
                                                                                    <input type="text"
                                                                                           class="form-control"
                                                                                           name="note"
                                                                                           value=<?php echo $rdt->note ?>>
                                                                                </div>
                                                                            </div>
                                                                        </form>
                                                                    </div>
                                                                    <div class="col-sm-1"></div>
                                                                    <div class="col-sm-4">
                                                                        <h2><?php _e('Applications', 'wfd_truck'); ?></h2>
                                                                        <form class="form-horizontal" action="#">
                                                                            <div class="form-group">
                                                                                <label><?php _e('Pickups < 250km', 'wfd_truck'); ?></label>
                                                                            </div>
                                                                            <div class="form-group">
                                                                                <label><?php _e('Pickups < 500km', 'wfd_truck'); ?></label>

                                                                            </div>
                                                                            <div class="form-group">
                                                                                <label><?php _e('Pickups > 500km', 'wfd_truck'); ?></label>

                                                                            </div>
                                                                            <div class="form-group">
                                                                                <label><?php _e('truck < 3.5to', 'wfd_truck'); ?></label>

                                                                            </div>
                                                                            <div class="form-group">
                                                                                <label><?php _e('truck < 7.5to', 'wfd_truck'); ?></label>

                                                                            </div>
                                                                        </form>
                                                                    </div>
                                                                    <div class="col-sm-2">
                                                                        <img src="wp-admin/images/4.jpg"
                                                                             class="img-thumbnail" alt="Cinque Terre"
                                                                             width="200" height="150">
                                                                        <p class="col-sm-12"><?php _e('driver photo', 'wfd_truck'); ?></p>
                                                                    </div>
                                                                </div>
                                                                <div cla="row">
                                                                    <div class="col-sm-6">
                                                                        <h2><?php _e('Driving Licences', 'wfd_truck'); ?></h2>
                                                                        <form>
                                                                            <div class="checkbox">
                                                                                <label><input type="checkbox" value=""
                                                                                              checked><?php _e('C1', 'wfd_truck'); ?>
                                                                                </label>
                                                                            </div>
                                                                            <div class="checkbox">
                                                                                <label><input type="checkbox" value=""
                                                                                              checked><?php _e('C1E', 'wfd_truck'); ?>
                                                                                </label>
                                                                            </div>
                                                                            <div class="checkbox disabled">
                                                                                <label><input type="checkbox"
                                                                                              value=""><?php _e('crane', 'wfd_truck'); ?>
                                                                                </label>
                                                                            </div>
                                                                            <div class="checkbox disabled">
                                                                                <label><input type="checkbox"
                                                                                              value=""><?php _e('Kennz 95', 'wfd_truck'); ?>
                                                                                </label>
                                                                            </div>
                                                                        </form>
                                                                    </div>
                                                                    <div class="col-sm-6">
                                                                        <h2><?php _e('Qualification', 'wfd_truck'); ?></h2>
                                                                        <form>
                                                                            <div class="checkbox">
                                                                                <label><input type="checkbox"
                                                                                              value=""><?php _e('motor mechatronics', 'wfd_truck'); ?>
                                                                                </label>
                                                                            </div>
                                                                            <div class="checkbox">
                                                                                <label><input type="checkbox" value=""
                                                                                              checked><?php _e('motor foreman', 'wfd_truck'); ?>
                                                                                </label>
                                                                            </div>
                                                                            <div class="checkbox disabled">
                                                                                <label><input type="checkbox"
                                                                                              value=""><?php _e('learned', 'wfd_truck'); ?>
                                                                                </label>
                                                                            </div>
                                                                            <div class="checkbox disabled">
                                                                                <label><input type="checkbox"
                                                                                              value=""><?php _e('unlearned', 'wfd_truck'); ?>
                                                                                </label>
                                                                            </div>
                                                                            <div class="checkbox disabled">
                                                                                <label><input type="checkbox" value=""
                                                                                              checked><?php _e('commerical vehicle technology', 'wfd_truck'); ?>
                                                                                </label>
                                                                            </div>
                                                                        </form>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="submit" class="btn btn-primary">
                                                                    <span class="glyphicon glyphicon-floppy-disk"></span> <?php _e('Save', 'wfd_truck'); ?>
                                                                </button>
                                                                <button type="button" class="btn btn-primary">
                                                                    <span class="glyphicon glyphicon-pencil"></span> <?php _e('Edit', 'wfd_truck'); ?>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- Modal TEdit-->
                                                <div class="modal fade" id="mdtedit_<?php echo $rdt->id ?>"
                                                     tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                                                    <div style="width: 60%" class="modal-dialog" role="document">
                                                        <div class="modal-content">
                                                            <div style="background-color: #5cb85c; color: white !important;"
                                                                 class="modal-header">
                                                                <button type="button" class="close" data-dismiss="modal"
                                                                        aria-label="Close"><span
                                                                            aria-hidden="true">&times;</span></button>
                                                                <h4 class="modal-title" id="myModalLabel">
                                                                    <?php _e('Driver:', 'wfd_truck'); ?><?php echo $rdt->fname ?><?php echo $rdt->lname ?></h4>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="row">
                                                                    <div class="col-sm-5">
                                                                        <h2><?php _e('Core Data', 'wfd_truck'); ?></h2>
                                                                        <form class="form-horizontal" action="#">
                                                                            <div class="form-group">
                                                                                <label class="control-label col-sm-5"><?php _e('First Name', 'wfd_truck'); ?>
                                                                                    :</label>
                                                                                <div class="col-sm-7">
                                                                                    <input type="text"
                                                                                           class="form-control"
                                                                                           name="fname"
                                                                                           value=<?php echo $rdt->fname ?>>
                                                                                </div>
                                                                            </div>
                                                                            <div class="form-group">
                                                                                <label class="control-label col-sm-5"><?php _e('Last Name', 'wfd_truck'); ?>
                                                                                    :</label>
                                                                                <div class="col-sm-7">
                                                                                    <input type="text"
                                                                                           class="form-control"
                                                                                           name="lname"
                                                                                           value=<?php echo $rdt->lname ?>>
                                                                                </div>
                                                                            </div>
                                                                            <div class="form-group">
                                                                                <label class="control-label col-sm-5"><?php _e('Street', 'wfd_truck'); ?>
                                                                                    :</label>
                                                                                <div class="col-sm-7">
                                                                                    <input type="text"
                                                                                           class="form-control"
                                                                                           name="street"
                                                                                           value=<?php echo $rdt->street ?>>
                                                                                </div>
                                                                            </div>
                                                                            <div class="form-group">
                                                                                <label class="control-label col-sm-5"><?php _e('City', 'wfd_truck'); ?>
                                                                                    :</label>
                                                                                <div class="col-sm-7">
                                                                                    <input type="text"
                                                                                           class="form-control"
                                                                                           name="city"
                                                                                           value=<?php echo $rdt->city ?>>
                                                                                </div>
                                                                            </div>
                                                                            <div class="form-group">
                                                                                <label class="control-label col-sm-5"><?php _e('Phone', 'wfd_truck'); ?>
                                                                                    :</label>
                                                                                <div class="col-sm-7">
                                                                                    <input type="text"
                                                                                           class="form-control"
                                                                                           name="phone"
                                                                                           value=<?php echo $rdt->phone ?>>
                                                                                </div>
                                                                            </div>
                                                                            <div class="form-group">
                                                                                <label class="control-label col-sm-5"><?php _e('Note', 'wfd_truck'); ?>
                                                                                    :</label>
                                                                                <div class="col-sm-7">
                                                                                    <input type="text"
                                                                                           class="form-control"
                                                                                           name="note"
                                                                                           value=<?php echo $rdt->note ?>>
                                                                                </div>
                                                                            </div>
                                                                        </form>
                                                                    </div>
                                                                    <div class="col-sm-1"></div>
                                                                    <div class="col-sm-4">
                                                                        <h2><?php _e('Applications', 'wfd_truck'); ?></h2>
                                                                        <form class="form-horizontal" action="#">
                                                                            <div class="form-group">
                                                                                <label><?php _e('Pickups < 250km', 'wfd_truck'); ?></label>
                                                                            </div>
                                                                            <div class="form-group">
                                                                                <label><?php _e('Pickups < 500km', 'wfd_truck'); ?></label>

                                                                            </div>
                                                                            <div class="form-group">
                                                                                <label><?php _e('Pickups > 500km', 'wfd_truck'); ?></label>

                                                                            </div>
                                                                            <div class="form-group">
                                                                                <label><?php _e('truck < 3.5to', 'wfd_truck'); ?></label>

                                                                            </div>
                                                                            <div class="form-group">
                                                                                <label><?php _e('truck < 7.5to', 'wfd_truck'); ?></label>

                                                                            </div>
                                                                        </form>
                                                                    </div>
                                                                    <div class="col-sm-2">
                                                                        <img src="wp-admin/images/4.jpg"
                                                                             class="img-thumbnail" alt="Cinque Terre"
                                                                             width="200" height="150">
                                                                        <p class="col-sm-12"><?php _e('driver photo', 'wfd_truck'); ?></p>
                                                                    </div>
                                                                </div>
                                                                <div cla="row">
                                                                    <div class="col-sm-6">
                                                                        <h2><?php _e('Driving Licences', 'wfd_truck'); ?></h2>
                                                                        <form>
                                                                            <div class="checkbox">
                                                                                <label><input type="checkbox" value=""
                                                                                              checked><?php _e('C1', 'wfd_truck'); ?>
                                                                                </label>
                                                                            </div>
                                                                            <div class="checkbox">
                                                                                <label><input type="checkbox" value=""
                                                                                              checked><?php _e('C1E', 'wfd_truck'); ?>
                                                                                </label>
                                                                            </div>
                                                                            <div class="checkbox disabled">
                                                                                <label><input type="checkbox"
                                                                                              value=""><?php _e('crane', 'wfd_truck'); ?>
                                                                                </label>
                                                                            </div>
                                                                            <div class="checkbox disabled">
                                                                                <label><input type="checkbox"
                                                                                              value=""><?php _e('Kennz 95', 'wfd_truck'); ?>
                                                                                </label>
                                                                            </div>
                                                                        </form>
                                                                    </div>
                                                                    <div class="col-sm-6">
                                                                        <h2><?php _e('Qualification', 'wfd_truck'); ?></h2>
                                                                        <form>
                                                                            <div class="checkbox">
                                                                                <label><input type="checkbox"
                                                                                              value=""><?php _e('motor mechatronics', 'wfd_truck'); ?>
                                                                                </label>
                                                                            </div>
                                                                            <div class="checkbox">
                                                                                <label><input type="checkbox" value=""
                                                                                              checked><?php _e('motor foreman', 'wfd_truck'); ?>
                                                                                </label>
                                                                            </div>
                                                                            <div class="checkbox disabled">
                                                                                <label><input type="checkbox"
                                                                                              value=""><?php _e('learned', 'wfd_truck'); ?>
                                                                                </label>
                                                                            </div>
                                                                            <div class="checkbox disabled">
                                                                                <label><input type="checkbox"
                                                                                              value=""><?php _e('unlearned', 'wfd_truck'); ?>
                                                                                </label>
                                                                            </div>
                                                                            <div class="checkbox disabled">
                                                                                <label><input type="checkbox" value=""
                                                                                              checked><?php _e('commerical vehicle technology', 'wfd_truck'); ?>
                                                                                </label>
                                                                            </div>
                                                                        </form>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="submit" class="btn btn-primary">
                                                                    <span class="glyphicon glyphicon-floppy-disk"></span> <?php _e('Save', 'wfd_truck'); ?>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- Modal TNew-->
                                                <div class="modal fade" id="mdtnew_<?php echo $rdt->id ?>" tabindex="-1"
                                                     role="dialog" aria-labelledby="myModalLabel">
                                                    <div style="width: 60%" class="modal-dialog" role="document">
                                                        <div class="modal-content">
                                                            <div style="background-color: #5cb85c; color: white !important;"
                                                                 class="modal-header">
                                                                <button type="button" class="close" data-dismiss="modal"
                                                                        aria-label="Close"><span
                                                                            aria-hidden="true">&times;</span></button>
                                                                <h4 class="modal-title" id="myModalLabel">
                                                                    <?php _e('Driver:', 'wfd_truck'); ?><?php echo $rdt->fname ?><?php echo $rdt->lname ?></h4>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="row">
                                                                    <div class="col-sm-5">
                                                                        <h2><?php _e('Core Data', 'wfd_truck'); ?></h2>
                                                                        <form class="form-horizontal" action="#">
                                                                            <div class="form-group">
                                                                                <label class="control-label col-sm-5"><?php _e('First Name', 'wfd_truck'); ?>
                                                                                    :</label>
                                                                                <div class="col-sm-7">
                                                                                    <input type="text"
                                                                                           class="form-control"
                                                                                           name="fname" value="">
                                                                                </div>
                                                                            </div>
                                                                            <div class="form-group">
                                                                                <label class="control-label col-sm-5"><?php _e('Last Name', 'wfd_truck'); ?>
                                                                                    :</label>
                                                                                <div class="col-sm-7">
                                                                                    <input type="text"
                                                                                           class="form-control"
                                                                                           name="lname" value="">
                                                                                </div>
                                                                            </div>
                                                                            <div class="form-group">
                                                                                <label class="control-label col-sm-5"><?php _e('Street', 'wfd_truck'); ?>
                                                                                    :</label>
                                                                                <div class="col-sm-7">
                                                                                    <input type="text"
                                                                                           class="form-control"
                                                                                           name="street" value="">
                                                                                </div>
                                                                            </div>
                                                                            <div class="form-group">
                                                                                <label class="control-label col-sm-5"><?php _e('City', 'wfd_truck'); ?>
                                                                                    :</label>
                                                                                <div class="col-sm-7">
                                                                                    <input type="text"
                                                                                           class="form-control"
                                                                                           name="city" value="">
                                                                                </div>
                                                                            </div>
                                                                            <div class="form-group">
                                                                                <label class="control-label col-sm-5"><?php _e('Phone', 'wfd_truck'); ?>
                                                                                    :</label>
                                                                                <div class="col-sm-7">
                                                                                    <input type="text"
                                                                                           class="form-control"
                                                                                           name="phone" value="">
                                                                                </div>
                                                                            </div>
                                                                            <div class="form-group">
                                                                                <label class="control-label col-sm-5"><?php _e('Note', 'wfd_truck'); ?>
                                                                                    :</label>
                                                                                <div class="col-sm-7">
                                                                                    <input type="text"
                                                                                           class="form-control"
                                                                                           name="note" value="">
                                                                                </div>
                                                                            </div>
                                                                        </form>
                                                                    </div>
                                                                    <div class="col-sm-1"></div>
                                                                    <div class="col-sm-4">
                                                                        <h2><?php _e('Applications', 'wfd_truck'); ?></h2>
                                                                        <form class="form-horizontal" action="#">
                                                                            <div class="form-group">
                                                                                <label><?php _e('Pickups < 250km', 'wfd_truck'); ?></label>
                                                                            </div>
                                                                            <div class="form-group">
                                                                                <label><?php _e('Pickups < 500km', 'wfd_truck'); ?></label>

                                                                            </div>
                                                                            <div class="form-group">
                                                                                <label><?php _e('Pickups > 500km', 'wfd_truck'); ?></label>

                                                                            </div>
                                                                            <div class="form-group">
                                                                                <label><?php _e('truck < 3.5to', 'wfd_truck'); ?></label>

                                                                            </div>
                                                                            <div class="form-group">
                                                                                <label><?php _e('truck < 7.5to', 'wfd_truck'); ?></label>

                                                                            </div>
                                                                        </form>
                                                                    </div>
                                                                    <div class="col-sm-2">
                                                                        <img src="wp-admin/images/4.jpg"
                                                                             class="img-thumbnail" alt="Cinque Terre"
                                                                             width="200" height="150">
                                                                        <p class="col-sm-12"><?php _e('driver photo', 'wfd_truck'); ?></p>
                                                                    </div>
                                                                </div>
                                                                <div cla="row">
                                                                    <div class="col-sm-6">
                                                                        <h2><?php _e('Driving Licences', 'wfd_truck'); ?></h2>
                                                                        <form>
                                                                            <div class="checkbox">
                                                                                <label><input type="checkbox"
                                                                                              value=""><?php _e('C1', 'wfd_truck'); ?>
                                                                                </label>
                                                                            </div>
                                                                            <div class="checkbox">
                                                                                <label><input type="checkbox"
                                                                                              value=""><?php _e('C1E', 'wfd_truck'); ?>
                                                                                </label>
                                                                            </div>
                                                                            <div class="checkbox disabled">
                                                                                <label><input type="checkbox"
                                                                                              value=""><?php _e('crane', 'wfd_truck'); ?>
                                                                                </label>
                                                                            </div>
                                                                            <div class="checkbox disabled">
                                                                                <label><input type="checkbox"
                                                                                              value=""><?php _e('Kennz 95', 'wfd_truck'); ?>
                                                                                </label>
                                                                            </div>
                                                                        </form>
                                                                    </div>
                                                                    <div class="col-sm-6">
                                                                        <h2><?php _e('Qualification', 'wfd_truck'); ?></h2>
                                                                        <form>
                                                                            <div class="checkbox">
                                                                                <label><input type="checkbox"
                                                                                              value=""><?php _e('motor mechatronics', 'wfd_truck'); ?>
                                                                                </label>
                                                                            </div>
                                                                            <div class="checkbox">
                                                                                <label><input type="checkbox"
                                                                                              value=""><?php _e('motor foreman', 'wfd_truck'); ?>
                                                                                </label>
                                                                            </div>
                                                                            <div class="checkbox disabled">
                                                                                <label><input type="checkbox"
                                                                                              value=""><?php _e('learned', 'wfd_truck'); ?>
                                                                                </label>
                                                                            </div>
                                                                            <div class="checkbox disabled">
                                                                                <label><input type="checkbox"
                                                                                              value=""><?php _e('unlearned', 'wfd_truck'); ?>
                                                                                </label>
                                                                            </div>
                                                                            <div class="checkbox disabled">
                                                                                <label><input type="checkbox"
                                                                                              value=""><?php _e('commerical vehicle technology', 'wfd_truck'); ?>
                                                                                </label>
                                                                            </div>
                                                                        </form>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="submit" class="btn btn-primary">
                                                                    <span class="glyphicon glyphicon-floppy-disk"></span> <?php _e('Save', 'wfd_truck'); ?>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                <!-- Modal TCopy-->
                                                <div class="modal fade" id="mdtcopy_<?php echo $rdt->id ?>"
                                                     tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                                                    <div style="width: 60%" class="modal-dialog" role="document">
                                                        <div class="modal-content">
                                                            <div style="background-color: #5cb85c; color: white !important;"
                                                                 class="modal-header">
                                                                <button type="button" class="close" data-dismiss="modal"
                                                                        aria-label="Close"><span
                                                                            aria-hidden="true">&times;</span></button>
                                                                <h4 class="modal-title" id="myModalLabel">
                                                                    <?php _e('Driver:', 'wfd_truck'); ?><?php echo $rdt->fname ?><?php echo $rdt->lname ?></h4>
                                                            </div>
                                                            <div class="modal-body">
                                                                <div class="row">
                                                                    <div class="col-sm-5">
                                                                        <h2><?php _e('Core Data', 'wfd_truck'); ?></h2>
                                                                        <form class="form-horizontal" action="#">
                                                                            <div class="form-group">
                                                                                <label class="control-label col-sm-5"><?php _e('First Name', 'wfd_truck'); ?>
                                                                                    :</label>
                                                                                <div class="col-sm-7">
                                                                                    <input type="text"
                                                                                           class="form-control"
                                                                                           name="fname" value="">
                                                                                </div>
                                                                            </div>
                                                                            <div class="form-group">
                                                                                <label class="control-label col-sm-5"><?php _e('Last Name', 'wfd_truck'); ?>
                                                                                    :</label>
                                                                                <div class="col-sm-7">
                                                                                    <input type="text"
                                                                                           class="form-control"
                                                                                           name="lname" value="">
                                                                                </div>
                                                                            </div>
                                                                            <div class="form-group">
                                                                                <label class="control-label col-sm-5"><?php _e('Street', 'wfd_truck'); ?>
                                                                                    :</label>
                                                                                <div class="col-sm-7">
                                                                                    <input type="text"
                                                                                           class="form-control"
                                                                                           name="street" value="">
                                                                                </div>
                                                                            </div>
                                                                            <div class="form-group">
                                                                                <label class="control-label col-sm-5"><?php _e('City', 'wfd_truck'); ?>
                                                                                    :</label>
                                                                                <div class="col-sm-7">
                                                                                    <input type="text"
                                                                                           class="form-control"
                                                                                           name="city" value="">
                                                                                </div>
                                                                            </div>
                                                                            <div class="form-group">
                                                                                <label class="control-label col-sm-5"><?php _e('Phone', 'wfd_truck'); ?>
                                                                                    :</label>
                                                                                <div class="col-sm-7">
                                                                                    <input type="text"
                                                                                           class="form-control"
                                                                                           name="phone" value="">
                                                                                </div>
                                                                            </div>
                                                                            <div class="form-group">
                                                                                <label class="control-label col-sm-5"><?php _e('Note', 'wfd_truck'); ?>
                                                                                    :</label>
                                                                                <div class="col-sm-7">
                                                                                    <input type="text"
                                                                                           class="form-control"
                                                                                           name="note" value="">
                                                                                </div>
                                                                            </div>
                                                                        </form>
                                                                    </div>
                                                                    <div class="col-sm-1"></div>
                                                                    <div class="col-sm-4">
                                                                        <h2><?php _e('Applications', 'wfd_truck'); ?></h2>
                                                                        <form class="form-horizontal" action="#">
                                                                            <div class="form-group">
                                                                                <label><?php _e('Pickups < 250km', 'wfd_truck'); ?></label>
                                                                            </div>
                                                                            <div class="form-group">
                                                                                <label><?php _e('Pickups < 500km', 'wfd_truck'); ?></label>

                                                                            </div>
                                                                            <div class="form-group">
                                                                                <label><?php _e('Pickups > 500km', 'wfd_truck'); ?></label>

                                                                            </div>
                                                                            <div class="form-group">
                                                                                <label><?php _e('truck < 3.5to', 'wfd_truck'); ?></label>

                                                                            </div>
                                                                            <div class="form-group">
                                                                                <label><?php _e('truck < 7.5to', 'wfd_truck'); ?></label>

                                                                            </div>
                                                                        </form>
                                                                    </div>
                                                                    <div class="col-sm-2">
                                                                        <img src="wp-admin/images/4.jpg"
                                                                             class="img-thumbnail" alt="Cinque Terre"
                                                                             width="200" height="150">
                                                                        <p class="col-sm-12"><?php _e('driver photo', 'wfd_truck'); ?></p>
                                                                    </div>
                                                                </div>
                                                                <div cla="row">
                                                                    <div class="col-sm-6">
                                                                        <h2><?php _e('Driving Licences', 'wfd_truck'); ?></h2>
                                                                        <form>
                                                                            <div class="checkbox">
                                                                                <label><input type="checkbox"
                                                                                              value=""><?php _e('C1', 'wfd_truck'); ?>
                                                                                </label>
                                                                            </div>
                                                                            <div class="checkbox">
                                                                                <label><input type="checkbox"
                                                                                              value=""><?php _e('C1E', 'wfd_truck'); ?>
                                                                                </label>
                                                                            </div>
                                                                            <div class="checkbox disabled">
                                                                                <label><input type="checkbox"
                                                                                              value=""><?php _e('crane', 'wfd_truck'); ?>
                                                                                </label>
                                                                            </div>
                                                                            <div class="checkbox disabled">
                                                                                <label><input type="checkbox"
                                                                                              value=""><?php _e('Kennz 95', 'wfd_truck'); ?>
                                                                                </label>
                                                                            </div>
                                                                        </form>
                                                                    </div>
                                                                    <div class="col-sm-6">
                                                                        <h2><?php _e('Qualification', 'wfd_truck'); ?></h2>
                                                                        <form>
                                                                            <div class="checkbox">
                                                                                <label><input type="checkbox"
                                                                                              value=""><?php _e('motor mechatronics', 'wfd_truck'); ?>
                                                                                </label>
                                                                            </div>
                                                                            <div class="checkbox">
                                                                                <label><input type="checkbox"
                                                                                              value=""><?php _e('motor foreman', 'wfd_truck'); ?>
                                                                                </label>
                                                                            </div>
                                                                            <div class="checkbox disabled">
                                                                                <label><input type="checkbox"
                                                                                              value=""><?php _e('learned', 'wfd_truck'); ?>
                                                                                </label>
                                                                            </div>
                                                                            <div class="checkbox disabled">
                                                                                <label><input type="checkbox"
                                                                                              value=""><?php _e('unlearned', 'wfd_truck'); ?>
                                                                                </label>
                                                                            </div>
                                                                            <div class="checkbox disabled">
                                                                                <label><input type="checkbox"
                                                                                              value=""><?php _e('commerical vehicle technology', 'wfd_truck'); ?>
                                                                                </label>
                                                                            </div>
                                                                        </form>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                            <div class="modal-footer">
                                                                <button type="submit" class="btn btn-primary">
                                                                    <span class="glyphicon glyphicon-floppy-disk"></span> <?php _e('Save', 'wfd_truck'); ?>
                                                                </button>
                                                                <button type="button" class="btn btn-primary">
                                                                    <span class="glyphicon glyphicon-download-alt"></span> <?php _e('Paste', 'wfd_truck'); ?>
                                                                </button>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                            </td>
                                        </tr>
                                    <?php } ?>
                                    </tbody>

                                </table>


                                <button class="btn btn-primary" type="button" data-toggle="collapse"
                                        data-target="#newpDriver" aria-expanded="false" aria-controls="newpDriver">
                                    <span class="glyphicon glyphicon-user"></span><span
                                            class="glyphicon glyphicon-plus"></span><?php _e('  Add Pickup Driver', 'wfd_truck'); ?>
                                </button>

                                <div class="collapse" id="newpDriver">
                                    <div class="well">
                                        <form method="POST">
                                            <div class="form-group">
                                                <label><?php _e('First Name', 'wfd_truck'); ?></label>
                                                <input type="text" name="fname" class="form-control"
                                                       placeholder="Frist Name">
                                            </div>
                                            <div class="form-group">
                                                <label><?php _e('Last Name', 'wfd_truck'); ?></label>
                                                <input type="text" name="lname" class="form-control"
                                                       placeholder="Last Name">
                                            </div>
                                            <div class="form-group">
                                                <label><?php _e('Address', 'wfd_truck'); ?></label>
                                                <input type="text" name="street" class="form-control"
                                                       placeholder="Street Address">
                                            </div>
                                            <div class="form-group">
                                                <label><?php _e('City', 'wfd_truck'); ?></label>
                                                <input type="text" name="city" class="form-control" placeholder="City">
                                            </div>
                                            <div class="form-group">
                                                <label><?php _e('Phone', 'wfd_truck'); ?></label>
                                                <input type="text" name="phone" class="form-control"
                                                       placeholder="Phone Number">
                                            </div>
                                            <div class="form-group">
                                                <label><?php _e('Note', 'wfd_truck'); ?></label>
                                                <textarea name="note" class="form-control"
                                                          placeholder="Note"></textarea>
                                            </div>
                                            <div class="form-group">
                                                <input type="hidden" name="type" value="Pickup Driver">
                                                <input type="hidden" name="cid" value="<?php echo $id ?>">
                                                <input type="submit" name="drive_save" class="form-controll"
                                                       value="Save">
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <div role="tabpanel" class="tab-pane" id="tpool">
                                <h2><?php _e('Truck Pool', 'wfd_truck'); ?></h2>
                                <table class="table table-striped dataTable">
                                    <thead>
                                    <tr>
                                        <th><?php _e('ID', 'wfd_truck'); ?></th>
                                        <th><?php _e('Brand', 'wfd_truck'); ?></th>
                                        <th><?php _e('Weight', 'wfd_truck'); ?></th>
                                        <th><?php _e('Max Load', 'wfd_truck'); ?></th>
                                        <th><?php _e('Load Height', 'wfd_truck'); ?></th>
                                        <th><?php _e('Type', 'wfd_truck'); ?></th>
                                        <th><?php _e('Status', 'wfd_truck'); ?></th>
                                        <th><?php _e('Action', 'wfd_truck'); ?></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php foreach ($res_truck_info as $ti) { ?>
                                        <tr>
                                            <td><?php echo $ti->id ?></td>
                                            <td><?php echo $ti->brand ?></td>
                                            <td><?php echo $ti->weight ?></td>
                                            <td><?php echo $ti->max_load ?></td>
                                            <td><?php echo $ti->load_height ?></td>
                                            <td><?php echo $ti->type ?></td>
                                            <td><?php echo $ti->status ?></td>
                                            <td>
                                                <button type="button" class="btn btn-link" data-toggle="modal"
                                                        data-target="#md_tpview_<?php echo $ti->id ?>"><?php _e('', 'wfd_truck'); ?>
                                                    <?php _e('View', 'wfd_truck'); ?>
                                                </button>
                                                ||
                                                <button type="button" class="btn btn-link" data-toggle="modal"
                                                        data-target="#md_tpedit_<?php echo $ti->id ?>"><?php _e('', 'wfd_truck'); ?>
                                                    <?php _e('Edit', 'wfd_truck'); ?>
                                                </button>
                                                ||
                                                <button type="button" class="btn btn-link" data-toggle="modal"
                                                        data-target="#md_tpnew_<?php echo $ti->id ?>"><?php _e('', 'wfd_truck'); ?>
                                                    <?php _e('Del', 'wfd_truck'); ?>
                                                </button> ||
                                                <button type="button" class="btn btn-link" data-toggle="modal"
                                                        data-target="#md_tpcopy_<?php echo $ti->id ?>"><?php _e('', 'wfd_truck'); ?>
                                                    <?php _e('Copy', 'wfd_truck'); ?>
                                                </button>

                                            <!-- Modal TPView-->
                                            <div class="modal fade" id="md_tpview_<?php echo $ti->id ?>" tabindex="-1" role="dialog" aria-labelledby="myModalLabel">
                                                <div style="width: 60%" class="modal-dialog" role="document">
                                                    <div class="modal-content">
                                                        <div style="background-color: #5cb85c; color: white !important;" class="modal-header">
                                                            <button type="button" class="close" data-dismiss="modal"
                                                                    aria-label="Close"><span
                                                                        aria-hidden="true">&times;</span></button>
                                                            <h4 class="modal-title" id="myModalLabel">
                                                                <?php _e('Truck:', 'wfd_truck'); ?> <?php echo $ti->id ?> - <?php echo $ti->brand ?></h4>
                                                        </div>
                                                        <div class="modal-body">
                                                            <div class="row">
                                                                <div class="col-sm-8">
                                                                    <h2><?php _e('Truck Data', 'wfd_truck'); ?></h2>
                                                                    <form class="form-horizontal" action="#">
                                                                      <div class="col-sm-6">
                                                                        <div class="form-group">
                                                                            <label class="control-label col-sm-5"><?php _e('ID', 'wfd_truck'); ?>:</label>
                                                                            <div class="col-sm-7">
                                                                                <input type="text" class="form-control" name="id" value=<?php echo $ti->id ?>>
                                                                            </div>
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label class="control-label col-sm-5"><?php _e('Brand', 'wfd_truck'); ?>:</label>
                                                                            <div class="col-sm-7">
                                                                                <input type="text" class="form-control" name="brand" value=<?php echo $ti->brand ?>>
                                                                            </div>
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label class="control-label col-sm-5"><?php _e('weight', 'wfd_truck'); ?>:</label>
                                                                            <div class="col-sm-7">
                                                                                <input type="text" class="form-control" name="weight" value=<?php echo $ti->weight ?>>
                                                                            </div>
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label class="control-label col-sm-5"><?php _e('max load', 'wfd_truck'); ?>:</label>
                                                                            <div class="col-sm-7">
                                                                                <input type="text" class="form-control" name="max_load" value=<?php echo $ti->max_load ?>>
                                                                            </div>
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label class="control-label col-sm-5"><?php _e('load height', 'wfd_truck'); ?>:</label>
                                                                            <div class="col-sm-7">
                                                                                <input type="text" class="form-control" name="load_height" value=<?php echo $ti->load_height ?>>
                                                                            </div>
                                                                        </div>
                                                                        <div class="form-group">
                                                                            <label class="control-label col-sm-5"><?php _e('plateau height', 'wfd_truck'); ?>:</label>
                                                                            <div class="col-sm-7">
                                                                                <input type="text" class="form-control" name="pheight" value=<?php echo $ti->pheight ?>>
                                                                            </div>
                                                                        </div>
                                                                      </div>
                                                                      <div class="col-sm-6">
                                                                         <div class="form-group">
                                                                            <label class="control-label col-sm-5"><?php _e('spectacle force', 'wfd_truck'); ?>:</label>
                                                                            <div class="col-sm-7">
                                                                               <input type="text" class="form-control" name="spec_force" value=<?php echo $ti->spec_force ?>>
                                                                            </div>
                                                                         </div>
                                                                         <div class="form-group">
                                                                            <label class="control-label col-sm-5"><?php _e('cable winch force', 'wfd_truck'); ?>:</label>
                                                                            <div class="col-sm-7">
                                                                               <input type="text" class="form-control" name="cable_force" value=<?php echo $ti->cable_force ?>>
                                                                            </div>
                                                                         </div>
                                                                         <div class="form-group">
                                                                            <label class="control-label col-sm-5"><?php _e('crane', 'wfd_truck'); ?>:</label>
                                                                            <div class="col-sm-7">
                                                                               <input type="text" class="form-control" name="crane" value=<?php echo $ti->crane ?>>
                                                                            </div>
                                                                         </div>
                                                                         <div class="form-group">
                                                                            <label class="control-label col-sm-5"><?php _e('plateau length', 'wfd_truck'); ?>:</label>
                                                                            <div class="col-sm-7">
                                                                              <input type="text" class="form-control" name="plength" value=<?php echo $ti->plength ?>>
                                                                            </div>
                                                                         </div>
                                                                         <div class="form-group">
                                                                            <label class="control-label col-sm-5"><?php _e('motorcycle', 'wfd_truck'); ?>:</label>
                                                                            <div class="col-sm-7">
                                                                                <label class="switch">
                                                                                    <input type="checkbox" checked>
                                                                                    <div class="slider round"></div>
                                                                                </label>
                                                                            </div>
                                                                         </div>
                                                                         <div class="form-group">
                                                                            <label class="control-label col-sm-5"><?php _e('seats', 'wfd_truck'); ?>:</label>
                                                                            <div class="col-sm-7">
                                                                               <input type="text" class="form-control" name="seats" value=<?php echo $ti->seats ?>>
                                                                            </div>
                                                                         </div>
                                                                         <div class="form-group">
                                                                            <label class="control-label col-sm-5"><?php _e('under lift', 'wfd_truck'); ?>:</label>
                                                                            <div class="col-sm-7">
                                                                               <input type="text" class="form-control" name="under_lift" value=<?php echo $ti->under_lift ?>>
                                                                            </div>
                                                                         </div>
                                                                      </div>
                                                                    </form>
                                                                </div>
                                                                <div class="col-sm-2">
                                                                    <form>
                                                                        <div class="form-group">
                                                                            <label><?php _e('Type', 'wfd_truck'); ?></label>
                                                                            <select class="form-control" id="">
                                                                                <option><?php echo $ti->type ?></option>
                                                                                <option><?php _e('Rig', 'wfd_truck'); ?></option>
                                                                                <option><?php _e('Spectacle truck', 'wfd_truck'); ?></option>
                                                                                <option><?php _e('Crane', 'wfd_truck'); ?></option>
                                                                                <option><?php _e('truck salvage', 'wfd_truck'); ?></option>
                                                                            </select>
                                                                        </div>
                                                                    </form>
                                                                </div>
                                                                <div class="col-sm-2">
                                                                    <img src="wp-admin/images/4.jpg" class="img-thumbnail" alt="Cinque Terre" width="200" height="150">
                                                                    <p class="col-sm-12"><?php _e('truck photo', 'wfd_truck'); ?></p>

                                                                    <form>
                                                                       <label class="switch_red">
                                                                          <input type="checkbox" checked>
                                                                          <div class="slider round"></div>
                                                                       </label>
                                                                       <label class="switch_label"><?php _e('out of order', 'wfd_truck'); ?></label>
                                                                    </form>
                                                                </div>
                                                            </div>

                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="submit" class="btn btn-primary">
                                                                <span class="glyphicon glyphicon-floppy-disk"></span>  <?php _e('Save', 'wfd_truck'); ?></button>
                                                            <button type="button" class="btn btn-primary">
                                                                <span class="glyphicon glyphicon-pencil"></span>  <?php _e('Edit', 'wfd_truck'); ?></button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </tr>
                                    <?php } ?>
                                    </tbody>
                                </table>
                                <button class="btn btn-primary" type="button" data-toggle="collapse"
                                        data-target="#addtpool" aria-expanded="false" aria-controls="addtpool">
                                    </span><span
                                            class="glyphicon glyphicon-plus"></span><?php _e('  Add Truck', 'wfd_truck'); ?>
                                </button>

                                <div class="collapse" id="addtpool">
                                    <div class="well">
                                        <form method="POST">
                                            <div class="form-group">
                                                <label><?php _e('ID', 'wfd_truck'); ?></label>
                                                <input type="text" name="id" class="form-control" placeholder="ID">
                                            </div>
                                            <div class="form-group">
                                                <label><?php _e('Brand', 'wfd_truck'); ?></label>
                                                <input type="text" name="brand" class="form-control"
                                                       placeholder="Brand">
                                            </div>
                                            <div class="form-group">
                                                <label><?php _e('Weight', 'wfd_truck'); ?></label>
                                                <input type="text" name="weight" class="form-control"
                                                       placeholder="Weight">
                                            </div>
                                            <div class="form-group">
                                                <label><?php _e('Max Load', 'wfd_truck'); ?></label>
                                                <input type="text" name="max_load" class="form-control"
                                                       placeholder="Max Load">
                                            </div>
                                            <div class="form-group">
                                                <label><?php _e('Load Height', 'wfd_truck'); ?></label>
                                                <input type="text" name="load_height" class="form-control"
                                                       placeholder="Load Height">
                                            </div>
                                            <div class="form-group">
                                                <label><?php _e('Type', 'wfd_truck'); ?></label>
                                                <input type="text" name="type" class="form-control" placeholder="Type">
                                            </div>
                                            <div class="form-group">
                                                <label><?php _e('Status', 'wfd_truck'); ?></label>
                                                <input type="text" name="status" class="form-control"
                                                       placeholder="Status">
                                            </div>
                                            <div class="form-group">
                                                <input type="hidden" name="cid" value="<?php echo $id ?>">
                                                <input type="submit" name="tpool_save" class="form-controll"
                                                       value="Save">
                                            </div>
                                        </form>
                                    </div>
                                </div>

                            <div role="tabpanel" class="tab-pane" id="callNum">
                                <h2><?php _e('Call Numbers', 'wfd_truck'); ?></h2>
                                <table class="table table-striped dataTable">
                                    <thead>
                                    <tr>
                                        <th><?php _e('Sl#', 'wfd_truck'); ?></th>
                                        <th><?php _e('Name', 'wfd_truck'); ?></th>
                                        <th><?php _e('Phone', 'wfd_truck'); ?></th>
                                        <th><?php _e('Note', 'wfd_truck'); ?></th>
                                        <th><?php _e('Category', 'wfd_truck'); ?></th>
                                        <th><?php _e('Action', 'wfd_truck'); ?></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php $i = 1;
                                    foreach ($res_call_num as $cn) {
                                        ?>
                                        <tr>
                                            <td><?php echo $i;
                                                $i++; ?></td>
                                            <td><?php echo $cn->name ?></td>
                                            <td><?php echo $cn->phone ?></td>
                                            <td><?php echo $cn->note ?></td>
                                            <td><?php echo $cn->category ?></td>
                                            <td><?php _e('View', 'wfd_truck'); ?> | <?php _e('Edit', 'wfd_truck'); ?>
                                                | <?php _e('Del', 'wfd_truck'); ?>
                                                | <?php _e('Copy', 'wfd_truck'); ?></td>
                                        </tr>
                                    <?php } ?>
                                    </tbody>
                                </table>
                                <button class="btn btn-primary" type="button" data-toggle="collapse"
                                        data-target="#addtno" aria-expanded="false" aria-controls="addtno">
                                    </span><span
                                            class="glyphicon glyphicon-plus"></span><?php _e('  Add No', 'wfd_truck'); ?>
                                </button>

                                <div class="collapse" id="addtno">
                                    <div class="well">
                                        <form method="POST">
                                            <div class="form-group">
                                                <label><?php _e('Name', 'wfd_truck'); ?></label>
                                                <input type="text" name="name" class="form-control" placeholder="Name">
                                            </div>
                                            <div class="form-group">
                                                <label><?php _e('Phone', 'wfd_truck'); ?></label>
                                                <input type="text" name="phone" class="form-control"
                                                       placeholder="Phone">
                                            </div>
                                            <div class="form-group">
                                                <label><?php _e('Note', 'wfd_truck'); ?></label>
                                                <textarea name="note" class="form-control"
                                                          placeholder="Note"></textarea>
                                            </div>
                                            <div class="form-group">
                                                <label><?php _e('Category', 'wfd_truck'); ?></label>
                                                <input type="text" name="category" class="form-control"
                                                       placeholder="Category">
                                            </div>
                                            <div class="form-group">
                                                <input type="hidden" name="cid" value="<?php echo $id ?>">
                                                <input type="submit" name="no_save" class="form-controll" value="Save">
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <div role="tabpanel" class="tab-pane" id="prices">
                                <h2><?php _e('Service Prices', 'wfd_truck'); ?></h2>
                                <table class="table table-striped dataTable">
                                    <thead>
                                    <tr>
                                        <th><?php _e('Sl#', 'wfd_truck'); ?></th>
                                        <th><?php _e('Service', 'wfd_truck'); ?></th>
                                        <th><?php _e('Description', 'wfd_truck'); ?></th>
                                        <th><?php _e('Price', 'wfd_truck'); ?></th>
                                        <th><?php _e('Action', 'wfd_truck'); ?></th>
                                    </tr>
                                    </thead>
                                    <tbody>
                                    <?php $ip = 1;
                                    foreach ($res_prices as $p) {
                                        ?>
                                        <tr>
                                            <td><?php echo $ip;
                                                $ip++; ?></td>
                                            <td><?php echo $p->service ?></td>
                                            <td><?php echo $p->description ?></td>
                                            <td><?php echo $p->price ?></td>

                                            <td><?php _e('View', 'wfd_truck'); ?> | <?php _e('Edit', 'wfd_truck'); ?>
                                                | <?php _e('Del', 'wfd_truck'); ?>
                                                | <?php _e('Copy', 'wfd_truck'); ?></td>
                                        </tr>
                                    <?php } ?>
                                    </tbody>
                                </table>
                                <button class="btn btn-primary" type="button" data-toggle="collapse"
                                        data-target="#addservice" aria-expanded="false" aria-controls="addservice">
                                    <span
                                            class="glyphicon glyphicon-plus"></span><?php _e('  Add Service', 'wfd_truck'); ?>
                                </button>

                                <div class="collapse" id="addservice">
                                    <div class="well">
                                        <form method="POST">
                                            <div class="form-group">
                                                <label><?php _e('Service', 'wfd_truck'); ?></label>
                                                <input type="text" name="service" class="form-control"
                                                       placeholder="Service">
                                            </div>
                                            <div class="form-group">
                                                <label><?php _e('Description', 'wfd_truck'); ?></label>
                                                <input type="text" name="description" class="form-control"
                                                       placeholder="Description">
                                            </div>
                                            <div class="form-group">
                                                <label><?php _e('Price', 'wfd_truck'); ?></label>
                                                <input type="text" name="price" class="form-control"
                                                       placeholder="Price">
                                            </div>
                                            <div class="form-group">
                                                <input type="hidden" name="cid" value="<?php echo $id ?>">
                                                <input type="submit" name="service_save" class="form-controll"
                                                       value="Save">
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>

                            <div role="tabpanel" class="tab-pane" id="handover">
                                <h2><?php _e('Handover', 'wfd_truck'); ?></h2>
                                <ul class="nav nav-tabs">
                                    <li class="active"><a data-toggle="tab" href="#step1">Step 1</a></li>
                                    <li><a data-toggle="tab" href="#step2">Step 2</a></li>
                                    <li><a data-toggle="tab" href="#step3">Step 3</a></li>
                                    <li><a data-toggle="tab" href="#save">Save</a></li>
                                </ul>

                                <div class="tab-content">
                                    <div id="step1" class="tab-pane fade in active">
                                        <div class="container">
                                            <h3>Company: Truck Ltd</h3>
                                            <div class="col-sm-2">
                                                <h3>Date:</h3>
                                            </div>
                                            <div class="col-sm-2">
                                                <h3>Time:</h3>
                                            </div>
                                            <div class="col-sm-2">
                                                <h3>Handover by:</h3>
                                            </div>
                                        </div>
                                        <button type="button" class="btn btn-success"><span
                                                    class="glyphicon glyphicon-forward"></span> Next
                                        </button>
                                    </div>

                                    <div id="step2" class="tab-pane fade">
                                        <div class="col-sm-3">
                                            <h3>standby driver</h3>
                                            <table class="table table-hover">
                                                <tbody>
                                                <tr>
                                                    <td>Max</td>
                                                    <td><a href="#" onClick="return confirm('Are you sure?')"><span
                                                                    class="glyphicon glyphicon-trash"></span></a></td>
                                                </tr>
                                                <tr>
                                                    <td>John</td>
                                                    <td><a href="#" onClick="return confirm('Are you sure?')"><span
                                                                    class="glyphicon glyphicon-trash"></span></a></td>
                                                </tr>
                                                <tr>
                                                    <td>Mike</td>
                                                    <td><a href="#" onClick="return confirm('Are you sure?')"><span
                                                                    class="glyphicon glyphicon-trash"></span></a></td>
                                                </tr>
                                                </tbody>
                                            </table>

                                            <span class="glyphicon glyphicon-user"></span><span
                                                    class="glyphicon glyphicon-plus"></span>
                                            <select class="selectpicker form-control">
                                                <option>Top 20</option>
                                                <option>International</option>
                                                <option>Euroupa</option>
                                                <option>Asien</option>
                                                <option>Amerikan</option>
                                            </select>

                                        </div>
                                        <div class="col-sm-3">
                                            <h3>standby trucks</h3>
                                            <table class="table table-hover">
                                                <tbody>
                                                <tr>
                                                    <td>truck 1</td>
                                                    <td><a href="#" onClick="return confirm('Are you sure?')"><span
                                                                    class="glyphicon glyphicon-trash"></span></a></td>
                                                </tr>
                                                <tr>
                                                    <td>truck 2</td>
                                                    <td><a href="#" onClick="return confirm('Are you sure?')"><span
                                                                    class="glyphicon glyphicon-trash"></span></a></td>
                                                </tr>
                                                <tr>
                                                    <td>truck 3</td>
                                                    <td><a href="#" onClick="return confirm('Are you sure?')"><span
                                                                    class="glyphicon glyphicon-trash"></span></a></td>
                                                </tr>
                                                </tbody>
                                            </table>
                                            <span class="glyphicon glyphicon-plus"></span>
                                            <select class="selectpicker form-control">
                                                <option>Top 20</option>
                                                <option>International</option>
                                                <option>Euroupa</option>
                                                <option>Asien</option>
                                                <option>Amerikan</option>
                                            </select>
                                        </div>
                                        <div class="col-sm-6">
                                            <table class="table table-hover">
                                                <tbody>
                                                <tr>
                                                    <td>
                                                        <button type="button" class="btn btn-success"><span
                                                                    class="glyphicon glyphicon-forward"></span> Next
                                                        </button>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>
                                                        <button type="button" class="btn btn-success"><span
                                                                    class="glyphicon glyphicon-backward"></span> Back
                                                        </button>
                                                    </td>
                                                </tr>
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                    <div id="step3" class="tab-pane fade">
                                        <div class="row" id="step3button">
                                            <button type="button" class="btn btn-success"><span
                                                        class="glyphicon glyphicon-forward"></span> Next
                                            </button>
                                        </div>
                                        <div class="row" id="step3button">
                                            <button type="button" class="btn btn-success"><span
                                                        class="glyphicon glyphicon-backward"></span> Back
                                            </button>
                                        </div>
                                    </div>
                                    <div id="save" class="tab-pane fade">
                                        <button type="button" class="btn btn-success"><span
                                                    class="glyphicon glyphicon-file"></span> Open PDF file
                                        </button>
                                        <button type="button" class="btn btn-success"><span
                                                    class="glyphicon glyphicon-floppy-disk"></span> Save / Send
                                        </button>
                                        <button type="button" class="btn btn-success"><span
                                                    class="glyphicon glyphicon-backward"></span> Back
                                        </button>
                                    </div>
                                </div>
                            </div>

                        </div>

                        <br>
                    <?php } ?>

                </div>
            </div>

        <?php } ?>
    </div>

    <style>

    </style>
    <?php
}