<?php

class Keypic_ControllerPublic_Login extends XFCP_Keypic_ControllerPublic_Login
{
	public function actionLogin()
	{
		$this->_assertPostOnly();

		$token = $this->_input->filterSingle('keypicToken', XenForo_Input::STRING);
		$return = Keypic_Helper::post('login', $token);

		if (is_object($return))
		{
			return $this->responseError($return);
		}

		$response = parent::actionLogin();
		
		if ($userId = XenForo_Visitor::getUserId())
		{
			Keypic_Helper::save($userId, 'user');
		}

		return $response;
	}
}
