<?php

class Keypic_DataWriter_DiscussionMessage_Post extends XFCP_Keypic_DataWriter_DiscussionMessage_Post
{
	protected function _messagePreSave()
	{
		parent::_messagePreSave();

		$instance = XenForo_Application::getInstance();
		if ($instance->offsetExists('keypicToken'))
		{
			$userinfo = $this->_getUserModel()->getUserById($this->get('user_id'));
			if (!$userinfo)
			{
				return;
			}

			$return = Keypic_Helper::post('post', $instance->offsetGet('keypicToken'), $userinfo['email'], 
				$this->get('username'), $this->get('message'));

			if ($return && is_object($return))
			{
				$this->set('message_state', 'moderated');
			}
		}
	}

	protected function _messagePostSave()
	{
		parent::_messagePostSave();

		if ($this->isInsert() && $this->get('post_id'))
		{
			Keypic_Helper::save($this->get('post_id'), 'post');
		}
	}

	protected function _messagePostDelete()
	{
		parent::_messagePostDelete();

		$this->_db->query('
			DELETE FROM keypic WHERE content_id = ? AND content_type = ?
		', array($this->get('post_id'), 'post'));
	}
}
