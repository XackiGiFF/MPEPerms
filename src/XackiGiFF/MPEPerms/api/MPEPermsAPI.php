<?php

declare(strict_types=1);

namespace XackiGiFF\MPEPerms\api;

use XackiGiFF\MPEPerms\MPEPerms;
use XackiGiFF\MPEPerms\api\CommandsRegisterAPI;

use pocketmine\utils\TextFormat;

class MPEPermsAPI {
	/*
		MPEPerms by XackiGiFF (Remake by @mpe_coders from MPEPerms by #64FF00)

		╔═╗╔═╗╔═══╗╔═══╗     ╔═══╗╔═══╗╔═══╗╔═╗╔═╗╔═══╗
		║║╚╝║║║╔═╗║║╔══╝     ║╔═╗║║╔══╝║╔═╗║║║╚╝║║║╔═╗║
		║╔╗╔╗║║╚═╝║║╚══╗     ║╚═╝║║╚══╗║╚═╝║║╔╗╔╗║║╚══╗
		║║║║║║║╔══╝║╔══╝     ║╔══╝║╔══╝║╔╗╔╝║║║║║║╚══╗║
		║║║║║║║║───║╚══╗     ║║───║╚══╗║║║╚╗║║║║║║║╚═╝║
		╚╝╚╝╚╝╚╝───╚═══╝     ╚╝───╚═══╝╚╝╚═╝╚╝╚╝╚╝╚═══╝
	*/

	public static $instance;

    public function __construct(protected MPEPerms $plugin) {
		self::$instance = $this;
    }

    public static function getAPI() {
        return self::$instance;
    }

	public static function getPlugin(): MPEPerms {
        return self::getAPI()->plugin;
    }

    public static function registerCommands(): void {
        CommandsRegisterAPI::registerCommands();
    }

    public function getTemplate(string $template, bool $type = true, array $data = []): string {
		($type) ? $format = TextFormat::GREEN : $format = TextFormat::RED;
		return $format . MPEPerms::MAIN_PREFIX . ' ' . $this->plugin->getMessage($template, $data);
	}

	public function sendTemplate($sender, string $template, bool $type = true, array $data = []): void {
		$sender->sendMessage($this->getTemplate($template, $type = true, $data));
	}

}