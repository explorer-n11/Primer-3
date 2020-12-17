<?php

namespace applications\controllers;

use applications\core\Controller;
use applications\lib\Pagination;

class AdminController extends Controller{

	public function __construct($route) {
		parent::__construct($route); //Вызывает конструктор в Controller, чтобы подключить View
		$this->view->layout = 'admin'; //Изменяет свойство layout во View с default на admin
	}

	public function loginAction() {
		if (isset($_SESSION['administrator'])) {
			$this->view->redirect('admin/withdraw');
		}
		if (!empty($_POST)){
			if (!$this->model->loginValidate($_POST)) {
				$this->view->message('error', $this->model->error);
			}
			$_SESSION['administrator'] = true;
			$this->view->redirect_j('admin/withdraw');
		}
		$this->view->render('Вход');
	}

	public function logoutAction() {
		unset($_SESSION['administrator']);
		$this->view->redirect('admin/login');
	}

	public function withdrawAction() {
		if (!empty($_POST)){
			if ($_POST['type'] == 'ref') {
				if ($this->model->withdrawRefComplete($_POST['id'])) {
					$this->view->redirect_j('admin/withdraw');
				} else {
					$this->view->error('error', 'Запрашиваемый id не найден');
				}
			}
			elseif ($_POST['type'] == 'tariff') {
				if ($this->model->withdrawTariffComplete($_POST['id'])) {
					$this->view->redirect_j('admin/withdraw');
				} else {
					$this->view->error('error', 'Запрашиваемый id не найден');
				}
			}
		}
		$vars = [
			'listRef' => $this->model->withdrawRefList(),
			'listTariffs' => $this->model->withdrawTariffList(),
		];
		$this->view->render('Выплаты', $vars);
	}

	public function tariffsAction() {
		$pagination = new Pagination($this->route, $this->model->tariffsCount());
		$vars = [
			'pagination' => $pagination->get(),
			'list' => $this->model->tariffsList($this->route),
			'tariffs' => $this->tariffs,
		];
		$this->view->render('Тарифы', $vars);
	}

	public function historyAction() {
		$pagination = new Pagination($this->route, $this->model->historyCount());
		$vars = [
			'pagination' => $pagination->get(),
			'list' => $this->model->historyList($this->route),
			'tariffs' => $this->tariffs,
		];
		$this->view->render('История операций', $vars);
	}
}