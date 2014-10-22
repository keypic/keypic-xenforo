<?php

class Keypic_Model_Post extends XFCP_Keypic_Model_Post
{
	public function preparePostJoinOptions(array $fetchOptions)
	{
		if (isset($fetchOptions['keypic']))
		{
			$joinOptions = parent::preparePostJoinOptions($fetchOptions);
			
			$joinOptions['selectFields'] .= ', keypic.details as keypic_details';
			$joinOptions['joinTables'] .= '
				LEFT JOIN keypic AS keypic
						ON (keypic.content_type = \'post\'
							AND keypic.content_id = post.post_id)
			';

			return $joinOptions;
		}

		return parent::preparePostJoinOptions($fetchOptions);
	}

	public function getKeypicDetails($postId)
	{
		return $this->_getDb()->fetchRow('
			SELECT details
			FROM keypic
			WHERE content_type = \'post\' AND content_id = ?
		', $postId);
	}

	public function preparePost(array $post, array $thread, array $forum, array $nodePermissions = null, array $viewingUser = null)
	{
		$post = parent::preparePost($post, $thread, $forum, $nodePermissions, $viewingUser);
		if (isset($post['keypic_details']) && !empty($post['keypic_details']))
		{
			$post['keypic_details'] = @unserialize($post['keypic_details']);
		}

		return $post;
	}
}
