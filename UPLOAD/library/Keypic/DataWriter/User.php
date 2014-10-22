<?php

class Keypic_DataWriter_User extends XFCP_Keypic_DataWriter_User
{
	protected function _postSave()
	{
		parent::_postSave();

		if ($this->isInsert() && $this->get('user_id'))
		{
			$contentId = $this->get('user_id');
			$contentType = 'user';

			$instance = XenForo_Application::getInstance();
			if ($instance->offsetExists('keypicDetails'))
			{
				$keypicDetails = serialize($instance->offsetGet('keypicDetails'));

				$this->_db->query('
					INSERT INTO keypic (content_id, content_type, details) VALUES(?, ?, ?)
				', array($contentId, $contentType, $keypicDetails));
			}	
		}
	}

	protected function _postDelete()
	{
		parent::_postDelete();

		$this->_db->query('
			DELETE FROM keypic WHERE content_id = ? AND content_type = ?
		', array($this->get('user_id'), 'user'));
	}
}
