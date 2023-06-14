<?php

namespace XackiGiFF\MPEPerms\cmd;

use XackiGiFF\MPEPerms\MPEPerms;
use XackiGiFF\MPEPerms\permissions\MPEPermsPermissions;

use CortexPE\Commando\BaseCommand;

use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class Groups extends BaseCommand {
	/*
		MPEPerms by XackiGiFF (Remake by @mpe_coders from MPEPerms by #64FF00)

		╔═╗╔═╗╔═══╗╔═══╗     ╔═══╗╔═══╗╔═══╗╔═╗╔═╗╔═══╗
		║║╚╝║║║╔═╗║║╔══╝     ║╔═╗║║╔══╝║╔═╗║║║╚╝║║║╔═╗║
		║╔╗╔╗║║╚═╝║║╚══╗     ║╚═╝║║╚══╗║╚═╝║║╔╗╔╗║║╚══╗
		║║║║║║║╔══╝║╔══╝     ║╔══╝║╔══╝║╔╗╔╝║║║║║║╚══╗║
		║║║║║║║║───║╚══╗     ║║───║╚══╗║║║╚╗║║║║║║║╚═╝║
		╚╝╚╝╚╝╚╝───╚═══╝     ╚╝───╚═══╝╚╝╚═╝╚╝╚╝╚╝╚═══╝
	*/

	protected function prepare(): void {
		// This is where we'll register our arguments and subcommands

		$this->setPermission(MPEPermsPermissions::COMMAND_GROUPS_PERMISSION);
		
	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args): void {
		if(!$this->testPermission($sender)){
			return;
		}
		$result = [];

		foreach($this->getOwningPlugin()->getGroups() as $group){
			$result[] = $group->getName();
		}

		$sender->sendMessage(TextFormat::GREEN . MPEPerms::MAIN_PREFIX . ' ' . $this->getOwningPlugin()->getMessage("cmds.groups.messages.all_registered_groups", [implode(", ", $result)]));

		return;
    }
}