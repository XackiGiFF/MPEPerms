<?php
namespace XackiGiFF\MPEPerms\DataProviders;

use XackiGiFF\MPEPerms\MPGroup;
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

    public function getGroupData(MPGroup $group){
    }

    public function getGroupsData(){
    }

    public function getPlayerData(IPlayer $player){
    }

    public function getUsers(){
    }

    public function setGroupData(MPGroup $group, array $tempGroupData){
    }

    public function setGroupsData(array $tempGroupsData){
    }

    public function setPlayerData(IPlayer $player, array $tempPlayerData){
    }

    public function close(){
    }

}