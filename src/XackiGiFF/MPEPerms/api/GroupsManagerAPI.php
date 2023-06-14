<?php

declare(strict_types=1);

namespace XackiGiFF\MPEPerms\api;

use XackiGiFF\MPEPerms\MPEPerms;
use XackiGiFF\MPEPerms\MPGroup;
use RuntimeException;

class GroupsManagerAPI {
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

    private $isGroupsLoaded = false;
    private $groups;

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

    public function getGroups(): array{
            if($this->isGroupsLoaded !== true)
                throw new RuntimeException("No groups loaded, maybe a provider error?");
            return $this->groups;
    }

    private function isValidGroupName($groupName): int|false{
        return preg_match('/[0-9a-zA-Z\xA1-\xFE]$/', $groupName);
    }

    public function removeGroup($groupName): int{
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

            if($this->plugin->getConfigValue("enable-multiworld-perms")) {
                /** @var World $World */
                foreach($this->plugin->getServer()->getWorldManager()->getWorlds() as $World) {
                    $WorldName = $World->getDisplayName();
                    $mpGroup->createWorldData($WorldName);
                }
            }
        }
    }

    public function updateGroups(): void{
        if(!$this->plugin->isValidProvider())
            throw new RuntimeException("Failed to load groups: Invalid data provider");
        // Make group list empty first to reload it
        $this->groups = [];
        foreach(array_keys($this->plugin->getProvider()->getGroupsData()) as $groupName)
        {
            $this->groups[$groupName] = new MPGroup($this->plugin, $groupName);
        }
        if(empty($this->groups))
            throw new RuntimeException("No groups found, I guess there's definitely something wrong with your data provider... *cough cough*");
        $this->isGroupsLoaded = true;
        $this->sortGroupData();
    }





}