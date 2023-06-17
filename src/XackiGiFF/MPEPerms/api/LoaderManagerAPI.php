<?php

declare(strict_types=1);

namespace XackiGiFF\MPEPerms\api;

use XackiGiFF\MPEPerms\MPEPerms;
use XackiGiFF\MPEPerms\MPEListener;
use XackiGiFF\MPEPerms\Task\MPExpDateCheckTask;
use XackiGiFF\MPEPerms\api\services\CommandsRegisterAPI;
use XackiGiFF\MPEPerms\utils\Utils as UtilsAPI;

use XackiGiFF\MPEPerms\api\GroupSystem\GroupAPI;
use XackiGiFF\MPEPerms\api\GroupSystem\player\UserDataManagerAPI;


class LoaderManagerAPI {
	/*
		MPEPerms by XackiGiFF (Remake by @mpe_coders from MPEPerms by #64FF00)

		╔═╗╔═╗╔═══╗╔═══╗     ╔═══╗╔═══╗╔═══╗╔═╗╔═╗╔═══╗
		║║╚╝║║║╔═╗║║╔══╝     ║╔═╗║║╔══╝║╔═╗║║║╚╝║║║╔═╗║
		║╔╗╔╗║║╚═╝║║╚══╗     ║╚═╝║║╚══╗║╚═╝║║╔╗╔╗║║╚══╗
		║║║║║║║╔══╝║╔══╝     ║╔══╝║╔══╝║╔╗╔╝║║║║║║╚══╗║
		║║║║║║║║───║╚══╗     ║║───║╚══╗║║║╚╗║║║║║║║╚═╝║
		╚╝╚╝╚╝╚╝───╚═══╝     ╚╝───╚═══╝╚╝╚═╝╚╝╚╝╚╝╚═══╝
	*/
    
    private $cmds;
    private $utils;
    private $group;
    private UserDataManagerAPI $userDataMgr;

    public function __construct(protected MPEPerms $plugin) {
		$this->loadCommandsRegisterAPI();
        $this->loadUtilsAPI();
        $this->loadGroupManagerAPI();
        $this->loadUserDataManagerAPI();
    }

//
// LOADING...
//

    private function loadCommandsRegisterAPI(): void
    {
        $this->cmds = new CommandsRegisterAPI($this->plugin);
    }

    private function loadGroupManagerAPI(): void
    {
        $this->group = new GroupAPI($this->plugin);
    }

    private function loadUtilsAPI(): void
    {
        $this->utils = new UtilsAPI($this->plugin);
    }

    private function loadUserDataManagerAPI(): void
    {
        $this->userDataMgr = new UserDataManagerAPI($this->plugin);
    }

//
// GETTING...
//

    public function getGroupManagerAPI() {
        return $this->group;
    }

    public function getCommandsRegisterAPI() {
        return $this->cmds;
    }

    public function getUtilsAPI() {
        return $this->utils;
    }

    public function getUserDataMgr(): UserDataManagerAPI{
        return $this->userDataMgr;
    }

    
//
// STARTING
//

    public function startService(): void{
        $this->plugin->getServer()->getPluginManager()->registerEvents(new MPEListener($this->plugin), $this->plugin);
        $this->plugin->getScheduler()->scheduleRepeatingTask(new MPExpDateCheckTask($this->plugin), 20 * 5);
    }

}