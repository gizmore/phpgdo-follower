<?php
namespace GDO\Follower\Method;

use GDO\Core\GDO;
use GDO\Core\GDT;
use GDO\Core\GDT_Response;
use GDO\DB\Query;
use GDO\Follower\GDO_Follower;
use GDO\Table\MethodQueryList;
use GDO\UI\GDT_Bar;
use GDO\UI\GDT_Link;
use GDO\User\GDO_User;
use GDO\User\GDT_User;

/**
 * List all users a user is following.
 *
 * @version 6.07
 * @author gizmore
 */
final class Following extends MethodQueryList
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
		$uid = $this->gdoParameter('id')->getParameterValue()->getID();
		return GDO_Follower::table()->select('*')->where("follow_user=$uid");
	}

	public function execute(): GDT
	{
		$tabs = GDT_Bar::make()->horizontal();
		$tabs->addFields(
			GDT_Link::make('link_follow')->href(href('Follower', 'Follow'))->icon('add'),
			GDT_Link::make('link_followers')->href(href('Follower', 'Followers'))->icon('list'),
		);
		return GDT_Response::makeWith($tabs, parent::execute());
	}

}
