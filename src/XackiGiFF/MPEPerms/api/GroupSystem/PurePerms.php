<?php

declare(strict_types=1);

namespace XackiGiFF\MPEPerms\api\GroupSystem;

use XackiGiFF\MPEPerms\api\GroupSystem\PurePerms;
use XackiGiFF\MPEPerms\api\GroupSystem\GroupAPI;
use XackiGiFF\MPEPerms\MPEPerms;
use pocketmine\Server;
use RuntimeException;

class PurePerms {
	/*
		MPEPerms by XackiGiFF (Remake by @mpe_coders from MPEPerms by #64FF00)

		╔═╗╔═╗╔═══╗╔═══╗     ╔═══╗╔═══╗╔═══╗╔═╗╔═╗╔═══╗
		║║╚╝║║║╔═╗║║╔══╝     ║╔═╗║║╔══╝║╔═╗║║║╚╝║║║╔═╗║
		║╔╗╔╗║║╚═╝║║╚══╗     ║╚═╝║║╚══╗║╚═╝║║╔╗╔╗║║╚══╗
		║║║║║║║╔══╝║╔══╝     ║╔══╝║╔══╝║╔╗╔╝║║║║║║╚══╗║
		║║║║║║║║───║╚══╗     ║║───║╚══╗║║║╚╗║║║║║║║╚═╝║
		╚╝╚╝╚╝╚╝───╚═══╝     ╚╝───╚═══╝╚╝╚═╝╚╝╚╝╚╝╚═══╝
	*/

	protected $plugin;

    public function __construct(){
		$this->plugin = $this->getServer()->getPluginManager()->getPlugin('MPEPerms');
    }

    public function getAPI(){
        return new GroupAPI($this->plugin);
    }
}