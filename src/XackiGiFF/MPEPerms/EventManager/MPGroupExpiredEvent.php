<?php

namespace XackiGiFF\MPEPerms\EventManager;

use XackiGiFF\MPEPerms\MPGroup;
use XackiGiFF\MPEPerms\MPEPerms;

use pocketmine\event\plugin\PluginEvent;

use pocketmine\player\IPlayer;
use pocketmine\world\World;

class MPGroupExpiredEvent extends PluginEvent
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
     * @param MPGroup $group
     * @param $worldName
     */
    public function __construct(protected MPEPerms $plugin, protected IPlayer $player, protected ?string $worldName)
    {
        parent::__construct($plugin);
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