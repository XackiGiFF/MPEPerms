<?php

namespace XackiGiFF\MPEPerms\EventManager;

use XackiGiFF\MPEPerms\PPGroup;
use XackiGiFF\MPEPerms\MPEPerms;

use pocketmine\event\plugin\PluginEvent;

use pocketmine\player\IPlayer;
use pocketmine\world\World;

class PPGroupChangedEvent extends PluginEvent
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
    public function __construct(MPEPerms $plugin, IPlayer $player, PPGroup $group, ?string $worldName)
    {
        parent::__construct($plugin);
        $this->group = $group;
        $this->player = $player;
        $this->worldName = $worldName;
    }

	/**
     * @return PPGroup
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
        return $this->getPlugin()->getServer()->getWorldManager()->getWorldByName($this->worldName);
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