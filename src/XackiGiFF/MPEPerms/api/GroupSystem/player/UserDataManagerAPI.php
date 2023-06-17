<?php

namespace XackiGiFF\MPEPerms\api\GroupSystem\player;

use pocketmine\permission\DefaultPermissions;
use pocketmine\permission\PermissionAttachment;
use pocketmine\permission\PermissionManager;
use pocketmine\player\Player;
use XackiGiFF\MPEPerms\api\GroupSystem\group\Group;
use XackiGiFF\MPEPerms\MPEPerms;
use XackiGiFF\MPEPerms\EventManager\GroupChangedEvent;

use pocketmine\player\IPlayer;

class UserDataManagerAPI {
	/*
		MPEPerms by XackiGiFF (Remake by @mpe_coders from MPEPerms by #64FF00)

		╔═╗╔═╗╔═══╗╔═══╗     ╔═══╗╔═══╗╔═══╗╔═╗╔═╗╔═══╗
		║║╚╝║║║╔═╗║║╔══╝     ║╔═╗║║╔══╝║╔═╗║║║╚╝║║║╔═╗║
		║╔╗╔╗║║╚═╝║║╚══╗     ║╚═╝║║╚══╗║╚═╝║║╔╗╔╗║║╚══╗
		║║║║║║║╔══╝║╔══╝     ║╔══╝║╔══╝║╔╗╔╝║║║║║║╚══╗║
		║║║║║║║║───║╚══╗     ║║───║╚══╗║║║╚╗║║║║║║║╚═╝║
		╚╝╚╝╚╝╚╝───╚═══╝     ╚╝───╚═══╝╚╝╚═╝╚╝╚╝╚╝╚═══╝
	*/

    private array $attachments = [];

    /**
     * @param MPEPerms $plugin
     */
    public function __construct(protected MPEPerms $plugin)
    {

    }

    /**
     * @param IPlayer $player
     * @return array
     */
    public function getData(IPlayer $player): array
    {
        return $this->plugin->getProvider()->getPlayerData($player);
    }

    public function getExpDate(IPlayer $player, $WorldName = null)
    {

        return ($WorldName !== null) ? $this->getWorldData($player, $WorldName)["expTime"] : $this->getNode($player, "expTime");
    }

    /**
     * @param IPlayer $player
     * @param null $WorldName
     * @return Group|null
     */
    public function getGroup(IPlayer $player, $WorldName = null): ?Group
    {
        $groupName = ($WorldName !== null) ? $this->getWorldData($player, $WorldName)["group"] : $this->getNode($player, "group");
        $group = $this->plugin->getGroup($groupName);
        if($group === null)
        {
            $this->plugin->getLogger()->critical("Invalid group name found in " . $player->getName() . "'s player data (World: " . ($WorldName === null ? "GLOBAL" : $WorldName) . ")");
            $this->plugin->getLogger()->critical("Restoring the group data to 'default'");
            $defaultGroup = $this->plugin->getDefaultGroup($WorldName);
            $this->setGroup($player, $defaultGroup, $WorldName);
            return $defaultGroup;
        }

        return $group;
    }

    /**
     * @param IPlayer $player
     * @param $node
     * @return null|mixed
     */
    public function getNode(IPlayer $player, $node): mixed
    {
        $userData = $this->getData($player);
        if(!isset($userData[$node]))
            return null;
        return $userData[$node];
    }

    /**
     * @param null $WorldName
     * @return array
     */
    public function getUserPermissions(IPlayer $player, $WorldName = null): array
    {
        $permissions = $WorldName != null ? $this->getWorldData($player, $WorldName)["permissions"] : $this->getNode($player, "permissions");
        if(!is_array($permissions))
        {
            $this->plugin->getLogger()->critical("Invalid 'permissions' node given to " . __METHOD__ . '()');
            return [];
        }
        return $permissions;
    }

    /**
     * @param IPlayer $player
     * @param $WorldName
     * @return array
     */
    public function getWorldData(IPlayer $player, $WorldName): array
    {
        if($WorldName === null)
            $WorldName = $this->plugin->getServer()->getWorldManager()->getDefaultWorld()->getDisplayName();
        if(!isset($this->getData($player)["worlds"][$WorldName]))
            return [
                "group" => $this->plugin->getDefaultGroup($WorldName)->getName(),
                "permissions" => [
                ],
                "expTime" => -1
            ];
        return $this->getData($player)["worlds"][$WorldName];
    }

    public function removeNode(IPlayer $player, $node): void
    {
        $tempUserData = $this->getData($player);
        if(isset($tempUserData[$node]))
        {
            unset($tempUserData[$node]);
            $this->setData($player, $tempUserData);
        }
    }

    /**
     * @param IPlayer $player
     * @param array $data
     */
    public function setData(IPlayer $player, array $data): void
    {
        $this->plugin->getProvider()->setPlayerData($player, $data);
    }

    /**
     * @param IPlayer $player
     * @param Group $group
     * @param $WorldName
     * @param int $time
     */
    public function setGroup(IPlayer $player, Group $group, $WorldName, $time = -1): void
    {
        if($WorldName === null)
        {
            $this->setNode($player, "group", $group->getName());
            $this->setNode($player, "expTime", $time);
        }
        else
        {
            $worldData = $this->getWorldData($player, $WorldName);
            $worldData["group"] = $group->getName();
            $worldData["expTime"] = $time;
            $this->setWorldData($player, $WorldName, $worldData);
        }

        $event = new GroupChangedEvent($this->plugin, $player, $group, $WorldName);

        $event->call();
    }

    /**
     * @param IPlayer $player
     * @param $node
     * @param $value
     */
    public function setNode(IPlayer $player, $node, $value): void
    {
        $tempUserData = $this->getData($player);
        $tempUserData[$node] = $value;
        $this->setData($player, $tempUserData);
    }

    /**
     * @param IPlayer $player
     * @param $permission
     * @param null $WorldName
     */
    public function setPermission(IPlayer $player, $permission, $WorldName = null): void
    {
        if($WorldName === null)
        {
            $tempUserData = $this->getData($player);
            $tempUserData["permissions"][] = $permission;
            $this->setData($player, $tempUserData);
        }
        else
        {
            $worldData = $this->getWorldData($player, $WorldName);
            $worldData["permissions"][] = $permission;
            $this->setWorldData($player, $WorldName, $worldData);
        }

        $this->plugin->updatePermissions($player);
    }

    public function setWorldData(IPlayer $player, $WorldName, array $worldData): void
    {
        $tempUserData = $this->getData($player);
        if(!isset($this->getData($player)["worlds"][$WorldName]))
        {
            $tempUserData["worlds"][$WorldName] = [
                "group" => $this->plugin->getDefaultGroup()->getName(),
                "permissions" => [
                ],
                "expTime" => -1
            ];

            $this->setData($player, $tempUserData);
        }
        $tempUserData["worlds"][$WorldName] = $worldData;
        $this->setData($player, $tempUserData);
    }

    /**
     * @param IPlayer $player
     * @param $permission
     * @param null $WorldName
     */
    public function unsetPermission(IPlayer $player, $permission, $WorldName = null): void
    {
        if($WorldName === null)
        {
            $tempUserData = $this->getData($player);
            if(!in_array($permission, $tempUserData["permissions"])) return;
            $tempUserData["permissions"] = array_diff($tempUserData["permissions"], [$permission]);
            $this->setData($player, $tempUserData);
        }
        else
        {
            $worldData = $this->getWorldData($player, $WorldName);
            if(!in_array($permission, $worldData["permissions"])) return;
            $worldData["permissions"] = array_diff($worldData["permissions"], [$permission]);
            $this->setWorldData($player, $WorldName, $worldData);
        }

        $this->plugin->updatePermissions($player);
    }
    public function getPermissions(IPlayer $player, $WorldName): array{
        $group = $this->plugin->getAPI()->getUserDataMgr()->getGroup($player, $WorldName);
        $groupPerms = $group->getGroupPermissions($WorldName);
        $userPerms = $this->plugin->getAPI()->getUserDataMgr()->getUserPermissions($player, $WorldName);

        return array_merge($groupPerms, $userPerms);
    }

    public function getPlayer($userName): Player|IPlayer{
        $player = $this->plugin->getServer()->getPlayerExact($userName);
        return $player instanceof Player ? $player : $this->plugin->getServer()->getOfflinePlayer($userName);
    }

    public function getPocketMinePerms() : array {
        return array_keys(PermissionManager::getInstance()->getPermissions());
    }

    public function getValidUUID(Player $player) : null|string{
        return $player->getUniqueId()->toString();
    }

    public function registerPlayer(Player $player): void
    {
        $this->plugin->getLogger()->debug($this->plugin->getMessage("logger_messages.registerPlayer", [$player->getName()]));
        $uniqueId = $this->getValidUUID($player);
        if(!isset($this->attachments[$uniqueId]))
        {
            $attachment = $player->addAttachment($this->plugin);
            $this->attachments[$uniqueId] = $attachment;
            $this->updatePermissions($player);
        }
    }

    public function registerPlayers(): void
    {
        foreach($this->plugin->getServer()->getOnlinePlayers() as $player)
        {
            $this->registerPlayer($player);
        }
    }

    public function updatePermissions(IPlayer $player, string $WorldName = null): void{
        if($player instanceof Player)
        {
            if($this->plugin->getAPI()->getConfigValue("enable-multiworld-perms") == null) {
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
                    $player->addAttachment($this->plugin, DefaultPermissions::ROOT_OPERATOR, true);
                }
                else
                {
                    $isNegative = str_starts_with($permission, "-");
                    if($isNegative)
                        $permission = substr($permission, 1);

                    $permissions[$permission] = !$isNegative;
                }
            }

            $permissions[MPEPerms::CORE_PERM] = true;
            /* This need run asynk Task */
            /** @var PermissionAttachment $attachment */
            $attachment = $player->addAttachment($this->plugin->getServer()->getPluginManager()->getPlugin('MPEPerms'));
            $attachment->clearPermissions();
            $attachment->setPermissions($permissions); //Tnx you, https://vk.com/stefanfox_dev
            var_dump($attachment->getPermissions());
            /* End */
        }
    }

    public function unregisterPlayer(Player $player): void
    {
        $this->plugin->getLogger()->debug($this->plugin->getMessage("logger_messages.unregisterPlayer", [$player->getName()]));
        $uniqueId = $this->getValidUUID($player);
        if(isset($this->attachments[$uniqueId]))
            $player->removeAttachment($this->attachments[$uniqueId]);
        unset($this->attachments[$uniqueId]);
    }

    public function unregisterPlayers(): void
    {
        foreach($this->plugin->getServer()->getOnlinePlayers() as $player)
        {
            $this->unregisterPlayer($player);
        }
    }
}