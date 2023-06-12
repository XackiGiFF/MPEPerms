<?php

declare(strict_types=1);

namespace XackiGiFF\MPEPerms\api;

use XackiGiFF\MPEPerms\MPEPerms;
use XackiGiFF\MPEPerms\api\CommandsRegisterAPI;

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

    public function getTemplate(string $template, $data): string {
		return MPEPerms::MAIN_PREFIX . ' ' . $this->plugin->getMessage($template, $data);
	}

	public function sendTemplate($sender, string $template, $data): void {
		$sender->sendMessage($this->getTemplate($template, $data));
	}
	
	public function getFormat($type) : string {
		return ($type) ? $format = TextFormat::GREEN : $format = TextFormat::RED;
	}

}