<?php

namespace Promulgate\Middlewares;

use Josantonius\Session\Session;
use Pecee\Http\Middleware\IMiddleware;
use Pecee\Http\Request;

class UserAuth implements IMiddleware
{

	public function handle(Request $request): void
	{

		$request->user = Session::get('user') ?? NULL;

		if($request->getUrl()->contains('/login')) {

			if($request->user) {

				Session::set('REDIRECT_MESSAGES', [
					[
						'type'    => 'success',
						'message' => 'You are already logged in',
					],
				]);
				session_write_close();
				redirect(url('home'));
			}

		} elseif(!$request->getUrl()->contains('/logout')) {

			if(!$request->user) {

				Session::set('REDIRECT_MESSAGES', [
					[
						'type'    => 'error',
						'message' => 'Please login to continue',
					],
				]);
				session_write_close();
				redirect(url('user_login'));
			}
		};

	}

}