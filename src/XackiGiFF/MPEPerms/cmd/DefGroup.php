<?php

namespace XackiGiFF\MPEPerms\cmd;

use XackiGiFF\MPEPerms\MPEPerms;
use XackiGiFF\MPEPerms\permissions\MPEPermsPermissions;

use CortexPE\Commando\BaseCommand;

use CortexPE\Commando\args\RawStringArgument;

use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class DefGroup extends BaseCommand
{
	/*
		MPEPerms by XackiGiFF (Remake by @mpe_coders from MPEPerms by #64FF00)

		╔═╗╔═╗╔═══╗╔═══╗     ╔═══╗╔═══╗╔═══╗╔═╗╔═╗╔═══╗
		║║╚╝║║║╔═╗║║╔══╝     ║╔═╗║║╔══╝║╔═╗║║║╚╝║║║╔═╗║
		║╔╗╔╗║║╚═╝║║╚══╗     ║╚═╝║║╚══╗║╚═╝║║╔╗╔╗║║╚══╗
		║║║║║║║╔══╝║╔══╝     ║╔══╝║╔══╝║╔╗╔╝║║║║║║╚══╗║
		║║║║║║║║───║╚══╗     ║║───║╚══╗║║║╚╗║║║║║║║╚═╝║
		╚╝╚╝╚╝╚╝───╚═══╝     ╚╝───╚═══╝╚╝╚═╝╚╝╚╝╚╝╚═══╝
	*/

	protected const ARGUMENT_GROUP_NAME = "group_name";
	protected const ARGUMENT_WORLD_NAME = "world_name";

	protected function prepare(): void {
		// This is where we'll register our arguments and subcommands

		$this->setPermission(MPEPermsPermissions::COMMAND_DEFGROUP_PERMISSION);
		try {
			$this->registerArgument(0, new RawStringArgument(DefGroup::ARGUMENT_GROUP_NAME));
            $this->registerArgument(1, new RawStringArgument(DefGroup::ARGUMENT_WORLD_NAME, true));
        } catch (Exception) {
        }
		$this->setErrorFormat(0x01, TextFormat::YELLOW . MPEPerms::MAIN_PREFIX . $this->getOwningPlugin()->getMessage("cmds.defgroup.usage"));
		$this->setErrorFormat(0x02, TextFormat::YELLOW . MPEPerms::MAIN_PREFIX . $this->getOwningPlugin()->getMessage("cmds.defgroup.usage"));
		$this->setErrorFormat(0x03, TextFormat::YELLOW . MPEPerms::MAIN_PREFIX . $this->getOwningPlugin()->getMessage("cmds.defgroup.usage"));

	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args): void {
		if(!$this->testPermission($sender)){
			return;
		}

		$group_name = $this->getOwningPlugin()->getGroup($args["group_name"]);
		$world_name = (isset($args["world_name"])) ? $args["world_name"] : null;


		if($group_name === null){
			$sender->sendMessage(TextFormat::RED . MPEPerms::MAIN_PREFIX . ' ' . $this->getOwningPlugin()->getMessage("cmds.defgroup.messages.group_not_exist", [$args["group_name"]]));

			return;
		}

		$levelName = null;

		if(isset($world_name)){
			$level = $this->getOwningPlugin()->getServer()->getWorldManager()->getWorldByName($world_name);

			if($level === null){
				$sender->sendMessage(TextFormat::RED . MPEPerms::MAIN_PREFIX . ' ' . $this->getOwningPlugin()->getMessage("cmds.defgroup.messages.level_not_exist", [$args["world_name"]]));

				return;
			}

			$levelName = $level->getFolderName();
			var_dump($levelName);
		}

		$this->getOwningPlugin()->setDefaultGroup($group_name, $levelName);

		$sender->sendMessage(TextFormat::GREEN . MPEPerms::MAIN_PREFIX . ' ' . $this->getOwningPlugin()->getMessage("cmds.defgroup.messages.defgroup_successfully", [$args["group_name"]]));

		return;
    }
}