<?php

declare(strict_types=1);

namespace XackiGiFF\MPEPerms\api\GroupSystem;

use pocketmine\player\IPlayer;
use pocketmine\world\World;
use XackiGiFF\MPEPerms\api\GroupSystem\group\Group;
use XackiGiFF\MPEPerms\MPEPerms;
use RuntimeException;

class GroupAPI {
	/*
		MPEPerms by XackiGiFF (Remake by @mpe_coders from MPEPerms by #64FF00)

		╔═╗╔═╗╔═══╗╔═══╗     ╔═══╗╔═══╗╔═══╗╔═╗╔═╗╔═══╗
		║║╚╝║║║╔═╗║║╔══╝     ║╔═╗║║╔══╝║╔═╗║║║╚╝║║║╔═╗║
		║╔╗╔╗║║╚═╝║║╚══╗     ║╚═╝║║╚══╗║╚═╝║║╔╗╔╗║║╚══╗
		║║║║║║║╔══╝║╔══╝     ║╔══╝║╔══╝║╔╗╔╝║║║║║║╚══╗║
		║║║║║║║║───║╚══╗     ║║───║╚══╗║║║╚╗║║║║║║║╚═╝║
		╚╝╚╝╚╝╚╝───╚═══╝     ╚╝───╚═══╝╚╝╚═╝╚╝╚╝╚╝╚═══╝
	*/

    const NOT_FOUND = null;
    const INVALID_NAME = -1;
    const ALREADY_EXISTS = 0;
    const SUCCESS = 1;

    private bool $isGroupsLoaded = false;
    private array $groups;

    public function __construct(protected MPEPerms $plugin){
    }

    public function addGroup($groupName): int{
        $groupsData = $this->plugin->getProvider()->getGroupsData();
        if(!$this->isValidGroupName($groupName))
            return self::INVALID_NAME;
        if(isset($groupsData[$groupName]))
            return self::ALREADY_EXISTS;
        $groupsData[$groupName] = [
            "alias" => "",
            "isDefault" => false,
            "inheritance" => [
            ],
            "permissions" => [
            ],
            "worlds" => [
            ]
        ];
        $this->plugin->getProvider()->setGroupsData($groupsData);
        $this->plugin->updateGroups();
        return self::SUCCESS;
    }

    public function getDefaultGroup($WorldName = null): Group|null{
        $defaultGroups = [];
        foreach($this->getGroups() as $defaultGroup)
        {
            if($defaultGroup->isDefault($WorldName))
                $defaultGroups[] = $defaultGroup;
        }

        if(count($defaultGroups) === 1)
        {
            return $defaultGroups[0];
        }
        else
        {
            if(count($defaultGroups) > 1)
            {
                $this->plugin->getLogger()->warning($this->plugin->getMessage("logger_messages.getDefaultGroup_01"));
            }
            elseif(count($defaultGroups) < 1)
            {
                $this->plugin->getLogger()->warning($this->plugin->getMessage("logger_messages.getDefaultGroup_02"));
            }

            $this->plugin->getLogger()->info($this->plugin->getMessage("logger_messages.getDefaultGroup_03"));

            foreach($this->getGroups() as $tempGroup)
            {
                if(count($tempGroup->getParentGroups()) === 0)
                {
                    $this->setDefaultGroup($tempGroup, $WorldName);

                    return $tempGroup;
                }
            }
        }

        return null;
    }

    public function getGroup($groupName): Group|null{
        if(!isset($this->groups[$groupName]))
        {
            /** @var Group $group */
            foreach($this->groups as $group)
            {
                if($group->getAlias() === $groupName)
                    return $group;
            }
            $this->plugin->getLogger()->debug($this->plugin->getMessage("logger_messages.getGroup_01", [$groupName]));
            return null;
        }

        /** @var Group $group */
        $group = $this->groups[$groupName];

        if(empty($group->getData()))
        {
            $this->plugin->getLogger()->warning($this->plugin->getMessage("logger_messages.getGroup_02", [$groupName]));
            return null;
        }

        return $group;
    }

    public function getGroups(): array{
            if($this->isGroupsLoaded !== true)
                throw new RuntimeException("No groups loaded, maybe a provider error?");
            return $this->groups;
    }

    public function getOnlinePlayersInGroup(Group $group): array{
        $users = [];
        foreach($this->plugin->getServer()->getOnlinePlayers() as $player)
        {
            foreach($this->plugin->getServer()->getWorldManager()->getWorlds() as $World)
            {
                $WorldName = $World->getDisplayName();
                if($this->plugin->getAPI()->getUserDataMgr()->getGroup($player, $WorldName) === $group)
                    $users[] = $player;
            }
        }

        return $users;
    }

    private function isValidGroupName($groupName): int|false{
        return preg_match('/[0-9a-zA-Z\xA1-\xFE]$/', $groupName);
    }

    public function removeGroup($groupName): int|null{
        if(!$this->isValidGroupName($groupName))
            return self::INVALID_NAME;
        $groupsData = $this->plugin->getProvider()->getGroupsData();
        if(!isset($groupsData[$groupName]))
            return self::NOT_FOUND;
        unset($groupsData[$groupName]);
        $this->plugin->getProvider()->setGroupsData($groupsData);
        $this->updateGroups();
        return self::SUCCESS;
    }

    public function sortGroupData(): void{
        foreach($this->getGroups() as $groupName => $mpGroup) {
            $mpGroup->sortPermissions();

            if($this->plugin->getAPI()->getConfigValue("enable-multiworld-perms")) {
                foreach($this->plugin->getServer()->getWorldManager()->getWorlds() as $World) {
                    $WorldName = $World->getDisplayName();
                    $mpGroup->createWorldData($WorldName);
                }
            }
        }
    }

	public function setDefaultGroup(Group $group, $levelName = null): void{
		foreach($this->getGroups() as $currentGroup){
			if($levelName === null){
				$isDefault = $currentGroup->getNode("isDefault");

				if($isDefault)
					$currentGroup->removeNode("isDefault");
			}else{
				$isDefault = $currentGroup->getWorldNode($levelName, "isDefault");

				if($isDefault)
					$currentGroup->removeWorldNode($levelName, "isDefault");
			}
		}

		$group->setDefault($levelName);
	}

    public function setGroup(IPlayer $player, Group $group, $WorldName = null, $time = -1): void
    {
        $this->plugin->getAPI()->getUserDataMgr()->setGroup($player, $group, $WorldName, $time);
    }

    public function updateGroups(): void{
        if(!$this->plugin->isValidProvider())
            throw new RuntimeException("Failed to load groups: Invalid data provider");
        // Make group list empty first to reload it
        $this->groups = [];
        foreach(array_keys($this->plugin->getProvider()->getGroupsData()) as $groupName)
        {
            $this->groups[$groupName] = new Group($this->plugin, $groupName);
        }
        if(empty($this->groups))
            throw new RuntimeException("No groups found, I guess there's definitely something wrong with your data provider... *cough cough*");
        $this->isGroupsLoaded = true;
        $this->sortGroupData();
    }

    public function updatePlayersInGroup(Group $group): void{
        foreach($this->plugin->getServer()->getOnlinePlayers() as $player)
        {
            if($this->plugin->getAPI()->getUserDataMgr()->getGroup($player) === $group)
                $this->plugin->updatePermissions($player);
        }
    }


}