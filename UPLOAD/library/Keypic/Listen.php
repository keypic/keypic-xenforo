<?php

class Keypic_Listen
{
	public static function init(XenForo_Dependencies_Abstract $dependencies, array $data)
	{
		XenForo_Template_Helper_Core::$helperCallbacks += array(
		    'keypic' => array('Keypic_Helper', 'request')
		);
	}

	public static function extendModel($class, array &$extend)
	{
		if ($class == 'XenForo_Model_Post')
			$extend[] = 'Keypic_Model_Post';
	}

	public static function extendController($class, array &$extend)
	{
		if ($class == 'XenForo_ControllerPublic_Post')
			$extend[] = 'Keypic_ControllerPublic_Post';

		if ($class == 'XenForo_ControllerPublic_Thread')
			$extend[] = 'Keypic_ControllerPublic_Thread';

		if ($class == 'XenForo_ControllerPublic_Forum')
			$extend[] = 'Keypic_ControllerPublic_Forum';

		if ($class == 'XenForo_ControllerPublic_Misc')
			$extend[] = 'Keypic_ControllerPublic_Misc';

		if ($class == 'XenForo_ControllerPublic_Register')
			$extend[] = 'Keypic_ControllerPublic_Register';

		if ($class == 'XenForo_ControllerPublic_Login')
			$extend[] = 'Keypic_ControllerPublic_Login';

		if ($class == 'XenForo_ControllerAdmin_Banning')
			$extend[] = 'Keypic_ControllerAdmin_Banning';
	}		

	public static function extendDataWriter($class, array &$extend)
	{
		if ($class == 'XenForo_DataWriter_DiscussionMessage_Post')
			$extend[] = 'Keypic_DataWriter_DiscussionMessage_Post';

		if ($class == 'XenForo_DataWriter_User')
			$extend[] = 'Keypic_DataWriter_User';
	}

	public static function keypicOptionsTemplateCreate(&$templateName, array &$params, XenForo_Template_Abstract $template)
	{
		$params['keypicDimensions'] = Keypic_Helper::getKeypicDimensions();
	}

	/*public static function threadViewTemplateCreate(&$templateName, array &$params, XenForo_Template_Abstract $template)
	{
		if ($params['canQuickReply'] && !empty($params['qrEditor']))
		{
			$token = Keypic_Helper::getToken();
			$params['qrEditor'] .= '<input type="hidden" name="keypicToken" value="' . $token . '" />';
		}
	}*/

	public static function contactTemplatePostRender($templateName, &$content, array &$containerData, XenForo_Template_Abstract $template)
	{
		$keypic = Keypic_Helper::request('contact', true);
		if ($keypic)
		{
			$find = '<input type="hidden" name="_xfToken"';
			$content = str_replace($find, $keypic . $find, $content);
		}
	}

	public static function registerTemplatePostRender($templateName, &$content, array &$containerData, XenForo_Template_Abstract $template)
	{
		$keypic = Keypic_Helper::request('register');
		if ($keypic)
		{
			$find = '<input type="hidden" name="_xfToken"';
			$content = str_replace($find, $keypic . $find, $content);
		}
	}

	/*public static function loginTemplatePostRender($templateName, &$content, array &$containerData, XenForo_Template_Abstract $template)
	{
		if (in_array($templateName, array('error_with_login', 'login', 'login_bar')))
		{
			$keypic = Keypic_Helper::request('login');
			if ($keypic)
			{
				$find = '<input type="hidden" name="_xfToken"';
				$content = str_replace($find, $keypic . $find, $content);
			}
		}
	}*/

	public static function frontControllerPostView(XenForo_FrontController $fc, &$output)
	{
		if (get_class($fc->getDependencies()) == 'XenForo_Dependencies_Public')
		{
			if (in_array($fc->route()->getControllerName(), array('XenForo_ControllerPublic_Forum', 'XenForo_ControllerPublic_Thread',
				'XenForo_ControllerPublic_Post', 'XenForo_ControllerPublic_Register')))
			{
				$token = '<input type="hidden" name="keypicToken" value="' . Keypic_Helper::getToken() . '" />';
				$find = '<input type="hidden" name="_xfToken"';
				$output = str_replace($find, $token . $find, $output);
			}

			if (!XenForo_Visitor::getUserId() && $keypic = Keypic_Helper::request('login', true))
			{
				$find = '<input type="hidden" name="cookie_check" value="1" />';
				$output = str_replace($find, $keypic . $find, $output);
			}
		}
	}

	public static function templateHook($hookName, &$contents, array $hookParams, XenForo_Template_Abstract $template)
	{
		$keypic = false;

		switch ($hookName)
		{
			case 'thread_create_fields_extra':
			case 'thread_reply':
				$contents .= Keypic_Helper::request('post');
				break;
			case 'thread_view_qr_after':
				if ($template->getParam('canQuickReply'))
				{
					$contents .= Keypic_Helper::request('post');
					$template->addRequiredExternal('js', 'js/keypic/tokenupdate.js');
				}
				break;
			case 'post_private_controls':
				if ($hookParams['post']['canDelete'])
				{
					$contents .= $template->create('keypic_post_control', $hookParams)->render();
				}
				break;
		}
	}

	public static function templateUserExtraPostRender($templateName, &$content, array &$containerData, XenForo_Template_Abstract $template)
	{
		$content .= $template->create('keypic_user_details', $template->getParams())->render();	
	}

	public static function postTemplateCreate(&$templateName, array &$params, XenForo_Template_Abstract $template)
	{
		if ($templateName == 'post' || $templateName == 'thread_view')
		{
			$template->preloadTemplate('keypic_post_control');
		}

		if ($templateName == 'user_extra')
		{
			$params['keypicDetails'] = Keypic_Helper::get($params['user']['user_id'], 'user');
			if (!empty($params['keypicDetails']))
			{
				$params['keypicDetails'] = @unserialize($params['keypicDetails']);
			}
			$template->preloadTemplate('keypic_user_details');
		}
	}
}
