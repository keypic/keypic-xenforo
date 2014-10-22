<?php

class Keypic_Helper
{
	public static $initialize = false;

	public static function init()
	{
		if (!self::$initialize)
		{
			require_once 'Keypic/class_keypic.php';

			$formId = XenForo_Application::getOptions()->keypicFormId;
			$keypic_version = Keypic::getVersion();

			Keypic::setFormID($formId);
			Keypic::setUserAgent('User-Agent: XenForo/' . XenForo_Application::$version . ' | Keypic/' . $keypic_version);

			$keypicCache = XenForo_Application::getSimpleCacheData('keypicCache');
			if (!isset($keypicCache['status']) && $formId)
			{
				$response = Keypic::checkFormID($formId);
				$keypicCache['status'] = $response['status'];
				XenForo_Application::setSimpleCacheData('keypicCache', $keypicCache);
			}

			self::$initialize = true;
		}
	}

	public static function getToken()
	{
		self::init();
		return Keypic::getToken(false); 
	}

	public static function request($type, $withToken = false, $updateToken = false)
	{
		$kpFormDetails = XenForo_Application::getOptions()->keypicFormDetails;

		if (isset($kpFormDetails[$type]) && $kpFormDetails[$type])
		{
			self::init();
			$token = self::getToken();
			$keypic = Keypic::getIt('getScript', $kpFormDetails[$type . 'Dimension']);

			if ($updateToken || strval($keypic) === '' || (isset($_REQUEST['_xfResponseType']) && $_REQUEST['_xfResponseType'] == 'json'))
			{
				$content = '<a target="_blank" href="//ws.keypic.com/?RequestType=getClick&Token=' . $token . '"><img src="//ws.keypic.com/?RequestType=getImage&Token=' . $token . '&PublisherID=&WidthHeight=1x1" /></a>';
			}
			else
			{
				$content = '<div style="text-align:center;" id="keypictoken_' . $type . '">' . $keypic . '</div>';
			}
			
			if ($withToken)
			{
				$content .= '<input type="hidden" name="keypicToken" value="' . $token . '" />';
			}
			return $content;
		}

		return '';
	}

	public static function post($type, $token = '', $email = '', $name = '', $message = '', $ClientFingerprint = '')
	{
		if (empty(XenForo_Application::getOptions()->keypicFormId))
		{
			return;
		}
		elseif ($token == '' || empty($token))
		{
			return new XenForo_Phrase('invalid_keypic_post_token');
		}

		$kpFormDetails = XenForo_Application::getOptions()->keypicFormDetails;
		if (isset($kpFormDetails[$type]) && $kpFormDetails[$type])
		{
			self::init();
			$spam = Keypic::isSpam($token, $email, $name, $message, $ClientFingerprint);
		
			$keypicDetails = array('token' => $token, 'ts' => XenForo_Application::$time, 'spam' => $spam);
			XenForo_Application::set('keypicDetails', $keypicDetails);

			if(!is_numeric($spam) || $spam > Keypic::getSpamPercentage())
			{
				return new XenForo_Phrase('keypic_' . $type . '_spam_message');
			}
		}

		return;
	}

	public static function validateToken(&$controller, $type, $keypicToken)
	{
		$kpFormDetails = XenForo_Application::getOptions()->keypicFormDetails;
		if (!empty(XenForo_Application::getOptions()->keypicFormId) && isset($kpFormDetails[$type]) && $kpFormDetails[$type])
		{
			if (strval($keypicToken) === '')
			{
				throw $controller->responseException(self::_responseKeypicTokenError($controller));
			}

			XenForo_Application::set('keypicToken', $keypicToken);
		}

		return true;
	}

	public static function report($token)
	{
		self::init();
		return Keypic::reportSpam($token);
	}

	public static function save($contentId, $contentType)
	{
		$instance = XenForo_Application::getInstance();
		if ($instance->offsetExists('keypicDetails'))
		{
			$keypicDetails = serialize($instance->offsetGet('keypicDetails'));
			XenForo_Application::get('db')->query('
				INSERT INTO keypic (content_id, content_type, details) VALUES(?, ?, ?)
				ON DUPLICATE KEY UPDATE details = values(details)
			', array($contentId, $contentType, $keypicDetails));
		}
	}

	public static function get($contentId, $contentType)
	{
		return XenForo_Application::get('db')->fetchOne('
			SELECT details FROM keypic WHERE content_id = ? AND content_type = ?		
		', array($contentId, $contentType));
	}

	protected static function _responseKeypicTokenError(&$controller)
	{
		return $controller->responseError(new XenForo_Phrase('invalid_keypic_post_token'));
	}

	public static function getKeypicDimensions()
	{
		return array(
			'250x250' => 'Square Pop-Up (250 x 250)',
			'300x250' => 'Medium Rectangle (300 x 250)',
			'336x280' => 'Large rectangle (336 x 280)',
		
			'720x300' => 'Pop-under (720 x 300)',
			'468x60' => 'Full Banner (468 x 60)',
			'234x60' => 'Half Banner (234 x 60)',
			'125x125' => 'Square Button (125 x 125)',
			'728x90' => 'Leaderboard (728 x 90)',
			'120x600' => 'Skyscraper (120 x 600)',
			'160x600' => 'Wide Skyscraper (160 x 600)',
			'300x600' => 'Half Page Ad (300 x 600)'
		);
	}
}
