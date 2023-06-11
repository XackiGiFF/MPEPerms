<?php

namespace XackiGiFF\MPEPerms\cmd;

use XackiGiFF\MPEPerms\MPEPerms;
use XackiGiFF\MPEPerms\permissions\MPEPermsPermissions;

use CortexPE\Commando\BaseCommand;
use CortexPE\Commando\args\RawStringArgument;

use pocketmine\command\CommandSender;

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
		$this->setErrorFormat(0x01, $this->getOwningPlugin()->getAPI()->getTemplate("cmds.defgroup.usage", true));
		$this->setErrorFormat(0x02, $this->getOwningPlugin()->getAPI()->getTemplate("cmds.defgroup.usage", true));
		$this->setErrorFormat(0x03, $this->getOwningPlugin()->getAPI()->getTemplate("cmds.defgroup.usage", true));

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

        if(!isset($args["group_name"]) || count($args) > 2)
        {
            $this->getOwningPlugin()->getAPI()->sendTemplate("cmds.defgroup.usage");
            return true;
        }

        $group = $this->plugin->getGroup($args["group_name"]);
        if($group === null)
        {
            $this->getOwningPlugin()->getAPI()->sendTemplate("cmds.defgroup.messages.group_not_exist", false, $args["group_name"]);
            return true;
        }
        $WorldName = null;
        if(isset($args["world_name"]))
        {
            $World = $this->plugin->getServer()->getWorldManager()->getWorldByName($args["world_name"]);
            if($World === null)
            {
                $this->getOwningPlugin()->getAPI()->sendTemplate("cmds.defgroup.messages.level_not_exist", false, $args["world_name"]);
                return true;
            }

            $WorldName = $World->getDisplayName();
        }
        $this->plugin->setDefaultGroup($group, $WorldName);
        $this->getOwningPlugin()->getAPI()->sendTemplate("cmds.defgroup.messages.defgroup_successfully", true, $args["group_name"]);
        
        return true;
    }
    
    public function getPlugin() : Plugin
    {
        return $this->plugin;
    }
}