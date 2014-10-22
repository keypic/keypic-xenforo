<?php

class Keypic_ControllerPublic_Forum extends XFCP_Keypic_ControllerPublic_Forum
{
	public function actionAddThread()
	{
		$keypicToken = $this->_input->filterSingle('keypicToken', XenForo_Input::STRING);
		Keypic_Helper::validateToken($this, 'post', $keypicToken);
		
		return parent::actionAddThread();
	}
}