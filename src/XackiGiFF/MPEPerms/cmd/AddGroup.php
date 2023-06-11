<?php

namespace XackiGiFF\MPEPerms\cmd;

use XackiGiFF\MPEPerms\MPEPerms;
use XackiGiFF\MPEPerms\permissions\MPEPermsPermissions;

use CortexPE\Commando\BaseCommand;
use CortexPE\Commando\args\RawStringArgument;

use pocketmine\command\CommandSender;

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
		$this->setErrorFormat(0x02, $this->getOwningPlugin()->getAPI()->getTemplate("cmds.addgroup.usage", true) );
		$this->setErrorFormat(0x03, $this->getOwningPlugin()->getAPI()->getTemplate("cmds.addgroup.usage", true) );

	}

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void {
        $group = $args["group"];
		
		if(!$this->testPermission($sender)){
			return;
		}

		$result = $this->getOwningPlugin()->addGroup($group);

		if($result === MPEPerms::SUCCESS){
			$this->getOwningPlugin()->getAPI()->sendTemplate($sender, "cmds.addgroup.messages.group_added_successfully", true, [$group]);

		}elseif($result === MPEPerms::ALREADY_EXISTS){
            $this->getOwningPlugin()->getAPI()->sendTemplate($sender, "cmds.addgroup.messages.group_already_exists", true, [$group]);
		}else{
            $this->getOwningPlugin()->getAPI()->sendTemplate($sender, "cmds.addgroup.messages.invalid_group_name", false, [$group]);
		}

		return;
    }
    
}