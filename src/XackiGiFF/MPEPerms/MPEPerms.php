<?php

namespace XackiGiFF\MPEPerms;

use XackiGiFF\MPEPerms\api\MPEPermsAPI;
use XackiGiFF\MPEPerms\MPGroup;

use XackiGiFF\MPEPerms\DataManager\UserDataManager;

use XackiGiFF\MPEPerms\DataProviders\SQLite3Provider;
use XackiGiFF\MPEPerms\DataProviders\DefaultProvider;
use XackiGiFF\MPEPerms\DataProviders\MySQLProvider;
use XackiGiFF\MPEPerms\DataProviders\YamlV1Provider;
use XackiGiFF\MPEPerms\DataProviders\ProviderInterface;
use XackiGiFF\MPEPerms\DataProviders\JsonProvider;

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

class MPEPerms extends PluginBase {
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

    /** @var PPMessages $messages */
    private $messages;

    /** @var ProviderInterface $provider */
    private $provider;

    /** @var UserDataManager $userDataMgr */
    private $userDataMgr;

    /** @var MPEPermsAPI $api */
	public $api;

    private $attachments = [], $groups = [], $pmDefaultPerms = [];

    public function onLoad(): void {
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
    
    public function onEnable(): void {
        $this->setProvider();
        $this->registerPlayers();
        $this->api->registerCommands();
        $this->api->startService();
    }

    public function onDisable(): void {
        $this->unregisterPlayers();
        if($this->isValidProvider())
            $this->provider->close();
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

    public function getAPI(): MPEPermsAPI{
		return $this->api;
	}

//
// GroupsAPI
//

    public function addGroup($groupName): int{
        return $this->getAPI()->addGroup($groupName);
    }

// TODO
    public function getDefaultGroup($WorldName = null): MPGroup|null{
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

// TODO
    public function getGroup($groupName): MPGroup|null{
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

    public function getGroups(): array{
        return $this->getAPI()->getGroups();
    }

// TODO
    public function getOnlinePlayersInGroup(MPGroup $group): array{
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

    public function isValidGroupName($groupName): int|false{
        return $this->getAPI()->isValidGroupName($groupName);
    }

    public function removeGroup($groupName): int{
        return $this->getAPI()->removeGroup($groupName);
    }

    public function sortGroupData(): void{
        $this->getAPI()->sortGroupData($groupName);
    }
// TODO
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

// TODO
    public function setGroup(IPlayer $player, MPGroup $group, $WorldName = null, $time = -1) {
        $this->userDataMgr->setGroup($player, $group, $WorldName, $time);
    }

    public function updateGroups(): void{
        $this->getAPI()->updateGroups();
    }
// TODO
    public function updatePlayersInGroup(MPGroup $group) {
        foreach($this->getServer()->getOnlinePlayers() as $player)
        {
            if($this->userDataMgr->getGroup($player) === $group)
                $this->updatePermissions($player);
        }
    }

//
// UtilsAPI
//

    public function date2Int($date): int{
        return $this->getAPI()->getUtils()->date2Int($date);
    }

    public function getPPVersion(): string{
        return $this->getDescription()->getVersion();
    }

// Config?
    public function getConfigValue($key): string|null{
        $value = $this->getConfig()->getNested($key);
        if($value === null)
        {
            $this->getLogger()->warning($this->getMessage("logger_messages.getConfigValue_01", [$key]));

            return null;
        }

        return $value;
    }

// Visual?
    public function getMessage($node, array $vars = []) {
        return $this->messages->getMessage($node, $vars);
    }

// Players & Permissions
    public function getPermissions(IPlayer $player, $WorldName): array{
        // TODO: Fix this
        $group = $this->userDataMgr->getGroup($player, $WorldName);
        $groupPerms = $group->getGroupPermissions($WorldName);
        $userPerms = $this->userDataMgr->getUserPermissions($player, $WorldName);

        return array_merge($groupPerms, $userPerms);
    }

    public function getPlayer($userName): Player{
        $player = $this->getServer()->getPlayerByPrefix($userName);
        return $player instanceof Player ? $player : $this->getServer()->getOfflinePlayer($userName);
    }

    public function getPocketMinePerms() : array {
        return array_keys(PermissionManager::getInstance()->getPermissions());
    }


// Provider
    public function getProvider(): ProviderInterface{
        if(!$this->isValidProvider())
            $this->setProvider(false);

        return $this->provider;
    }
// Provider
    public function isValidProvider(): bool{
        if(!isset($this->provider) || ($this->provider === null) || !($this->provider instanceof ProviderInterface))
            return false;
        return true;
    }
//Provider
    private function setProvider($onEnable = true) {
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

// For API
    public function getUserDataMgr(): UserDataManager{
        return $this->userDataMgr;
    }

// Players & Permissions
    public function getValidUUID(Player $player) : null|string{
		return $player->getUniqueId()->toString();
    }

    public function registerPlayer(Player $player) {
        $this->getLogger()->debug($this->getMessage("logger_messages.registerPlayer", [$player->getName()]));
        $uniqueId = $this->getValidUUID($player);
        if(!isset($this->attachments[$uniqueId]))
        {
            $attachment = $player->addAttachment($this);
            $this->attachments[$uniqueId] = $attachment;
            $this->updatePermissions($player);
        }
    }

    public function registerPlayers() {
        foreach($this->getServer()->getOnlinePlayers() as $player)
        {
            $this->registerPlayer($player);
        }
    }

    public function updatePermissions(IPlayer $player, string $WorldName = null): string|null{
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
                    $player->addAttachment($this, DefaultPermissions::ROOT_OPERATOR, true);
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

    public function unregisterPlayer(Player $player) {
        $this->getLogger()->debug($this->getMessage("logger_messages.unregisterPlayer", [$player->getName()]));
        $uniqueId = $this->getValidUUID($player);
		if(isset($this->attachments[$uniqueId]))
			$player->removeAttachment($this->attachments[$uniqueId]);
		unset($this->attachments[$uniqueId]);
    }

    public function unregisterPlayers() {
        foreach($this->getServer()->getOnlinePlayers() as $player)
        {
            $this->unregisterPlayer($player);
        }
    }
}
