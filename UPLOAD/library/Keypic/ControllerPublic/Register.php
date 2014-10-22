<?php

class Keypic_ControllerPublic_Register extends XFCP_Keypic_ControllerPublic_Register
{
	protected function _getRegistrationInputData()
	{
		$registerData = parent::_getRegistrationInputData();
		$keypicToken = $this->_input->filterSingle('keypicToken', XenForo_Input::STRING);

		$return = Keypic_Helper::post('register', $keypicToken, $registerData['data']['email'], $registerData['data']['username']);
		if (is_object($return))
		{
			$registerData['errors'][] = $return;	
		}

		return $registerData;
	}

}