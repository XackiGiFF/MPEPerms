<?php

namespace XackiGiFF\MPEPerms\cmd;

use CortexPE\Commando\BaseCommand;
use CortexPE\Commando\args\RawStringArgument;
use pocketmine\command\CommandSender;
use pocketmine\plugin\Plugin;
use pocketmine\utils\TextFormat;
use XackiGiFF\MPEPerms\MPEPerms;
use XackiGiFF\MPEPerms\permissions\MPEPermsPermissions;

class AddGroup extends BaseCommand {
	/*
		MPEPerms by XackiGiFF (Remake by @mpe_coders from PurePerms by #64FF00)

		╔═╗╔═╗╔═══╗╔═══╗     ╔═══╗╔═══╗╔═══╗╔═╗╔═╗╔═══╗
		║║╚╝║║║╔═╗║║╔══╝     ║╔═╗║║╔══╝║╔═╗║║║╚╝║║║╔═╗║
		║╔╗╔╗║║╚═╝║║╚══╗     ║╚═╝║║╚══╗║╚═╝║║╔╗╔╗║║╚══╗
		║║║║║║║╔══╝║╔══╝     ║╔══╝║╔══╝║╔╗╔╝║║║║║║╚══╗║
		║║║║║║║║───║╚══╗     ║║───║╚══╗║║║╚╗║║║║║║║╚═╝║
		╚╝╚╝╚╝╚╝───╚═══╝     ╚╝───╚═══╝╚╝╚═╝╚╝╚╝╚╝╚═══╝
	*/

	protected const ARGUMENT_GROUP_NAME = "group";

	protected function prepare(): void {

		$this->setPermission(MPEPermsPermissions::COMMAND_ADDGROUP_PERMISSION);
		try {
            $this->registerArgument(0, new RawStringArgument(AddGroup::ARGUMENT_GROUP_NAME));
        } catch (Exception) {
        }
		$this->setErrorFormat(0x02, $this->getTemplate("cmds.addgroup.usage", true, []) );
		$this->setErrorFormat(0x03, $this->getTemplate("cmds.addgroup.usage", true, []) );


	}
	
	public function onRun(CommandSender $sender, string $aliasUsed, array $args): void {

		$group = $args["group"];
		
		if(!$this->testPermission($sender)){
			return;
		}

		$result = $this->getOwningPlugin()->addGroup($group);

		if($result === MPEPerms::SUCCESS){
			$this->sendTemplate($sender, "cmds.addgroup.messages.group_added_successfully", true, [$group]);
			$sender->sendMessage(TextFormat::GREEN . MPEPerms::MAIN_PREFIX . ' ' . $this->getOwningPlugin()->getMessage("cmds.addgroup.messages.group_added_successfully", [$group]));
		}elseif($result === MPEPerms::ALREADY_EXISTS){
			$sender->sendMessage(TextFormat::RED . MPEPerms::MAIN_PREFIX . ' ' . $this->getOwningPlugin()->getMessage("cmds.addgroup.messages.group_already_exists", [$group]));
		}else{
			$sender->sendMessage(TextFormat::RED . MPEPerms::MAIN_PREFIX . ' ' . $this->getOwningPlugin()->getMessage("cmds.addgroup.messages.invalid_group_name", [$group]));
		}

		return;

	}

	public function getPlugin() : Plugin{
		return $this->getOwningPlugin();
	}

	public function getTemplate(string $template, bool $type = true, $data = []): string {
		($type) ? $format = TextFormat::GREEN : $format = TextFormat::RED;
		return $format . MPEPerms::MAIN_PREFIX . ' ' . $this->getOwningPlugin()->getMessage($template, $data);
	}

	public function sendTemplate($sender, string $template, bool $type = true, $data = []): void {
		$sender->sendMessage($this->getTemplate($template, $type = true));
	}
}
