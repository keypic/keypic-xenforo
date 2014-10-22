<?php

class Keypic_Install_Base
{
	public static function install()
	{
		XenForo_Application::get('db')->query("
			CREATE TABLE IF NOT EXISTS keypic  (
				content_id INT UNSIGNED NOT NULL DEFAULT 0,
				content_type enum('user', 'post') NOT NULL DEFAULT 'post',
				details MEDIUMTEXT NOT NULL,
				PRIMARY KEY (content_id, content_type)
			)
		");
	}

	public static function uninstall()
	{
		XenForo_Application::get('db')->query('
			DROP TABLE keypic'
		);

		XenForo_Application::setSimpleCacheData('keypicCache', false);
	}
}