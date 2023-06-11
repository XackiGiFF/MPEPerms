<?php

namespace XackiGiFF\MPEPerms\cmd;

use XackiGiFF\MPEPerms\MPEPerms;
use XackiGiFF\MPEPerms\permissions\MPEPermsPermissions;

use CortexPE\Commando\BaseCommand;
use CortexPE\Commando\args\RawStringArgument;

use pocketmine\command\CommandSender;

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

    /**
     * @param CommandSender $sender
     * @param $label
     * @param array $args
     * @return bool
     */
    public function execute(CommandSender $sender, string $label, array $args) : bool
    {
        if(!$this->testPermission($sender))
        {
            return false;
        }
        
        if(count($args) < 2 || count($args) > 4)
        {
            $sender->sendMessage(TextFormat::GREEN . MPEPerms::MAIN_PREFIX . ' ' . $this->plugin->getMessage("cmds.setgroup.usage"));
            return true;
        }
        $player = $this->plugin->getPlayer($args[0]);
        $group = $this->plugin->getGroup($args[1]);
        if($group === null)
        {
            $sender->sendMessage(TextFormat::RED . MPEPerms::MAIN_PREFIX . ' ' . $this->plugin->getMessage("cmds.setgroup.messages.group_not_exist", $args[1]));
            return true;
        }

        $expTime = -1;
        if(isset($args[2]))
            $expTime = $this->plugin->date2Int($args[2]);
        $WorldName = null;
        if(isset($args[3]))
        {
            $world = $this->plugin->getServer()->getWorldManager()->getWorldByName($args[3]);
            if($world === null)
            {
                $sender->sendMessage(TextFormat::RED . MPEPerms::MAIN_PREFIX . ' ' . $this->plugin->getMessage("cmds.setgroup.messages.level_not_exist", $args[3]));
                return true;
            }

            $WorldName = $world->getDisplayName();
        }

        $superAdmingroups = $this->plugin->getConfigValue("superadmin-groups");
        foreach(array_values($superAdmingroups) as $value)
        {
            $tmpSuperAdmingroups[$value] = 1;
        }

        if(!($sender instanceof ConsoleCommandSender))
        {
            if(isset($tmpSuperAdmingroups[$group->getName()]))
            {
                $sender->sendMessage(TextFormat::RED . MPEPerms::MAIN_PREFIX . ' ' . $this->plugin->getMessage("cmds.setgroup.messages.access_denied_01", $group->getName()));
                return true;
            }

            $userGroup = $this->plugin->getUserDataMgr()->getGroup($player, $WorldName);
            if(isset($tmpSuperAdmingroups[$userGroup->getName()]))
            {
                $sender->sendMessage(TextFormat::RED . MPEPerms::MAIN_PREFIX . ' ' . $this->plugin->getMessage("cmds.setgroup.messages.access_denied_02", $userGroup->getName()));
                return true;
            }
        }

        $this->plugin->getUserDataMgr()->setGroup($player, $group, $WorldName, $expTime);
        
        $sender->sendMessage(TextFormat::GREEN . MPEPerms::MAIN_PREFIX . ' ' . $this->plugin->getMessage("cmds.setgroup.messages.setgroup_successfully", $player->getName()));
        
        if($player instanceof Player)
        {
            if(!$this->plugin->getConfigValue("enable-multiworld-perms") || ($this->plugin->getConfigValue("enable-multiworld-perms") and $WorldName === $player->getWorld()->getDisplayName()))
                $player->sendMessage(TextFormat::GREEN . MPEPerms::MAIN_PREFIX . ' ' . $this->plugin->getMessage("cmds.setgroup.messages.on_player_group_change", strtolower($group->getName())));
        }

        return true;
    }
    
    public function getPlugin() : Plugin
    {
        return $this->plugin;
    }
}