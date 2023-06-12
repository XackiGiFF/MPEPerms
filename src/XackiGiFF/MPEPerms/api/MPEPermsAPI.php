<?php

declare(strict_types=1);

namespace XackiGiFF\MPEPerms\api;

use XackiGiFF\MPEPerms\MPEPerms;
use XackiGiFF\MPEPerms\api\CommandsRegisterAPI;

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
	private $cmds;

    public function __construct(protected MPEPerms $plugin) {
		$this->cmds = new CommandsRegisterAPI($this->plugin);
    }

	public function getPlugin() {
        return $this->plugin;
    }

    public function registerCommands(): void {
		$this->cmds->registerCommands();
    }

	public function getCommands(){  // TODO
			return array (
				"addgroup" => 
					array("class" => "AddGroup",
						  "desc" => "cmds.addgroup.desc"),
				"defgroup" => 
					array("class" => "DefGroup",
						  "desc" => "cmds.defgroup.desc"),
				"fperms" => 
					array("class" => "FPerms",
						  "desc" => "cmds.fperms.desc"),
				"groups" => 
					array("class" => "Groups",
						  "desc" => "cmds.groups.desc"),
				"ppinfo" => 
					array("class" => "MPInfo",
						  "desc" => "cmds.ppinfo.desc"),
				"rmgroup" => 
					array("class" => "RmGroup",
						  "desc" => "cmds.rmgroup.desc"),
				"setgroup" => 
					array("class" => "SetGroup",
						  "desc" => "cmds.setgroup.desc"),
			);
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