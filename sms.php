<?php
class Sms extends Object {
	public static $accountToken = '123456'; # chaveAPI
	public static $accountUser = 'fulano'; # seu login no smsBR
	public static $api = 'http://smsbr.com.br/restrito/integracaoGrupoSMS.php'; # API
	public static $errors = array();
	public static $requiredFields = array(
		'assinatura',
		'celular_numero',
		'mensagem'
	);
	
	public static function send($params) {
		if (self::validate($params)) {
			Http::clear();
			return self::performSend($params);
		} else {
			self::outputErrors();
		}
	}
	
	public static function performSend($params) {
		$params['api'] = self::$accountToken;
		$params['login'] = self::$accountUser;
		$params['id_propio'] = rand(00000000,99999999);
		Http::post(self::$api, $params);
	}
	
	public static function result() {
		return Http::result();
	}
	
	public static function parseResponse() {
		$response = substr(self::result(), 0, -1);
	}
	
	public static function validate($params) {
		foreach (self::$requiredFields as $index) {
			if (empty($params[$index])) {
				self::$errors[] = $index;
			}
		}
		return (count(self::$errors) > 0) ? false : true;
	}
	
	public function outputErrors() {
		$errors = implode(',', self::$errors);
		if ($errors) {
			throw new InvalidArgumentException('Check this empty fields:' . $errors);
		}
	}
}