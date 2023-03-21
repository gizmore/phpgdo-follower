<?php
namespace GDO\Follower\Method;

use GDO\Core\GDO;
use GDO\Core\GDT_Response;
use GDO\DB\Query;
use GDO\Follower\GDO_Follower;
use GDO\Table\MethodQueryList;
use GDO\UI\GDT_Bar;
use GDO\UI\GDT_Link;
use GDO\User\GDO_User;
use GDO\User\GDT_User;

/**
 * List all followers for a user.
 *
 * @version 6.07
 * @author gizmore
 */
final class Followers extends MethodQueryList
{

	public function gdoTable(): GDO
	{
		return GDO_Follower::table();
	}

	public function gdoParameters(): array
	{
		return [
			GDT_User::make('id')->initial(GDO_User::current()->getID()),
		];
	}

	public function getQuery(): Query
	{
		$uid = $this->gdoParameterValue('id')->getID();
		return GDO_Follower::table()->select('*')->where("follow_following=$uid");
	}

	public function execute()
	{
		$tabs = GDT_Bar::make()->horizontal();
		$tabs->addFields(
			GDT_Link::make('link_follow')->href(href('Follower', 'Follow'))->icon('add'),
			GDT_Link::make('link_following')->href(href('Follower', 'Following'))->icon('list'),
		);
		return GDT_Response::makeWith($tabs, parent::execute());
	}

}
