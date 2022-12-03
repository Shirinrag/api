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

$route['register-user'] = 'userapp_api/register_user';
$route['login-data'] = 'userapp_api/login';
$route['update-user-profile'] = 'userapp_api/update_user_profile';
$route['add-user-car'] = 'userapp_api/add_user_car';
$route['delete-user-car'] = 'userapp_api/delete_user_car';
$route['user-cars-list-data'] = 'userapp_api/user_cars_list_data';
$route['booking-history'] = 'userapp_api/booking_history';
$route['booking-deatils-on-id'] = 'userapp_api/booking_details_on_id';
$route['user-wallet'] = 'userapp_api/user_wallet';



// =========================Super Admin API=============================
$route['loggedin-data'] = 'common/login_data';
$route['add-role'] = 'superadmin_api/add_role';
$route['display-all-role-data'] = 'superadmin_api/display_all_role_data';
$route['get-all-role-on-id'] = 'superadmin_api/get_all_role_on_id';
$route['update-role'] = 'superadmin_api/update_role';
$route['delete-role'] = 'superadmin_api/delete_role';
$route['change-password'] = 'superadmin_api/change_password';
$route['add-admin'] = 'superadmin_api/add_admin';
$route['display-all-admin-data'] = 'superadmin_api/display_all_admin_data';
$route['get-admin-data-on-id'] = 'superadmin_api/get_admin_data_on_id';
$route['update-admin'] = 'superadmin_api/update_admin';
$route['delete-admin'] = 'superadmin_api/delete_admin';
$route['add-user'] = 'superadmin_api/add_user';
$route['display-all-user-data'] = 'superadmin_api/display_all_user_data';
$route['get-user-data-on-id'] = 'superadmin_api/get_user_data_on_id';
$route['update-user'] = 'superadmin_api/update_user';
$route['delete-user'] = 'superadmin_api/delete_user';
$route['booking-history-data'] = 'superadmin_api/booking_history_data';


