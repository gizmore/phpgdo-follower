<?php
namespace GDO\Follower;

use GDO\Core\GDO_Module;
use GDO\Core\GDT_Checkbox;
use GDO\UI\GDT_Link;
use GDO\UI\GDT_Page;
use GDO\User\GDO_User;

/**
 * Manage followers.
 * This module does not use any templates at all.
 * If you need changes do a pull request for hooks and config.
 *
 * @version 6.07
 * @since 6.10
 * @author gizmore
 */
final class Module_Follower extends GDO_Module
{

	##############
	### Module ###
	##############
	public function getClasses(): array
	{
		return [
			GDO_Follower::class,
		];
	}

	public function onLoadLanguage(): void { $this->loadLanguage('lang/follower'); }

	public function getConfig(): array
	{
		return [
			GDT_Checkbox::make('hook_right_bar')->initial('1'),
		];
	}

	public function onInitSidebar(): void
	{
// 	    if ($this->cfgHookRightBar())
		{
			if (GDO_User::current()->isAuthenticated())
			{
				$bar = GDT_Page::$INSTANCE->rightBar();
				$bar->addField(GDT_Link::make('link_followers')->href(href('Follower', 'Followers')));
			}
		}
	}


	#############
	### Hooks ###
	#############

	public function cfgHookRightBar() { return $this->getConfigValue('hook_right_bar'); }

}
