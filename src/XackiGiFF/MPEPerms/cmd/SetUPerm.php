<?php

namespace XackiGiFF\MPEPerms\cmd;

use XackiGiFF\MPEPerms\MPEPerms;
use XackiGiFF\MPEPerms\permissions\MPEPermsPermissions;

use CortexPE\Commando\BaseCommand;

use CortexPE\Commando\args\RawStringArgument;

use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

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

	public function onRun(CommandSender $sender, string $aliasUsed, array $args): void {
		// This is where the processing will occur if it's NOT handled by other subcommands
        if(!$this->testPermission($sender))
            return false;
        if(count($args) < 2 || count($args) > 3)
        {
            $sender->sendMessage(TextFormat::RED . MPEPerms::MAIN_PREFIX . ' ' . $this->getOwningPlugin()->getMessage("cmds.setuperm.usage"));
            return true;
        }
        
        $player = $this->getOwningPlugin()->getPlayer($args[0]);
        $permission = $args[1];
        $WorldName = null;
        if(isset($args[2]))
        {
            $world = $this->getOwningPlugin()->getServer()->getWorldManager()->getWorldByName($args[2]);
            if($world === null)
            {
                $sender->sendMessage(TextFormat::RED . MPEPerms::MAIN_PREFIX . ' ' . $this->getOwningPlugin()->getMessage("cmds.setuperm.messages.level_not_exist", $args[2]));
                return true;
            }

            $WorldName = $world->getDisplayName();
        }
        
        $this->getOwningPlugin()->getUserDataMgr()->setPermission($player, $permission, $WorldName);
        $sender->sendMessage(TextFormat::RED . MPEPerms::MAIN_PREFIX . ' ' . $this->getOwningPlugin()->getMessage("cmds.setuperm.messages.uperm_added_successfully", $permission, $player->getName()));
        return true;
    }
    
    public function getPlugin() : Plugin
    {
        return $this->getOwningPlugin();
    }
}