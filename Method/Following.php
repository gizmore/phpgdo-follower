<?php
namespace GDO\Follower\Method;
use GDO\Table\MethodQueryList;
use GDO\Follower\GDO_Follower;
use GDO\User\GDT_User;
use GDO\User\GDO_User;
use GDO\UI\GDT_Bar;
use GDO\UI\GDT_Link;
use GDO\Core\GDO;
use GDO\Core\GDT_Response;
use GDO\DB\Query;
/**
 * List all users a user is following.
 *
 * @author gizmore
 * @version 6.07
 */
final class Following extends MethodQueryList
{
	public function gdoTable() : GDO
	{
		return GDO_Follower::table();
	}
	
	public function gdoParameters() : array
	{
		return array(
			GDT_User::make('id')->initial(GDO_User::current()->getID()),
		);
	}
	
	public function getQuery() : Query
	{
		$uid = $this->gdoParameter('id')->getParameterValue()->getID();
		return GDO_Follower::table()->select('*')->where("follow_user=$uid");
	}
	
	public function execute()
	{
		$tabs = GDT_Bar::make()->horizontal();
		$tabs->addFields(
			GDT_Link::make('link_follow')->href(href('Follower', 'Follow'))->icon('add'),
			GDT_Link::make('link_followers')->href(href('Follower', 'Followers'))->icon('list'),
		);
		return GDT_Response::makeWith($tabs, parent::execute());
	}
}
