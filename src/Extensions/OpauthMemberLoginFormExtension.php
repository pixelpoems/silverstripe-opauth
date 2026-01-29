<?php

declare(strict_types=1);

namespace Silverstripe\Opauth\Extensions;

use SilverStripe\Core\Config\Config;
use SilverStripe\Core\Extension;
use Silverstripe\Opauth\Models\OpauthIdentity;
use SilverStripe\Security\Member;

class OpauthMemberLoginFormExtension extends Extension
{

    /**
     * @config
     */
    private static bool $allow_password_reset = true;

    /**
     * Deny password resets
     */
    public function forgotPassword(Member $member): ?bool
    {
        if (Config::inst()->get('OpauthMemberLoginFormExtension', 'allow_password_reset')) {
            return null;
        }

        $identity = OpauthIdentity::get()->find('MemberID', $member->ID);
        if (!$member->Password && $identity) {
            $this->getOwner()->sessionMessage(
                _t(
                    'OpauthMemberLoginFormExtension.NoResetPassword',
                    "Can't reset password for accounts registered through {provider}",
                    array('provider' => $identity->Provider)
                ),
                'bad'
            );
            return false;
        }
        return null;
    }
}
