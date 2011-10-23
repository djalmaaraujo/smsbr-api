<?php
/**
 * SMS BR API Class Helper
 * @djalmaaraujo
 * BETA
 */ 
require_once('http.php');
class Sms {
	public static $accountToken = '123456'; # chaveAPI, API
	public static $accountUser = 'fulano'; # seu login no smsBR
	public static $apiType = 'multiple'; # default
	public static $errors = array();
	public static $api = array(
		'multiple' => 'http://smsbr.com.br/restrito/integracaoGrupoSMS.php',
		'single' => 'http://smsbr.com.br/enviosms.php'
	);
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
			return self::$errors;
		}
	}

	public static function mergeAccountFields($params) {
		$returnParams = array();
		
		switch (self::$apiType) {
			case 'single':
				$returnParams['chaveAPI'] = self::$accountToken;
				$returnParams['usuarioNome'] = self::$accountUser;
				$returnParams['numeroTel'] = $params['phone'];
				$returnParams['mensTexto'] = $params['message'];
				break;
				
			case 'multiple':
				$returnParams['celular_numero'] = $params['phone'];
				$returnParams['mensagem'] = $params['message'];
				$returnParams['api'] = self::$accountToken;
				$returnParams['login'] = self::$accountUser;
				$returnParams['id_propio'] = rand(00000000, 99999999);
				$returnParams['partners_id'] = $params['partners_id'];
				break;
				
			default:
				throw new InvalidArgumentException('Unknown self::$apiType');
		}

		# Commom
		$returnParams['assinatura'] = $params['signature'];
		
		return $returnParams;
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
	
	public static function performSend($params) {
		$params = self::mergeAccountFields($params);
		Http::post(self::$api[self::$apiType], $params);
	}
}