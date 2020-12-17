<?php

namespace applications\controllers;

use applications\core\Controller;

class MerchantController extends Controller{

	public function perfectmoneyAction() {
		/*$_POST['PAYMENT_ID'] = '3,32';
		$_POST['PAYEE_ACCOUNT'] = '';
		$_POST['PAYMENT_AMOUNT'] = 10450;
		$_POST['PAYMENT_UNITS'] = 'USD';
		$_POST['PAYMENT_BATCH_NUM'] = '';
		$_POST['PAYER_ACCOUNT'] = '';
		$_POST['TIMESTAMPGMT'] = '';*/
		if(!empty($_POST)) {
			$this->view->errorCode(404);
		}
		$data = $this->model->validatePerfectMoney($_POST, $this->tariffs);
		if (!$data) {
			$this->view->errorCode(403);
		}
		$this->model->createTariff($data, $this->tariffs[$data['tid']]);
	}
}