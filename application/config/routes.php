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
|	https://codeigniter.com/user_guide/general/routing.html
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
$route['default_controller'] = 'dashboard';
$route['404_override'] = '';
$route['translate_uri_dashes'] = FALSE;
$route['admin'] = 'administrator/Admin_dashboard_controller/index';
$route['admin/login'] = 'administrator/admin_auth_controller/index';
$route['admin/logout'] = 'administrator/admin_auth_controller/logout';
$route['admin/kategori'] = 'administrator/kategori_controller/index';
$route['admin/kategori/tambah'] = 'administrator/kategori_controller/tambah_kategori';
$route['admin/kategori/hapus/(:num)'] = 'administrator/kategori_controller/hapus_kategori/$1';
$route['admin/kategori/ubah/(:num)'] = 'administrator/kategori_controller/ubah_kategori/$1';
$route['admin/produk'] = 'administrator/Produk_controller/index';
$route['admin/produk/tambah'] = 'administrator/Produk_controller/tambah_produk';
$route['admin/produk/ubah/(:num)'] = 'administrator/Produk_controller/ubah_produk/$1';
$route['admin/produk/hapus/(:num)'] = 'administrator/Produk_controller/hapus_produk/$1';
$route['admin/produk/hapus_gambar/(:num)/(:num)'] = 'administrator/Produk_controller/hapus_gambar/$1/$2';