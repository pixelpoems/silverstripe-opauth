<?php

declare(strict_types=1);

namespace Silverstripe\Opauth\Extensions;


use SilverStripe\Core\Extension;
use Silverstripe\Opauth\Models\OpauthIdentity;

class OpauthMemberExtension extends Extension
{
    private static array $has_many = array(
        "OpauthIdentities" => OpauthIdentity::class
    );

    public function onBeforeDelete(): void
    {
        $this->getOwner()->OpauthIdentities()->removeAll();
    }
}
