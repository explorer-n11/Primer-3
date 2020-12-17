<?php

namespace applications\models;

use applications\core\Model;

class Account extends Model {

	public function validate($input,$post) {
		$rules = [
			'email' => [
				'pattern' => '#^([a-zA-Z0-9_.-]{1,20}+)@([a-zA-Z0-9_.-]+)\.([a-z\.]{2,10})$#',
				'message' => 'e-mail указан не верно',
			],
			'login' => [
				'pattern' => '#^[a-zA-Z0-9]{3,20}$#',
				'message' => 'Логин указан не верно',
			],
			'ref' => [
				'pattern' => '#^[a-zA-Z0-9]{3,20}$#',
				'message' => 'Логин указан не верно',
			],
			'wallet' => [
				'pattern' => '#^[U0-9]{3,20}$#',
				'message' => 'Идентифткатор кошелька указан не верно',
			],
			'password' => [
				'pattern' => '#^[a-zA-Z0-9]{6,20}$#',
				'message' => 'Пароль указан не верно',
			],
		];

		foreach ($input as $val) {
			if (!isset($post[$val]) or empty($post[$val]) or !preg_match($rules[$val]['pattern'], $post[$val])) {
				$this->error = $rules[$val]['message'];
				return false;
			}
		}
		if (isset($post['ref'])) {
			if ($post['login'] == $post['ref']) {
				$this->error = 'Не правильная реферальная ссылка';
				return false;
			}
		}	
		return true;
	}

	public function checkEmailExists($email) {

		$params = [
			'email' => $email,
		];
		return $this->db->column('SELECT id FROM accounts WHERE email = :email', $params);		
	}

	public function checkLoginExists($login) {

		$params = [
			'login' => $login,
		];

		if($this->db->column('SELECT id FROM accounts WHERE login = :login', $params)) {
			$this->error = 'Такой логин уже существует';
			return false;
		}
		return true;
	}

	public function checkTokenExists($token) {

		$params = [
			'token' => $token,
		];
		if(!$this->db->column('SELECT id FROM accounts WHERE token = :token', $params)) {
			$this->error = 'Не верный код активации';
			return false;
		}
		return true;
	}

	public function checkRefExists($login) {
		$params = [
			'login' => $login,
		];
		return $this->db->column('SELECT id FROM accounts WHERE login = :login', $params);
	}

	public function checkData($login, $password) {
		$params = [
			'login' => $login,
		];

		$hash = $this->db->column('SELECT password FROM accounts WHERE login = :login', $params);

		if(!$hash or !password_verify($password, $hash)) {
			$this->error = 'Логин или пароль указан неверно';
			return false;
		}
		return true;
	}

	public function checkStatus($type, $data) {

		$params = [
			$type => $data,
		];
		$status = $this->db->column('SELECT status FROM accounts WHERE '.$type.' = :'.$type, $params);
		if ($status != 1) {
			$this->error = 'Ошибка входа';
			return false;
		}
		return true;
	}

	public function checkEmailId($email) {

		$params = [
			'email' => $email,
		];
		return $this->db->column('SELECT id FROM accounts WHERE email = :email', $params);
	}

	public function createToken() {
		return substr(str_shuffle(str_repeat('0123456789abcdefghijklmnopqrstuvwxyz', 5)),0, 30);
	}

	public function login($login) {
		$params = [
			'login' => $login,
		];

		$data = $this->db->row('SELECT * FROM accounts WHERE login = :login', $params);
		$_SESSION['authorize'] = $data[0];
	}

	public function register($post) {
		$token = $this->createToken();
		
		if (isset($post['ref'])) {
			$ref = $this->checkRefExists($post['ref']);
			if (!$ref) {
				$ref = 0;
			}
		} else {
			$ref = 0;
		}
		$params = [
				'id' => '',
				'email' => $post['email'],
				'login' => $post['login'],
				'wallet' => $post['wallet'],
				'password' => password_hash($post['password'], PASSWORD_BCRYPT),
				'ref' => 0,
				'refBalance' => 0,
				'token' => $token,
				'status' => 0,
				'ref' => $ref,
			];	
		$this->db->query('INSERT INTO accounts VALUES (:id, :email, :login, :wallet, :password, :ref, :refBalance, :token, :status)', $params);
		mail($post['email'], 'Регистрация нового пользователя', 'Подтвердите регистрацию на сайте: '.$_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'].'/account/confirm/'.$token);
	}

	public function recovery($post) {
		$token = $this->createToken();
		$params = [
				'email' => $post['email'],
				'token' => $token,
			];	
		$this->db->query('UPDATE accounts SET token = :token WHERE email = :email', $params);
		mail($post['email'], 'Изменение пароля на test.mvc3', 'Подтвердите изменение пароля на сайте: '.$_SERVER['REQUEST_SCHEME'].'://'.$_SERVER['HTTP_HOST'].'/account/reset/'.$token);
	}

	public function activate($token) {
		$params = [
			'token' => $token,
		];
		$this->db->column('UPDATE accounts SET status = 1, token = "" WHERE token = :token', $params);
	}

	public function reset($token) {
		$new_password = substr(str_shuffle(str_repeat('0123456789abcdefghijklmnopqrstuvwxyz', 5)),0, 6);
		$params = [
			'token' => $token,
			'password' => password_hash($new_password, PASSWORD_BCRYPT),
		];
		$this->db->column('UPDATE accounts SET status = 1, token = "", password = :password WHERE token = :token', $params);
		return $new_password;
	}

	public function save($post) {
		$params = [
			'id' => $_SESSION['authorize']['id'],
			'email' => $post['email'],
			'wallet' => $post['wallet'],
			
		];
		if(!empty($post['password'])) {
			$params['password'] = password_hash($post['password'], PASSWORD_BCRYPT);
			$sql = ', password = :password';
		} else {
			$sql = '';
		}

		foreach ($params as $key => $val) {
			$_SESSION['authorize'][$key] = $val;
		}
		$this->db->query('UPDATE accounts SET email = :email, wallet = :wallet'.$sql.' WHERE id = :id', $params);
	}


}