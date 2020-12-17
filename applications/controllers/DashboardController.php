<?php

namespace applications\controllers;

use applications\core\Controller;
use applications\lib\Pagination;

class DashboardController extends Controller{

	public function investAction() {
		$vars = [
			'tariff' => $this->tariffs[$this->route['id']],
		];
		$this->view->render('Главная страница', $vars);
	}
	public function tariffsAction() {
		$pagination = new Pagination($this->route, $this->model->tariffsCount());
		$vars = [
			'pagination' => $pagination->get(),
			'list' => $this->model->tariffsList($this->route),
			'tariffs' => $this->tariffs,
		];
		$this->view->render('Ваши тарифы', $vars);
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
	public function referralsAction() {
		if (!empty($_POST)) {
			if($_SESSION['authorize']['refBalance'] <= 0) {
				$this->view->message('error', 'Недостаточно средств');
			}
			$this->model->createRefWithdraw();
			$this->view->message('success','Заявка на вывод средств отправлена '.$_SESSION['authorize']['refBalance']);
		}
		$pagination = new Pagination($this->route, $this->model->referralsCount());
		$vars = [
			'pagination' => $pagination->get(),
			'list' => $this->model->referralsList($this->route),
		];
		$this->view->render('Ваши рефералы', $vars);
	}
}