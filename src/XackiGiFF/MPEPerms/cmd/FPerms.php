<?php

namespace XackiGiFF\MPEPerms\cmd;

use XackiGiFF\MPEPerms\MPEPerms;
use XackiGiFF\MPEPerms\permissions\MPEPermsPermissions;

use CortexPE\Commando\BaseCommand;
use CortexPE\Commando\args\IntegerArgument;
use CortexPE\Commando\args\RawStringArgument;

use pocketmine\command\CommandSender;

class FPerms extends BaseCommand
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

	protected const ARGUMENT_PLUGIN_NAME = "plugin_name";
    protected const ARGUMENT_PAGE = "page";

	protected function prepare(): void {
		// This is where we'll register our arguments and subcommands

		$this->setPermission(MPEPermsPermissions::COMMAND_FPERMS_PERMISSION);
		try {
			$this->registerArgument(0, new RawStringArgument(FPerms::ARGUMENT_PLUGIN_NAME));
            $this->registerArgument(1, new IntegerArgument(FPerms::ARGUMENT_PAGE, true));
        } catch (Exception) {
        }
		$this->setErrorFormat(0x01, TextFormat::GREEN . MPEPerms::MAIN_PREFIX . ' ' . $this->getOwningPlugin()->getMessage("cmds.fperms.usage"));
		$this->setErrorFormat(0x02, TextFormat::GREEN . MPEPerms::MAIN_PREFIX . ' ' . $this->getOwningPlugin()->getMessage("cmds.fperms.usage"));
		$this->setErrorFormat(0x03, TextFormat::GREEN . MPEPerms::MAIN_PREFIX . ' ' . $this->getOwningPlugin()->getMessage("cmds.fperms.usage"));

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
        if(!isset($args[0]) || count($args) > 2)
        {
            $sender->sendMessage(TextFormat::GREEN . MPEPerms::MAIN_PREFIX . ' ' . $this->plugin->getMessage("cmds.plperms.usage"));
            return true;
        }
        
        $plugin = (strtolower($args[0]) === 'pocketmine' || strtolower($args[0]) === 'pmmp') ? 'pocketmine' : $this->plugin->getServer()->getPluginManager()->getPlugin($args[0]);
        if($plugin === null)
        {
            $sender->sendMessage(TextFormat::RED . MPEPerms::MAIN_PREFIX . ' ' . $this->plugin->getMessage("cmds.plperms.messages.plugin_not_exist", $args[0]));
            return true;
        }
        
        $permissions = ($plugin instanceof PluginBase) ? $plugin->getDescription()->getPermissions() : $this->plugin->getPocketMinePerms();
        if(empty($permissions))
        {
            $sender->sendMessage(TextFormat::GREEN . MPEPerms::MAIN_PREFIX . ' ' . $this->plugin->getMessage("cmds.plperms.messages.no_plugin_perms", $plugin->getName()));
            return true;
        }
        
        $pageHeight = $sender instanceof ConsoleCommandSender ? 48 : 6;
        $chunkedPermissions = array_chunk($permissions, $pageHeight);
        $maxPageNumber = count($chunkedPermissions);
        if(!isset($args[1]) || !is_numeric($args[1]) || $args[1] <= 0) 
        {
            $pageNumber = 1;
        }
        else if($args[1] > $maxPageNumber)
        {
            $pageNumber = $maxPageNumber;   
        }
        else 
        {
            $pageNumber = $args[1];
        }
        
        $sender->sendMessage(TextFormat::GREEN . MPEPerms::MAIN_PREFIX . ' ' . $this->plugin->getMessage("cmds.plperms.messages.plugin_perms_list", ($plugin instanceof PluginBase) ? $plugin->getName(): 'PocketMine-MP', $pageNumber, $maxPageNumber));
        foreach($chunkedPermissions[$pageNumber - 1] as $permission)
        {
            $sender->sendMessage(TextFormat::GREEN . MPEPerms::MAIN_PREFIX . ' - ' . $permission->getName());
        }
        return true;
    }
    
    public function getPlugin() : Plugin
    {
        return $this->plugin;
    }
}