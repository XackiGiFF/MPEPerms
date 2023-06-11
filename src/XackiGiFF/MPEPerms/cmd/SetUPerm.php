<?php

namespace XackiGiFF\MPEPerms\cmd;

use XackiGiFF\MPEPerms\MPEPerms;
use XackiGiFF\MPEPerms\permissions\MPEPermsPermissions;

use CortexPE\Commando\BaseCommand;
use CortexPE\Commando\args\RawStringArgument;

use pocketmine\command\CommandSender;

class SetUPerm extends BaseCommand
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

		$this->setPermission(MPEPermsPermissions::COMMAND_SETUPERM_PERMISSION);

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
            return false;
        if(count($args) < 2 || count($args) > 3)
        {
            $sender->sendMessage(TextFormat::GREEN . MPEPerms::MAIN_PREFIX . ' ' . $this->plugin->getMessage("cmds.setuperm.usage"));
            return true;
        }
        
        $player = $this->plugin->getPlayer($args[0]);
        $permission = $args[1];
        $WorldName = null;
        if(isset($args[2]))
        {
            $world = $this->plugin->getServer()->getWorldManager()->getWorldByName($args[2]);
            if($world === null)
            {
                $sender->sendMessage(TextFormat::RED . MPEPerms::MAIN_PREFIX . ' ' . $this->plugin->getMessage("cmds.setuperm.messages.level_not_exist", $args[2]));
                return true;
            }

            $WorldName = $world->getDisplayName();
        }
        
        $this->plugin->getUserDataMgr()->setPermission($player, $permission, $WorldName);
        $sender->sendMessage(TextFormat::GREEN . MPEPerms::MAIN_PREFIX . ' ' . $this->plugin->getMessage("cmds.setuperm.messages.uperm_added_successfully", $permission, $player->getName()));
        return true;
    }
    
    public function getPlugin() : Plugin
    {
        return $this->plugin;
    }
}