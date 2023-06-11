<?php

namespace XackiGiFF\MPEPerms\EventManager;

use XackiGiFF\MPEPerms\PPGroup;
use XackiGiFF\MPEPerms\MPEPerms;

use pocketmine\event\plugin\PluginEvent;

use pocketmine\player\IPlayer;
use pocketmine\world\World;

class PPGroupExpiredEvent extends PluginEvent
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

    public static $handlerList = null;

    /**
     * @param MPEPerms $plugin
     * @param IPlayer $player
     * @param PPGroup $group
     * @param $worldName
     */
    public function __construct(MPEPerms $plugin, IPlayer $player, ?string $worldName)
    {
        parent::__construct($plugin);
        $this->player = $player;
        $this->worldName = $worldName;
    }

    /**
     * @return World
     */
    public function getWorld()
    {
        return $this->getPlugin()->getServer()->getLevelByName($this->worldName);
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