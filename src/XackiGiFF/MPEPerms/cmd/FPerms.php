<?php

namespace XackiGiFF\MPEPerms\cmd;

use XackiGiFF\MPEPerms\MPEPerms;
use XackiGiFF\MPEPerms\permissions\MPEPermsPermissions;

use CortexPE\Commando\BaseCommand;

use CortexPE\Commando\args\IntegerArgument;
use CortexPE\Commando\args\RawStringArgument;

use pocketmine\command\CommandSender;
use pocketmine\console\ConsoleCommandSender;
use pocketmine\plugin\Plugin;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat;

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
		$this->setErrorFormat(0x01, TextFormat::YELLOW . MPEPerms::MAIN_PREFIX . ' ' . $this->getOwningPlugin()->getMessage("cmds.fperms.usage"));
		$this->setErrorFormat(0x02, TextFormat::YELLOW . MPEPerms::MAIN_PREFIX . ' ' . $this->getOwningPlugin()->getMessage("cmds.fperms.usage"));
		$this->setErrorFormat(0x03, TextFormat::YELLOW . MPEPerms::MAIN_PREFIX . ' ' . $this->getOwningPlugin()->getMessage("cmds.fperms.usage"));

	}

	public function onRun(CommandSender $sender, string $aliasUsed, array $args): void {
		// This is where the processing will occur if it's NOT handled by other subcommands
		if(!$this->testPermission($sender)){
			return;
		}
        
        $plugin = (strtolower($args["plugin_name"]) === 'pocketmine' || strtolower($args["plugin_name"]) === 'pmmp') ? 'pocketmine' : $this->getOwningPlugin()->getServer()->getPluginManager()->getPlugin($args["plugin_name"]);
        
        if($plugin === null)
        {
            $sender->sendMessage(TextFormat::RED . MPEPerms::MAIN_PREFIX . ' ' . $this->getOwningPlugin()->getMessage("cmds.fperms.messages.plugin_not_exist", [$args["plugin_name"]]));
            
            return;
        }
        
        $permissions = ($plugin instanceof PluginBase) ? $plugin->getDescription()->getPermissions() : $this->getOwningPlugin()->getPocketMinePerms();
        
        if(empty($permissions))
        {
            $sender->sendMessage(TextFormat::GREEN . MPEPerms::MAIN_PREFIX . ' ' . $this->getOwningPlugin()->getMessage("cmds.fperms.messages.no_plugin_perms", [$plugin->getName()]));
            
            return;
        }
        
        $pageHeight = $sender instanceof ConsoleCommandSender ? 48 : 6;
                
        $chunkedPermissions = array_chunk($permissions, $pageHeight); 
        
        $maxPageNumber = count($chunkedPermissions);
        
        if(!isset($args["page"]) || !is_numeric($args["page"]) || $args["page"] <= 0) 
        {
            $pageNumber = 1;
        }
        else if($args["page"] > $maxPageNumber)
        {
            $pageNumber = $maxPageNumber;   
        }
        else 
        {
            $pageNumber = $args["page"];
        }
        
        $sender->sendMessage(TextFormat::GREEN . MPEPerms::MAIN_PREFIX . ' ' . $this->getOwningPlugin()->getMessage("cmds.fperms.messages.plugin_perms_list", [($plugin instanceof PluginBase) ? $plugin->getName(): 'PocketMine-MP', $pageNumber, $maxPageNumber]));

        foreach($chunkedPermissions[$pageNumber - 1] as $page) 
        {
		$i = 0; 
		foreach($page as $permission){
			$sender->sendMessage(TextFormat::GREEN . MPEPerms::MAIN_PREFIX . ' - ' . $permission->getName() . " - " . $permission->getDescription());
			$i++;
		}
		$i = 0; 

        }
        
        return;
    }
    
    public function getPlugin() : Plugin
    {
        return $this->getOwningPlugin();
    }
}