<?php

namespace XackiGiFF\MPEPerms\DataProviders;

use XackiGiFF\MPEPerms\MPEPerms;
use XackiGiFF\MPEPerms\PPGroup;

use pocketmine\player\IPlayer;

use pocketmine\utils\Config;
use RuntimeException;

class DefaultProvider implements ProviderInterface
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

    private $groups, $players;

    /**
     * @param MPEPerms $plugin
     */
    public function __construct(protected MPEPerms $plugin)
    {
        $this->plugin->saveResource("groups.yml");
        $this->groups = new Config($this->plugin->getDataFolder() . "groups.yml", Config::YAML);
        if(empty($this->groups->getAll())){
			throw new RuntimeException($this->plugin->getMessage("logger_messages.YAMLProvider_InvalidGroupsSettings"));
		}
        $this->plugin->saveResource("players.yml");
        $this->players = new Config($this->plugin->getDataFolder() . "players.yml", Config::YAML);
    }

    /**
     * @param PPGroup $group
     * @return mixed
     */
    public function getGroupData(PPGroup $group)
    {
        $groupName = $group->getName();
        if(!isset($this->getGroupsData()[$groupName]) || !is_array($this->getGroupsData()[$groupName])) return [];
        return $this->getGroupsData()[$groupName];
    }

    /**
     * @return mixed
     */
    public function getGroupsConfig()
    {
        return $this->groups;
    }

    /**
     * @return mixed
     */
    public function getGroupsData()
    {
        return $this->groups->getAll();
    }

    public function getPlayerData(IPlayer $player)
    {
        $userName = strtolower($player->getName());

        if(!$this->players->exists($userName))
        {
            return [
                "group" => $this->plugin->getDefaultGroup()->getName(),
                "permissions" => [],
                "worlds" => [],
                "time" => -1
            ];
        }

        return $this->players->get($userName);
    }

    public function getUsers()
    {
        /*
        if(empty($this->players->getAll()))
            return null;

        return $this->players->getAll();
        */
    }

    /**
     * @param PPGroup $group
     * @param array $tempGroupData
     */
    public function setGroupData(PPGroup $group, array $tempGroupData)
    {
        $groupName = $group->getName();
        $this->groups->set($groupName, $tempGroupData);
        $this->groups->save();
    }

    /**
     * @param array $tempGroupsData
     */
    public function setGroupsData(array $tempGroupsData)
    {
        $this->groups->setAll($tempGroupsData);
        $this->groups->save();
    }

    /**
     * @param IPlayer $player
     * @param array $tempUserData
     */
    public function setPlayerData(IPlayer $player, array $tempUserData)
    {
        $userName = strtolower($player->getName());
        if(!$this->players->exists($userName))
        {
            $this->players->set($userName, [
                "group" => $this->plugin->getDefaultGroup()->getName(),
                "permissions" => [],
                "worlds" => [],
                "time" => -1
            ]);
        }

        if(isset($tempUserData["userName"]))
            unset($tempUserData["userName"]);
        $this->players->set($userName, $tempUserData);
        $this->players->save();
    }

    public function close()
    {
    }
}