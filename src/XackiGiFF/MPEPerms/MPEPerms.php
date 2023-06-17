<?php

namespace XackiGiFF\MPEPerms;

use XackiGiFF\MPEPerms\api\MPEPermsAPI;

use XackiGiFF\MPEPerms\api\GroupSystem\group\Group;
use XackiGiFF\MPEPerms\api\GroupSystem\player\UserDataManagerAPI;

use XackiGiFF\MPEPerms\api\services\providers\SQLite3Provider;
use XackiGiFF\MPEPerms\api\services\providers\DefaultProvider;
use XackiGiFF\MPEPerms\api\services\providers\MySQLProvider;
use XackiGiFF\MPEPerms\api\services\providers\YamlV1Provider;
use XackiGiFF\MPEPerms\api\services\providers\ProviderInterface;
use XackiGiFF\MPEPerms\api\services\providers\JsonProvider;

use pocketmine\permission\DefaultPermissions;
use pocketmine\permission\PermissionAttachment;
use pocketmine\permission\PermissionManager;

use pocketmine\player\IPlayer;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;

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

    private PPMessages $messages;

    private ProviderInterface $provider;

	public MPEPermsAPI $api;

    private array $attachments = [];

    public function onLoad(): void {
        $this->saveDefaultConfig();

        $this->api = new MPEPermsAPI($this);

        $this->api->fixConfig();
        
        $this->messages = new PPMessages($this);

        if($this->getAPI()->getConfigValue("enable-multiworld-perms") === false){
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

//
// GroupsAPI
//

    public function addGroup($groupName): int{
        return $this->getAPI()->addGroup($groupName);
    }

    public function getDefaultGroup($WorldName = null): Group|null{
        return $this->getAPI()->getDefaultGroup($WorldName = null);
    }

    public function getGroup($groupName): Group|null{
        return $this->getAPI()->getGroup($groupName);
    }

    public function getGroups(): array{
        return $this->getAPI()->getGroups();
    }

    public function getOnlinePlayersInGroup(Group $group): array{
        return $this->getAPI()->getOnlinePlayersInGroup($group);
    }

    public function isValidGroupName($groupName): int|false{
        return $this->getAPI()->isValidGroupName($groupName);
    }

    public function removeGroup($groupName): int{
        return $this->getAPI()->removeGroup($groupName);
    }

    public function sortGroupData(Group $groupName): void{
        $this->getAPI()->sortGroupData($groupName);
    }

    public function setDefaultGroup(Group $group, $levelName = null): void{
        $this->getAPI()->sortGroupData($group, $levelName = null);
    }

    public function setGroup(IPlayer $player, Group $group, $WorldName = null, $time = -1): void
    {
        $this->getAPI()->setGroup($player, $group, $WorldName, $time);
    }

    public function updateGroups(): void{
        $this->getAPI()->updateGroups();
    }

    public function updatePlayersInGroup(Group $group): void{
        $this->getAPI()->updatePlayersInGroup($group);
    }

    /* End Block Group? Need MORE!!! */

//
// UtilsAPI
//

    public function getAPI(): MPEPermsAPI
    {
        return $this->api;
    }

    public function getPPVersion(): string{
        return $this->getDescription()->getVersion();
    }

    public function getUserDataMgr(): UserDataManagerAPI{
        return $this->getAPI()->getUserDataMgr();
    }

    public function date2Int($date): int{
        return $this->getAPI()->getUtils()->date2Int($date);
    }

    public function getMessage($node, array $vars = []): ?string
    {
        return $this->messages->getMessage($node, $vars);
    }

// Players & Permissions
// TODO
    public function getPermissions(IPlayer $player, $WorldName): array{
        // TODO: Fix this
        $group = $this->getUserDataMgr()->getGroup($player, $WorldName);
        $groupPerms = $group->getGroupPermissions($WorldName);
        $userPerms = $this->getUserDataMgr()->getUserPermissions($player, $WorldName);

        return array_merge($groupPerms, $userPerms);
    }
// TODO
    public function getPlayer($userName): Player|IPlayer{
        $player = $this->getServer()->getPlayerExact($userName);
        return $player instanceof Player ? $player : $this->getServer()->getOfflinePlayer($userName);
    }
// TODO
    public function getPocketMinePerms() : array {
        return array_keys(PermissionManager::getInstance()->getPermissions());
    }
// TODO
    public function getValidUUID(Player $player) : null|string{
        return $player->getUniqueId()->toString();
    }
// TODO
    public function registerPlayer(Player $player): void
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
// TODO
    public function registerPlayers(): void
    {
        foreach($this->getServer()->getOnlinePlayers() as $player)
        {
            $this->registerPlayer($player);
        }
    }

// TODO | Fixed
    public function updatePermissions(IPlayer $player, string $WorldName = null): void{
        if($player instanceof Player)
        {
            if($this->getAPI()->getConfigValue("enable-multiworld-perms") == null) {
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
                    $isNegative = str_starts_with($permission, "-");
                    if($isNegative)
                        $permission = substr($permission, 1);

                    $permissions[$permission] = !$isNegative;
                }
            }

            $permissions[self::CORE_PERM] = true;
            /* This need run asynk Task */
            /** @var PermissionAttachment $attachment */
            $attachment = $player->addAttachment($this->getServer()->getPluginManager()->getPlugin('MPEPerms'));
            $attachment->clearPermissions();
            $attachment->setPermissions($permissions); //Tnx you, https://vk.com/stefanfox_dev
            var_dump($attachment->getPermissions());
            /* End */
        }
    }
// TODO
    public function unregisterPlayer(Player $player): void
    {
        $this->getLogger()->debug($this->getMessage("logger_messages.unregisterPlayer", [$player->getName()]));
        $uniqueId = $this->getValidUUID($player);
        if(isset($this->attachments[$uniqueId]))
            $player->removeAttachment($this->attachments[$uniqueId]);
        unset($this->attachments[$uniqueId]);
    }
// TODO
public function unregisterPlayers(): void
{
    foreach($this->getServer()->getOnlinePlayers() as $player)
    {
        $this->unregisterPlayer($player);
    }
}

// Provider
// TODO
    public function isValidProvider(): bool{
        if(!isset($this->provider))
            return false;
        return true;
    }

// TODO
    public function getProvider(): ProviderInterface{
        if(!$this->isValidProvider())
            $this->setProvider(false);

        return $this->provider;
    }

// TODO
    private function setProvider($onEnable = true): void
    {
        $providerName = $this->getAPI()->getConfigValue("data-provider");
        switch(strtolower($providerName))
        {
            case "mysql":
                $provider = new MySQLProvider($this);
                if($onEnable === true)
                    $this->getLogger()->notice($this->getMessage("logger_messages.setProvider_MySQL"));
                break;
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
            $this->provider = $provider;
        $this->updateGroups();
    }

}
