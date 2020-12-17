<?php

namespace applications\controllers;

use applications\core\Controller;

class AccountController extends Controller{

	//Регистрация

	public function registerAction() {
		if (!empty($_POST)) {
			if (!$this->model->validate(['email','login','wallet','password'], $_POST)) {
				$this->view->message('error', $this->model->error);
			}elseif ($this->model->checkEmailExists($_POST['email'])) {
				$this->view->message('error', 'Указанный e-mail адрес уже используется');
			}elseif (!$this->model->checkLoginExists($_POST['login'])) {
				$this->view->message('error', $this->model->error);
			}
			$this->model->register($_POST);
			$this->view->message('success', 'Для завершения регистрации подтвердите свой e-mail');
		}
		$this->view->render('Регистрация');
	}

	public function confirmAction() {
		if (!$this->model->checkTokenExists($this->route['token'])) {
				$this->view->redirect('account/login');
		}
		$this->model->activate($this->route['token']);
		$this->view->render('Подтрерждение электронной почты');
	}

	//Вход

	public function loginAction() {
		if (!empty($_POST)) {
			if (!$this->model->validate(['login','password'], $_POST)) {
				$this->view->message('error', $this->model->error);
			}
			elseif (!$this->model->checkData($_POST['login'],$_POST['password'])) {
				$this->view->message('error', $this->model->error);
			}elseif (!$this->model->checkStatus('login', $_POST['login'])) {
				$this->view->message('error', $this->model->error);
			}
			$this->model->login($_POST['login']);
			$this->view->redirect_j('account/profile');
		}
		$this->view->render('Вход');
	}

	//Профиль

	public function profileAction() {
		if (!empty($_POST)) {
			if (!$this->model->validate(['email','wallet'], $_POST)) {
				$this->view->message('error', $this->model->error);
			}
			$id = $this->model->checkEmailExists($_POST['email']);
			if ( $id and $id != $_SESSION['authorize']['id']) {
				$this->view->message('error', 'Указанный e-mail адрес уже используется');
			}
			if (!empty($_POST['password']) and !$this->model->validate(['password'], $_POST)) {
				$this->view->message('error', $this->model->error);
			}
			$this->model->save($_POST);
			$this->view->message('success', 'Изменения сохранены');
		}
		$this->view->render('Личный кабинет');
	}

	public function logoutAction() {
		unset($_SESSION['authorize']);
		$this->view->redirect('account/login');
	}

	//Восстановление пароля

	public function recoveryAction() {
		if (!empty($_POST)) {
			if (!$this->model->validate(['email'], $_POST)) {
				$this->view->message('error', $this->model->error);
			}elseif (!$this->model->checkEmailExists($_POST['email'])) {
				$this->view->message('error', 'Указанный e-mail адрес не найден');
			}elseif (!$this->model->checkStatus('email', $_POST['email'])) {
				$this->view->message('error', $this->model->error);
			}
			$this->model->recovery($_POST);
			$this->view->message('success', 'Запрос на восстановление пароля отправлен на e-mail');
		}
		$this->view->render('Восстановление пароля');
	}

	public function resetAction() {
		if (!$this->model->checkTokenExists($this->route['token'])) {
				$this->view->redirect('account/login');
		}
		$password = $this->model->reset($this->route['token']);
			$vars = [
				'password' => $password,
			];
		$this->view->render('Подтрерждение сброса пароля', $vars);
	}
	
}