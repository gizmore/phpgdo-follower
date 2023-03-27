<?php
namespace GDO\Follower\Method;

use GDO\Core\GDT;
use GDO\Core\GDT_Hook;
use GDO\Core\GDT_Response;
use GDO\Follower\GDO_Follower;
use GDO\Form\GDT_AntiCSRF;
use GDO\Form\GDT_Form;
use GDO\Form\GDT_Submit;
use GDO\Form\GDT_Validator;
use GDO\Form\MethodForm;
use GDO\UI\GDT_Bar;
use GDO\UI\GDT_Link;
use GDO\User\GDO_User;
use GDO\User\GDT_User;

/**
 * Add a user to follow.
 *
 * @version 6.10
 * @since 6.07
 * @author gizmore
 */
final class Follow extends MethodForm
{

	public function renderPage(): GDT
	{
		$tabs = GDT_Bar::make()->horizontal();
		$tabs->addFields(
			GDT_Link::make('link_followers')->href(href('Follower', 'Followers'))->icon('list'),
			GDT_Link::make('link_following')->href(href('Follower', 'Following'))->icon('list'),
		);
		return GDT_Response::makeWith($tabs)->addField(parent::renderPage());
	}

	public function createForm(GDT_Form $form): void
	{
		$follow = GDO_Follower::table();
		$following = $follow->gdoColumn('follow_following');
		$form->addFields(
			$following,
			GDT_Validator::make()->validator($form, $following, [$this, 'validateFollowing']),
			GDT_AntiCSRF::make(),
		);
		$form->actions()->addField(GDT_Submit::make());
	}

	public function formValidated(GDT_Form $form): GDT
	{
		# User which follows
		$userid = GDO_User::current()->getID();

		/**
		 * @var GDO_User $following
		 */
		$following = $form->getFormValue('follow_following');

		# Insert record
		GDO_Follower::blank($form->getFormVars())->
		setVar('follow_user', $userid)->
		insert();

		#
		GDT_Hook::callWithIPC('FollowerFollow', $userid, $following->getID());

		#
		return
			$this->redirectMessage('msg_following', [$following->renderName()], url('Follower', 'Following'));
	}

	public function validateFollowing(GDT_Form $form, GDT_User $field, $value)
	{
		if ($user = $field->getValue())
		{
			if ($user === GDO_User::current())
			{
				return $field->error('err_follow_self');
			}
			$uid = GDO_User::current()->getID();
			if ('1' === GDO_Follower::table()->select('1')->where("follow_user=$uid AND follow_following={$user->getID()}")->exec()->fetchValue())
			{
				return $field->error('err_follow_already', [$user->renderUserName()]);
			}
		}
		return true;
	}

}
