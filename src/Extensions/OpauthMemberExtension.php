<?php
namespace Silverstripe\Opauth\Extensions;


use Silverstripe\Opauth\Models\OpauthIdentity;
use SilverStripe\ORM\DataExtension;

class OpauthMemberExtension extends DataExtension
{
    private static $has_many = array(
        "OpauthIdentities" => OpauthIdentity::class
    );

    public function onBeforeDelete()
    {
        $this->owner->OpauthIdentities()->removeAll();
    }
}
