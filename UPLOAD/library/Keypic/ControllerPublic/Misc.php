<?php

class Keypic_ControllerPublic_Misc extends XFCP_Keypic_ControllerPublic_Misc
{
	public function actionUpdateKeypicToken()
	{
		$type = $this->_input->filterSingle('type', XenForo_Input::STRING);
		$keypic = Keypic_Helper::request($type, false, true);

		$token = Keypic::getToken(false); 

		$viewParams = array(
			'token'	=> $token,
			'keypic' => $keypic
		);

		return $this->responseView('Keypic_ViewPublic_Misc_UpdateToken', '', $viewParams);
	}

	public function actionContact()
	{
		if ($this->_request->isPost())
		{
			$user = XenForo_Visitor::getInstance()->toArray();
			if (!$user['user_id'])
			{
				$user['email'] = $this->_input->filterSingle('email', XenForo_Input::STRING);
				if (!XenForo_Helper_Email::isEmailValid($user['email']))
				{
					return $this->responseError(new XenForo_Phrase('please_enter_valid_email'));
				}
			}

			$input = $this->_input->filter(array(
				'message' => XenForo_Input::STRING,
				'keypicToken'	=> XenForo_Input::STRING
			));

			if (!$user['username'] || !$input['message'])
			{
				return $this->responseError(new XenForo_Phrase('please_complete_required_fields'));
			}
			
			$return = Keypic_Helper::post('contact', $input['keypicToken'], $user['email'], $user['username'], $input['message']);
			if (is_object($return))
			{
				return $this->responseError($return);
			}
		}

		return parent::actionContact();
	}
}
