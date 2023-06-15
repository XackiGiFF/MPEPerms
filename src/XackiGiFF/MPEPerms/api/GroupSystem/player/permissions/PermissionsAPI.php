<?php

declare(strict_types=1);

namespace XackiGiFF\MPEPerms\api\GroupSystem\player\permissions;

use XackiGiFF\MPEPerms\MPEPerms;
use XackiGiFF\MPEPerms\api\GroupSystem\group\Group;

class PermissionsAPI {

    public function getPermissions(IPlayer $player, $WorldName): array{
        // TODO: Fix this
        $group = $this->getUserDataMgr()->getGroup($player, $WorldName);
        $groupPerms = $group->getGroupPermissions($WorldName);
        $userPerms = $this->getUserDataMgr()->getUserPermissions($player, $WorldName);

        return array_merge($groupPerms, $userPerms);
    }

}