<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/*
| -------------------------------------------------------------------------
| URI ROUTING
| -------------------------------------------------------------------------
| This file lets you re-map URI requests to specific controller functions.
|
| Typically there is a one-to-one relationship between a URL string
| and its corresponding controller class/method. The segments in a
| URL normally follow this pattern:
|
|	example.com/class/method/id/
|
| In some instances, however, you may want to remap this relationship
| so that a different class/function is called than the one
| corresponding to the URL.
|
| Please see the user guide for complete details:
|
|	https://codeigniter.com/userguide3/general/routing.html
|
| -------------------------------------------------------------------------
| RESERVED ROUTES
| -------------------------------------------------------------------------
|
| There are three reserved routes:
|
|	$route['default_controller'] = 'welcome';
|
| This route indicates which controller class should be loaded if the
| URI contains no data. In the above example, the "welcome" class
| would be loaded.
|
|	$route['404_override'] = 'errors/page_missing';
|
| This route will tell the Router which controller/method to use if those
| provided in the URL cannot be matched to a valid route.
|
|	$route['translate_uri_dashes'] = FALSE;
|
| This is not exactly a route, but allows you to automatically route
| controller and method names that contain dashes. '-' isn't a valid
| class or method name character, so it requires translation.
| When you set this option to TRUE, it will replace ALL dashes in the
| controller and method URI segments.
|
| Examples:	my-controller/index	-> my_controller/index
|		my-controller/my-method	-> my_controller/my_method
*/
$route['default_controller'] = 'welcome';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;
// ====================User App API=======================================================
$route['register-user'] = 'user_api/register_user';
$route['login-data'] = 'user_api/login';
$route['update-user-profile'] = 'user_api/update_user_profile';
$route['add-user-car'] = 'user_api/add_user_car';
$route['delete-user-car'] = 'user_api/delete_user_car';
$route['user-cars-list-data'] = 'user_api/user_cars_list_data';
$route['booking-history'] = 'user_api/booking_history';
$route['booking-deatils-on-id'] = 'user_api/booking_details_on_id';
$route['user-wallet'] = 'user_api/user_wallet';
$route['place-list'] = 'user_api/place_list';
$route['place-details-on-id'] = 'user_api/place_details_on_id';
$route['place-traffic'] = 'user_api/place_traffic';
$route['place-booking'] = 'user_api/place_booking';
$route['user-terms-condition'] = 'user_api/user_terms_condition';
$route['extend-place-booking'] = 'user_api/extend_place_booking';
$route['booking-cancel'] = 'user_api/booking_cancel';
$route['delete-user-account'] = 'user_api/delete_user_account';
$route['add-place-suggestion'] = 'user_api/add_place_suggestion';
$route['user-complaint'] = 'user_api/user_complaint';
$route['apply-for-vendor'] = 'user_api/apply_for_vendor';
// ============================= Verifier App API=========================================

$route['login-verifier'] = 'verifier_api/login_verifier';
$route['verify-booking'] = 'verifier_api/verify_booking';


// =============Super Admin API=============================
$route['loggedin-data'] = 'common/login_data';
$route['get-all-user-type'] = 'common/get_all_user_type';
$route['get-all-parking-data'] = 'common/get_all_parking_data';
$route['get-state-data-on-country-id'] = 'common/get_state_data_on_country_id';
$route['get-city-data-on-state-id'] = 'common/get_city_data_on_state_id';
// =======================Add Role=====================================
$route['add-role'] = 'superadmin_api/add_role';
$route['display-all-role-data'] = 'superadmin_api/display_all_role_data';
$route['get-all-role-on-id'] = 'superadmin_api/get_all_role_on_id';
$route['update-role'] = 'superadmin_api/update_role';
$route['delete-role'] = 'superadmin_api/delete_role';
// ===========================Change Password======================================
$route['change-password'] = 'superadmin_api/change_password';
// ================================Add Admin ==================================
$route['add-admin'] = 'superadmin_api/add_admin';
$route['display-all-admin-data'] = 'superadmin_api/display_all_admin_data';
$route['get-admin-data-on-id'] = 'superadmin_api/get_admin_data_on_id';
$route['update-admin'] = 'superadmin_api/update_admin';
$route['delete-admin'] = 'superadmin_api/delete_admin';
// =========================== Add User========================================
$route['add-user'] = 'superadmin_api/add_user';
$route['display-all-user-data'] = 'superadmin_api/display_all_user_data';
$route['get-user-data-on-id'] = 'superadmin_api/get_user_data_on_id';
$route['update-user'] = 'superadmin_api/update_user';
$route['delete-user'] = 'superadmin_api/delete_user';
$route['update-user-status'] = 'superadmin_api/update_user_status';
// ================================= Booking History==================================
$route['booking-history-data'] = 'superadmin_api/booking_history_data';
// ========================= Add Place================================
$route['add-place'] = 'superadmin_api/add_place';
$route['display-all-parking-place-data'] = 'superadmin_api/display_all_parking_place_data';
$route['get-parking-place-details-on-id'] = 'superadmin_api/get_parking_place_details_on_id';
$route['update-place'] = 'superadmin_api/update_place';
$route['update-parking-place-status'] = 'superadmin_api/update_parking_place_status';
$route['delete-parking-place'] = 'superadmin_api/delete_parking_place';
// ================================ Add Device======================================
$route['add-device'] = 'superadmin_api/add_device';
$route['display-all-device-data'] = 'superadmin_api/display_all_device_data';
$route['update-device-status'] = 'superadmin_api/update_device_status';
// ============================= Bouns Amount=====================================
$route['add-bonus-amount'] = 'superadmin_api/add_bonus_amount';
$route['display-all-bonus-data'] = 'superadmin_api/display_all_bonus_data';
$route['update-bonus-status'] = 'superadmin_api/update_bonus_status';
// ============================ Place Status=====================================
$route['add-place-status'] = 'superadmin_api/add_place_status';
$route['display-all-place-status-data'] = 'superadmin_api/display_all_place_status_data';
$route['update-place-status'] = 'superadmin_api/update_place_status';
$route['update-place-status-data'] = 'superadmin_api/update_place_status_data';
// ============================= Price Type ======================================
$route['add-price-type'] = 'superadmin_api/add_price_type';
$route['display-all-price-type-data'] = 'superadmin_api/display_all_price_type_data';
$route['update-price-type'] = 'superadmin_api/update_price_type_status';
$route['update-price-type-data'] = 'superadmin_api/update_price_type_data';
// ================================== Mapped Device ====================================
$route['save-mapped-device'] = 'superadmin_api/save_mapped_device';
$route['update-machine-device-status'] = 'superadmin_api/update_machine_device_status';
$route['delete-slots-device-status'] = 'superadmin_api/delete_slots_device_status';

// ============================== Dashboard ==========================================
$route['dashboard-data'] = 'superadmin_api/dashboard';
// ============================ Duty Allocation =====================================
$route['get-allocation-data'] = 'superadmin_api/get_allocation_data';
$route['save-duty-allocation'] = 'superadmin_api/save_duty_allocation';
$route['display-all-duty-allocation-data'] = 'superadmin_api/display_all_duty_allocation_data';
$route['get-duty-allocation-details-on-id'] = 'superadmin_api/get_duty_allocation_details_on_id';
$route['delete-duty-allocation'] = 'superadmin_api/delete_duty_allocation';
// ================================= Blogs==========================================
$route['save-blogs'] = 'superadmin_api/save_blogs';
$route['display-all-blogs-data'] = 'superadmin_api/display_all_blogs';
$route['update-blogs'] = 'superadmin_api/update_blogs';
$route['delete-blogs'] = 'superadmin_api/delete_blogs';
// ============================= Vehicle Type========================================
$route['add-vehicle-type'] = 'superadmin_api/add_vehicle_type';
$route['display-all-vehicle-type-data'] = 'superadmin_api/display_all_vehicle_type_data';
$route['update-vehicle-type-status'] = 'superadmin_api/update_vehicle_type_status';
$route['update-vehicle-type-data'] = 'superadmin_api/update_vehicle_type_data';
$route['delete-vehicle-type'] = 'superadmin_api/delete_vehicle_type';
// =============================== POS Mapped Device====================================
$route['get-vehicle-details'] = 'superadmin_api/get_vehicle_details';

$route['get-pos-map-data'] = 'superadmin_api/get_pos_map_data';
$route['add-pos-device-map'] = 'superadmin_api/add_pos_device_mapped';
$route['display-all-pos-device-map-data'] = 'superadmin_api/display_all_pos_device_map_data';
$route['update-pos-device-map'] = 'superadmin_api/update_pos_device_map';
$route['delete-pos-device-map'] = 'superadmin_api/delete_pos_device_map';
$route['update-pos-device-map-status'] = 'superadmin_api/update_pos_device_map_status';
// =========================== POS Duty Allocation ===================================
$route['get-all-pos-verifier'] = 'superadmin_api/get_all_pos_verifier';
$route['save-pos-verifier-duty-allocation'] = 'superadmin_api/save_pos_verifier_duty_allocation';
$route['display-all-pos-duty-allocation-data'] = 'superadmin_api/display_all_pos_verifier_duty_allocation_data';
$route['delete-pos-duty-allocation'] = 'superadmin_api/delete_pos_duty_allocation';
// ================================ Add POS Device ================================
$route['add-pos-device'] = 'superadmin_api/add_pos_device';
$route['display-all-pos-device-data'] = 'superadmin_api/display_all_pos_device_data';
$route['update-pos-device-status'] = 'superadmin_api/update_pos_device_status';
// ============================ POS Booking History====================================
$route['display-all-pos-booking-data'] = 'pos_api/display_all_pos_booking_data';

//================================Terms & Condition=====================================
$route['user-terms-n-condition'] = 'superadmin_api/user_terms_n_condition';
$route['verifier-terms-n-condition'] = 'superadmin_api/verifier_terms_n_condition';
$route['vendor-terms-n-condition'] = 'superadmin_api/vendor_terms_n_condition';
$route['update-terms-n-condition'] = 'superadmin_api/update_terms_n_condition';

// ============================= POS API=====================================
$route['register-pos-verifier'] = 'pos_api/register_pos_verifier';
$route['login-pos-verifier'] = 'pos_api/login_pos_verifier';
$route['get-all-vehicle-type'] = 'pos_api/get_all_vehicle_type';
$route['get-all-price-data-on-id'] = 'pos_api/get_all_price_data_on_id';
$route['check-in-data'] = 'pos_api/check_in';
$route['check-out-data'] = 'pos_api/check_out';
$route['logout-data'] = 'pos_api/logout';
$route['pos-report-data'] = 'pos_api/pos_report_data';

