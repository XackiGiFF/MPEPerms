<?php

namespace XackiGiFF\MPEPerms\EventManager;

use XackiGiFF\MPEPerms\api\GroupSystem\group\Group;
use XackiGiFF\MPEPerms\MPEPerms;

use pocketmine\event\plugin\PluginEvent;

use pocketmine\player\IPlayer;
use pocketmine\world\World;

class GroupExpiredEvent extends PluginEvent {
	/*
		MPEPerms by XackiGiFF (Remake by @mpe_coders from MPEPerms by #64FF00)

		╔═╗╔═╗╔═══╗╔═══╗     ╔═══╗╔═══╗╔═══╗╔═╗╔═╗╔═══╗
		║║╚╝║║║╔═╗║║╔══╝     ║╔═╗║║╔══╝║╔═╗║║║╚╝║║║╔═╗║
		║╔╗╔╗║║╚═╝║║╚══╗     ║╚═╝║║╚══╗║╚═╝║║╔╗╔╗║║╚══╗
		║║║║║║║╔══╝║╔══╝     ║╔══╝║╔══╝║╔╗╔╝║║║║║║╚══╗║
		║║║║║║║║───║╚══╗     ║║───║╚══╗║║║╚╗║║║║║║║╚═╝║
		╚╝╚╝╚╝╚╝───╚═══╝     ╚╝───╚═══╝╚╝╚═╝╚╝╚╝╚╝╚═══╝
	*/

    public static $handlerList = null;

    /**
     * @param MPEPerms $plugin
     * @param IPlayer $player
     * @param Group $group
     * @param $worldName
     */
    public function __construct(protected MPEPerms $plugin, protected IPlayer $player, protected ?string $worldName)
    {
    }

    /**
     * @return World
     */
    public function getWorld()
    {
        return $this->plugin->getServer()->getLevelByName($this->worldName);
    }

    /**
     * @return string
     */
    public function getLevelName()
    {
        return $this->worldName;
    }

    /**
     * @return IPlayer
     */
    public function getPlayer()
    {
        return $this->player;
    }
}