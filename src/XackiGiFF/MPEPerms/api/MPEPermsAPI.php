<?php

declare(strict_types=1);

namespace XackiGiFF\MPEPerms\api;

use XackiGiFF\MPEPerms\MPEPerms;
use XackiGiFF\MPEPerms\MPGroup;


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

	private $manager;
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
// GroupsAPI
//

	public function addGroup($groupName): int{
		return $this->manager->getGroupManagerAPI()->addGroup($groupName);
	}

	public function getGroups(): array{
		return $this->manager->getGroupManagerAPI()->getGroups();
	}

	public function isValidGroupName(): int|false{
		return $this->manager->getGroupManagerAPI()->isValidGroupName();
	}

	public function removeGroup($groupName): int{
		return $this->manager->getGroupManagerAPI()->removeGroup($groupName);
	}

	public function sortGroupData(): void{
		$this->manager->getGroupManagerAPI()->sortGroupData();
	}

	public function updateGroups(): void{
		$this->manager->getGroupManagerAPI()->updateGroups();
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