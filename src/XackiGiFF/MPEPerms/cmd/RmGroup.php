<?php

namespace XackiGiFF\MPEPerms\cmd;

use XackiGiFF\MPEPerms\MPEPerms;
use XackiGiFF\MPEPerms\permissions\MPEPermsPermissions;

use CortexPE\Commando\BaseCommand;
use CortexPE\Commando\args\RawStringArgument;

use pocketmine\command\CommandSender;

class RmGroup extends BaseCommand
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

	protected const ARGUMENT_GROUP_NAME = "group";

	protected function prepare(): void {
		// This is where we'll register our arguments and subcommands

		$this->setPermission(MPEPermsPermissions::COMMAND_RMGROUP_PERMISSION);
		try {
            $this->registerArgument(0, new RawStringArgument(RmGroup::ARGUMENT_GROUP_NAME));
        } catch (Exception) {
        }
		$this->setErrorFormat(0x02, $this->getTemplate("cmds.rmgroup.usage", true) );
		$this->setErrorFormat(0x03, $this->getTemplate("cmds.rmgroup.usage", true) );
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
        if(!isset($args[0]) || count($args) > 1)
        {
            $sender->sendMessage(TextFormat::GREEN . MPEPerms::MAIN_PREFIX . ' ' . $this->plugin->getMessage("cmds.rmgroup.usage"));
            return true;
        }

        $result = $this->plugin->removeGroup($args[0]);
        if($result === MPEPerms::SUCCESS)
        {
            $sender->sendMessage(TextFormat::GREEN . MPEPerms::MAIN_PREFIX . ' ' . $this->plugin->getMessage("cmds.rmgroup.messages.groups_removed_successfully", $args[0]));
        }
        elseif($result === MPEPerms::INVALID_NAME)
        {
            $sender->sendMessage(TextFormat::RED . MPEPerms::MAIN_PREFIX . ' ' . $this->plugin->getMessage("cmds.rmgroup.messages.invalid_groups_name", $args[0]));
        }
        else
        {
            $sender->sendMessage(TextFormat::RED . MPEPerms::MAIN_PREFIX . ' ' . $this->plugin->getMessage("cmds.rmgroup.messages.groups_not_exist", $args[0]));
        }
        
        return true;
    }
    
    public function getPlugin() : Plugin
    {
        return $this->plugin;
    }
}