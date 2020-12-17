<?php

return [
	//MainControler
		'' => [
		'controller' => 'main',
		'action' => 'index',
	],
	//MerchantController
		'merchant/perfectmoney' => [
		'controller' => 'merchant',
		'action' => 'perfectmoney',
	],
	//DashboardControler
		'dashboard/invest/{id:\d+}' => [
		'controller' => 'dashboard',
		'action' => 'invest',
	],
		'dashboard/history' => [
		'controller' => 'dashboard',
		'action' => 'history',
	],
		'dashboard/history/{page:\d+}' => [
		'controller' => 'dashboard',
		'action' => 'history',
	],
		'dashboard/tariffs' => [
		'controller' => 'dashboard',
		'action' => 'tariffs',
	],
		'dashboard/tariffs/{page:\d+}' => [
		'controller' => 'dashboard',
		'action' => 'tariffs',
	],
		'dashboard/referrals' => [
		'controller' => 'dashboard',
		'action' => 'referrals',
	],
		'dashboard/referrals/{page:\d+}' => [
		'controller' => 'dashboard',
		'action' => 'referrals',
	],
	//AccountController
		'account/login' => [
		'controller' => 'account',
		'action' => 'login',
	],

		'account/register' => [
		'controller' => 'account',
		'action' => 'register',
	],

		'account/recovery' => [
		'controller' => 'account',
		'action' => 'recovery',
	],

		'account/confirm/{token:\w+}' => [
		'controller' => 'account',
		'action' => 'confirm',
	],

		'account/reset/{token:\w+}' => [
		'controller' => 'account',
		'action' => 'reset',
	],

		'account/register/{ref:\w+}' => [
		'controller' => 'account',
		'action' => 'register',
	],

		'account/profile' => [
		'controller' => 'account',
		'action' => 'profile',
	],

		'account/logout' => [
		'controller' => 'account',
		'action' => 'logout',
	],
	// AdminController

		'admin/login' => [
		'controller' => 'admin',
		'action' => 'login',
	],

		'admin/logout' => [
		'controller' => 'admin',
		'action' => 'logout',
	],

		'admin/history/{page:\d+}' => [
		'controller' => 'admin',
		'action' => 'history',
	],

		'admin/history' => [
		'controller' => 'admin',
		'action' => 'history',
	],

		'admin/tariffs/{page:\d+}' => [
		'controller' => 'admin',
		'action' => 'tariffs',
	],

		'admin/tariffs' => [
		'controller' => 'admin',
		'action' => 'tariffs',
	],

	'admin/withdraw' => [
		'controller' => 'admin',
		'action' => 'withdraw',
	],
];