<?php

namespace XackiGiFF\MPEPerms\Task;

use XackiGiFF\MPEPerms\EventManager\MPgroupExpiredEvent;
use XackiGiFF\MPEPerms\MPEPerms;

use pocketmine\scheduler\Task;

class MPExpDateCheckTask extends Task {
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
            $exptime = $this->plugin->getUserDataMgr()->getNode($player, "expTime");
            // TODO проверку на миры

            if(time() > $exptime && $exptime !== -1)
            {
                echo("Время вышло");
                $WorldName = $this->plugin->getConfigValue("enable-multiworld-perms") ? $player->getWorld()->getDisplayName() : null;
                $event = new MPgroupExpiredEvent($this->plugin, $player, $WorldName);
                $event->call();
            }
        }
    }
}
