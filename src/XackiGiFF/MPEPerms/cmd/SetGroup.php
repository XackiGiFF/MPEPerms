<?php

namespace XackiGiFF\MPEPerms\cmd;

use XackiGiFF\MPEPerms\MPEPerms;
use XackiGiFF\MPEPerms\permissions\MPEPermsPermissions;

use CortexPE\Commando\BaseCommand;

use CortexPE\Commando\args\RawStringArgument;

use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class SetGroup extends BaseCommand
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

	protected function prepare(): void {
		// This is where we'll register our arguments and subcommands

		$this->setPermission(MPEPermsPermissions::COMMAND_SETGROUP_PERMISSION);

	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args): void {
		if(!$this->testPermission($sender)){
			return;
		}

		if(count($args) < 2 || count($args) > 4){
			$sender->sendMessage(TextFormat::GREEN . MPEPerms::MAIN_PREFIX . ' ' . $this->getOwningPlugin()->getMessage("cmds.setgroup.usage"));

			return;
		}

		$player = $this->getOwningPlugin()->getPlayer($args[0]);

		$group = $this->getOwningPlugin()->getGroup($args[1]);

		if($group === null){
			$sender->sendMessage(TextFormat::RED . MPEPerms::MAIN_PREFIX . ' ' . $this->getOwningPlugin()->getMessage("cmds.setgroup.messages.group_not_exist", [$args[1]]));

			return;
		}

		if(isset($args[2])) {
			$expTime = $this->getOwningPlugin()->date2Int($args[2]);
			$sender->sendMessage("Время окончания: " .$expTime. ".");
		} else {
		    $expTime = -1;
		    $sender->sendMessage("не указано время. Бессрочно.");
		}
		$levelName = null;

		if(isset($args[3])){
			$level = $this->getOwningPlugin()->getServer()->getLevelByName($args[3]);

			if($level === null){
				$sender->sendMessage(TextFormat::RED . MPEPerms::MAIN_PREFIX . ' ' . $this->getOwningPlugin()->getMessage("cmds.setgroup.messages.level_not_exist", [$args[3]]));

				return;
			}

			$levelName = $level->getName();
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
    
    public function getPlugin() : Plugin
    {
        return $this->getOwningPlugin();
    }
}