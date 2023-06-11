<?php

namespace XackiGiFF\MPEPerms;

use XackiGiFF\MPEPerms\EventManager\PPGroupChangedEvent;
use XackiGiFF\MPEPerms\EventManager\PPGroupExpiredEvent;
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
     * @param PPGroupChangedEvent $event
     * @priority LOWEST
     */
    public function onGroupChanged(PPGroupChangedEvent $event)
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
        if(!$player instanceof Player) return;

        if(str_starts_with($message, "/")){
            $command = substr($message, 1);
            $args = explode(" ", $command);

			if(!$this->plugin->getNoeulAPI()->isAuthed($player)){
				$event->cancel();

				if($args[0] === "ppsudo" or $args[0] === "help"){
					$this->plugin->getServer()->dispatchCommand($player, $command);
				}else{
					$this->plugin->getNoeulAPI()->sendAuthMsg($player);
				}
			}else{
				$disableOp = $this->plugin->getConfigValue("disable-op");

				if($disableOp and $args[0] === "op"){
					$event->cancel();
                    
                    $player->sendMessage(new Translatable(TextFormat::RED . "%commands.generic.permission"));
				}
			}
        }
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
     * @param PPgroupExpiredEvent $event
     * @priority LOWEST
     */
    public function ongroupExpired(PPgroupExpiredEvent $event)
    {
        $player = $event->getPlayer();
        $this->plugin->setGroup($player, $this->plugin->getDefaultGroup());
    }
}