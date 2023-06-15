<?php

namespace XackiGiFF\MPEPerms\api\services\providers;

use XackiGiFF\MPEPerms\MPEPerms;
use XackiGiFF\MPEPerms\api\GroupSystem\group\Group;

use pocketmine\player\IPlayer;

use pocketmine\utils\Config;
use RuntimeException;

class JsonProvider implements ProviderInterface {
	/*
		MPEPerms by XackiGiFF (Remake by @mpe_coders from MPEPerms by #64FF00)

		╔═╗╔═╗╔═══╗╔═══╗     ╔═══╗╔═══╗╔═══╗╔═╗╔═╗╔═══╗
		║║╚╝║║║╔═╗║║╔══╝     ║╔═╗║║╔══╝║╔═╗║║║╚╝║║║╔═╗║
		║╔╗╔╗║║╚═╝║║╚══╗     ║╚═╝║║╚══╗║╚═╝║║╔╗╔╗║║╚══╗
		║║║║║║║╔══╝║╔══╝     ║╔══╝║╔══╝║╔╗╔╝║║║║║║╚══╗║
		║║║║║║║║───║╚══╗     ║║───║╚══╗║║║╚╗║║║║║║║╚═╝║
		╚╝╚╝╚╝╚╝───╚═══╝     ╚╝───╚═══╝╚╝╚═╝╚╝╚╝╚╝╚═══╝
	*/

    private $groups, $userDataFolder;

    /**
     * @param MPEPerms $plugin
     */
    public function __construct(protected MPEPerms $plugin)
    {
        $this->plugin->saveResource("groups.yml");
        $this->groups = new Config($this->plugin->getDataFolder() . "groups.yml", Config::YAML, []);
        $this->userDataFolder = $this->plugin->getDataFolder() . "players/";
        if(!file_exists($this->userDataFolder))
            @mkdir($this->userDataFolder, 0777, true);
    }
    /**
     * @param Group $group
     * @return mixed
     */
    public function getGroupData(Group $group)
    {
        $groupName = $group->getName();

        if(!isset($this->getGroupsData()[$groupName]) || !is_array($this->getGroupsData()[$groupName]))
            return [];

        return $this->getGroupsData()[$groupName];
    }

    /**
     * @return Config
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
    /**
     * @param IPlayer $player
     * @param bool $onUpdate
     * @return array|Config
     */
    public function getPlayerConfig(IPlayer $player, $onUpdate = false)
    {
        $userName = $player->getName();

        // TODO
        if($onUpdate === true)
        {
            if(!file_exists($this->userDataFolder . strtolower($userName) . ".json"))
            {
                return new Config($this->userDataFolder . strtolower($userName) . ".json", Config::JSON, [
                    "userName" => $userName,
                    "group" => $this->plugin->getDefaultGroup()->getName(),
                    "permissions" => [],
                    "worlds" => [],
                    "time" => -1
                ]);
            }
            else
            {
                return new Config($this->userDataFolder . strtolower($userName) . ".json", Config::JSON, [
                ]);
            }
        }
        else
        {
            if(file_exists($this->userDataFolder . strtolower($userName) . ".json"))
            {
                return new Config($this->userDataFolder . strtolower($userName) . ".json", Config::JSON, [
                ]);
            }
            else
            {
                return [
                    "userName" => $userName,
                    "group" => $this->plugin->getDefaultGroup()->getName(),
                    "permissions" => [],
                    "worlds" => [],
                    "time" => -1
                ];
            }
        }
    }
    /**
     * @param IPlayer $player
     * @return array|Config
     */
    public function getPlayerData(IPlayer $player)
    {
        $userConfig = $this->getPlayerConfig($player);

        return (($userConfig instanceof Config) ? $userConfig->getAll() : $userConfig);
    }

    public function getUsers()
    {
        // TODO: Implement getUsers() method.
    }

    /**
     * @param Group $group
     * @param array $tempGroupData
     */
    public function setGroupData(Group $group, array $tempGroupData)
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
        $userData = $this->getPlayerConfig($player, true);

        if(!$userData instanceof Config)
            throw new RuntimeException("Failed to update player data: Invalid data type (" . get_class($userData) . ")");

        $userData->setAll($tempUserData);

        $userData->save();
    }

    public function close()
    {
    }
}