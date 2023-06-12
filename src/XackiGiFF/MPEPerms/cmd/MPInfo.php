<?php

namespace XackiGiFF\MPEPerms\cmd;

use XackiGiFF\MPEPerms\MPEPerms;
use XackiGiFF\MPEPerms\permissions\MPEPermsPermissions;

use CortexPE\Commando\BaseCommand;

use CortexPE\Commando\args\RawStringArgument;

use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

use pocketmine\plugin\PluginBase;

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
		$commandmap = $this->getOwningPlugin()->getServer()->getCommandMap();

		$sender->sendMessage(TextFormat::GREEN . MPEPerms::MAIN_PREFIX . ' ' . $this->getOwningPlugin()->getMessage("cmds.mpinfo.messages.mpinfo_player", [$version, $author]));
		$sender->sendMessage(TextFormat::GREEN . MPEPerms::MAIN_PREFIX . ' ' . $this->getOwningPlugin()->getMessage("cmds.mpinfo.messages.plugin_cmds_list", [($this->getOwningPlugin() instanceof PluginBase) ? $this->getOwningPlugin()->getName(): 'PocketMine-MP']));
		$commands = $this->getOwningPlugin()->getAPI()->getCommands();
		foreach ($commands as $command => $keys){

			$sender->sendMessage(TextFormat::GREEN . MPEPerms::MAIN_PREFIX . ' - /' . $commandmap->getCommand($command)->getName() . " - " . $commandmap->getCommand($command)->getDescription());
		}

		return;
    }
}