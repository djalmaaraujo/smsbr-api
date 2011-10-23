<?php
/**
 * SMS BR API Class Helper
 * @djalmaaraujo
 * BETA
 */ 
require_once('http.php');
class Sms {
	public static $accountToken = '123456'; # chaveAPI
	public static $accountUser = 'fulano'; # seu login no smsBR
	public static $api = 'http://smsbr.com.br/restrito/integracaoGrupoSMS.php'; # API Single Send
	public static $errors = array();
	public static $apiType = 'multiple';
	public static $requiredFields = array(
		'signature',
		'phone',
		'message'
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
		$params = self::mergeAccountFields($params);
		Http::post(self::$api_single, $params);
	}
	
	public static function mergeAccountFields($params) {
		$params['api'] = self::$accountToken;
		$params['login'] = self::$accountUser;
		$params['id_propio'] = rand(00000000, 99999999);
		$params = self::translateFields($params);
		
		return $params;
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
	
	public static function translateFields($params) {
		$translateParams = array(
			'api' => 'api', 
			'login' => 'accountUser', 
			'id_propio' => 'unique_id', 
			'assinatura' => 'signature', 
			'celular_numero' => 'phone', 
			'mensagem' => 'message',
			'usuarioNome' => 'user_ame',
			'numeroTel' => 'phone',
			'mensTexto' => 'message',
			'chaveAPI' => 'api'
		);
		
	}
}