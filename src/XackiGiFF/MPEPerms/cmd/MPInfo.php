<?php

namespace XackiGiFF\MPEPerms\cmd;

use XackiGiFF\MPEPerms\MPEPerms;
use XackiGiFF\MPEPerms\permissions\MPEPermsPermissions;

use CortexPE\Commando\BaseCommand;

use CortexPE\Commando\args\RawStringArgument;

use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class MPInfo extends BaseCommand
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
        $this->setPermission(MPEPermsPermissions::COMMAND_PPINFO_PERMISSION);

    }

	public function onRun(CommandSender $sender, string $aliasUsed, array $args): void {
		if(!$this->testPermission($sender)){
			return;
		}

		$author = $this->getOwningPlugin()->getDescription()->getAuthors()[0];
		$version = $this->getOwningPlugin()->getDescription()->getVersion();

		if($sender instanceof ConsoleCommandSender){
			$sender->sendMessage(TextFormat::GREEN . MPEPerms::MAIN_PREFIX . ' ' . $this->getOwningPlugin()->getMessage("cmds.mpinfo.messages.mpinfo_console", [$version, $author]));
		}else{
			$sender->sendMessage(TextFormat::GREEN . MPEPerms::MAIN_PREFIX . ' ' . $this->getOwningPlugin()->getMessage("cmds.mpinfo.messages.mpinfo_player", [$version, $author]));
		}

		return;
    }
    
    public function getPlugin() : Plugin
    {
        return $this->getOwningPlugin();
    }
}