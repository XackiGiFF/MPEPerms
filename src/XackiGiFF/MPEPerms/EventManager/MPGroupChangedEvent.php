<?php

namespace XackiGiFF\MPEPerms\EventManager;

use XackiGiFF\MPEPerms\MPGroup;
use XackiGiFF\MPEPerms\MPEPerms;

use pocketmine\event\plugin\PluginEvent;

use pocketmine\player\IPlayer;
use pocketmine\world\World;

class MPGroupChangedEvent extends PluginEvent {
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
    public function __construct(protected MPEPerms $plugin, protected IPlayer $player, protected MPGroup $group, protected ?string $worldName)
    {
    }

	/**
     * @return MPGroup
     */
    public function getGroup()
    {
        return $this->group;
    }

    /**
     * @return World
     */
    public function getLevel()
    {
        return $this->plugin->getServer()->getWorldManager()->getWorldByName($this->worldName);
    }

    /**
     * @return string
     */
    public function getWorldName(): ?string
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