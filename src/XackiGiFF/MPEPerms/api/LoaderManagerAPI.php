<?php

declare(strict_types=1);

namespace XackiGiFF\MPEPerms\api;

use XackiGiFF\MPEPerms\MPEPerms;
use XackiGiFF\MPEPerms\PPListener;
use XackiGiFF\MPEPerms\Task\MPExpDateCheckTask;
use XackiGiFF\MPEPerms\api\commands\CommandsRegisterAPI;
use XackiGiFF\MPEPerms\utils\Utils as UtilsAPI;
use XackiGiFF\MPEPerms\api\GroupsManagerAPI;
use XackiGiFF\MPEPerms\api\player\UserDataManagerAPI;


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
    private $userDataMgr;

    public function __construct(protected MPEPerms $plugin) {
		$this->loadCommandsRegisterAPI();
        $this->loadUtilsAPI();
        $this->loadGroupManagerAPI();
        $this->loadUserDataManagerAPI();
    }

//
// LOADING...
//

    private function loadCommandsRegisterAPI(){
        $this->cmds = new CommandsRegisterAPI($this->plugin);
    }

    private function loadGroupManagerAPI(){
        $this->group = new GroupsManagerAPI($this->plugin);
    }

    private function loadUtilsAPI(){
        $this->utils = new UtilsAPI($this->plugin);
    }

    private function loadUserDataManagerAPI(){
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
        $this->plugin->getServer()->getPluginManager()->registerEvents(new PPListener($this->plugin), $this->plugin);
        $this->plugin->getScheduler()->scheduleRepeatingTask(new MPExpDateCheckTask($this->plugin), 20 * 5);
    }

}