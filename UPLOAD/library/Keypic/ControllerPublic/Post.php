<?php

class Keypic_ControllerPublic_Post extends XFCP_Keypic_ControllerPublic_Post
{
	public function actionDelete()
	{
		$response = parent::actionDelete();
		if (!$this->isConfirmedPost())
		{
			if ($keypic = $this->_input->filterSingle('keypic', XenForo_Input::UINT) && $response->params['post']['post_id'])
			{
				$keypic_details = $this->_getPostModel()->getKeypicDetails($response->params['post']['post_id']);
				
				if (!empty($keypic_details['details']))
				{
					$keypic_details = @unserialize($keypic_details['details']);
					$report = Keypic_Helper::report($keypic_details['token']);

					if (isset($report['status']) && $report['status'] == 'response')
					{
						$response->params['keypicPostReported'] = true;
					}
				}
			}
		}
		
		return $response;
	}
}
