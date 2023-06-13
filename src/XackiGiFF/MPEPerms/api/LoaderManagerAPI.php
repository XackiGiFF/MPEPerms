<?php

declare(strict_types=1);

namespace XackiGiFF\MPEPerms\api;

use XackiGiFF\MPEPerms\MPEPerms;
use XackiGiFF\MPEPerms\PPListener;
use XackiGiFF\MPEPerms\Task\MPExpDateCheckTask;
use XackiGiFF\MPEPerms\api\commands\CommandsRegisterAPI;
use XackiGiFF\MPEPerms\utils\Utils as UtilsAPI;
use XackiGiFF\MPEPerms\api\GroupsManagerAPI;


class LoaderManagerAPI {

    private $cmds;
    private $utils;
    private $group;

    public function __construct(protected MPEPerms $plugin) {
		$this->loadCommandsRegisterAPI();
        $this->loadUtilsAPI();
        $this->loadGroupManagerAPI();
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
    
//
// STARTING
//

    public function startService(): void{
        $this->plugin->getServer()->getPluginManager()->registerEvents(new PPListener($this->plugin), $this->plugin);
        $this->plugin->getScheduler()->scheduleRepeatingTask(new MPExpDateCheckTask($this->plugin), 20 * 5);
    }

}