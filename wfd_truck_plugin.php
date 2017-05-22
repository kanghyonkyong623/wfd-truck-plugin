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
    add_menu_page('WFD Truck Management', 'Truck Admin', 'manage_options', 'wfd_truck_admin_view', 'wfd_admin_view_as_wp_menu');
    add_submenu_page('wfd_truck_admin_view', 'Settings', 'Settings', 'manage_options', 'wfd_truck_settings', 'wfd_truck_settings_fn');
}

add_action('plugins_loaded', 'plugin_init');

function plugin_init()
{
    load_plugin_textdomain('wfd_truck', false, dirname(plugin_basename(__FILE__)) . '/languages/');
}

add_action('wp_logout', wp_user_logout);

function wp_user_logout()
{
    $_SESSION['client_login'] = 'false';
    $_SESSION['client_username'] = '';
    $_SESSION['client_id'] = '';
}

function is_wp_admin(){
    if ( is_user_logged_in() ){
        if( current_user_can( 'manage_options' ) )
        {
            return true;
        }
        else
        {
            return false;
        }
    }
    return false;
}
//add_action( 'wp_enqueue_scripts', 'my_enqueue' );
function my_enqueue()
{

    wp_enqueue_style('bootstrap-style', 'http://netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap.min.css', null);
    wp_enqueue_style('bootstrap-theme', 'http://netdna.bootstrapcdn.com/bootstrap/3.1.1/css/bootstrap-theme.min.css', null);
    wp_enqueue_style('bootstrap-select', 'https://cdnjs.cloudflare.com/ajax/libs/bootstrap-select/1.6.4/css/bootstrap-select.min.css', null);
    wp_enqueue_style('bootstrap-responsive', 'https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/2.3.2/css/bootstrap-responsive.css', null);
    wp_enqueue_style('bootstrap-table-css', 'https://rawgit.com/wenzhixin/bootstrap-table/master/src/bootstrap-table.css', null);
    wp_enqueue_style('font-awesome-css', 'http://maxcdn.bootstrapcdn.com/font-awesome/4.4.0/css/font-awesome.min.css', null);
//    wp_dequeue_style('style');

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
            'successTitle' => __('Succeed', 'wfd_truck'), 'deleteConformMessage' => __('Do you confirm to delete?', 'wfd_truck'),
            'saveConformMessage' => __('Do you want to save changes of core data?', 'wfd_truck'), 'coreDataEditMode' => $_SESSION['edit_mode'],
            'enterNewAssistanceMessage' => __('Please enter assistance type', 'wfd_truck')));
}

add_action('wp_ajax_wfd_add_client', 'wfd_add_client');
add_action('wp_ajax_nopriv_wfd_add_client', 'wfd_add_client');
add_action('wp_ajax_wfd_edit_client', 'wfd_edit_client');
add_action('wp_ajax_nopriv_wfd_edit_client', 'wfd_edit_client');
add_action('wp_ajax_wfd_driver_save', 'wfd_driver_save');
add_action('wp_ajax_nopriv_wfd_driver_save', 'wfd_driver_save');
add_action('wp_ajax_wfd_update_client', 'wfd_update_client');
add_action('wp_ajax_nopriv_wfd_update_client', 'wfd_update_client');
add_action('wp_ajax_wfd_delete_client', 'wfd_delete_client');
add_action('wp_ajax_nopriv_wfd_delete_client', 'wfd_delete_client');
add_action('wp_ajax_wfd_truck_save_core', 'wfd_truck_save_core');
add_action('wp_ajax_nopriv_wfd_truck_save_core', 'wfd_truck_save_core');
add_action('wp_ajax_wfd_add_service', 'wfd_add_service');
add_action('wp_ajax_wfd_delete_service', 'wfd_delete_service');
add_action('wp_ajax_wfd_update_service', 'wfd_update_service');
add_action('wp_ajax_wfd_add_callnum', 'wfd_add_callnum');
add_action('wp_ajax_wfd_update_callnum', 'wfd_update_callnum');
add_action('wp_ajax_wfd_delete_callnum', 'wfd_delete_callnum');
add_action('wp_ajax_wfd_add_truck', 'wfd_add_truck');
add_action('wp_ajax_wfd_delete_truck', 'wfd_delete_truck');
add_action('wp_ajax_nopriv_wfd_add_service', 'wfd_add_service');
add_action('wp_ajax_nopriv_wfd_delete_service', 'wfd_delete_service');
add_action('wp_ajax_nopriv_wfd_update_service', 'wfd_update_service');
add_action('wp_ajax_nopriv_wfd_add_callnum', 'wfd_add_callnum');
add_action('wp_ajax_nopriv_wfd_update_callnum', 'wfd_update_callnum');
add_action('wp_ajax_nopriv_wfd_delete_callnum', 'wfd_delete_callnum');
add_action('wp_ajax_nopriv_wfd_add_truck', 'wfd_add_truck');
add_action('wp_ajax_nopriv_wfd_delete_truck', 'wfd_delete_truck');
add_action('wp_ajax_nopriv_wfd_update_truck', 'wfd_update_truck');
add_action('wp_ajax_wfd_update_truck', 'wfd_update_truck');
add_action('wp_ajax_wfd_get_driver_detail', 'wfd_get_driver_detail');
add_action('wp_ajax_nopriv_wfd_get_driver_detail', 'wfd_get_driver_detail');
add_action('wp_ajax_wfd_delete_driver', 'wfd_delete_driver');
add_action('wp_ajax_nopriv_wfd_delete_driver', 'wfd_delete_driver');
add_action('wp_ajax_wfd_get_pickup_driver_detail', 'wfd_get_pickup_driver_detail');
add_action('wp_ajax_nopriv_wfd_get_pickup_driver_detail', 'wfd_get_pickup_driver_detail');
add_action('wp_ajax_wfd_delete_pickup_driver', 'wfd_delete_pickup_driver');
add_action('wp_ajax_nopriv_wfd_delete_pickup_driver', 'wfd_delete_pickup_driver');
add_action('wp_ajax_wfd_pickup_driver_save', 'wfd_pickup_driver_save');
add_action('wp_ajax_nopriv_wfd_pickup_driver_save', 'wfd_pickup_driver_save');
add_action('wp_ajax_wfd_get_truck_detail', 'wfd_get_truck_detail');
add_action('wp_ajax_nopriv_wfd_get_truck_detail', 'wfd_get_truck_detail');

function wfd_add_client()
{
    global $wpdb;
    $new_user_name = $_POST['new_user_name'];
    $new_email_address = $_POST['new_email_address'];
    $new_company_name = $_POST['new_company_name'];
    $new_password = md5($_POST['new_password']);

    $result_array = array();
    $tbl_wp_users = $wpdb->users;
    $existing_users = $wpdb->get_results("SELECT * FROM $tbl_wp_users WHERE `display_name`='$new_user_name' OR `user_email`='$new_email_address'", OBJECT);
    if (count($existing_users) > 0) {
        $result_array['result'] = false;
        $result_array['errorMessage'] = __('Your name or email address is already using on this site!', 'wfd_truck');
    } else {
        $tbl_clients = $wpdb->prefix . "wfd_truck_client_info";
        $existing_users = $wpdb->get_results("SELECT * FROM $tbl_clients WHERE `username`='$new_user_name' OR `email`='$new_email_address'", OBJECT);
        if (count($existing_users) > 0) {
            $result_array['result'] = false;
            $result_array['errorMessage'] = __('Your name or email address is already using on this site!', 'wfd_truck');
        } else {
            $clientIds = $wpdb->get_results("SELECT ID FROM $tbl_clients", OBJECT);
            if (count($clientIds) > 0) {
                $sql_add_client = "INSERT INTO $tbl_clients (`username`, `email`, `company`, `password`) values ('$new_user_name', '$new_email_address', '$new_company_name', '$new_password')";
            } else {
                $sql_add_client = "INSERT INTO $tbl_clients (`type`, `username`, `email`, `company`, `password`) values ('0', '$new_user_name', '$new_email_address', '$new_company_name', '$new_password')";
            }
            if ($wpdb->query($sql_add_client) != false) {
                $result_array['result'] = true;
                $result_array['message'] = __('Congratulate! Your account successfully created!', 'wdf_truck');
                $result_array['clientId'] = $wpdb->insert_id;
            } else {
                $result_array['result'] = false;
                $result_array['errorMessage'] = $wpdb->last_error;
            }
        }
    }
    echo json_encode($result_array);
    wp_die();
}

function wfd_edit_client()
{
    global $wpdb;
    $new_user_name = $_POST['new_user_name'];
    $new_email_address = $_POST['new_email_address'];
    $new_company_name = $_POST['new_company_name'];
    $new_password = md5($_POST['new_password']);
    $client_id = $_POST['client_id'];

    $result_array = array();
    $tbl_wp_users = $wpdb->users;
    $existing_users = $wpdb->get_results("SELECT * FROM $tbl_wp_users WHERE `display_name`='$new_user_name' OR `user_email`='$new_email_address'", OBJECT);
    if (count($existing_users) > 0) {
        $result_array['result'] = false;
        $result_array['errorMessage'] = __('Your name or email address is already using on this site!', 'wfd_truck');
    } else {
        $tbl_clients = $wpdb->prefix . "wfd_truck_client_info";
        $existing_users = $wpdb->get_results("SELECT * FROM $tbl_clients WHERE `username`='$new_user_name' OR `email`='$new_email_address' AND `id` NOT '$client_id'", OBJECT);
        if (count($existing_users) > 0) {
            $result_array['result'] = false;
            $result_array['errorMessage'] = __('Your name or email address is already using on this site!', 'wfd_truck');
        } else {
            $sql_add_client = "UPDATE $tbl_clients SET `username`='$new_user_name', `email`='$new_email_address', `company`='$new_company_name', `password`='$new_password' WHERE `id`='$client_id'";
            if ($wpdb->query($sql_add_client) != false) {
                $result_array['result'] = true;
                $result_array['message'] = __('Congratulate! Your account successfully created!', 'wdf_truck');
                $result_array['clientId'] = $client_id;
            } else {
                $result_array['result'] = false;
                $result_array['errorMessage'] = $wpdb->last_error;
            }
        }
    }
    echo json_encode($result_array);
    wp_die();
}

function wfd_delete_client()
{
    global $wpdb;
    $clientId = $_POST['clientId'];

    $tbl_clients = $wpdb->prefix . "wfd_truck_client_info";
    $sql_delete_client = "DELETE FROM $tbl_clients WHERE id='$clientId'";
    if ($wpdb->query($sql_delete_client) != false) {
        $result_array['result'] = true;
        $result_array['message'] = __('Client Data successfully deleted!', 'wfd_truck');
    } else {
        $result_array['result'] = false;
        $result_array['errorMessage'] = $wpdb->last_error;
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

function wfd_driver_save()
{
    global $wpdb;
    $clientId = $_SESSION['client_id'];
    $driverId = $_POST['driverId'];
    $coreData = json_decode(stripslashes($_POST['coreData']), true);
    $applicationData = json_decode(stripslashes($_POST['applicationData']), true);
    $licenseData = json_decode(stripslashes($_POST['licenseData']), true);
    $qualificationData = json_decode(stripslashes($_POST['qualificationData']), true);

    $tbl_drivers = $wpdb->prefix . "wfd_truck_driver_info";

    if ($_POST['mode'] == 'new') {
        $sql_driver = "INSERT INTO $tbl_drivers (`fname`, `lname`, `street`, `city`, `phone`, `note`, `type`, `cid`, `breakdown_rating`, `dragcar_rating`, `dragless_rating`, `dragmore_rating`, `crane_rating`, `truckservice_rating`, `c1_license`, `c1e_license`, `crane_license`, `kennz_license`, `clubmobile_license`, `caropening_license`, `motormech_qual`, `motorfore_qual`, `learned_qual`, `unlearned_qual`, `commercial_qual`, `picture`) VALUES ('%s', '%s', '%s', '%s', '%s', '%s', 'Driver', '$clientId', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')";
    } else {
        $sql_driver = "UPDATE $tbl_drivers SET fname='%s', lname='%s', street='%s', city='%s', phone='%s', note='%s', breakdown_rating='%s', dragcar_rating='%s', dragless_rating='%s', dragmore_rating='%s', crane_rating='%s', truckservice_rating='%s', c1_license='%s', c1e_license='%s', crane_license='%s', kennz_license='%s', clubmobile_license='%s', caropening_license='%s', motormech_qual='%s', motorfore_qual='%s', learned_qual='%s', unlearned_qual='%s', commercial_qual='%s', picture='%s' WHERE id='$driverId'";
    }

    $sql_driver = sprintf($sql_driver, $coreData['fname'], $coreData['lname'], $coreData['street'], $coreData['city'], $coreData['phone'], $coreData['note'], $applicationData['breakdown'], $applicationData['drag-cars'], $applicationData['drag-less-7'], $applicationData['drag-more-7'], $applicationData['crane'], $applicationData['truck-service'], $licenseData['c1'], $licenseData['c1e'], $licenseData['crane-lic'], $licenseData['kennz95'], $licenseData['club-mobil'], $licenseData['car-opening'], $qualificationData['motor-mechatronics'], $qualificationData['motor-foreman'], $qualificationData['learned'], $qualificationData['unlearned'], $qualificationData['commercial'], $_POST['new_profile_pic']);

    if ($wpdb->query($sql_driver) != false) {
        $result_array['result'] = true;
        $result_array['message'] = __('Driver information successfully updated!', 'wfd_truck');
        $result_array['driverId'] = $wpdb->insert_id;
    } else {
        $result_array['result'] = false;
        $result_array['errorMessage'] = $wpdb->last_error;
        $result_array['query'] = $sql_driver;
    }

    echo json_encode($result_array);
    wp_die();
}

function wfd_pickup_driver_save()
{
    global $wpdb;
    $clientId = $_SESSION['client_id'];
    $driverId = $_POST['pickupDriverId'];
    $coreData = json_decode(stripslashes($_POST['coreData']), true);
    $applicationData = json_decode(stripslashes($_POST['applicationData']), true);
    $licenseData = json_decode(stripslashes($_POST['licenseData']), true);
    $qualificationData = json_decode(stripslashes($_POST['qualificationData']), true);

    $tbl_drivers = $wpdb->prefix . "wfd_truck_pickup_driver_info";

    if ($_POST['mode'] == 'new') {
        $sql_driver = "INSERT INTO $tbl_drivers (`fname`, `lname`, `street`, `city`, `phone`, `note`, `type`, `cid`, `pickups_less_250`, `pickups_less_500`, `pickups_more_500`, `cars`, `truck_less_3`, `truck_less_7`, `c1_license`, `c1e_license`, `crane_lic`, `kennz95`, `motor_mechatronics`, `motor_foreman`, `learned`, `unlearned`, `commercial`, `picture`) VALUES ('%s', '%s', '%s', '%s', '%s', '%s', 'Pickup Driver', '$clientId', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s', '%s')";
    } else {
        $sql_driver = "UPDATE $tbl_drivers SET fname='%s', lname='%s', street='%s', city='%s', phone='%s', note='%s', pickups_less_250='%s', pickups_less_500='%s', pickups_more_500='%s', cars='%s', truck_less_3='%s', truck_less_7='%s', c1_license='%s', c1e_license='%s', crane_lic='%s', kennz95='%s', motor_mechatronics='%s', motor_foreman='%s', learned='%s', unlearned='%s', commercial='%s', picture='%s' WHERE id='$driverId'";
    }

    $sql_driver = sprintf($sql_driver, $coreData['fname'], $coreData['lname'], $coreData['street'], $coreData['city'], $coreData['phone'], $coreData['note'], $applicationData['pickups_less_250'], $applicationData['pickups_less_500'], $applicationData['pickups_more_500'], $applicationData['cars'], $applicationData['truck_less_3'], $applicationData['truck_less_7'], $licenseData['c1'], $licenseData['c1e'], $licenseData['crane-lic'], $licenseData['kennz95'], $qualificationData['motor_mechatronics'], $qualificationData['motor_foreman'], $qualificationData['learned'], $qualificationData['unlearned'], $qualificationData['commercial'], $_POST['new_profile_pic']);

    if ($wpdb->query($sql_driver) != false) {
        $result_array['result'] = true;
        $result_array['message'] = __('Pickup Driver information successfully updated!', 'wfd_truck');
        $result_array['driverId'] = $wpdb->insert_id;
    } else {
        $result_array['result'] = false;
        $result_array['errorMessage'] = $wpdb->last_error;
        $result_array['query'] = $sql_driver;
    }

    echo json_encode($result_array);
    wp_die();
}

function wfd_get_driver_detail(){
    global $wpdb;
    try {
        $driverId = $_POST['driverId'];
        $tbl_driver_info = $wpdb->prefix . "wfd_truck_driver_info";
        $sql_driver_detail = "SELECT * FROM $tbl_driver_info WHERE `id`='$driverId'";
        $res_driver = $wpdb->get_results($sql_driver_detail, OBJECT);
        if (count($res_driver) > 0) {
            echo json_encode($res_driver[0]);
        } else {
            echo json_encode(array('error' => $wpdb->last_error));
        }
    } catch (Exception $e) {
        echo $e->getMessage();
    }
    wp_die();
}

function wfd_get_pickup_driver_detail(){
    global $wpdb;
    try {
        $driverId = $_POST['pickupDriverId'];
        $tbl_driver_info = $wpdb->prefix . "wfd_truck_pickup_driver_info";
        $sql_driver_detail = "SELECT * FROM $tbl_driver_info WHERE `id`='$driverId'";
        $res_driver = $wpdb->get_results($sql_driver_detail, OBJECT);
        if (count($res_driver) > 0) {
            echo json_encode($res_driver[0]);
        } else {
            echo json_encode(array('error' => $wpdb->last_error));
        }
    } catch (Exception $e) {
        echo $e->getMessage();
    }
    wp_die();
}

function wfd_get_truck_detail(){
    global $wpdb;
    try {
        $driverId = $_POST['truckId'];
        $tbl_driver_info = $wpdb->prefix . "wfd_truck_truck_truck_info";
        $sql_driver_detail = "SELECT * FROM $tbl_driver_info WHERE `id`='$driverId'";
        $res_driver = $wpdb->get_results($sql_driver_detail, OBJECT);
        if (count($res_driver) > 0) {
            echo json_encode($res_driver[0]);
        } else {
            echo json_encode(array('error' => $wpdb->last_error));
        }
    } catch (Exception $e) {
        echo $e->getMessage();
    }
    wp_die();
}

function wfd_delete_driver()
{
    global $wpdb;
    $driverId = $_POST['driverId'];

    $tbl_drivers = $wpdb->prefix . "wfd_truck_driver_info";
    $sql_delete_driver = "DELETE FROM $tbl_drivers WHERE id='$driverId'";
    if ($wpdb->query($sql_delete_driver) != false) {
        $result_array['result'] = true;
        $result_array['message'] = __('Driver Data successfully deleted!', 'wfd_truck');
    } else {
        $result_array['result'] = false;
        $result_array['errorMessage'] = $wpdb->last_error;
    }

    echo json_encode($result_array);
    wp_die();

}

function wfd_delete_pickup_driver()
{
    global $wpdb;
    $driverId = $_POST['pickupDriverId'];

    $tbl_drivers = $wpdb->prefix . "wfd_truck_pickup_driver_info";
    $sql_delete_driver = "DELETE FROM $tbl_drivers WHERE id='$driverId'";
    if ($wpdb->query($sql_delete_driver) != false) {
        $result_array['result'] = true;
        $result_array['message'] = __('Driver Data successfully deleted!', 'wfd_truck');
    } else {
        $result_array['result'] = false;
        $result_array['errorMessage'] = $wpdb->last_error;
    }

    echo json_encode($result_array);
    wp_die();

}

function wfd_truck_save_core()
{
    global $wpdb;
    $errorMessages = array();
    $errorQueries = array();
    $core_info = json_decode(stripslashes($_POST['coreInfo']), true);
    $clientId = $core_info['clientId'];

    //region Core Info
    $tbl_client_info = $wpdb->prefix . 'wfd_truck_client_info';
    $company = $core_info['company'];
    $street = $core_info['street'];
    $zip = $core_info['zip'];
    $city = $core_info['city'];
    $phone = $core_info['phone'];
    $fax = $core_info['fax'];
    $website = $core_info['website'];
    $emergency = $core_info['emergencyPhone'];
    $note = $core_info['note'];

    $sql_update_driver = "UPDATE $tbl_client_info SET company='$company', fax='$fax', website='$website', emergency_phone='$emergency', zip='$zip', street='$street', city='$city', phone='$phone', note='$note' WHERE id='$clientId'";

    if ($wpdb->query($sql_update_driver) == false && $wpdb->last_error != "") {
        array_push($errorMessages, $wpdb->last_error);
        array_push($errorQueries, $sql_update_driver);
    }
    //endregion

    //region Opening Hours

    $tbl_opening_hours = $wpdb->prefix . 'wfd_truck_operating_hours';
    $opening_hours_info = json_decode(stripslashes($_POST['openHours']), true);
    $remove_sql = "DELETE FROM $tbl_opening_hours WHERE cid='$clientId'";
    $wpdb->query($remove_sql);

    $insert_opening_hours = "INSERT INTO $tbl_opening_hours (`cid`, `type`, `rdays_start`, `rdays_end`, `weday_start`, `weday_end`, `wday_start`, `wday_end`)VALUES('$clientId', 'Office', '%s', '%s', '%s', '%s', '%s', '%s');";
    $insert_opening_hours = sprintf($insert_opening_hours, $opening_hours_info['ohOfficeMonStart'], $opening_hours_info['ohOfficeMonEnd'], $opening_hours_info['ohOfficeSatStart'], $opening_hours_info['ohOfficeSatEnd'], $opening_hours_info['ohOfficeSunStart'], $opening_hours_info['ohOfficeSunEnd']);
    if ($wpdb->query($insert_opening_hours) == false) {
        array_push($errorMessages, $wpdb->last_error);
        array_push($errorQueries, $insert_opening_hours);
    }

    $insert_opening_hours = "INSERT INTO $tbl_opening_hours (`cid`, `type`, `rdays_start`, `rdays_end`, `weday_start`, `weday_end`, `wday_start`, `wday_end`) VALUES('$clientId', 'Garage', '%s', '%s', '%s', '%s', '%s', '%s');";
    $insert_opening_hours = sprintf($insert_opening_hours, $opening_hours_info['ohGarageMonStart'], $opening_hours_info['ohGarageMonEnd'], $opening_hours_info['ohGarageSatStart'], $opening_hours_info['ohGarageSatEnd'], $opening_hours_info['ohGarageSunStart'], $opening_hours_info['ohGarageSunEnd']);
    if ($wpdb->query($insert_opening_hours) == false) {
        array_push($errorMessages, $wpdb->last_error);
        array_push($errorQueries, $insert_opening_hours);
    }

    $insert_opening_hours = "INSERT INTO $tbl_opening_hours (`cid`, `type`, `rdays_start`, `rdays_end`, `weday_start`, `weday_end`, `wday_start`, `wday_end`) VALUES('$clientId', 'Car Rental', '%s', '%s', '%s', '%s', '%s', '%s');";
    $insert_opening_hours = sprintf($insert_opening_hours, $opening_hours_info['ohCarMonStart'], $opening_hours_info['ohCarMonEnd'], $opening_hours_info['ohCarSatStart'], $opening_hours_info['ohCarSatEnd'], $opening_hours_info['ohCarSunStart'], $opening_hours_info['ohCarSunEnd']);
    if ($wpdb->query($insert_opening_hours) == false) {
        array_push($errorMessages, $wpdb->last_error);
        array_push($errorQueries, $insert_opening_hours);
    }

    $insert_opening_hours = "INSERT INTO $tbl_opening_hours (`cid`, `type`, `rdays_start`, `rdays_end`, `weday_start`, `weday_end`, `wday_start`, `wday_end`) VALUES('$clientId', 'On call duty', '%s', '%s', '%s', '%s', '%s', '%s');";
    $insert_opening_hours = sprintf($insert_opening_hours, $opening_hours_info['ohDutyMonStart'], $opening_hours_info['ohDutyMonEnd'], $opening_hours_info['ohDutySatStart'], $opening_hours_info['ohDutySatEnd'], $opening_hours_info['ohDutySunStart'], $opening_hours_info['ohDutySunEnd']);
    if ($wpdb->query($insert_opening_hours) == false) {
        array_push($errorMessages, $wpdb->last_error);
        array_push($errorQueries, $insert_opening_hours);
    }
    //endregion

    //region Payment
    $payment = json_decode(stripslashes($_POST['payment']), true);
    $tbl_pay_m = $wpdb->prefix . "wfd_truck_pay_m";
    $tbl_pay_m_u = $wpdb->prefix . "wfd_truck_truck_pay_m_u";
    $sql_pay_insert_temp = "INSERT INTO $tbl_pay_m_u (payid, cid) SELECT p.id, '%s' FROM $tbl_pay_m p WHERE method='%s';";
    $sql_delete_pay = "DELETE FROM $tbl_pay_m_u WHERE cid='$clientId'";
    $wpdb->query($sql_delete_pay);

    foreach ($payment as $method) {
        $sql_pay_insert = sprintf($sql_pay_insert_temp, $clientId, $method);
        if ($wpdb->query($sql_pay_insert) == false) {
            array_push($errorMessages, $wpdb->last_error);
            array_push($errorQueries, $sql_pay_insert);
        }
    }
    //endregion

    //region Partner
    $partner = json_decode(stripslashes($_POST['partner']), true);
    $tbl_partner_m = $wpdb->prefix . "wfd_truck_truck_partner";
    $tbl_partner_m_u = $wpdb->prefix . "wfd_truck_truck_partner_u";
    $sql_partner_insert_temp = "INSERT INTO $tbl_partner_m_u (pid, cid) SELECT p.id, '%s' FROM $tbl_partner_m p WHERE partner='%s';";
    $sql_delete_partner = "DELETE FROM $tbl_partner_m_u WHERE cid='$clientId'";
    $wpdb->query($sql_delete_partner);

    foreach ($partner as $method) {
        $sql_partner_insert = sprintf($sql_partner_insert_temp, $clientId, $method);
        if ($wpdb->query($sql_partner_insert) == false) {
            array_push($errorMessages, $wpdb->last_error);
            array_push($errorQueries, $sql_partner_insert);
        }
    }
    //endregion

    //region Assistance
    $assistance = json_decode(stripslashes($_POST['assistance']), true);
    $tbl_assistance = $wpdb->prefix . "wfd_truck_assistance";
    $tbl_assistance_u = $wpdb->prefix . "wfd_truck_assistance_u";
    $assistance_values = $wpdb->get_results("SELECT * FROM $tbl_assistance", OBJECT);

    $sql_assistance_insert_temp = "INSERT INTO $tbl_assistance_u (aid, cid) SELECT p.id, '%s' FROM $tbl_assistance p WHERE assistance='%s';";
    $sql_delete_partner = "DELETE FROM $tbl_assistance_u WHERE cid='$clientId'";
    $wpdb->query($sql_delete_partner);

    foreach ($assistance as $method) {
        $is_new = true;
        foreach ($assistance_values as $ass) {
            if ($ass->assistance == $method) {
                $is_new = false;
                break;
            }
        }
        if ($is_new == true) {
            $wpdb->query("INSERT INTO $tbl_assistance (`assistance`) VALUES('$method')");
        }
        $sql_assistance_insert = sprintf($sql_assistance_insert_temp, $clientId, $method);
        if ($wpdb->query($sql_assistance_insert) == false) {
            array_push($errorMessages, $wpdb->last_error);
            array_push($errorQueries, $sql_assistance_insert);
        }
    }
    //endregion

    //region Mobi Service
    $mobi = json_decode(stripslashes($_POST['mobi']), true);
    $tbl_mobi = $wpdb->prefix . "wfd_truck_mobi_service";

    $wpdb->query("DELETE FROM $tbl_mobi WHERE cid='$clientId'");

    $m = join("'), ($clientId, '", $mobi);
    $sql_mobi = "INSERT INTO $tbl_mobi (cid, mobi_service) VALUES ($clientId, '$m')";
    if ($wpdb->query($sql_mobi) == false) {
        array_push($errorMessages, $wpdb->last_error);
        array_push($errorQueries, $sql_mobi);
    }
    //endregion


    if (count($errorMessages) == 0) {
        $result_array['result'] = true;
        $result_array['message'] = __('Client Core information successfully updated!', 'wfd_truck');
    } else {
        $result_array['result'] = false;
        $result_array['errorMessage'] = join("\n", $errorMessages);
        $result_array['query'] = join("\n", $errorQueries);
    }

//    echo $_POST['coreInfo'];
    echo json_encode($result_array);
    wp_die();

}

function wfd_add_truck()
{
    global $wpdb;
    $cid=$_POST['clientId'];
    $new_truck_id = $_POST['new_truck_id'];
    $new_brand = $_POST['new_brand'];
    $new_weight = $_POST['new_weight'];
    $new_max_load = $_POST['new_max_load'];
    $new_load_height= $_POST['new_load_height'];
    $new_truck_type = $_POST['new_truck_type'];
    $new_status = $_POST['new_status'];
    $new_pheight = $_POST['new_pheight'];
    $new_spec_force= $_POST['new_spec_force'];
    $new_cable_force = $_POST['new_cable_force'];
    $new_crane = $_POST['new_crane'];
    $new_plength = $_POST['new_plength'];
    $new_motorcycle= $_POST['new_motorcycle'];
    $new_seats = $_POST['new_seats'];
    $new_under_lift = $_POST['new_under_lift'];
    $new_image=$_POST['new_image'];
    $new_out_order = $_POST['new_out_order'];

    $result_array = array();
    $tbl_truck = $wpdb->prefix . "wfd_truck_truck_truck_info";
    $sql_add_truck = "INSERT INTO $tbl_truck (cid, brand, weight, max_load, load_height, type, status, plateau_height, plateau_lengh, spectacle_force, cable_winch_force, crane, motorcycle, seats, uder_lift, picture, truck_ID) values ('$cid', '$new_brand', '$new_weight', '$new_max_load', '$new_load_height', '$new_truck_type', '$new_status', '$new_pheight', '$new_plength', '$new_spec_force', '$new_cable_force', '$new_crane', '$new_motorcycle', '$new_seats', '$new_under_lift', '$new_image' ,'$new_truck_id')";
    if ($wpdb->query($sql_add_truck) != false) {
        $result_array['result'] = true;
        $result_array['message'] = __('Congratulate! Truck Data successfully created!', 'wdf_truck');
        $result_array['clientId'] = $wpdb->insert_id;
    } else {
        $result_array['result'] = false;
        $result_array['errorMessage'] = $wpdb->last_error;
    }

    echo json_encode($result_array);
    wp_die();
}

function wfd_update_truck()
{
    global $wpdb;
    $cid=$_POST['clientId'];
    $selId=$_POST['selId'];
    $new_truck_id = $_POST['new_truck_id'];
    $new_brand = $_POST['new_brand'];
    $new_weight = $_POST['new_weight'];
    $new_max_load = $_POST['new_max_load'];
    $new_load_height= $_POST['new_load_height'];
    $new_truck_type = $_POST['new_truck_type'];
    $new_status = $_POST['new_status'];
    $new_pheight = $_POST['new_pheight'];
    $new_spec_force= $_POST['new_spec_force'];
    $new_cable_force = $_POST['new_cable_force'];
    $new_crane = $_POST['new_crane'];
    $new_plength = $_POST['new_plength'];
    $new_motorcycle= $_POST['new_motorcycle'];
    $new_seats = $_POST['new_seats'];
    $new_under_lift = $_POST['new_under_lift'];
    $new_out_order = $_POST['new_out_order'];
    $picture = $_POST['new_profile_pic'];

    $result_array = array();
    $tbl_truck = $wpdb->prefix . "wfd_truck_truck_truck_info";
    $sql_update_truck = "UPDATE $tbl_truck SET cid='$cid', brand='$new_brand', weight='$new_weight', max_load='$new_max_load', load_height='$new_load_height', type='$new_truck_type', status='$new_status', plateau_height='$new_pheight', plateau_lengh='$new_plength', spectacle_force='$new_spec_force', cable_winch_force='$new_cable_force', crane='$new_crane', motorcycle='$new_motorcycle', seats='$new_seats', uder_lift='$new_under_lift', truck_ID='$new_truck_id', picture='$picture' WHERE id='$selId'";
    if ($wpdb->query($sql_update_truck) != false) {
        $result_array['result'] = true;
        $result_array['message'] = __('Congratulate! Truck Data successfully updated!', 'wfd_truck');
        $result_array['clientId'] = $wpdb->insert_id;
    } else {
        $result_array['result'] = false;
        $result_array['errorMessage'] = $wpdb->last_error;
    }

    echo json_encode($result_array);
    wp_die();
}

function wfd_delete_truck()
{
    global $wpdb;
    $selId = $_POST['selId'];

    $tbl_truck = $wpdb->prefix . "wfd_truck_truck_truck_info";
    $sql_delete_truck = "DELETE FROM $tbl_truck WHERE id='$selId'";
    if ($wpdb->query($sql_delete_truck) != false) {
        $result_array['result'] = true;
        $result_array['message'] = __('Truck Data successfully deleted!', 'wfd_truck');
    } else {
        $result_array['result'] = false;
        $result_array['errorMessage'] = $wpdb->last_error;
    }

    echo json_encode($result_array);
    wp_die();
}

function wfd_add_callnum()
{
    global $wpdb;
    $cid=$_POST['clientId'];
    $new_name = $_POST['new_name'];
    $new_phoneno = $_POST['new_phoneno'];
    $new_callnote = $_POST['new_callnote'];
    $new_category= $_POST['new_category'];

    $result_array = array();
    $tbl_callnum = $wpdb->prefix . "wfd_truck_call_num";
    $sql_add_callnum = "INSERT INTO $tbl_callnum (cid, name, phone, note, category) values ('$cid', '$new_name', '$new_phoneno', '$new_callnote', '$new_category')";
    if ($wpdb->query($sql_add_callnum) != false) {
        $result_array['result'] = true;
        $result_array['message'] = __('Congratulate! Call Number successfully created!', 'wdf_truck');
        $result_array['clientId'] = $wpdb->insert_id;
    } else {
        $result_array['result'] = false;
        $result_array['errorMessage'] = $wpdb->last_error;
    }

    echo json_encode($result_array);
    wp_die();
}

function wfd_update_callnum()
{
    global $wpdb;
    $selId = $_POST['selId'];
    $name = $_POST['new_name'];
    $phoneno = $_POST['new_phoneno'];
    $callnote = $_POST['new_callnote'];
    $category = $_POST['new_category'];
    $cid = $_POST['clientId'];

    $result_array = array();
    $tbl_callnum = $wpdb->prefix . "wfd_truck_call_num";
    $sql_update_callnum = "UPDATE $tbl_callnum SET cid='$cid', name='$name', phone='$phoneno', note='$callnote', category='$category' WHERE id='$selId'";
    if ($wpdb->query($sql_update_callnum) != false) {
        $result_array['result'] = true;
        $result_array['message'] = __('Call Number information successfully updated!', 'wfd_truck');
    } else {
        $result_array['result'] = false;
        $result_array['errorMessage'] = $wpdb->last_error;
    }

    echo json_encode($result_array);
    wp_die();
}

function wfd_delete_callnum()
{
    global $wpdb;
    $selId = $_POST['selId'];

    $tbl_callnum = $wpdb->prefix . "wfd_truck_call_num";
    $sql_delete_callnum = "DELETE FROM $tbl_callnum WHERE id='$selId'";
    if ($wpdb->query($sql_delete_callnum) != false) {
        $result_array['result'] = true;
        $result_array['message'] = __('Call Number Data successfully deleted!', 'wfd_truck');
    } else {
        $result_array['result'] = false;
        $result_array['errorMessage'] = $wpdb->last_error;
    }

    echo json_encode($result_array);
    wp_die();
}

function wfd_add_service()
{
    global $wpdb;
    $cid=$_POST['clientId'];
    $new_service = $_POST['new_service'];
    $new_description = $_POST['new_description'];
    $new_price = $_POST['new_price'];

    $result_array = array();
    $tbl_service = $wpdb->prefix . "wfd_truck_truck_prices";
    $sql_add_service = "INSERT INTO $tbl_service (cid, service, description, price) values ('$cid', '$new_service', '$new_description', '$new_price')";
    if ($wpdb->query($sql_add_service) != false) {
        $result_array['result'] = true;
        $result_array['message'] = __('Congratulate! Service Price successfully created!', 'wdf_truck');
        $result_array['clientId'] = $wpdb->insert_id;
    } else {
        $result_array['result'] = false;
        $result_array['errorMessage'] = $wpdb->last_error;
    }

    echo json_encode($result_array);
    wp_die();
}

function wfd_delete_service()
{
    global $wpdb;
    $selId = $_POST['selId'];

    $tbl_service = $wpdb->prefix . "wfd_truck_truck_prices";
    $sql_delete_service = "DELETE FROM $tbl_service WHERE id='$selId'";
    if ($wpdb->query($sql_delete_service) != false) {
        $result_array['result'] = true;
        $result_array['message'] = __('Service Data successfully deleted!', 'wfd_truck');
    } else {
        $result_array['result'] = false;
        $result_array['errorMessage'] = $wpdb->last_error;
    }

    echo json_encode($result_array);
    wp_die();
}

function wfd_update_service()
{
    global $wpdb;
    $selId = $_POST['selId'];
    $service = $_POST['new_service'];
    $description = $_POST['new_description'];
    $price = $_POST['new_price'];
    $cid = $_POST['clientId'];

    $result_array = array();
    $tbl_service = $wpdb->prefix . "wfd_truck_truck_prices";
    $sql_update_service = "UPDATE $tbl_service SET cid='$cid', service='$service', description='$description', price='$price' WHERE id='$selId'";
    if ($wpdb->query($sql_update_service) != false) {
        $result_array['result'] = true;
        $result_array['message'] = __('Service information successfully updated!', 'wfd_truck');
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
    $tbl_urls = $wpdb->prefix . "wfd_client_page";
    $client_page_urls = $wpdb->get_results("SELECT * FROM $tbl_urls WHERE pagename='client'");
    if (isset($_POST['client-page'])) {
        if (count($client_page_urls) > 0) {
            $page_id = $client_page_urls[0]->Id;
            $wpdb->query("UPDATE $tbl_urls SET `url`='" . $_POST['client-page'] . "' WHERE `Id`=$page_id");
        } else {
            $wpdb->query("INSERT INTO $tbl_urls (`pagename`, `url`) VALUES ('client', '" . $_POST['client-page'] . "')");
        }
        $client_page_urls = $wpdb->get_results("SELECT * FROM $tbl_urls WHERE pagename='client'");

    }

    ?>
    <div class="wrap">
        <label><h1>Truck Settings</h1></label>
        <form method="post">
            <table class="form-table">
                <tr>
                    <th scope="row"><label><?php _e('Select Client page', 'wfd_truck'); ?></label></th>
                    <td><select name="client-page" id="select-client-page" style="width: 15%;">

                            <?php
                            $pages = get_pages();
                            foreach ($pages as $page) {
                                if (count($client_page_urls) > 0 && $client_page_urls[0]->url == get_page_link($page->ID)) {
                                    $option = '<option value="' . get_page_link($page->ID) . '" selected>';
                                } else {
                                    $option = '<option value="' . get_page_link($page->ID) . '">';
                                }
                                $option .= $page->post_title;
                                $option .= '</option>';
                                echo $option;
                            }
                            ?>
                        </select></td>
                </tr>
            </table>
            <p class="submit">
                <input type="submit" class="button button-primary" id="settings-save"
                       value="<?php _e('Save Changes', 'wfd_truck'); ?>">
            </p>
        </form>
    </div>
    <?php
}

add_action('init', 'wfd_truck_init_fn');

function wfd_truck_init_fn()
{
    session_start();
    global $wpdb;
    $tbl_client_info = $wpdb->prefix . "wfd_truck_client_info";

//    if (isset($_POST['save_ranking'])) {
//        $rank_item = $_POST['rank_item'];
//        $tbl_ranking = $wpdb->prefix . "wfd_truck_driver_ranking";
//        $sql_ranking = "INSERT INTO $tbl_ranking (rank_item) values ('$rank_item')";
//        $wpdb->query($sql_ranking);
//    }
//    if (isset($_POST['update_ranking'])) {
//        $rank_item = $_POST['rank_item'];
//        $id = $_POST['id'];
//        $tbl_ranking = $wpdb->prefix . "wfd_truck_driver_ranking";
//        $sql = "UPDATE $tbl_ranking set rank_item='$rank_item' where id=$id";
//        $wpdb->query($sql);
//
//    }
//    if (isset($_POST['save_license'])) {
//        $licenses = $_POST['licenses'];
//        $tbl_driver_licences = $wpdb->prefix . "wfd_truck_driver_licences";
//        $sql = "INSERT INTO $tbl_driver_licences (licences) values ('$licenses')";
//        $wpdb->query($sql);
//    }
//    if (isset($_POST['save_qualification'])) {
//        $qualification = $_POST['qualification'];
//        $tbl_driver_qualification = $wpdb->prefix . "wfd_truck_driver_qualification";
//        $sql = "INSERT INTO $tbl_driver_qualification (qualification) values ('$qualification')";
//        $wpdb->query($sql);
//    }
//    if (isset($_POST['update_licenses'])) {
//        $licenses = $_POST['licenses'];
//        $id = $_POST['id'];
//        $tbl_driver_licences = $wpdb->prefix . "wfd_truck_driver_licences";
//        $sql = "UPDATE $tbl_driver_licences set licences='$licenses' where id=$id";
//        $wpdb->query($sql);
//    }
//    if (isset($_POST['update_qualification'])) {
//        $id = $_POST['id'];
//        $qualification = $_POST['qualification'];
//        $tbl_driver_qualification = $wpdb->prefix . "wfd_truck_driver_qualification";
//        $sql = "UPDATE $tbl_driver_qualification set qualification='$qualification' where id=$id";
//        $wpdb->query($sql);
//    }
//    if (isset($_POST['add_new_clinet'])) {
//        $company = $_POST['company'];
//        $email = $_POST['email'];
//        $username = $_POST['username'];
//        $password = md5($_POST['password']);
//        $street = $_POST['street'];
//        $zip = $_POST['zip'];
//        $city = $_POST['city'];
//        $phone = $_POST['phone'];
//        $fax = $_POST['fax'];
//        $website = $_POST['website'];
//        $emergency_phone = $_POST['emergency_phone'];
//        $note = $_POST['note'];
//
//        $tbl_client_info = $wpdb->prefix . "wfd_truck_client_info";
//        $sql_ins_client_info = "INSERT INTO $tbl_client_info (`company`, `email`, `username`, `password`, `street`, `zip`, `city`, `phone`, `fax`,  `website`, `emergency_phone`, `note`) "
//            . "values ('$company','$email', '$username', '$password', '$street','$zip', '$city', '$phone', '$fax', '$website', '$emergency_phone', '$note');";
//        //echo $sql_ins_client_info;
//        $wpdb->query($sql_ins_client_info);
//    }
//    if (isset($_POST['chang_pass'])) {
//        $id = $_POST['id'];
//        $old_pass = md5($_POST['old_pass']);
//        $new_pass = md5($_POST['new_pass']);
//        $tbl_client_info = $wpdb->prefix . "wfd_truck_client_info";
//        $sql_update_client_pass = "UPDATE $tbl_client_info SET `password`='$new_pass' where id=$id;";
//        //echo $sql_update_client_pass;
//        $wpdb->query($sql_update_client_pass);
//        $_SESSION['client_login'] = 'false';
//        $_SESSION['client_username'] = '';
//        $_SESSION['client_id'] = '';
//    if (isset($_POST['edit_new_clinet'])) {
//        $company = $_POST['company'];
//
//
//        $id = $_POST['id'];
//        $street = $_POST['street'];
//        $zip = $_POST['zip'];
//        $city = $_POST['city'];
//        $phone = $_POST['phone'];
//        $fax = $_POST['fax'];
//        $website = $_POST['website'];
//        $emergency_phone = $_POST['emergency_phone'];
//        $note = $_POST['note'];
//
//        $tbl_client_info = $wpdb->prefix . "wfd_truck_client_info";
//        $sql_update_client_info = "UPDATE $tbl_client_info SET `company`='$company',  `street`='$street', `zip`='$zip', `city`='$city', `phone`='$phone', `fax`='$fax',  `website`='$website', `emergency_phone`='$emergency_phone',`note`='$note' where id=$id;";
//        $wpdb->query($sql_update_client_info);
//    }
//    if (isset($_POST['edit_new_clinet_fn'])) {
//        $company = $_POST['company'];
//
//
//        $id = $_POST['id'];
//        $street = $_POST['street'];
//        $zip = $_POST['zip'];
//        $city = $_POST['city'];
//        $phone = $_POST['phone'];
//        $fax = $_POST['fax'];
//        $website = $_POST['website'];
//        $emergency_phone = $_POST['emergency_phone'];
//        $note = $_POST['note'];
//
//        $tbl_client_info = $wpdb->prefix . "wfd_truck_client_info";
//        $sql_update_client_info = "UPDATE $tbl_client_info SET `company`='$company',  `street`='$street', `zip`='$zip', `city`='$city', `phone`='$phone', `fax`='$fax',  `website`='$website', `emergency_phone`='$emergency_phone',`note`='$note' where id=$id;";
//        $wpdb->query($sql_update_client_info);
//    }

    if (isset($_POST['client_login'])) {
        $username = $_POST['username'];
        $password = md5($_POST['password']);
        $res_client_info = $wpdb->get_results("select * from $tbl_client_info where username='$username' and password='$password' limit 1", OBJECT);

        if (count($res_client_info) != 0) {
            $_SESSION['client_login'] = 'true';
            $_SESSION['client_username'] = $res_client_info[0]->username;
            $_SESSION['client_id'] = $res_client_info[0]->id;
            $_GET['action'] = 'profile';
        }
    }

    if (isset($_POST['admin'])) {
        $clientId = $_POST['client_id'];
        $editMode = $_POST['edit_mode'];
        $res_client_info = $wpdb->get_results("select * from $tbl_client_info where id='$clientId'", OBJECT);
        if (count($res_client_info) != 0) {
            $_SESSION['client_login'] = 'true';
            $_SESSION['client_username'] = $res_client_info[0]->username;
            $_SESSION['client_id'] = $res_client_info[0]->id;
            $_SESSION['edit_mode'] = $editMode;
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

    $tbl_client_page = $wpdb->prefix . "wfd_client_page";
    $sql_client_page = "CREATE TABLE `$tbl_client_page` (`Id` int(11) NOT NULL AUTO_INCREMENT, `pagename` varchar(255) DEFAULT '', `url` varchar(255) NOT NULL DEFAULT '', PRIMARY KEY (`Id`)) $charset_collate;";
    dbDelta($sql_client_page);

    $tbl_pay_m = $wpdb->prefix . "wfd_truck_pay_m";
    $sql_pay_m = "CREATE TABLE IF NOT EXISTS $tbl_pay_m (
    `id` int(9) NOT NULL AUTO_INCREMENT,
    `method` varchar(50) NOT NULL,
      PRIMARY KEY (id)
    ) $charset_collate";
    dbDelta($sql_pay_m);
    $sql_pay_m_insert = "INSERT INTO `wp_wfd_truck_pay_m` VALUES (1,'" . __('Cash', 'wfd_truck') . "'),(2,'" .
        __('Maestrocard', 'wfd_truck') . "'),(3,'" . __('Visa', 'wfd_truck') . "'),(4,'" . __('Master', 'wfd_truck') . "'),(5,'" .
        __('Amex', 'wfd_truck') . "');";
    $wpdb->query($sql_pay_m_insert);


    $tbl_assistance = $wpdb->prefix . "wfd_truck_assistance";
    $sql_assistance = "CREATE TABLE IF NOT EXISTS $tbl_assistance (
    `id` int(9) NOT NULL AUTO_INCREMENT,
    `assistance` varchar(150) NOT NULL,
      PRIMARY KEY (id)
    ) $charset_collate";
    dbDelta($sql_assistance);
    $sql_assistance_insert = sprintf("INSERT INTO `wp_wfd_truck_assistance` VALUES (1,'%s'),(2,'%s'),(3,'%s'),(4,'%s'),(5,'%s');",
        __('IMA', 'wfd_truck'), __('DEVK', 'wfd_truck'), __('DKV', 'wfd_truck'), __('LVM', 'wfd_truck'), __('HDI', 'wfd_truck'));
    $wpdb->query($sql_assistance_insert);


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
      `type` varchar(25) COLLATE utf8mb4_unicode_520_ci NOT NULL,
      `fname` varchar(100) COLLATE utf8mb4_unicode_520_ci NOT NULL,
      `lname` varchar(100) COLLATE utf8mb4_unicode_520_ci NOT NULL,
      `street` varchar(100) COLLATE utf8mb4_unicode_520_ci NOT NULL,
      `city` varchar(100) COLLATE utf8mb4_unicode_520_ci NOT NULL,
      `phone` varchar(20) COLLATE utf8mb4_unicode_520_ci NOT NULL,
      `note` varchar(500) COLLATE utf8mb4_unicode_520_ci NOT NULL,
      `picture` longblob,
      `breakdown_rating` tinyint(3) DEFAULT NULL,
      `dragcar_rating` tinyint(3) DEFAULT NULL,
      `dragless_rating` tinyint(3) DEFAULT NULL,
      `dragmore_rating` tinyint(3) DEFAULT NULL,
      `crane_rating` tinyint(3) DEFAULT NULL,
      `truckservice_rating` tinyint(3) DEFAULT NULL,
      `c1_license` tinyint(3) DEFAULT NULL,
      `c1e_license` tinyint(3) DEFAULT NULL,
      `crane_license` tinyint(3) DEFAULT NULL,
      `kennz_license` tinyint(3) DEFAULT NULL,
      `clubmobile_license` tinyint(3) DEFAULT NULL,
      `caropening_license` tinyint(3) DEFAULT NULL,
      `motormech_qual` tinyint(3) DEFAULT NULL,
      `motorfore_qual` tinyint(3) DEFAULT NULL,
      `learned_qual` tinyint(3) DEFAULT NULL,
      `unlearned_qual` tinyint(3) DEFAULT NULL,
      `commercial_qual` tinyint(3) DEFAULT NULL,
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
    $sql_truck_partner_insert = sprintf("INSERT INTO `wp_wfd_truck_truck_partner` VALUES (1,'ADAC'),(2,'ADACplus'),(3,'ADAC-Truck'),(4,'AvD'),(5,'ACE');",
        __('ADAC', 'wfd_truck'), __('ADACplus', 'wfd_truck'), __('ADAC-Truck', 'wfd_truck'), __('AvD', 'wfd_truck'), __('ACE', 'wfd_truck'));
    $wpdb->query($sql_truck_partner_insert);

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
      `brand` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
      `weight` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
      `max_load` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
      `load_height` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
      `type` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
      `status` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
      `plateau_height` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
      `plateau_lengh` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
      `spectacle_force` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
      `cable_winch_force` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
      `crane` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
      `motorcycle` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
      `seats` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
      `uder_lift` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
      `picture` longblob,
      `license` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
      `qualification` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
      `rating` varchar(500) COLLATE utf8mb4_unicode_ci NOT NULL,
      `truck_ID` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
      PRIMARY KEY (id)
    ) $charset_collate";
    dbDelta($sql_truck_truck_info);

    $tbl_pickup_driver_info = $wpdb->prefix . "wfd_truck_pickup_driver_info";
    $sql_pickup_driver_info = "CREATE TABLE IF NOT EXISTS $tbl_pickup_driver_info (
      `id` int(9) NOT NULL AUTO_INCREMENT,
      `cid` int(9) NOT NULL,
      `type` varchar(25) COLLATE utf8mb4_unicode_520_ci NOT NULL,
      `fname` varchar(100) COLLATE utf8mb4_unicode_520_ci NOT NULL,
      `lname` varchar(100) COLLATE utf8mb4_unicode_520_ci NOT NULL,
      `street` varchar(100) COLLATE utf8mb4_unicode_520_ci NOT NULL,
      `city` varchar(100) COLLATE utf8mb4_unicode_520_ci NOT NULL,
      `phone` varchar(20) COLLATE utf8mb4_unicode_520_ci NOT NULL,
      `note` varchar(500) COLLATE utf8mb4_unicode_520_ci NOT NULL,
      `picture` longblob,
      `pickups_less_250` tinyint(3) DEFAULT NULL,
      `pickups_less_500` tinyint(3) DEFAULT NULL,
      `pickups_more_500` tinyint(3) DEFAULT NULL,
      `truck_less_7` tinyint(3) DEFAULT NULL,
      `cars` tinyint(3) DEFAULT NULL,
      `truck_less_3` tinyint(3) DEFAULT NULL,
      `c1_license` tinyint(3) DEFAULT NULL,
      `c1e_license` tinyint(3) DEFAULT NULL,
      `crane_lic` tinyint(3) DEFAULT NULL,
      `kennz95` tinyint(3) DEFAULT NULL,
      `motor_mechatronics` tinyint(3) DEFAULT NULL,
      `motor_foreman` tinyint(3) DEFAULT NULL,
      `learned` tinyint(3) DEFAULT NULL,
      `unlearned` tinyint(3) DEFAULT NULL,
      `commercial` tinyint(3) DEFAULT NULL,
      PRIMARY KEY (id)
    ) $charset_collate";
    dbDelta($sql_pickup_driver_info);
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

function wfd_admin_view_as_wp_menu()
{
    global $wpdb;
    $tbl_client_info = $wpdb->prefix . "wfd_truck_client_info";
    $res_client_list = $wpdb->get_results("select * from $tbl_client_info", OBJECT);

    $tbl_client_view_url = $wpdb->prefix . "wfd_client_page";
    $res_client_view_url = $wpdb->get_results("SELECT * FROM $tbl_client_view_url WHERE pagename='client'", OBJECT);

    if (count($res_client_view_url) > 0) {
        $nav_url = $res_client_view_url[0]->url;
    } else {
        $nav_url = menu_page_url('wfd_truck_settings');
    }
    my_enqueue();
    ?>
    <div role="tabpanel" class="tab-pane" id="client_list">
        <div class="col-sm-12">
            <h2><?php _e('Clients List', 'wfd_truck'); ?></h2>

            <table>
                <colgroup>
                    <col class="col-md-1">
                    <col class="col-md-1">
                    <col class="col-md-1">
                    <col class="col-md-1">
                    <col class="col-md-1">
                    <col class="col-md-1">
                    <col class="col-md-1">
                </colgroup>
                <tr>
                    <td><input id="filter-company" placeholder="<?php _e('filter company', 'wfd_truck') ?>"></td>
                    <td></td>
                    <td><input id="filter-zip" placeholder="<?php _e('filter ZIP', 'wfd_truck') ?>"></td>
                    <td><input id="filter-city" placeholder="<?php _e('filter city', 'wfd_truck') ?>"></td>
                    <td><div></div></td>
                    <td><div></div></td>
                    <td><div></div></td>
                </tr>
            </table>
            <table class="table table-striped" data-toggle="table" id="clients-list" data-unique-id="id">
                <colgroup>
                    <col class="col-md-1">
                    <col class="col-md-1">
                    <col class="col-md-1">
                    <col class="col-md-1">
                    <col class="col-md-1">
                    <col class="col-md-1">
                    <col class="col-md-1">
                </colgroup>
                <thead>
                <tr>
                    <th data-field="id" data-visible="false"></th>
                    <th data-field="username" data-visible="false"></th>
                    <th data-field="email" data-visible="false"></th>
                    <th data-field="company"
                        data-sortable="true"><?php _e('Company', 'wfd_truck'); ?></th>
                    <th data-field="street"><?php _e('Street', 'wfd_truck'); ?></th>
                    <th data-field="zip"
                        data-sortable="true"><?php _e('Zip', 'wfd_truck'); ?></th>
                    <th data-field="city"
                        data-sortable="true"><?php _e('City', 'wfd_truck'); ?></th>
                    <th data-field="phone"><?php _e('Phone', 'wfd_truck'); ?></th>
                    <th data-field="note"><?php _e('Note', 'wfd_truck'); ?></th>
                    <th data-field="action" data-formatter="clientsActionFormatter" data-events="clientsActionEvents"><?php _e('Action', 'wfd_truck'); ?></th>
                </tr>
                </thead>
                <tbody>
                <?php

                foreach ($res_client_list as $client) { ?>
                    <tr data-user-id="<?php echo $client->id ?>" data-user-name="<?php echo $client->username ?>"
                        data-email-address="<?php echo $client->email ?>" data-word="<?php echo $client->password ?>">
                        <td><?php echo $client->id ?></td>
                        <td><?php echo $client->username ?></td>
                        <td><?php echo $client->email ?></td>
                        <td><?php echo $client->company ?></td>
                        <td><?php echo $client->street ?></td>
                        <td><?php echo $client->zip ?></td>
                        <td><?php echo $client->city ?></td>
                        <td><?php echo $client->phone ?></td>
                        <td><?php echo $client->note ?></td>
                        <td></td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
            <button type="button" class="btn btn-primary" id="btn-add-client">
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
                            <h4 class="modal-title" id="new-client-title"><?php _e('New Client', 'wfd_truck'); ?></h4>
                            <h4 class="modal-title" id="edit-client-title" style="display: none;"><?php _e('Edit Client', 'wfd_truck'); ?></h4>
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
        <div class="modal fade" id="modal_nav_client" tabindex="-1"
             role="dialog" aria-labelledby="myModalLabel">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close"
                                data-dismiss="modal"
                                aria-label="Close"><span
                                    aria-hidden="true">&times;</span>
                        </button>
                        <h4 class="modal-title"><?php _e('Navigate to Client View', 'wfd_truck'); ?></h4>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label class="col-sm-6"><?php _e('Please select client page', 'wfd_truck') ?></label>
                            <div class="col-sm-6">
                                <select name="page-dropdown" id="select-client-page">
                                    <option value="">
                                        <?php echo esc_attr(__('Select page')); ?></option>
                                    <?php
                                    $pages = get_pages();
                                    foreach ($pages as $page) {
                                        $option = '<option value="' . get_page_link($page->ID) . '">';
                                        $option .= $page->post_title;
                                        $option .= '</option>';
                                        echo $option;
                                    }
                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <form method="post" id="form-navigate-client-view" action="<?php echo $nav_url ?>">
                        <input hidden name="admin" value="true">
                        <input hidden name="client_id" value="">
                        <input hidden name="edit_mode" value="false">
                        <div class="modal-footer form-group">
                            <button type="submit" class="btn btn-primary" disabled
                                    id="navigate_client_view"><span
                                        class="glyphicon glyphicon-new-window"></span> <?php _e('Go to Client View', 'wfd_truck'); ?>
                            </button>
                        </div>
                    </form>

                </div>
            </div>
            <!--                </form>-->

        </div>
    </div>

    <?php
}

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

    if (count($res_client_list) == 0 || $res_client_info[0]->type == 0) {
        ?>
        <div role="tabpanel" class="tab-pane" id="client_list">
            <div class="col-sm-12">
                <h2><?php _e('Clients List', 'wfd_truck'); ?></h2>
                <div class="row" style="margin-left: 0px;">
                    <div class="col-sm-3" style="padding-left: 0px;"><select
                                class="selectpicker form-control" id="filter-company">
                            <option value="ALL" selected
                                    hidden><?php _e('filter company', 'wfd_truck') ?></option>
                            <?php
                            foreach ($res_company_list as $company) {
                                if ($company->company != null && $company->company != "") {
                                    echo "<option>" . $company->company . "</option>";
                                }
                            } ?>
                        </select></div>

                    <div class="col-sm-2"><select class="selectpicker form-control"
                                                  id="filter-zip">
                            <option value="ALL" selected
                                    hidden><?php _e('filter ZIP', 'wfd_truck') ?></option>
                            <?php
                            foreach ($res_zip_list as $zip) {
                                if ($zip->zip != null && $zip->zip != "") {
                                    echo "<option>$zip->zip</option>";
                                }
                            } ?>
                        </select></div>
                    <div class="col-sm-3"><select class="selectpicker form-control"
                                                  id="filter-city">
                            <option value="ALL" selected
                                    hidden><?php _e('filter city', 'wfd_truck') ?></option>
                            <?php
                            foreach ($res_city_list as $city) {
                                if ($city->city != null && $city->city != "") {
                                    echo "<option>$city->city</option>";
                                }
                            } ?>
                        </select></div>
                </div>
                <table class="table table-striped" data-toggle="table" id="clients-list">
                    <colgroup>
                        <col class="col-md-2">
                        <col class="col-md-2">
                        <col class="col-md-2">
                        <col class="col-md-2">
                        <col class="col-md-2">
                        <col class="col-md-2">
                        <col class="col-md-2">
                    </colgroup>
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
                        <tr data-user-id="<?php echo $client->id ?>">
                            <td><?php echo $client->company ?></td>
                            <td><?php echo $client->street ?></td>
                            <td><?php echo $client->zip ?></td>
                            <td><?php echo $client->city ?></td>
                            <td><?php echo $client->phone ?></td>
                            <td><?php echo $client->note ?></td>
                            <td>
                                <div class="btn-group-client">
                                    <button type="button" class="btn btn-primary btn-client-view btn-sm"
                                            data-client-id="<?php echo $client->id ?>"><span
                                                class="glyphicon glyphicon-th-list"></button>
                                    <button type="button" class="btn btn-primary btn-client-edit btn-sm"
                                            data-client-id="<?php echo $client->id ?>"><span
                                                class="glyphicon glyphicon-pencil"></button>
                                    <button type="button" class="btn btn-primary btn-client-delete btn-sm"
                                            data-client-id="<?php echo $client->id ?>"><span
                                                class="glyphicon glyphicon-remove"></button>
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
                                            <label><?php _e('Company', 'wfd_truck') ?></label>
                                        </div>
                                        <div class="col-sm-4"><input class="form-control"
                                                                     id="new_company">
                                        </div>
                                    </div>
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

                            <div class="modal-footer form-group">
                                <button type="button" class="btn btn-primary"
                                        id="add_clinet_core"><span
                                            class="glyphicon glyphicon-save-file"></span> <?php _e('Save', 'wfd_truck'); ?>
                                </button>
                            </div>
                        </div>
                </div>
                <!--                </form>-->

            </div>
        </div>
    <?php }

}

function wfd_core_data_view($res_client_info)
{
    global $wpdb;
    $id = $_SESSION['client_id'];
    $tbl_client_info = $wpdb->prefix . "wfd_truck_client_info";
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

    ?>
    <div role="tabpanel" class="tab-pane active" id="core">

        <div class="col-sm-3" id="core-data-container">
            <h2><?php _e('Core Info', 'wfd_truck'); ?></h2>
            <?php

            foreach ($res_client_info as $r1) { ?>
                <div class="row form-group">
                    <label class="control-label col-sm-6"
                           style="padding-left: 40px;line-height: 30px;"><?php _e('Company', 'wfd_truck') ?></label>
                    <div class="col-sm-6">
                        <input type="text" class="form-control" disabled name="company"
                               value="<?php echo $r1->company ?>">
                    </div>
                </div>
                <div class="row form-group">
                    <label class="control-label col-sm-6"
                           style="padding-left: 40px;line-height: 30px;"><?php _e('Street', 'wfd_truck') ?></label>
                    <div class="col-sm-6">
                        <input type="text" class="form-control" disabled name="street"
                               value="<?php echo $r1->street ?>">
                    </div>
                </div>
                <div class="row form-group">
                    <label class="control-label col-sm-6"
                           style="padding-left: 40px;line-height: 30px;"><?php _e('City', 'wfd_truck') ?></label>
                    <div class="col-sm-6">
                        <input type="text" class="form-control" disabled name="city"
                               value="<?php echo $r1->city ?>">
                    </div>
                </div>
                <div class="row form-group">
                    <label class="control-label col-sm-6"
                           style="padding-left: 40px;line-height: 30px;"><?php _e('Zip', 'wfd_truck') ?></label>
                    <div class="col-sm-6">
                        <input type="text" class="form-control" disabled name="zip"
                               value="<?php echo $r1->zip ?>">
                    </div>
                </div>
                <div class="row form-group">
                    <label class="control-label col-sm-6"
                           style="padding-left: 40px;line-height: 30px;"><?php _e('Phone', 'wfd_truck') ?>.</label>
                    <div class="col-sm-6">
                        <input type="text" class="form-control" disabled name="phone" value="<?php echo $r1->phone ?>">
                    </div>
                </div>
                <div class="row form-group">
                    <label class="control-label col-sm-6"
                           style="padding-left: 40px;line-height: 30px;"><?php _e('Fax', 'wfd_truck') ?>.</label>
                    <div class="col-sm-6">
                        <input type="text" class="form-control" disabled name="fax" value="<?php echo $r1->fax ?>">
                    </div>
                </div>
                <div class="row form-group">
                    <label class="control-label col-sm-6"
                           style="padding-left: 40px;line-height: 30px;"><?php _e('Website', 'wfd_truck') ?>.</label>
                    <div class="col-sm-6">
                        <input type="text" class="form-control" disabled name="website"
                               value="<?php echo $r1->website ?>">
                    </div>
                </div>
                <div class="row form-group">
                    <label class="control-label col-sm-6"
                           style="padding-left: 10px; padding-right:0px;line-height: 30px;"><?php _e('Emergency Phone', 'wfd_truck') ?>
                        .</label>
                    <div class="col-sm-6">
                        <input type="text" class="form-control" disabled name="emergencyPhone"
                               value="<?php echo $r1->emergency_phone ?>">
                    </div>
                </div>
                <div class="row form-group">
                    <label class="control-label col-sm-6"
                           style="padding-left: 40px; padding-right:0px;line-height: 30px;"><?php _e('Note', 'wfd_truck') ?>
                        .</label>
                    <div class="col-sm-6">
                        <input type="text" class="form-control" disabled name="note"
                               value="<?php echo $r1->note ?>">
                    </div>
                </div>
                <div class="row form-group">
                    <input id="client-id" class="col-sm-6" hidden name="clientId" value="<?php echo $id ?>">
                    <div class="col-sm-6">
                        <button class="btn btn-primary" type="button" data-toggle="button"
                                id="edit-core-data-toggle"><span
                                    class="glyphicon glyphicon-pencil"></span> <?php _e('Edit', 'wfd_truck'); ?>
                        </button>
                    </div>
                </div>
                <?php

            }
            ?>
        </div>
        <div class="col-sm-9">
            <div class="row">
                <div class="col-sm-12">
                    <h2><?php _e('Opening Hours', 'wfd_truck'); ?></h2>
                    <table class="table table-striped" data-toggle="table" id="opening-hours">
                        <colgroup>
                            <col class="col-md-2">
                            <col class="col-md-3">
                            <col class="col-md-3">
                            <col class="col-md-3">
                        </colgroup>
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
                            <td class="col-sm-2"><?php _e('Office', 'wfd_truck') ?></td>
                            <td>
                                <div>
                                    <div class="col-sm-5 time-input"><input type="time" name="ohOfficeMonStart"
                                                                            value="<?php echo $res_operating_hours_off[0]->rdays_start ?>">
                                    </div>
                                    <div class="col-sm-1">-</div>
                                    <div class="col-sm-5 time-input"><input type="time" name="ohOfficeMonEnd"
                                                                            value="<?php echo $res_operating_hours_off[0]->rdays_end ?>">
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div>
                                    <div class="col-sm-5 time-input"><input type="time" name="ohOfficeSatStart"
                                                                            value="<?php echo $res_operating_hours_off[0]->weday_start ?>">
                                    </div>
                                    <div class="col-sm-1">-</div>
                                    <div class="col-sm-5 time-input"><input type="time" name="ohOfficeSatEnd"
                                                                            value="<?php echo $res_operating_hours_off[0]->weday_end ?>">
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div>
                                    <div class="col-sm-5 time-input"><input type="time" name="ohOfficeSunStart"
                                                                            value="<?php echo $res_operating_hours_off[0]->wday_start ?>">
                                    </div>
                                    <div class="col-sm-1">-</div>
                                    <div class="col-sm-5 time-input"><input type="time" name="ohOfficeSunEnd"
                                                                            value="<?php echo $res_operating_hours_off[0]->wday_end ?>">
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="col-sm-2"><?php _e('Garage', 'wfd_truck') ?></td>
                            <td>
                                <div>
                                    <div class="col-sm-5 time-input"><input type="time" name="ohGarageMonStart"
                                                                            value="<?php echo $res_operating_hours_gar[0]->rdays_start ?>">
                                    </div>
                                    <div class="col-sm-1">-</div>
                                    <div class="col-sm-5 time-input"><input type="time" name="ohGarageMonEnd"
                                                                            value="<?php echo $res_operating_hours_gar[0]->rdays_end ?>">
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div>
                                    <div class="col-sm-5 time-input"><input type="time" name="ohGarageSatStart"
                                                                            value="<?php echo $res_operating_hours_gar[0]->weday_start ?>">
                                    </div>
                                    <div class="col-sm-1">-</div>
                                    <div class="col-sm-5 time-input"><input type="time" name="ohGarageSatEnd"
                                                                            value="<?php echo $res_operating_hours_gar[0]->weday_end ?>">
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div>
                                    <div class="col-sm-5 time-input"><input type="time" name="ohGarageSunStart"
                                                                            value="<?php echo $res_operating_hours_gar[0]->wday_start ?>">
                                    </div>
                                    <div class="col-sm-1">-</div>
                                    <div class="col-sm-5 time-input"><input type="time" name="ohGarageSunEnd"
                                                                            value="<?php echo $res_operating_hours_gar[0]->wday_end ?>">
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td><?php _e('Car rental', 'wfd_truck') ?></td>
                            <td>
                                <div>
                                    <div class="col-sm-5 time-input"><input type="time" name="ohCarMonStart"
                                                                            value="<?php echo $res_operating_hours_carrent[0]->rdays_start ?>">
                                    </div>
                                    <div class="col-sm-1">-</div>
                                    <div class="col-sm-5 time-input"><input type="time" name="ohCarMonEnd"
                                                                            value="<?php echo $res_operating_hours_carrent[0]->rdays_end ?>">
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div>
                                    <div class="col-sm-5 time-input"><input type="time" name="ohCarSatStart"
                                                                            value="<?php echo $res_operating_hours_carrent[0]->weday_start ?>">
                                    </div>
                                    <div class="col-sm-1">-</div>
                                    <div class="col-sm-5 time-input"><input type="time" name="ohCarSatEnd"
                                                                            value="<?php echo $res_operating_hours_carrent[0]->weday_end ?>">
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div>
                                    <div class="col-sm-5 time-input"><input type="time" name="ohCarSunStart"
                                                                            value="<?php echo $res_operating_hours_carrent[0]->wday_start ?>">
                                    </div>
                                    <div class="col-sm-1">-</div>
                                    <div class="col-sm-5 time-input"><input type="time" name="ohCarSunEnd"
                                                                            value="<?php echo $res_operating_hours_carrent[0]->wday_end ?>">
                                    </div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td><?php _e('on-call duty', 'wfd_truck') ?></td>
                            <td>
                                <div>
                                    <div class="col-sm-5 time-input"><input type="time" name="ohDutyMonStart"
                                                                            value="<?php echo $res_operating_hours_oncall[0]->rdays_start ?>">
                                    </div>
                                    <div class="col-sm-1">-</div>
                                    <div class="col-sm-5 time-input"><input type="time" name="ohDutyMonEnd"
                                                                            value="<?php echo $res_operating_hours_oncall[0]->rdays_end ?>">
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div>
                                    <div class="col-sm-5 time-input"><input type="time" name="ohDutySatStart"
                                                                            value="<?php echo $res_operating_hours_oncall[0]->weday_start ?>">
                                    </div>
                                    <div class="col-sm-1">-</div>
                                    <div class="col-sm-5 time-input"><input type="time" name="ohDutySatEnd"
                                                                            value="<?php echo $res_operating_hours_oncall[0]->weday_end ?>">
                                    </div>
                                </div>
                            </td>
                            <td>
                                <div>
                                    <div class="col-sm-5 time-input"><input type="time" name="ohDutySunStart"
                                                                            value="<?php echo $res_operating_hours_oncall[0]->wday_start ?>">
                                    </div>
                                    <div class="col-sm-1">-</div>
                                    <div class="col-sm-5 time-input"><input type="time" name="ohDutySunEnd"
                                                                            value="<?php echo $res_operating_hours_oncall[0]->wday_end ?>">
                                    </div>
                                </div>
                            </td>
                        </tr>

                        </tbody>
                    </table>
                </div>

            </div>
            <div class="row">
                <div class="col-sm-3" id="payment-container">
                    <h3><?php _e('Payment', 'wfd_truck'); ?></h3>
                    <ul class="nav" style="padding-left: 0; margin-left: 0">
                        <?php

                        foreach ($res_pay_m as $pm) { ?>
                            <div class="checkbox">
                                <label>
                                    <?php
                                    $inputElem = '<input name="' . $pm->method . '" type="checkbox">';
                                    foreach ($res_pay_m_u as $pmu) {
                                        if ($pm->id == $pmu->payid) {
                                            $inputElem = '<input name="' . $pm->method . '" type = "checkbox" checked >';
                                        }
                                    }
                                    echo $inputElem;
                                    ?>
                                    <span class="cr"><i class="cr-icon glyphicon glyphicon-ok"></i></span>
                                    <?php echo $pm->method ?>
                                </label>
                            </div>
                        <?php } ?>
                    </ul>
                </div>
                <div class="col-sm-3" id="partner-container">
                    <h3><?php _e('Partner', 'wfd_truck'); ?></h3>
                    <ul class="nav" style="padding-left: 0; margin-left: 0">
                        <?php

                        foreach ($res_partner as $p) { ?>
                            <div class="checkbox">
                                <label>
                                    <?php
                                    $inputElem = '<input name="' . $p->partner . '" type="checkbox">';
                                    foreach ($res_partner_u as $pmu) {
                                        if ($p->id == $pmu->pid) {
                                            $inputElem = '<input name="' . $p->partner . '" type = "checkbox" checked >';
                                        }
                                    }
                                    echo $inputElem;
                                    ?>
                                    <span class="cr"><i class="cr-icon glyphicon glyphicon-ok"></i></span>
                                    <?php echo $p->partner ?>
                                </label>
                            </div>
                        <?php } ?>
                    </ul>
                </div>
                <div class="col-sm-3">
                    <h3><?php _e('Assistance', 'wfd_truck'); ?></h3>
                    <div class="nav" id="assistance-container" style="padding-left: 0; margin-left: 0">
                        <?php

                        foreach ($res_assistance as $pa) { ?>
                            <div class="checkbox">
                                <label>
                                    <?php
                                    $inputElem = '<input name="' . $pa->assistance . '" type="checkbox">';
                                    foreach ($res_assistance_u as $pmu) {
                                        if ($pa->id == $pmu->aid) {
                                            $inputElem = '<input name="' . $pa->assistance . '" type = "checkbox" checked >';
                                        }
                                    }
                                    echo $inputElem;
                                    ?>
                                    <span class="cr"><i class="cr-icon glyphicon glyphicon-ok"></i></span>
                                    <?php echo $pa->assistance ?>
                                </label>
                            </div>
                        <?php } ?>
                    </div>
                    <button class="btn btn-primary" type="button" id="add-assistance"><span
                                class="glyphicon glyphicon-plus"></span> <?php _e('add', 'wfd_truck'); ?></button>
                </div>
                <div class="col-sm-3">
                    <h3><?php _e('Mobi Services', 'wfd_truck'); ?></h3>
                    <div class="container-fluid" id="mobi-service-container" style="padding-left: 0; margin-left: 0">
                        <?php
                        $count = 3;
                        foreach ($res_mobi_service as $ms) {
                            $labelNum = 4 - $count;
                            $count-- ?>
                            <div class="row form-group">
                                <label class="control-label col-sm-4"
                                       style="padding-left: 40px;line-height: 30px;"><?php echo $labelNum ?>.</label>
                                <div class="col-sm-8">
                                    <input type="text" class="form-control" name="fname"
                                           value="<?php echo $ms->mobi_service ?>"
                                           placeholder="<?php echo __('Car dealer', 'wfd_truck') . " $labelNum" ?>">
                                </div>
                            </div>
                        <?php }
                        if ($count > 0) {
                            for ($i = 0; $i < $count; $i++) {
                                $labelNum = 3 - $count + 1 + $i;
                                ?>
                                <div class="row form-group">
                                    <label class="control-label col-sm-4"
                                           style="padding-left: 40px;line-height: 30px;"><?php echo $labelNum ?>
                                        .</label>
                                    <div class="col-sm-8">
                                        <input type="text" class="form-control" name="fname"
                                               placeholder="<?php echo __('Car dealer', 'wfd_truck') . " $labelNum" ?>">
                                    </div>
                                </div>
                                <?php
                            }
                        }
                        ?>
                    </div>
                    <button class="btn btn-primary" type="button" id="add-mobi-service"><span
                                class="glyphicon glyphicon-plus"></span> <?php _e('add', 'wfd_truck'); ?></button>

                </div>
            </div>
        </div>
    </div>
    <?php
}

function wfd_driver_view()
{
    global $wpdb;
    $id = $_SESSION['client_id'];
    $tbl_driver_info = $wpdb->prefix . "wfd_truck_driver_info";
    $res_driver = $wpdb->get_results("select * from $tbl_driver_info where cid=$id and type='Driver'", OBJECT);
    ?>
    <div role="tabpanel" class="tab-pane" id="driver">
        <h2><?php _e('Driver', 'wfd_truck'); ?></h2>
        <table class="table table-striped dataTable" data-toggle="table" id="drivers-table" data-unique-id="id">
            <colgroup>
                <col class="col-md-2">
                <col class="col-md-2">
                <col class="col-md-2">
                <col class="col-md-2">
                <col class="col-md-2">
                <col class="col-md-2">
                <col class="col-md-3">
            </colgroup>
            <thead>
            <tr>
                <th data-field="id" data-visible="false"></th>
                <th data-field="fname" data-sortable="true"><?php _e('First Name', 'wfd_truck'); ?></th>
                <th data-field="lname" data-sortable="true"><?php _e('Last Name', 'wfd_truck'); ?></th>
                <th data-field="street"><?php _e('Street', 'wfd_truck'); ?></th>
                <th data-field="city" data-sortable="true"><?php _e('City', 'wfd_truck'); ?></th>
                <th data-field="phone"><?php _e('Phone', 'wfd_truck'); ?></th>
                <th data-field="note"><?php _e('Note', 'wfd_truck'); ?></th>
                <th data-field="action" data-formatter="allActionFormatter" data-events="driverActionEvents" ><?php _e('Action', 'wfd_truck'); ?></th>
            </tr>
            </thead>
            <tbody>
            <?php
            foreach ($res_driver as $rd) {
                ?>
                <tr>
                    <td><?php echo $rd->id ?></td>
                    <td><?php echo $rd->fname ?></td>
                    <td><?php echo $rd->lname ?></td>
                    <td><?php echo $rd->street ?></td>
                    <td><?php echo $rd->city ?></td>
                    <td><?php echo $rd->phone ?></td>
                    <td><?php echo $rd->note ?></td>
                    <td></td>
                </tr>
            <?php } ?>
            </tbody>

        </table>


        <button class="btn btn-primary" type="button" id="btn-add-driver">
            <span class="glyphicon glyphicon-user"></span><span
                    class="glyphicon glyphicon-plus"></span> <?php _e('Add Driver', 'wfd_truck'); ?>
        </button>

        <!-- Modal Driver-->
        <div class="modal fade" id="modal-driver" data-mode="new" tabindex="-1" role="dialog"
             aria-labelledby="myModalLabel">
            <div style="width: 50%" class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal"
                                aria-label="Close"><span
                                    aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel"><?php _e('Driver:', 'wfd_truck'); ?></h4>
                    </div>
                    <div class="modal-body well">
                        <div class="row">
                            <div class="col-sm-5">
                                <h2><?php _e('Core Data', 'wfd_truck'); ?></h2>
                                <form class="form-horizontal" id="driver-core-data">
                                    <div class="form-group">
                                        <label class="control-label col-sm-5"><?php _e('First Name', 'wfd_truck'); ?>:</label>
                                        <div class="col-sm-7">
                                            <input type="text" class="form-control" name="fname">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-sm-5"><?php _e('Last Name', 'wfd_truck'); ?>
                                            :</label>
                                        <div class="col-sm-7">
                                            <input type="text" class="form-control" name="lname">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-sm-5"><?php _e('Street', 'wfd_truck'); ?>:</label>
                                        <div class="col-sm-7">
                                            <input type="text" class="form-control" name="street">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-sm-5"><?php _e('City', 'wfd_truck'); ?>:</label>
                                        <div class="col-sm-7">
                                            <input type="text" class="form-control" name="city">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-sm-5"><?php _e('Phone', 'wfd_truck'); ?>:</label>
                                        <div class="col-sm-7">
                                            <input type="text" class="form-control" name="phone">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-sm-5"><?php _e('Note', 'wfd_truck'); ?>:</label>
                                        <div class="col-sm-7">
                                            <input type="text"
                                                   class="form-control"
                                                   name="note">
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="col-sm-5">
                                <h2><?php _e('Applications', 'wfd_truck'); ?></h2>
                                <form class="form-horizontal" id="driver-application-form">
                                    <div class="form-group">
                                        <label class="col-sm-5"><?php _e('breakdown service', 'wfd_truck'); ?></label>
                                        <div class="col-sm-7"><input
                                                    value="2" name="breakdown"
                                                    type="text"
                                                    class="rating col-sm-6"
                                                    data-min=0 data-max=5
                                                    data-step=0.5 data-size="xs"
                                                    data-show-caption=false
                                                    title=""></div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-5"><?php _e('drag cars', 'wfd_truck'); ?></label>
                                        <div class="col-sm-7">
                                            <input value="2"
                                                   type="text" name="drag-cars"
                                                   class="rating col-sm-6"
                                                   data-min=0 data-max=5
                                                   data-step=0.5 data-size="xs"
                                                   data-show-caption=false
                                                   title="">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-5"><?php _e('drag < 7.5to', 'wfd_truck'); ?></label>
                                        <div class="col-sm-7"><input
                                                    name="drag-less-7" value="2"
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
                                                    name="drag-more-7" value="2"
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
                                                    name="crane" value="2"
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
                                                    name="truck-service" value="2"
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
                                <img src="<?php echo plugins_url('/images/truck_profile_back.jpg', __FILE__)?>" class="img-thumbnail profile-pic"
                                     alt="Cinque Terre" width="200" height="150">
                                <input class="file-upload" type="file" name="image" accept="image/*"/>
                                <p class="col-sm-12"><?php _e('driver photo', 'wfd_truck'); ?></p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-5">
                                <h2><?php _e('Driving Licenses', 'wfd_truck'); ?></h2>
                                <form id="driver-license-form">
                                    <div class="col-sm-4">
                                        <div class="checkbox">
                                            <label>
                                                <input type="checkbox" value="" name="c1" checked>
                                                <span class="cr"><i class="cr-icon glyphicon glyphicon-ok"></i></span>
                                                <?php _e('C1', 'wfd_truck'); ?>
                                            </label>
                                        </div>
                                        <div class="checkbox">
                                            <label>
                                                <input type="checkbox" value="" checked name="c1e">
                                                <span class="cr"><i class="cr-icon glyphicon glyphicon-ok"></i></span>
                                                <?php _e('C1E', 'wfd_truck'); ?>
                                            </label>
                                        </div>
                                        <div class="checkbox disabled">
                                            <label>
                                                <input type="checkbox" value="" checked name="crane-lic">
                                                <span class="cr"><i class="cr-icon glyphicon glyphicon-ok"></i></span>
                                                <?php _e('crane', 'wfd_truck'); ?>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-sm-8">
                                        <div class="checkbox">
                                            <label>
                                                <input type="checkbox" value="" checked name="kennz95">
                                                <span class="cr"><i class="cr-icon glyphicon glyphicon-ok"></i></span>
                                                <?php _e('Kennz 95', 'wfd_truck'); ?>
                                            </label>
                                        </div>
                                        <div class="checkbox">
                                            <label>
                                                <input type="checkbox" value="" checked name="club-mobil">
                                                <span class="cr"><i class="cr-icon glyphicon glyphicon-ok"></i></span>
                                                <?php _e('Clubmobil', 'wfd_truck'); ?>
                                            </label>
                                        </div>
                                        <div class="checkbox disabled">
                                            <label>
                                                <input type="checkbox" value="" checked name="car-opening">
                                                <span class="cr"><i class="cr-icon glyphicon glyphicon-ok"></i></span>
                                                <?php _e('car opening', 'wfd_truck'); ?>
                                            </label>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="col-sm-7">
                                <h2><?php _e('Qualification', 'wfd_truck'); ?></h2>
                                <form id="driver-qualification-form">
                                    <div class="col-sm-6">
                                        <div class="checkbox">
                                            <label>
                                                <input type="checkbox" value="" checked name="motor-mechatronics">
                                                <span class="cr"><i class="cr-icon glyphicon glyphicon-ok"></i></span>
                                                <?php _e('motor mechatronics', 'wfd_truck'); ?>
                                            </label>
                                        </div>
                                        <div class="checkbox">
                                            <label>
                                                <input type="checkbox" value="" checked name="motor-foreman">
                                                <span class="cr"><i class="cr-icon glyphicon glyphicon-ok"></i></span>
                                                <?php _e('motor foreman', 'wfd_truck'); ?>
                                            </label>
                                        </div>
                                        <div class="checkbox disabled">
                                            <label>
                                                <input type="checkbox" value="" checked name="learned">
                                                <span class="cr"><i class="cr-icon glyphicon glyphicon-ok"></i></span>
                                                <?php _e('learned', 'wfd_truck'); ?>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="checkbox disabled">
                                            <label>
                                                <input type="checkbox" value="" checked name="unlearned">
                                                <span class="cr"><i class="cr-icon glyphicon glyphicon-ok"></i></span>
                                                <?php _e('unlearned', 'wfd_truck'); ?>
                                            </label>
                                        </div>
                                        <div class="checkbox disabled">
                                            <label>
                                                <input type="checkbox" value="" checked name="commercial">
                                                <span class="cr"><i class="cr-icon glyphicon glyphicon-ok"></i></span>
                                                <?php _e('commercial vehicle technology', 'wfd_truck'); ?>
                                            </label>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" id="driver-save">
                            <span class="glyphicon glyphicon-floppy-disk"></span> <?php _e('Save', 'wfd_truck'); ?>
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <?php
}

function wfd_pickup_driver_view()
{

    global $wpdb;
    $id = $_SESSION['client_id'];
    $tbl_driver_info = $wpdb->prefix . "wfd_truck_pickup_driver_info";
    $res_driver_truck = $wpdb->get_results("select * from $tbl_driver_info where cid=$id ", OBJECT);

    ?>
    <div role="tabpanel" class="tab-pane" id="pdriver">
        <h2><?php _e('Pickup  Driver', 'wfd_truck'); ?></h2>
        <table class="table table-striped dataTable" data-toggle="table" id="pickup-drivers-table" data-unique-id="id">
            <colgroup>
                <col class="col-md-2">
                <col class="col-md-2">
                <col class="col-md-2">
                <col class="col-md-2">
                <col class="col-md-2">
                <col class="col-md-2">
                <col class="col-md-3">
            </colgroup>
            <thead>
            <tr>
                <th data-field="id" data-visible="false"></th>
                <th data-field="fname" data-sortable="true"><?php _e('First Name', 'wfd_truck'); ?></th>
                <th data-field="lname" data-sortable="true"><?php _e('Last Name', 'wfd_truck'); ?></th>
                <th data-field="street"><?php _e('Street', 'wfd_truck'); ?></th>
                <th data-field="city" data-sortable="true"><?php _e('City', 'wfd_truck'); ?></th>
                <th data-field="phone"><?php _e('Phone', 'wfd_truck'); ?></th>
                <th data-field="note"><?php _e('Note', 'wfd_truck'); ?></th>
                <th data-field="action" data-formatter="allActionFormatter" data-events="pickupDriverActionEvents"><?php _e('Action', 'wfd_truck'); ?></th>
            </tr>
            </thead>
            <tbody>
            <?php
            foreach ($res_driver_truck as $rdt) {
                ?>
                <tr>
                    <td><?php echo $rdt->id ?></td>
                    <td><?php echo $rdt->fname ?></td>
                    <td><?php echo $rdt->lname ?></td>
                    <td><?php echo $rdt->street ?></td>
                    <td><?php echo $rdt->city ?></td>
                    <td><?php echo $rdt->phone ?></td>
                    <td><?php echo $rdt->note ?></td>
                    <td></td>
                </tr>
            <?php } ?>
            </tbody>

        </table>


        <button class="btn btn-primary" type="button" id="btn-add-pickup-driver" >
            <span class="glyphicon glyphicon-user"></span><span
                    class="glyphicon glyphicon-plus"></span>  <?php _e('Add Pickup Driver', 'wfd_truck'); ?>
        </button>

        <!-- Modal Driver-->
        <div class="modal fade" id="modal-pickup-driver" data-mode="new" tabindex="-1" role="dialog"
             aria-labelledby="myModalLabel">
            <div style="width: 50%" class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal"
                                aria-label="Close"><span
                                    aria-hidden="true">&times;</span></button>
                        <h4 class="modal-title" id="myModalLabel"><?php _e('Truck Driver:', 'wfd_truck'); ?></h4>
                    </div>
                    <div class="modal-body well">
                        <div class="row">
                            <div class="col-sm-5">
                                <h2><?php _e('Core Data', 'wfd_truck'); ?></h2>
                                <form class="form-horizontal" id="pickup-driver-core-data">
                                    <div class="form-group">
                                        <label class="control-label col-sm-5"><?php _e('First Name', 'wfd_truck'); ?>:</label>
                                        <div class="col-sm-7">
                                            <input type="text" class="form-control" name="fname">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-sm-5"><?php _e('Last Name', 'wfd_truck'); ?>
                                            :</label>
                                        <div class="col-sm-7">
                                            <input type="text" class="form-control" name="lname">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-sm-5"><?php _e('Street', 'wfd_truck'); ?>:</label>
                                        <div class="col-sm-7">
                                            <input type="text" class="form-control" name="street">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-sm-5"><?php _e('City', 'wfd_truck'); ?>:</label>
                                        <div class="col-sm-7">
                                            <input type="text" class="form-control" name="city">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-sm-5"><?php _e('Phone', 'wfd_truck'); ?>:</label>
                                        <div class="col-sm-7">
                                            <input type="text" class="form-control" name="phone">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="control-label col-sm-5"><?php _e('Note', 'wfd_truck'); ?>:</label>
                                        <div class="col-sm-7">
                                            <input type="text"
                                                   class="form-control"
                                                   name="note">
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="col-sm-5">
                                <h2><?php _e('Applications', 'wfd_truck'); ?></h2>
                                <form class="form-horizontal" id="pickup-driver-application-form">
                                    <div class="form-group">
                                        <label class="col-sm-5"><?php _e('Pickups < 250km', 'wfd_truck'); ?></label>
                                        <div class="col-sm-7"><input
                                                    value="2" name="pickups_less_250"
                                                    type="text"
                                                    class="rating col-sm-6"
                                                    data-min=0 data-max=5
                                                    data-step=0.5 data-size="xs"
                                                    data-show-caption=false
                                                    title=""></div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-5"><?php _e('Pickups < 500km', 'wfd_truck'); ?></label>
                                        <div class="col-sm-7">
                                            <input value="2"
                                                   type="text" name="pickups_less_500"
                                                   class="rating col-sm-6"
                                                   data-min=0 data-max=5
                                                   data-step=0.5 data-size="xs"
                                                   data-show-caption=false
                                                   title="">
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-5"><?php _e('Pickups > 500km', 'wfd_truck'); ?></label>
                                        <div class="col-sm-7"><input
                                                    name="pickups_more_500" value="2"
                                                    type="text"
                                                    class="rating col-sm-6"
                                                    data-min=0 data-max=5
                                                    data-step=0.5 data-size="xs"
                                                    data-show-caption=false
                                                    title=""></div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-5"><?php _e('cars', 'wfd_truck'); ?></label>
                                        <div class="col-sm-7"><input
                                                    name="cars" value="2"
                                                    type="text"
                                                    class="rating col-sm-6"
                                                    data-min=0 data-max=5
                                                    data-step=0.5 data-size="xs"
                                                    data-show-caption=false
                                                    title=""></div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-5"><?php _e('truck < 3.5 to', 'wfd_truck'); ?></label>
                                        <div class="col-sm-7"><input
                                                    name="truck_less_3" value="2"
                                                    type="text"
                                                    class="rating col-sm-6"
                                                    data-min=0 data-max=5
                                                    data-step=0.5 data-size="xs"
                                                    data-show-caption=false
                                                    title=""></div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-sm-5"><?php _e('truck < 7.5 to', 'wfd_truck'); ?></label>
                                        <div class="col-sm-7"><input
                                                    name="truck_less_7" value="2"
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
                                <img src="<?php echo plugins_url('/images/truck_profile_back.jpg', __FILE__)?>" class="img-thumbnail profile-pic"
                                     alt="Cinque Terre" width="200" height="150">
                                <input class="file-upload" type="file" name="image" accept="image/*"/>
                                <p class="col-sm-12"><?php _e('driver photo', 'wfd_truck'); ?></p>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-sm-5">
                                <h2><?php _e('Driving Licenses', 'wfd_truck'); ?></h2>
                                <form id="pickup-driver-license-form">
                                    <div class="col-sm-4">
                                        <div class="checkbox">
                                            <label>
                                                <input type="checkbox" value="" name="c1" checked>
                                                <span class="cr"><i class="cr-icon glyphicon glyphicon-ok"></i></span>
                                                <?php _e('C1', 'wfd_truck'); ?>
                                            </label>
                                        </div>
                                        <div class="checkbox">
                                            <label>
                                                <input type="checkbox" value="" checked name="c1e">
                                                <span class="cr"><i class="cr-icon glyphicon glyphicon-ok"></i></span>
                                                <?php _e('C1E', 'wfd_truck'); ?>
                                            </label>
                                        </div>
                                        <div class="checkbox disabled">
                                            <label>
                                                <input type="checkbox" value="" checked name="crane_lic">
                                                <span class="cr"><i class="cr-icon glyphicon glyphicon-ok"></i></span>
                                                <?php _e('crane', 'wfd_truck'); ?>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-sm-8">
                                        <div class="checkbox">
                                            <label>
                                                <input type="checkbox" value="" checked name="kennz95">
                                                <span class="cr"><i class="cr-icon glyphicon glyphicon-ok"></i></span>
                                                <?php _e('Kennz 95', 'wfd_truck'); ?>
                                            </label>
                                        </div>
                                    </div>
                                </form>
                            </div>
                            <div class="col-sm-7">
                                <h2><?php _e('Qualification', 'wfd_truck'); ?></h2>
                                <form id="pickup-driver-qualification-form">
                                    <div class="col-sm-6">
                                        <div class="checkbox">
                                            <label>
                                                <input type="checkbox" value="" checked name="motor_mechatronics">
                                                <span class="cr"><i class="cr-icon glyphicon glyphicon-ok"></i></span>
                                                <?php _e('motor mechatronics', 'wfd_truck'); ?>
                                            </label>
                                        </div>
                                        <div class="checkbox">
                                            <label>
                                                <input type="checkbox" value="" checked name="motor_foreman">
                                                <span class="cr"><i class="cr-icon glyphicon glyphicon-ok"></i></span>
                                                <?php _e('motor foreman', 'wfd_truck'); ?>
                                            </label>
                                        </div>
                                        <div class="checkbox disabled">
                                            <label>
                                                <input type="checkbox" value="" checked name="learned">
                                                <span class="cr"><i class="cr-icon glyphicon glyphicon-ok"></i></span>
                                                <?php _e('learned', 'wfd_truck'); ?>
                                            </label>
                                        </div>
                                    </div>
                                    <div class="col-sm-6">
                                        <div class="checkbox disabled">
                                            <label>
                                                <input type="checkbox" value="" checked name="unlearned">
                                                <span class="cr"><i class="cr-icon glyphicon glyphicon-ok"></i></span>
                                                <?php _e('unlearned', 'wfd_truck'); ?>
                                            </label>
                                        </div>
                                        <div class="checkbox disabled">
                                            <label>
                                                <input type="checkbox" value="" checked name="commercial">
                                                <span class="cr"><i class="cr-icon glyphicon glyphicon-ok"></i></span>
                                                <?php _e('commercial vehicle technology', 'wfd_truck'); ?>
                                            </label>
                                        </div>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-primary" id="pickup-driver-save">
                            <span class="glyphicon glyphicon-floppy-disk"></span> <?php _e('Save', 'wfd_truck'); ?>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php
}

function wfd_truck_pool_view()
{
    global $wpdb;
    $id = $_SESSION['client_id'];
    $tbl_truck_info = $wpdb->prefix . "wfd_truck_truck_truck_info";
    $res_truck_info = $wpdb->get_results("select * from $tbl_truck_info where cid=$id", OBJECT);


    ?>
    <div role="tabpanel" class="tab-pane" id="tpool">
        <h2><?php _e('Truck Pool', 'wfd_truck'); ?></h2>
        <table class="table table-striped" data-toggle="table" id="truck-list" data-unique-id="id">
            <thead>
            <tr>
                <th data-field="id" data-visible="false"></th>
                <th data-field="truckId" data-sortable="true"><?php _e('ID', 'wfd_truck'); ?></th>
                <th data-field="brand"
                    data-sortable="true"><?php _e('Brand', 'wfd_truck'); ?></th>
                <th data-field="weight"><?php _e('Weight', 'wfd_truck'); ?></th>
                <th data-field="maxload"
                    data-sortable="true"><?php _e('Max Load', 'wfd_truck'); ?></th>
                <th data-field="lheight"><?php _e('Load Height', 'wfd_truck'); ?></th>
                <th data-field="type"><?php _e('Type', 'wfd_truck'); ?></th>
                <th class="col-xs-1" data-field="status"><?php _e('Status', 'wfd_truck'); ?></th>
                <th class="col-xs-2" data-field="action" data-formatter="allActionFormatter" data-events="truckPoolActionEvents"><?php _e('Action', 'wfd_truck'); ?></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($res_truck_info as $ti) { ?>
                <tr>
                    <td><?php echo $ti->id ?></td>
                    <td><?php echo $ti->truck_ID ?></td>
                    <td><?php echo $ti->brand ?></td>
                    <td><?php echo $ti->weight ?></td>
                    <td><?php echo $ti->max_load ?></td>
                    <td><?php echo $ti->load_height ?></td>
                    <td><?php echo $ti->type ?></td>
                    <td><?php echo $ti->status ?></td>
                    <td></td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
        <button type="button" class="btn btn-primary" id="btn-add-truck">
            <span class="glyphicon glyphicon-plus"></span>    <?php _e('add Truck', 'wfd_truck'); ?>
        </button>

                        <!-- Modal TPView-->
                        <div class="modal fade" id="modal_add_truck" data-client-id="<?php echo $id ?>"
                             tabindex="-1"
                             role="dialog" aria-labelledby="myModalLabel">
                            <div style="width: 60%" class="modal-dialog" role="document">
                              <!--<form method="post">-->
                                <div class="modal-content">
                                    <div style="background-color: #5cb85c; color: white !important;"
                                         class="modal-header">
                                        <button type="button" class="close" data-dismiss="modal"
                                                aria-label="Close"><span
                                                    aria-hidden="true">&times;</span></button>
                                        <h4 class="modal-title" id="truckModalLabel"></h4>
                                    </div>
                                    <div class="modal-body">
                                        <div class="row">
                                            <div class="col-sm-8">
                                                <h2><?php _e('Truck Data', 'wfd_truck'); ?></h2>
                                                <form class="form-horizontal">
                                                    <div class="col-sm-6">
                                                        <div class="form-group">
                                                            <label class="control-label col-sm-5"><?php _e('ID', 'wfd_truck'); ?>
                                                                </label>
                                                            <div class="col-sm-7">
                                                                <input type="text" class="form-control" id="new_truck_id">
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <label class="control-label col-sm-5"><?php _e('Brand', 'wfd_truck'); ?>
                                                                </label>
                                                            <div class="col-sm-7">
                                                                <input type="text" class="form-control" id="new_brand">
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <label class="control-label col-sm-5"><?php _e('Weight', 'wfd_truck'); ?>
                                                                </label>
                                                            <div class="col-sm-7">
                                                                <input type="text" class="form-control" id="new_weight">
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <label class="control-label col-sm-5"><?php _e('Max load', 'wfd_truck'); ?>
                                                                </label>
                                                            <div class="col-sm-7">
                                                                <input type="text" class="form-control" id="new_max_load">
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <label class="control-label col-sm-5"><?php _e('Load height', 'wfd_truck'); ?>
                                                                </label>
                                                            <div class="col-sm-7">
                                                                <input type="text" class="form-control" id="new_load_height">
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <label class="control-label col-sm-5"><?php _e('Type', 'wfd_truck'); ?>
                                                                </label>
                                                            <div class="col-sm-7">
                                                                <input type="text" class="form-control" id="new_truck_type">
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <label class="control-label col-sm-5"><?php _e('Status', 'wfd_truck'); ?>
                                                                </label>
                                                            <div class="col-sm-7">
                                                                <input type="text" class="form-control" id="new_status">
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <label class="control-label col-sm-5"><?php _e('Plateau height', 'wfd_truck'); ?>
                                                                </label>
                                                            <div class="col-sm-7">
                                                                <input type="text" class="form-control" id="new_pheight">
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="col-sm-6">
                                                        <div class="form-group">
                                                            <label class="control-label col-sm-5"><?php _e('Spectacle force', 'wfd_truck'); ?>
                                                                </label>
                                                            <div class="col-sm-7">
                                                                <input type="text" class="form-control" id="new_spec_force">
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <label class="control-label col-sm-5"><?php _e('Cable winch force', 'wfd_truck'); ?>
                                                                </label>
                                                            <div class="col-sm-7">
                                                                <input type="text" class="form-control" id="new_cable_force">
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <label class="control-label col-sm-5"><?php _e('Crane', 'wfd_truck'); ?>
                                                                </label>
                                                            <div class="col-sm-7">
                                                                <input type="text" class="form-control" id="new_crane">
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <label class="control-label col-sm-5"><?php _e('Plateau length', 'wfd_truck'); ?>
                                                                </label>
                                                            <div class="col-sm-7">
                                                                <input type="text" class="form-control" id="new_plength">
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <label class="control-label col-sm-5"><?php _e('Motorcycle', 'wfd_truck'); ?>
                                                                </label>
                                                            <div class="col-sm-7">
                                                                <label class="switch">
                                                                    <input type="checkbox" checked id="new_motorcycle">
                                                                    <div class="slider round"></div>
                                                                </label>
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <label class="control-label col-sm-5"><?php _e('Seats', 'wfd_truck'); ?>
                                                                </label>
                                                            <div class="col-sm-7">
                                                                <input type="text" class="form-control" id="new_seats">
                                                            </div>
                                                        </div>
                                                        <div class="form-group">
                                                            <label class="control-label col-sm-5"><?php _e('Under lift', 'wfd_truck'); ?>
                                                                </label>
                                                            <div class="col-sm-7">
                                                                <input type="text" class="form-control" id="new_under_lift">
                                                            </div>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                            <div class="col-sm-2">
                                                <form>
                                                    <div class="form-group">
                                                        <label><?php _e('Type', 'wfd_truck'); ?></label>
                                                        <select class="form-control">
                                                            <option><?php _e('Rig', 'wfd_truck'); ?></option>
                                                            <option><?php _e('Spectacle truck', 'wfd_truck'); ?></option>
                                                            <option><?php _e('Crane', 'wfd_truck'); ?></option>
                                                            <option><?php _e('truck salvage', 'wfd_truck'); ?></option>
                                                        </select>
                                                    </div>
                                                </form>
                                            </div>
                                            <div class="col-sm-2">
                                                <img src="<?php echo plugins_url('/images/truck_profile_back.jpg', __FILE__)?>" class="img-thumbnail profile-pic"
                                                     alt="Cinque Terre" width="200" height="150">
                                                <script type="text/javascript">
                                                    var backImgUrl = "<?php echo plugins_url('/images/truck_profile_back.jpg', __FILE__) ?>";
                                                </script>
                                                <input class="file-upload" type="file" name="image" accept="image/*"/>
                                                <p class="col-sm-12"><?php _e('truck photo', 'wfd_truck'); ?></p>

                                                <form>
                                                    <label class="switch_red">
                                                        <input type="checkbox" checked id="new_out_order">
                                                        <div class="slider round"></div>
                                                    </label>
                                                    <label class="switch_label"><?php _e('Out of order', 'wfd_truck'); ?></label>
                                                </form>
                                            </div>
                                        </div>

                                    </div>
                                    <div class="modal-footer">
                                        <button type="button" class="btn btn-primary" id="btn_save_truck" disabled/>
                                            <span class="glyphicon glyphicon-file"></span>    <?php _e('Save', 'wfd_truck'); ?>
                                        </button>
                                    </div>
                                </div>
                              <!--</form>-->
                            </div>
                        </div>

    </div>

    <?php
}

function wfd_call_numbers_view()
{
    global $wpdb;
    $id = $_SESSION['client_id'];
    $tbl_call_num = $wpdb->prefix . "wfd_truck_call_num";
    $res_call_num = $wpdb->get_results("select * from $tbl_call_num where cid=$id", OBJECT);
    ?>
    <div role="tabpanel" class="tab-pane" id="callNum">
        <h2><?php _e('Call Numbers', 'wfd_truck'); ?></h2>
        <table class="table table-striped" data-toggle="table" id="callnum-list" data-unique-id="id">
            <thead>
            <tr>
                <th data-field="id" data-visible="false"></th>
                <th data-field="name"
                    data-sortable="true"><?php _e('Name', 'wfd_truck'); ?></th>
                <th data-field="phone"><?php _e('Phone no', 'wfd_truck'); ?></th>
                <th data-field="note"><?php _e('Note', 'wfd_truck'); ?></th>
                <th data-field="category"
                    data-sortable="true"><?php _e('Category', 'wfd_truck'); ?></th>
                <th data-field="action" data-formatter="editDelActionFormatter" data-events="callNumActionEvents"><?php _e('Action', 'wfd_truck'); ?></th>
            </tr>
            </thead>
            <tbody>
            <?php $i = 1;
            foreach ($res_call_num as $cn) {
                ?>
                <tr data-callnum-id=<?php echo $cn->id ?>>
                    <td><?php echo $cn->id ?></td>
                    <td><?php echo $cn->name ?></td>
                    <td><?php echo $cn->phone ?></td>
                    <td><?php echo $cn->note ?></td>
                    <td><?php echo $cn->category ?></td>
                    <td></td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
        <button type="button" class="btn btn-primary btn-add-callnum">
        <span class="glyphicon glyphicon-plus"></span>    <?php _e('add No', 'wfd_truck'); ?>
        </button>
    </div>
    <div class="modal fade" id="modal_add_callnum" data-client-id="<?php echo $id ?>"
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
                        <h4 class="modal-title" id="callnumModalLabel"></h4>
                    </div>
                    <div class="well">
                        <div class="container-fluid">
                            <div class="row form-group">
                                <div class="col-sm-4">
                                    <label><?php _e('Name', 'wfd_truck') ?></label>
                                </div>
                                <div class="col-sm-4"><input class="form-control"
                                                             id="new_name">
                                </div>
                            </div>
                            <div class="row form-group">
                                <div class="col-sm-4">
                                    <label><?php _e('Phone no', 'wfd_truck') ?></label>
                                </div>
                                <div class="col-sm-4"><input class="form-control"
                                                             id="new_phoneno">
                                </div>
                            </div>
                            <div class="row form-group">
                                <div class="col-sm-4">
                                    <label><?php _e('Note', 'wfd_truck') ?></label>
                                </div>
                                <div class="col-sm-4"><input class="form-control"
                                                             id="new_callnote">
                                </div>
                            </div>
                            <div class="row form-group">
                                <div class="col-sm-4">
                                    <label><?php _e('Category', 'wfd_truck') ?></label>
                                </div>
                                <div class="col-sm-4"><input class="form-control"
                                                             id="new_category">
                                </div>
                            </div>
                            <div class="row form-group">
                                <input hidden name="edit_mode" value="false">
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer form-group">
                        <button type="button" class="btn btn-primary" id="btn_save_callnum">
                            <span class="glyphicon glyphicon-file"></span>    <?php _e('Save', 'wfd_truck'); ?>
                        </button>
                    </div>
                </div>
            </form>

        </div>
    </div>

    <?php
}

function wfd_prices_view()
{
    global $wpdb;
    $id = $_SESSION['client_id'];

    $tbl_prices = $wpdb->prefix . "wfd_truck_truck_prices";
    $res_prices = $wpdb->get_results("select * from $tbl_prices where cid=$id", OBJECT);
    ?>
    <div role="tabpanel" class="tab-pane" id="prices">
        <h2><?php _e('Service Prices', 'wfd_truck'); ?></h2>
        <table class="table table-striped" data-toggle="table" id="service-list" data-unique-id="id">
            <thead>
            <tr>
                <th data-field="id" data-visible="false"></th>
                <th data-field="service"
                    data-sortable="true"><?php _e('Service', 'wfd_truck'); ?></th>
                <th data-field="description"
                    data-sortable="true"><?php _e('Description', 'wfd_truck'); ?></th>
                <th data-field="price"><?php _e('Price', 'wfd_truck'); ?></th>
                <th data-field="action" data-formatter="editDelActionFormatter" data-events="priceActionEvents"><?php _e('Action', 'wfd_truck'); ?></th>
            </tr>
            </thead>
            <tbody>
            <?php
            foreach ($res_prices as $p) {
                ?>
                <tr>
                    <td><?php echo $p->id ?></td>
                    <td><?php echo $p->service ?></td>
                    <td><?php echo $p->description ?></td>
                    <td><?php echo $p->price ?></td>
                    <td></td>
                </tr>
            <?php } ?>
            </tbody>
        </table>
        <button type="button" class="btn btn-primary btn-add-service">
               <span class="glyphicon glyphicon-plus"></span>    <?php _e('add Service', 'wfd_truck'); ?>
        </button>
    </div>
    <div class="modal fade" id="modal_add_service" data-client-id="<?php echo $id ?>"
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
                        <h4 class="modal-title" id="serviceModalLabel"></h4>
                    </div>
                    <div class="well">
                        <div class="container-fluid">
                            <div class="row form-group">
                                <div class="col-sm-4">
                                    <label><?php _e('Service', 'wfd_truck') ?></label>
                                </div>
                                <div class="col-sm-4"><input class="form-control"
                                                             id="new_service">
                                </div>
                            </div>
                            <div class="row form-group">
                                <div class="col-sm-4">
                                    <label><?php _e('Description', 'wfd_truck') ?></label>
                                </div>
                                <div class="col-sm-4"><input class="form-control"
                                                             id="new_description">
                                </div>
                            </div>
                            <div class="row form-group">
                                <div class="col-sm-4">
                                    <label><?php _e('Price', 'wfd_truck') ?></label>
                                </div>
                                <div class="col-sm-4"><input class="form-control"
                                                             id="new_price">
                                </div>
                            </div>
                            <div class="row form-group">
                               <input hidden name="service_id" value=<?php echo $p->id ?>>
                               <input hidden name="client_id" value=<?php echo $p->cid ?>>
                               <input hidden name="edit_mode" value="false">
                            </div>
                        </div>

                    </div>
                    <div class="modal-footer form-group">
                        <button type="button" class="btn btn-primary" id="btn_save_service">
                            <span class="glyphicon glyphicon-file"></span>    <?php _e('Save', 'wfd_truck'); ?>
                        </button>
                    </div>
                </div>
            </form>

        </div>
    </div>

    <?php
}

function wfd_handover_view()
{
    ?>
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

    <?php
}

function wfd_truck_user_dashboard_fn()
{
    my_enqueue();
    if (!isset($_GET['action']))
        $_GET['action'] = 'profile';

    ?>
    <div class="container">

        <?php
        global $wpdb;

        $tbl_client_info = $wpdb->prefix . "wfd_truck_client_info";
        $res_client_list = $wpdb->get_results("select * from $tbl_client_info", OBJECT);
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

        if (!isset($_SESSION['client_login']) || $_SESSION['client_login'] != 'true') {
            ?>

            <div class="row">
                <div class="col-sm-3 col-sm-offset-2 well">
                    <form method="POST">
                        <div class="form-group">
                            <label><b><?php _e('Username', 'wfd_truck'); ?></b></label>
                            <input type="text" placeholder="Enter Username" name="username" required
                                   class="form-control">
                        </div>
                        <div class="form-group">
                            <label><b><?php _e('Password', 'wfd_truck'); ?></b></label>
                            <input type="password" placeholder="Enter Password" name="password" required
                                   class="form-control">
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary" value="<?php _e('Login', 'wfd_truck'); ?>"
                                    name="client_login"><span
                                        class="glyphicon glyphicon-ok"></span> <?php _e('Login', 'wfd_truck'); ?>
                            </button>
                        </div>
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
                    <div class="col-sm-3">
                        <?php
                        if(is_wp_admin()){
                            ?>
                            <a href="<?php echo admin_url(); ?>/admin.php?page=wfd_truck_admin_view"><?php _e('Back to admin view', 'wfd_truck')?></a>
                        <?php
                        }
                        ?>
                    </div>
                    <div class="col-sm-9">
                        <ul class="pull-right nav  navbar-nav">
                            <li>
                                <a href=""><?php _e('Welcome', 'wfd_truck'); ?>  <?php echo $_SESSION['client_username'] ?></a>
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
                        $id = $_SESSION['client_id'];

                        $res_client_info = $wpdb->get_results("select * from $tbl_client_info where id=$id", OBJECT);

                        ?>

                        <br>
                        <ul class="nav nav-tabs" role="tablist">
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
                            <?php
                            wfd_core_data_view($res_client_info);
                            wfd_driver_view();
                            wfd_pickup_driver_view();
                            wfd_truck_pool_view();
                            wfd_call_numbers_view();
                            wfd_prices_view();
                            wfd_handover_view();
                            ?>
                        </div>
                        <div id="snackbar">Some text some message..</div>
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