<?php

namespace applications\models;

use applications\core\Model;

class Admin extends Model {

	public $error;

	public function loginValidate($post) {
		$cfg = require 'applications/config/admin.php';
		if (($cfg['login'] != $post['login']) or ($cfg['password'] != $post['password'])) {
			$this->error = 'Неправильно введён логин или пароль.';
			return false;
		}
	return true;
	}

	public function historyCount() {
		return $this->db->column('SELECT COUNT(id) FROM history');
	}

	public function historyList($route) {
		$max = 10;
		$params = [
			'max' => $max,
			'start' => (($route['page'] ?? 1) - 1 ) * $max,
		];
		$arr = [];
		$result = $this->db->row('SELECT * FROM history ORDER BY id DESC LIMIT :start, :max', $params);
		if(!empty($result)) {
			foreach ($result as $key => $val) {
				$arr[$key] = $val;
				$params = [
					'id' => $val['uid'],
				];
				$account = $this->db->row('SELECT login,email FROM accounts WHERE id = :id', $params)[0];
				$arr[$key]['login'] = $account['login'];
				$arr[$key]['email'] = $account['email'];
			}
		}
		return $arr;
	}

	public function withdrawRefList() {
		$arr = [];
		$result = $this->db->row('SELECT * FROM withdraw ORDER BY id DESC');
		if(!empty($result)) {
			foreach ($result as $key => $val) {
				$arr[$key] = $val;
				$params = [
					'id' => $val['uid'],
				];
				$account = $this->db->row('SELECT login, wallet FROM accounts WHERE id = :id', $params)[0];
				$arr[$key]['login'] = $account['login'];
				$arr[$key]['wallet'] = $account['wallet'];
			}
		}
		return $arr;
	}

	public function withdrawRefComplete($id) {
		$params = [
			'id' => $id,
		];
		$data = $this->db->row('SELECT uid,amount FROM withdraw WHERE id = :id', $params);
		if (!$data) {
			return false;
		}
		$this->db->query('DELETE FROM withdraw WHERE id = :id', $params);
		$data = $data[0];
		$params = [
			'id' => '',
			'uid' => $data['uid'],
			'unixTime' => time(),
			'description' => 'Выплата реферального вознаграждения на сумму: '.$data['amount'].'$',
		];
		$this->db->query('INSERT INTO history VALUES (:id, :uid, :unixTime, :description)', $params);
		return true;
	}

	public function withdrawTariffList() {
		$arr = [];
		$result = $this->db->row('SELECT * FROM tariffs WHERE UNIX_TIMESTAMP() >= unixTimeFinish AND sumout != 0 ORDER BY id DESC');
		if(!empty($result)) {
			foreach ($result as $key => $val) {
				$arr[$key] = $val;
				$params = [
					'id' => $val['uid'],
				];
				$account = $this->db->row('SELECT login, wallet FROM accounts WHERE id = :id', $params)[0];
				$arr[$key]['login'] = $account['login'];
				$arr[$key]['wallet'] = $account['wallet'];
			}
		}
		return $arr;
	}

	public function withdrawTariffComplete($id) {
		$params = [
			'id' => $id,
		];
		$data = $this->db->row('SELECT uid,sumout FROM tariffs WHERE id = :id', $params);
		if (!$data) {
			return false;
		}
		$this->db->query('UPDATE tariffs SET sumout = 0 WHERE id = :id', $params);
		$data = $data[0];
		$params = [
			'id' => '',
			'uid' => $data['uid'],
			'unixTime' => time(),
			'description' => 'Выплата инвестиционного вознаграждения по тарифу #'.$id.' на сумму: '.$data['sumout'].'$',
		];
		$this->db->query('INSERT INTO history VALUES (:id, :uid, :unixTime, :description)', $params);
		return true;
	}

	public function tariffsCount() {
		return $this->db->column('SELECT COUNT(id) FROM tariffs');
	}

	public function tariffsList($route) {
		$max = 10;
		$params = [
			'max' => $max,
			'start' => (($route['page'] ?? 1) - 1 ) * $max,
		];
		$arr = [];
		$result = $this->db->row('SELECT * FROM tariffs ORDER BY id DESC LIMIT :start, :max', $params);
		if(!empty($result)) {
			foreach ($result as $key => $val) {
				$arr[$key] = $val;
				$params = [
					'id' => $val['uid'],
				];
				$account = $this->db->row('SELECT login,email FROM accounts WHERE id = :id', $params)[0];
				$arr[$key]['login'] = $account['login'];
				$arr[$key]['email'] = $account['email'];
			}
		}
		return $arr;
	}
}