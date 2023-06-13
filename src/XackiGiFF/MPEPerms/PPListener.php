<?php

namespace XackiGiFF\MPEPerms;

use XackiGiFF\MPEPerms\EventManager\MPGroupChangedEvent;
use XackiGiFF\MPEPerms\EventManager\MPGroupExpiredEvent;
use pocketmine\event\Listener;
use pocketmine\event\entity\EntityTeleportEvent;
use pocketmine\event\server\CommandEvent;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\event\player\PlayerLoginEvent;
use pocketmine\lang\Translatable;
use pocketmine\player\Player;
use pocketmine\utils\TextFormat;

class PPListener implements Listener
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

    /**
     * @param MPEPerms $plugin
     */
    public function __construct(protected MPEPerms $plugin)
    {
        //parent::__construct($plugin);
    }

    /**
     * @param MPGroupChangedEvent $event
     * @priority LOWEST
     */
    public function onGroupChanged(MPGroupChangedEvent $event)
    {
        $player = $event->getPlayer();
        $this->plugin->updatePermissions($player);
    }

    /**
     * @param EntityTeleportEvent $event
     * @priority MONITOR
     */
    public function onLevelChange(EntityTeleportEvent $event)
    {
        if($event->isCancelled()) return;
        $player = $event->getEntity();
        if($player instanceof Player) {
            $this->plugin->updatePermissions($player, $event->getTo()->getWorld()->getDisplayName());
        }
    }

    public function onPlayerCommand(CommandEvent $event)
    {
        $message = $event->getCommand();
		$player = $event->getSender();
        // TODO проверка на команды и на то, включен ли NoeulAPI

        /*
        if(!$player instanceof Player) return;

			if(!$this->plugin->getAPI()->getNoeulAPI()->isAuthed($player)){
				$event->cancel();

				if($args[0] === "ppsudo" or $args[0] === "help"){
					$this->plugin->getServer()->dispatchCommand($player, $command);
				}else{
					$this->plugin->getAPI()->getNoeulAPI()->sendAuthMsg($player);
				}
			}else{
				$disableOp = $this->plugin->getAPI()->getConfigValue("disable-op");

				if($disableOp and $args[0] === "op"){
					$event->cancel();
                    
                    $player->sendMessage(new Translatable(TextFormat::RED . "%commands.generic.permission"));
				}
			}
        */
    }

    /**
     * @param PlayerLoginEvent $event
     * @priority LOWEST
     */
    public function onPlayerLogin(PlayerLoginEvent $event)
    {
        $player = $event->getPlayer();
        $this->plugin->registerPlayer($player);
    }

    /**
     * @param PlayerQuitEvent $event
     * @priority HIGHEST
     */
    public function onPlayerQuit(PlayerQuitEvent $event)
    {
        $player = $event->getPlayer();
        $this->plugin->unregisterPlayer($player);
    }

    /**
     * @param MPgroupExpiredEvent $event
     * @priority LOWEST
     */
    public function ongroupExpired(MPGroupExpiredEvent $event)
    {
        $player = $event->getPlayer();
        $this->plugin->setGroup($player, $this->plugin->getDefaultGroup());
    }
}