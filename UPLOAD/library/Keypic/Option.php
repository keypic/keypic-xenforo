<?php

abstract class Keypic_Option
{
	public static function verifyOption(&$formId, XenForo_DataWriter $dw, $fieldName)
	{	
		if ($dw->isChanged('option_value'))
		{
			require_once 'Keypic/class_keypic.php';

			$response = Keypic::checkFormID($formId);
			$keypicCache = XenForo_Application::getSimpleCacheData('keypicCache');
			
			if (!$keypicCache)
			{
				$keypicCache = array();
			}

			$keypicCache['status'] = $response['status'];
			XenForo_Application::setSimpleCacheData('keypicCache', $keypicCache);
		}

		return true;
	}
}