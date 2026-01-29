<?php

use Silverstripe\Opauth\Models\OpauthIdentity;

class OpauthMemberExtensionTest extends SapphireTest {

	protected $usesDatabase = true;

	protected $requiredExtensions = array(
		'Member' => array('OpauthMemberExtension')
	);

	public function testDeletesOpauthIdentityOnDelete(): void {
		$member = new Member(array('Email' => 'test@test.com'));
		$member->write();

		$identity = OpauthIdentity::create();
		$identity->write();
		$member->OpauthIdentities()->add($identity);

		$member->delete();

		$this->assertEquals(0, $member->OpauthIdentities()->Count());
	}

}
