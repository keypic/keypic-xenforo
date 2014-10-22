<?php

class Keypic_ViewPublic_Misc_UpdateToken extends XenForo_ViewPublic_Base
{
	public function renderJson()
	{
		return XenForo_ViewRenderer_Json::jsonEncodeForOutput(array(
			'keypic' => $this->_params['keypic'],
			'token' => $this->_params['token']
		));
	}
}
