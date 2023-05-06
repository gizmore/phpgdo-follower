<?php
namespace GDO\Follower\Method;

use GDO\Core\GDT;
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
use GDO\Util\Common;

final class Unfollow extends MethodForm
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
		$follower = GDT_User::make('follower')->initial(Common::getRequestInt('id'));
		$form->addFields(
			$follower,
			GDT_Validator::make()->validator($form, $follower, [$this, 'validateFollower']),
			GDT_AntiCSRF::make(),
		);
		$form->actions()->addField(GDT_Submit::make());
	}

	public function formValidated(GDT_Form $form): GDT
	{
		$uid = GDO_User::current()->getID();
		$following = $form->getFormValue('follower');

		GDO_Follower::table()->deleteWhere("follow_user=$uid AND follow_following={$following->getID()}");

		return $this->redirectMessage('msg_unfollow', [$following->renderUserName()], href('Follower', 'Following'));
	}

	public function validateFollower(GDT_Form $form, GDT_User $field, $value)
	{
		$uid = GDO_User::current()->getID();
		if ('1' === GDO_Follower::table()->select('1')->where("follow_user=$uid AND follow_following={$field->getValue()->getID()}")->exec()->fetchVar())
		{
			return true;
		}
		return $field->error('err_not_following');
	}

}
