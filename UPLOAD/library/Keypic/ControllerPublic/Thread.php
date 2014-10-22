<?php

class Keypic_ControllerPublic_Thread extends XFCP_Keypic_ControllerPublic_Thread
{
	protected function _getPostFetchOptions(array $thread, array $forum)
	{
		$postFetchOptions = parent::_getPostFetchOptions($thread, $forum);
		$postFetchOptions['keypic'] = 1;
		
		return $postFetchOptions;
	}

	public function actionAddReply()
	{
		$keypicToken = $this->_input->filterSingle('keypicToken', XenForo_Input::STRING);
		Keypic_Helper::validateToken($this, 'post', $keypicToken);
		
		return parent::actionAddReply();
	}
}