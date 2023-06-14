<?php

namespace XackiGiFF\MPEPerms\cmd;

use XackiGiFF\MPEPerms\MPEPerms;
use XackiGiFF\MPEPerms\permissions\MPEPermsPermissions;

use CortexPE\Commando\BaseCommand;

use CortexPE\Commando\args\RawStringArgument;

use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class SetGroup extends BaseCommand {
	/*
		MPEPerms by XackiGiFF (Remake by @mpe_coders from MPEPerms by #64FF00)

		╔═╗╔═╗╔═══╗╔═══╗     ╔═══╗╔═══╗╔═══╗╔═╗╔═╗╔═══╗
		║║╚╝║║║╔═╗║║╔══╝     ║╔═╗║║╔══╝║╔═╗║║║╚╝║║║╔═╗║
		║╔╗╔╗║║╚═╝║║╚══╗     ║╚═╝║║╚══╗║╚═╝║║╔╗╔╗║║╚══╗
		║║║║║║║╔══╝║╔══╝     ║╔══╝║╔══╝║╔╗╔╝║║║║║║╚══╗║
		║║║║║║║║───║╚══╗     ║║───║╚══╗║║║╚╗║║║║║║║╚═╝║
		╚╝╚╝╚╝╚╝───╚═══╝     ╚╝───╚═══╝╚╝╚═╝╚╝╚╝╚╝╚═══╝
	*/

	protected const ARGUMENT_PLAYER_NAME = "player_name";
	protected const ARGUMENT_GROUP_NAME  = "group_name";
	protected const ARGUMENT_EXP_TIME = "exp_time";
	protected const ARGUMENT_WORLD_NAME  = "world_name";

	protected function prepare(): void {
		// This is where we'll register our arguments and subcommands

		$this->setPermission(MPEPermsPermissions::COMMAND_SETGROUP_PERMISSION);
		try {
			$this->registerArgument(0, new RawStringArgument(SetGroup::ARGUMENT_PLAYER_NAME));
            $this->registerArgument(1, new RawStringArgument(SetGroup::ARGUMENT_GROUP_NAME));
			$this->registerArgument(2, new RawStringArgument(SetGroup::ARGUMENT_EXP_TIME, true));
			$this->registerArgument(3, new RawStringArgument(SetGroup::ARGUMENT_WORLD_NAME, true));
        } catch (Exception) {
        }
		$this->setErrorFormat(0x01, TextFormat::YELLOW . MPEPerms::MAIN_PREFIX . $this->getOwningPlugin()->getMessage("cmds.setgroup.usage"));
		$this->setErrorFormat(0x02, TextFormat::YELLOW . MPEPerms::MAIN_PREFIX . $this->getOwningPlugin()->getMessage("cmds.setgroup.usage"));
		$this->setErrorFormat(0x03, TextFormat::YELLOW . MPEPerms::MAIN_PREFIX . $this->getOwningPlugin()->getMessage("cmds.setgroup.usage"));

	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args): void {
		if(!$this->testPermission($sender)){
			return;
		}

		$player = $this->getOwningPlugin()->getPlayer($args["player_name"]);

		$group = $this->getOwningPlugin()->getGroup($args["group_name"]);

		if($group === null){
			$sender->sendMessage(TextFormat::RED . MPEPerms::MAIN_PREFIX . ' ' . $this->getOwningPlugin()->getMessage("cmds.setgroup.messages.group_not_exist", [$args["group_name"]]));

			return;
		}

		if(isset($args["exp_time"])) {
			$expTime = $this->getOwningPlugin()->date2Int($args["exp_time"]); // Format [0-9]d[0-9]h[0-9]m // Examlpe: 1d23h59m - is 1 Day, 23 Hour and 59 minutes
			$sender->sendMessage("Время окончания: " .$expTime. ".");
		} else {
		    $expTime = -1;
		    $sender->sendMessage("Не указано время. Бессрочно.");
		}
		$levelName = null;

		if(isset($args["world_name"])){
			$level = $this->getOwningPlugin()->getServer()->getWorldManager()->getWorldByName($args["world_name"]);

			if($level === null){
				$sender->sendMessage(TextFormat::RED . MPEPerms::MAIN_PREFIX . ' ' . $this->getOwningPlugin()->getMessage("cmds.setgroup.messages.level_not_exist", [$args["world_name"]]));

				return;
			}

			$levelName = $level->getFolderName();
		}

		$superAdminRanks = $this->getOwningPlugin()->getConfigValue("superadmin-ranks");

		foreach(array_values($superAdminRanks) as $value){
			$tmpSuperAdminRanks[$value] = 1;
		}

		if(!($sender instanceof ConsoleCommandSender)){
			if(isset($tmpSuperAdminRanks[$group->getName()])){
				$sender->sendMessage(TextFormat::RED . MPEPerms::MAIN_PREFIX . ' ' . $this->getOwningPlugin()->getMessage("cmds.setgroup.messages.access_denied_01", [$group->getName()]));

				return;
			}

			$userGroup = $this->getOwningPlugin()->getUserDataMgr()->getGroup($player, $levelName);

			if(isset($tmpSuperAdminRanks[$userGroup->getName()])){
				$sender->sendMessage(TextFormat::RED . MPEPerms::MAIN_PREFIX . ' ' . $this->getOwningPlugin()->getMessage("cmds.setgroup.messages.access_denied_02", [$userGroup->getName()]));

				return;
			}
		}

		$this->getOwningPlugin()->getUserDataMgr()->setGroup($player, $group, $levelName, $expTime);

		$sender->sendMessage(TextFormat::GREEN . MPEPerms::MAIN_PREFIX . ' ' . $this->getOwningPlugin()->getMessage("cmds.setgroup.messages.setgroup_successfully", [$player->getName()]));

		if($player instanceof Player){
			if(!$this->getOwningPlugin()->getConfigValue("enable-multiworld-perms") || ($this->getOwningPlugin()->getConfigValue("enable-multiworld-perms") and $levelName === $player->getWorld()->getFolderName()))
				$player->sendMessage(TextFormat::GREEN . MPEPerms::MAIN_PREFIX . ' ' . $this->getOwningPlugin()->getMessage("cmds.setgroup.messages.on_player_group_change", [strtolower($group->getName())]));
		}

		return;
    }
}
