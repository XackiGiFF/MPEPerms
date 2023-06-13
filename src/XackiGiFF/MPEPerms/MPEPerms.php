<?php

namespace XackiGiFF\MPEPerms;

use XackiGiFF\MPEPerms\api\MPEPermsAPI;

use XackiGiFF\MPEPerms\DataManager\UserDataManager;
use XackiGiFF\MPEPerms\DataProviders\SQLite3Provider;
use XackiGiFF\MPEPerms\DataProviders\DefaultProvider;
use XackiGiFF\MPEPerms\DataProviders\MySQLProvider;
use XackiGiFF\MPEPerms\DataProviders\YamlV1Provider;
use XackiGiFF\MPEPerms\DataProviders\ProviderInterface;
use XackiGiFF\MPEPerms\DataProviders\JsonProvider;
use XackiGiFF\MPEPerms\Task\MPExpDateCheckTask;

use pocketmine\permission\DefaultPermissions;
use pocketmine\permission\Permission;
use pocketmine\permission\PermissionAttachment;
use pocketmine\permission\PermissionManager;
use pocketmine\player\IPlayer;
use pocketmine\world\World;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;
use Ramsey\Uuid\Uuid;
use RuntimeException;

class MPEPerms extends PluginBase
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

    const MAIN_PREFIX = "§b§lMP §r§7>>";

    const CORE_PERM = "\x70\x70\x65\x72\x6d\x73\x2e\x63\x6f\x6d\x6d\x61\x6e\x64\x2e\x70\x70\x69\x6e\x66\x6f";

    const NOT_FOUND = null;
    const INVALID_NAME = -1;
    const ALREADY_EXISTS = 0;
    const SUCCESS = 1;

    private $isGroupsLoaded = false;

    /** @var PPMessages $messages */
    private $messages;

    /** @var ProviderInterface $provider */
    private $provider;

    /** @var UserDataManager $userDataMgr */
    private $userDataMgr;

    /** @var MPEPermsAPI $api */
	public $api;

    private $attachments = [], $groups = [], $pmDefaultPerms = [];

    public function onLoad(): void
    {
        $this->saveDefaultConfig();

        $this->api = new MPEPermsAPI($this);

        $this->api->fixConfig();
        
        $this->messages = new PPMessages($this);
        $this->userDataMgr = new UserDataManager($this);

        if($this->getConfigValue("enable-multiworld-perms") === false){
			$this->getLogger()->notice($this->getMessage("logger_messages.onLoad_01"));
			$this->getLogger()->notice($this->getMessage("logger_messages.onLoad_02"));
		}else{
			$this->getLogger()->notice($this->getMessage("logger_messages.onLoad_03"));
		}
    }
    
    public function onEnable(): void
    {
        $this->api->registerCommands();
        $this->setProvider();
        $this->registerPlayers();
        $this->getServer()->getPluginManager()->registerEvents(new PPListener($this), $this);
        $this->getScheduler()->scheduleRepeatingTask(new MPExpDateCheckTask($this), 20);
    }

    public function onDisable(): void
    {
        $this->unregisterPlayers();
        if($this->isValidProvider())
            $this->provider->close();
    }

    /**
     * @param bool $onEnable
     */
    private function setProvider($onEnable = true)
    {
        $providerName = $this->getConfigValue("data-provider");
        switch(strtolower($providerName))
        {
            case "sqlite3":
                $provider = new SQLite3Provider($this);
                if($onEnable === true)
                    $this->getLogger()->notice($this->getMessage("logger_messages.setProvider_SQLITE3"));
                break;
            case "json":
                $provider = new JsonProvider($this);
                if($onEnable === true)
                    $this->getLogger()->notice($this->getMessage("logger_messages.setProvider_JSON"));
                break;
            case "yamlv1":
                $provider = new YamlV1Provider($this);
                if($onEnable === true)
                    $this->getLogger()->notice($this->getMessage("logger_messages.setProvider_YAML"));
                break;
            default:
                $provider = new DefaultProvider($this);
                if($onEnable === true)
                    $this->getLogger()->warning($this->getMessage("logger_messages.setProvider_NotFound", [$providerName]));
                break;
        }
        if($provider instanceof ProviderInterface)
            $this->provider = $provider;
        $this->updateGroups();
    }

    /*
          888  888          d8888 8888888b. 8888888
          888  888         d88888 888   Y88b  888
        888888888888      d88P888 888    888  888
          888  888       d88P 888 888   d88P  888
          888  888      d88P  888 8888888P"   888
        888888888888   d88P   888 888         888
          888  888    d8888888888 888         888
          888  888   d88P     888 888       8888888
    */

    public function getAPI(): MPEPermsAPI {
		return $this->api;
	}

    /**
     * @param $groupName
     * @return bool
     */
    public function addGroup($groupName)
    {
        $groupsData = $this->getProvider()->getGroupsData();
        if(!$this->isValidGroupName($groupName))
            return self::INVALID_NAME;
        if(isset($groupsData[$groupName]))
            return self::ALREADY_EXISTS;
        $groupsData[$groupName] = [
            "alias" => "",
            "isDefault" => false,
            "inheritance" => [
            ],
            "permissions" => [
            ],
            "worlds" => [
            ]
        ];
        $this->getProvider()->setGroupsData($groupsData);
        $this->updateGroups();
        return self::SUCCESS;
    }

    /**
     * @param $date
     * @return int
     * Example: $date = '1d2h3m';
     */
    public function date2Int($date)
    {
        if(preg_match("/([0-9]+)d([0-9]+)h([0-9]+)m/", $date, $result_array) and count($result_array) === 4)
            return time() + ($result_array[1] * 86400) + ($result_array[2] * 3600) + ($result_array[3] * 60);
        return -1;
    }

    /**
     * @param $key
     * @return null
     */
    public function getConfigValue($key)
    {
        $value = $this->getConfig()->getNested($key);
        if($value === null)
        {
            $this->getLogger()->warning($this->getMessage("logger_messages.getConfigValue_01", [$key]));

            return null;
        }

        return $value;
    }

    /**
     * @param null $WorldName
     * @return MPGroup|null
     */
    public function getDefaultGroup($WorldName = null)
    {
        $defaultGroups = [];
        foreach($this->getGroups() as $defaultGroup)
        {
            if($defaultGroup->isDefault($WorldName))
                $defaultGroups[] = $defaultGroup;
        }

        if(count($defaultGroups) === 1)
        {
            return $defaultGroups[0];
        }
        else
        {
            if(count($defaultGroups) > 1)
            {
                $this->getLogger()->warning($this->getMessage("logger_messages.getDefaultGroup_01"));
            }
            elseif(count($defaultGroups) <= 0)
            {
                $this->getLogger()->warning($this->getMessage("logger_messages.getDefaultGroup_02"));
            }

            $this->getLogger()->info($this->getMessage("logger_messages.getDefaultGroup_03"));

            foreach($this->getGroups() as $tempGroup)
            {
                if(count($tempGroup->getParentGroups()) === 0)
                {
                    $this->setDefaultGroup($tempGroup, $WorldName);

                    return $tempGroup;
                }
            }
        }

        return null;
    }

    /**
     * @param $groupName
     * @return MPGroup|null
     */
    public function getGroup($groupName)
    {
        if(!isset($this->groups[$groupName]))
        {
            /** @var MPGroup $group */
            foreach($this->groups as $group)
            {
                if($group->getAlias() === $groupName)
                    return $group;
            }
            $this->getLogger()->debug($this->getMessage("logger_messages.getGroup_01", [$groupName]));
            return null;
        }

        /** @var MPGroup $group */
        $group = $this->groups[$groupName];

        if(empty($group->getData()))
        {
            $this->getLogger()->warning($this->getMessage("logger_messages.getGroup_02", [$groupName]));
            return null;
        }

        return $group;
    }

    /**
     * @return MPGroup[]
     */
    public function getGroups()
    {
        if($this->isGroupsLoaded !== true)
            throw new RuntimeException("No groups loaded, maybe a provider error?");
        return $this->groups;
    }

    public function getMessage($node, array $vars = [])
    {
        return $this->messages->getMessage($node, $vars);
    }


    /**
     * @param MPGroup $group
     * @return array
     */
    public function getOnlinePlayersInGroup(MPGroup $group)
    {
        $users = [];
        foreach($this->getServer()->getOnlinePlayers() as $player)
        {
            foreach($this->getServer()->getWorldManager()->getWorlds() as $World)
            {
                $WorldName = $World->getDisplayName();
                if($this->userDataMgr->getGroup($player, $WorldName) === $group)
                    $users[] = $player;
            }
        }

        return $users;
    }

    /**
     * @param IPlayer $player
     * @param $WorldName
     * @return array
     */
    public function getPermissions(IPlayer $player, $WorldName)
    {
        // TODO: Fix this
        $group = $this->userDataMgr->getGroup($player, $WorldName);
        $groupPerms = $group->getGroupPermissions($WorldName);
        $userPerms = $this->userDataMgr->getUserPermissions($player, $WorldName);

        return array_merge($groupPerms, $userPerms);
    }

    /**
     * @param $userName
     * @return Player
     */
    public function getPlayer($userName)
    {
        $player = $this->getServer()->getPlayerByPrefix($userName);
        return $player instanceof Player ? $player : $this->getServer()->getOfflinePlayer($userName);
    }

    /**
     * @return array
     */
    public function getPocketMinePerms() : array
    {
        return array_keys(PermissionManager::getInstance()->getPermissions());
    }

    /**
     * @return string
     */
    public function getPPVersion()
    {
        return $this->getDescription()->getVersion();
    }

    /**
     * @return ProviderInterface
     */
    public function getProvider()
    {
        if(!$this->isValidProvider())
            $this->setProvider(false);

        return $this->provider;
    }

    /**
     * @return UserDataManager
     */
    public function getUserDataMgr()
    {
        return $this->userDataMgr;
    }

    /**
     * @param Player $player
     * @return null|string
     */
    public function getValidUUID(Player $player) : string
    {
		return $player->getUniqueId()->toString();
    }

    /**
     * @param $groupName
     * @return int
     */
    public function isValidGroupName($groupName)
    {
        return preg_match('/[0-9a-zA-Z\xA1-\xFE]$/', $groupName);
    }

    /**
     * @return bool
     */
    public function isValidProvider()
    {
        if(!isset($this->provider) || ($this->provider === null) || !($this->provider instanceof ProviderInterface))
            return false;
        return true;
    }

    /**
     * @param Player $player
     */
    public function registerPlayer(Player $player)
    {
        $this->getLogger()->debug($this->getMessage("logger_messages.registerPlayer", [$player->getName()]));
        $uniqueId = $this->getValidUUID($player);
        if(!isset($this->attachments[$uniqueId]))
        {
            $attachment = $player->addAttachment($this);
            $this->attachments[$uniqueId] = $attachment;
            $this->updatePermissions($player);
        }
    }

    public function registerPlayers()
    {
        foreach($this->getServer()->getOnlinePlayers() as $player)
        {
            $this->registerPlayer($player);
        }
    }

    /**
     * @param $groupName
     * @return bool
     */
    public function removeGroup($groupName)
    {
        if(!$this->isValidGroupName($groupName))
            return self::INVALID_NAME;
        $groupsData = $this->getProvider()->getGroupsData();
        if(!isset($groupsData[$groupName]))
            return self::NOT_FOUND;
        unset($groupsData[$groupName]);
        $this->getProvider()->setGroupsData($groupsData);
        $this->updateGroups();
        return self::SUCCESS;
    }

    /**
     * @param MPGroup $group
     * @param $WorldName
     */
	public function setDefaultGroup(MPGroup $group, $levelName = null){
		foreach($this->getGroups() as $currentGroup){
			if($levelName === null){
				$isDefault = $currentGroup->getNode("isDefault");

				if($isDefault)
					$currentGroup->removeNode("isDefault");
			}else{
				$isDefault = $currentGroup->getWorldNode($levelName, "isDefault");

				if($isDefault)
					$currentGroup->removeWorldNode($levelName, "isDefault");
			}
		}

		$group->setDefault($levelName);
	}

    /**
     * @param IPlayer $player
     * @param MPGroup $group
     * @param null $WorldName
     * @param int $time
     */
    public function setGroup(IPlayer $player, MPGroup $group, $WorldName = null, $time = -1)
    {
        $this->userDataMgr->setGroup($player, $group, $WorldName, $time);
    }

    public function sortGroupData()
    {
        foreach($this->getGroups() as $groupName => $mpGroup)
        {
            $mpGroup->sortPermissions();

            if($this->getConfigValue("enable-multiworld-perms"))
            {
                /** @var World $World */
                foreach($this->getServer()->getWorldManager()->getWorlds() as $World)
                {
                    $WorldName = $World->getDisplayName();
                    $mpGroup->createWorldData($WorldName);
                }
            }
        }
    }

    public function updateGroups()
    {
        if(!$this->isValidProvider())
            throw new RuntimeException("Failed to load groups: Invalid data provider");
        // Make group list empty first to reload it
        $this->groups = [];
        foreach(array_keys($this->getProvider()->getGroupsData()) as $groupName)
        {
            $this->groups[$groupName] = new MPGroup($this, $groupName);
        }
        if(empty($this->groups))
            throw new RuntimeException("No groups found, I guess there's definitely something wrong with your data provider... *cough cough*");
        $this->isGroupsLoaded = true;
        $this->sortGroupData();
    }

    /**
     * @param IPlayer $player
     * @param string|null $WorldName
     */
    public function updatePermissions(IPlayer $player, string $WorldName = null)
    {
        if($player instanceof Player)
        {
            if($this->getConfigValue("enable-multiworld-perms") == null) {
                $WorldName = null;
            }elseif($WorldName == null) {
                $WorldName = $player->getWorld()->getDisplayName();
            }
            $permissions = [];
            /** @var string $permission */
            foreach($this->getPermissions($player, $WorldName) as $permission)
            {
                if($permission === '*')
                {
                    foreach(PermissionManager::getInstance()->getPermissions() as $tmp)
                    {
                        $permissions[$tmp->getName()] = true;
                    }
                }
                else
                {
                    $isNegative = substr($permission, 0, 1) === "-";
                    if($isNegative)
                        $permission = substr($permission, 1);

                    $permissions[$permission] = !$isNegative;
                }
            }

            $permissions[self::CORE_PERM] = true;
            /* This need run asynk Task */
            /** @var \pocketmine\permission\PermissionAttachment $attachment */
            $attachment = $player->addAttachment($this->getServer()->getPluginManager()->getPlugin('MPEPerms'));
            $attachment->clearPermissions();
            $attachment->setPermissions($permissions); //Tnx you, https://vk.com/stefanfox_dev
            var_dump($attachment->getPermissions());
            /* End */
        }
    }

    /**
     * @param MPGroup $group
     */
    public function updatePlayersInGroup(MPGroup $group)
    {
        foreach($this->getServer()->getOnlinePlayers() as $player)
        {
            if($this->userDataMgr->getGroup($player) === $group)
                $this->updatePermissions($player);
        }
    }

    /**
     * @param Player $player
     */
    public function unregisterPlayer(Player $player)
    {
        $this->getLogger()->debug($this->getMessage("logger_messages.unregisterPlayer", [$player->getName()]));
        $uniqueId = $this->getValidUUID($player);
		if(isset($this->attachments[$uniqueId]))
			$player->removeAttachment($this->attachments[$uniqueId]);
		unset($this->attachments[$uniqueId]);
    }

    public function unregisterPlayers()
    {
        foreach($this->getServer()->getOnlinePlayers() as $player)
        {
            $this->unregisterPlayer($player);
        }
    }
}
