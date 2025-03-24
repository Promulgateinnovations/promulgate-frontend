<?php


namespace Promulgate\Core;


final class Config
{

	public const USER_ROLES          = [
		'DIRECTOR' => 1,
		'ADMIN'    => 2,
		'CREATOR'  => 3,
		'APPROVER' => 4,
		'AGENCY HEAD' =>5,
	];

	public const HTTP_RESPONSE_CODES = [
		'OK'                    => 200,
		'CREATED'               => 201,
		'NO_CONTENT'            => 204,
		'MOVED_PERMANENTLY'     => 301,
		'FOUND'                 => 302,
		'BAD_REQUEST'           => 400,
		'UNAUTHORIZED'          => 401,
		'FORBIDDEN'             => 403,
		'NOT_FOUND'             => 404,
		'CONFLICT'              => 409,
		'UNPROCESSABLE_ENTITY'  => 422,
		'INTERNAL_SERVER_ERROR' => 500,
		'BAD_GATEWAY'           => 502,
		'SERVICE_UNAVAILABLE'   => 503,
	];

	public const RESPONSE_CODE_GROUPS = [
		// We can read content
		'VALID'     => [
			self::HTTP_RESPONSE_CODES['OK'],
			self::HTTP_RESPONSE_CODES['CREATED'],
			self::HTTP_RESPONSE_CODES['NO_CONTENT'],
			self::HTTP_RESPONSE_CODES['UNPROCESSABLE_ENTITY'],
		],
		// Some problem with urls/auth tokens we can read errors but developer must fix the issues
		'DEVELOPER' => [
			self::HTTP_RESPONSE_CODES['UNAUTHORIZED'],
			self::HTTP_RESPONSE_CODES['BAD_REQUEST'],
			self::HTTP_RESPONSE_CODES['FORBIDDEN'],
		],
		// Some problem with response cant proceed further
		'SERVER'    => [
			self::HTTP_RESPONSE_CODES['NOT_FOUND'],
			self::HTTP_RESPONSE_CODES['BAD_GATEWAY'],
			self::HTTP_RESPONSE_CODES['SERVICE_UNAVAILABLE'],
			self::HTTP_RESPONSE_CODES['INTERNAL_SERVER_ERROR'],
		],
	];

	public const LOGIN_TYPES = [
		'normal' => [
			'self' => 'self',
		],
		'social' => [
			'google' => 'google',
		],
	];

	public const API_CONFIGURATION_CONNECTIONS = [
		// Connection Unique name => Call back JS function name
		'facebook' => [
			'status'               => true,
			'js_callback_function' => 'configureFacebookConnection',
		],
		'whatsapp' => [
			'status'               => true,
			'js_callback_function' => 'configureFacebookConnection',
		],
		'youtube'  => [
			'status'               => true,
			'js_callback_function' => 'configureYoutubeConnection',
		],
		'instagram'  => [
			'status'               => true,
			'js_callback_function' => 'configureInstagramConnection',
		],
		'linkedin'  => [
			'status'               => true,
			'oauth_authorization_url_constant' => 'LINKEDIN_OAUTH_AUTHORIZATION_URL', // JS Const to open that URL
		],
		'google_drive'  => [
			'status'               => true,
			'js_callback_function' => 'configureGoogleDriveConnection',
		],
		//		'sms'  => [
//			'status'               => true,
//			'js_callback_function' => 'configureSmsConnection',
//		],
		'e_mail'  => [
			'status'               => true,
			'js_callback_function' => 'configureEmailConnection',
		],
	];
}