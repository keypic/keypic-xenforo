<?php

class Keypic_ControllerAdmin_Banning extends XFCP_Keypic_ControllerAdmin_Banning
{
	public function actionUsersAdd()
	{
		if (($user_id = $this->_input->filterSingle('user_id', XenForo_Input::UINT)) && ($keypic = $this->_input->filterSingle('keypic', XenForo_Input::UINT)))
		{
			$keypicDetails = Keypic_Helper::get($user_id, 'user');
			if (!empty($keypicDetails))
			{
				$keypicDetails = @unserialize($keypicDetails);
				Keypic_Helper::report($keypicDetails['token']);
			}
		}
		
		return parent::actionUsersAdd();
	}
}
