<?php
namespace XackiGiFF\MPEPerms\api\services\providers;

use XackiGiFF\MPEPerms\api\GroupSystem\group\Group;
use XackiGiFF\MPEPerms\MPEPerms;
use pocketmine\player\IPlayer;

class SQLite3Provider implements ProviderInterface {
	/*
		MPEPerms by XackiGiFF (Remake by @mpe_coders from MPEPerms by #64FF00)

		╔═╗╔═╗╔═══╗╔═══╗     ╔═══╗╔═══╗╔═══╗╔═╗╔═╗╔═══╗
		║║╚╝║║║╔═╗║║╔══╝     ║╔═╗║║╔══╝║╔═╗║║║╚╝║║║╔═╗║
		║╔╗╔╗║║╚═╝║║╚══╗     ║╚═╝║║╚══╗║╚═╝║║╔╗╔╗║║╚══╗
		║║║║║║║╔══╝║╔══╝     ║╔══╝║╔══╝║╔╗╔╝║║║║║║╚══╗║
		║║║║║║║║───║╚══╗     ║║───║╚══╗║║║╚╗║║║║║║║╚═╝║
		╚╝╚╝╚╝╚╝───╚═══╝     ╚╝───╚═══╝╚╝╚═╝╚╝╚╝╚╝╚═══╝
	*/

    private $db;
    private $groupsData = [];

    /**
     * @param MPEPerms $plugin
     */
    public function __construct(protected MPEPerms $plugin)
    {
        $this->db = new \SQLite3($plugin->getDataFolder()."MPEPerms.db");
        $this->db->exec("");
        $this->loadGroupsData();
    }

    public function loadGroupsData()
    {
        //
    }

    public function getGroupData(Group $group){
    }

    public function getGroupsData(){
    }

    public function getPlayerData(IPlayer $player){
    }

    public function getUsers(){
    }

    public function setGroupData(Group $group, array $tempGroupData){
    }

    public function setGroupsData(array $tempGroupsData){
    }

    public function setPlayerData(IPlayer $player, array $tempPlayerData){
    }

    public function close(){
    }

}