<?php

declare(strict_types=1);

namespace XackiGiFF\MPEPerms\api;

use pocketmine\player\IPlayer;
use XackiGiFF\MPEPerms\MPEPerms;
use XackiGiFF\MPEPerms\api\GroupSystem\group\Group;
use XackiGiFF\MPEPerms\api\LoaderManagerAPI;
use XackiGiFF\MPEPerms\api\GroupSystem\player\UserDataManagerAPI;


class MPEPermsAPI {
	/*
		MPEPerms by XackiGiFF (Remake by @mpe_coders from MPEPerms by #64FF00)

		╔═╗╔═╗╔═══╗╔═══╗     ╔═══╗╔═══╗╔═══╗╔═╗╔═╗╔═══╗
		║║╚╝║║║╔═╗║║╔══╝     ║╔═╗║║╔══╝║╔═╗║║║╚╝║║║╔═╗║
		║╔╗╔╗║║╚═╝║║╚══╗     ║╚═╝║║╚══╗║╚═╝║║╔╗╔╗║║╚══╗
		║║║║║║║╔══╝║╔══╝     ║╔══╝║╔══╝║╔╗╔╝║║║║║║╚══╗║
		║║║║║║║║───║╚══╗     ║║───║╚══╗║║║╚╗║║║║║║║╚═╝║
		╚╝╚╝╚╝╚╝───╚═══╝     ╚╝───╚═══╝╚╝╚═╝╚╝╚╝╚╝╚═══╝
	*/

	private LoaderManagerAPI $manager;
    public function __construct(protected MPEPerms $plugin) {
		$this->manager = new LoaderManagerAPI($this->plugin);
    }

//
// Start System Service
//

	public function startService(): void{
		$this->manager->startService();
	}

	public function registerCommands(): void{
		$this->manager->getCommandsRegisterAPI()->registerCommands();
    }

	public function getPlugin() {
        return $this->plugin;
    }

//
// CommandsAPI
//

	public function getCommands(): array{
			return $this->manager->getCommandsRegisterAPI()->getCommands();
    }

//
// ServiceGroupAPI
//

	public function getUserDataMgr(): UserDataManagerAPI{
		return $this->manager->getUserDataMgr();
	}

//
// ServiceRankAPI
//



//
// GroupsAPI
//

	public function addGroup($groupName): int{
		return $this->manager->getGroupAPI()->addGroup($groupName);
	}

	public function getDefaultGroup($WorldName = null): Group|null{
		return $this->manager->getGroupAPI()->getDefaultGroup($WorldName);
	}

	public function getGroup($groupName): Group|null{
		return $this->manager->getGroupAPI()->getGroup($groupName);
	}

	public function getGroups(): array{
		return $this->manager->getGroupAPI()->getGroups();
	}

	public function getOnlinePlayersInGroup(Group $group): array{
		return $this->manager->getGroupAPI()->getOnlinePlayersInGroup($group);
	}

	public function isValidGroupName(): int|false{
		return $this->manager->getGroupAPI()->isValidGroupName();
	}

	public function removeGroup($groupName): int{
		return $this->manager->getGroupAPI()->removeGroup($groupName);
	}

	public function sortGroupData(): void{
		$this->manager->getGroupAPI()->sortGroupData();
	}

	public function setDefaultGroup(Group $group, $levelName = null): void{
		$this->manager->getGroupAPI()->sortGroupData($group, $levelName = null);
	}

	public function setGroup(IPlayer $player, Group $group, $WorldName = null, $time = -1): void
    {
		$this->manager->getGroupAPI()->setGroup($player, $group, $WorldName, $time);
	}

	public function updateGroups(): void{
		$this->manager->getGroupAPI()->updateGroups();
	}

	public function updatePlayersInGroup(Group $group): void{
		$this->manager->getGroupAPI()->updatePlayersInGroup($group);
	}

//
// UtilsAPI
//

	public function getUtils() {
		return $this->manager->getUtilsAPI();
	}

//
// Configs & Providers
//

	public function getConfigValue($key){
		$value = $this->plugin->getConfig()->getNested($key);
		if($value === null)
		{
			$this->plugin->getLogger()->warning($this->getMessage("logger_messages.getConfigValue_01", [$key]));

			return null;
		}

		return $value;
	}

	public function fixConfig(): void{
		$config = $this->getPlugin()->getConfig();

		if(!$config->exists("default-language"))
			$config->set("default-language", "ru");

		if(!$config->exists("disable-op"))
			$config->set("disable-op", false);

		if(!$config->exists("enable-multiworld-perms"))
			$config->set("enable-multiworld-perms", false);

		if(!$config->exists("enable-noeul-sixtyfour"))
			$config->set("enable-noeul-sixtyfour", false);

		if(!$config->exists("noeul-minimum-pw-length"))
			$config->set("noeul-minimum-pw-length", 6);

		if(!$config->exists("superadmin-ranks"))
			$config->set("superadmin-ranks", ["OP"]);

		$this->getPlugin()->saveConfig();
		$this->getPlugin()->reloadConfig();
	}

}