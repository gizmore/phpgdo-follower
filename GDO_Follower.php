<?php
namespace GDO\Follower;

use GDO\Core\GDO;
use GDO\Core\GDT_CreatedAt;
use GDO\Core\GDT_Template;
use GDO\User\GDO_User;
use GDO\User\GDT_User;

final class GDO_Follower extends GDO
{

	public function gdoCached(): bool { return false; }

	public function gdoColumns(): array
	{
		return [
			GDT_User::make('follow_user')->notNull()->primary(),
			GDT_User::make('follow_following')->notNull()->primary(),
			GDT_CreatedAt::make('follow_created'),
		];
	}

	public function renderList(): string { return GDT_Template::php('Follower', 'list/follower.php', ['follower' => $this]); }

	public function getFollowerID() { return $this->gdoVar('follow_following'); }

	/**
	 * @param GDO_User $user
	 *
	 * @return GDO_User
	 */
	public function getOther(GDO_User $user) { return $this->getUserID() === $user->getID() ? $this->getFollower() : $this->getUser(); }

	public function getUserID() { return $this->gdoVar('follow_user'); }

	/**
	 * @return GDO_User
	 */
	public function getFollower() { return $this->gdoValue('follow_following'); }

	/**
	 * @return GDO_User
	 */
	public function getUser() { return $this->gdoValue('follow_user'); }

}
