<?php

namespace XackiGiFF\MPEPerms\cmd;

use XackiGiFF\MPEPerms\MPEPerms;
use XackiGiFF\MPEPerms\permissions\MPEPermsPermissions;

use CortexPE\Commando\BaseCommand;

use CortexPE\Commando\args\RawStringArgument;

use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class AddGroup extends BaseCommand
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

		$this->setPermission(MPEPermsPermissions::COMMAND_ADDGROUP_PERMISSION);
		try {
            $this->registerArgument(0, new RawStringArgument(AddGroup::ARGUMENT_GROUP_NAME));
        } catch (Exception) {
        }
		$this->setErrorFormat(0x02, TextFormat::YELLOW . MPEPerms::MAIN_PREFIX . $this->getOwningPlugin()->getMessage("cmds.addgroup.usage") );
		$this->setErrorFormat(0x03, TextFormat::YELLOW . MPEPerms::MAIN_PREFIX . $this->getOwningPlugin()->getMessage("cmds.addgroup.usage") );

	}

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void {
        $group = $args["group"];
		
		if(!$this->testPermission($sender)){
			return;
		}

		$result = $this->getOwningPlugin()->addGroup($group);

		if($result === MPEPerms::SUCCESS){
			$sender->sendMessage(TextFormat::GREEN . MPEPerms::MAIN_PREFIX . $this->getOwningPlugin()->getMessage("cmds.addgroup.messages.group_added_successfully", [$group]) );
		}elseif($result === MPEPerms::ALREADY_EXISTS){
            $sender->sendMessage(TextFormat::YELLOW . MPEPerms::MAIN_PREFIX . $this->getOwningPlugin()->getMessage("cmds.addgroup.messages.group_already_exists", [$group]) );
		}else{
            $sender->sendMessage(TextFormat::RED . MPEPerms::MAIN_PREFIX . $this->getOwningPlugin()->getMessage("cmds.addgroup.messages.invalid_group_name", [$group]) );
		}

		return;
    }
    
}