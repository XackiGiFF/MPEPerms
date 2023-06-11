<?php

namespace XackiGiFF\MPEPerms\Task;

use XackiGiFF\MPEPerms\EventManager\PPgroupExpiredEvent;
use XackiGiFF\MPEPerms\MPEPerms;

use pocketmine\scheduler\Task;

class PPExpDateCheckTask extends Task
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

    /**
     * @param MPEPerms $plugin
     */
    public function __construct(protected MPEPerms $plugin)
    {
    }

    public function onRun():void
    {
        foreach($this->plugin->getServer()->getOnlinePlayers() as $player)
        {
            if(time() === $this->plugin->getUserDataMgr()->getNode($player, "expTime"))
            {
                $WorldName = $this->plugin->getConfigValue("enable-multiworld-perms") ? $player->getWorld()->getDisplayName() : null;
                $event = new PPgroupExpiredEvent($this->plugin, $player, $WorldName);
                $event->call();
            }
        }
    }
}
