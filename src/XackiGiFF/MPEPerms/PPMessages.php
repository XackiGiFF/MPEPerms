<?php

namespace XackiGiFF\MPEPerms;              

use pocketmine\utils\Config;
use XackiGiFF\MPEPerms\api\MPEPermsAPI;

class PPMessages extends MPEPermsAPI
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

    /** @var $language */
    private string $language;

    /** @var Config $messages */
    private Config $messages;
    private array $langList = [];

    /**
     * @param MPEPerms $plugin
     */
    public function __construct(protected MPEPerms $plugin)
    {
        parent::__construct($plugin);
        $this->registerLanguages();
        $this->loadMessages();
    }

    public function registerLanguages()
    {
        $result = [];
        foreach($this->plugin->getResources() as $resource)
        {
            if(mb_strpos($resource, "messages-") !== false) $result[] = substr($resource, -6, -4);
        }
        $this->langList = $result;
    }

	public function getMessage($node, array $vars = []): string|null{
		$msg = $this->messages->getNested($node);

		if($msg != null){
			$number = 0;

			foreach($vars as $v){
				$msg = str_replace("%var$number%", $v, $msg);

				$number++;
			}

			return $msg;
		}

		return null;
	}

    /**
     * @return mixed
     */
    public function getVersion() : string
    {
        $version = $this->messages->get("messages-version");
        return $version;
    }

    public function loadMessages()
    {       
        $defaultLang = $this->plugin->getConfigValue("default-language");
        foreach($this->langList as $langName)
        {
            if(strtolower($defaultLang) == $langName)
            {
                $this->language = $langName;
            }
        }
        
        if(!isset($this->language))
        {
            $this->plugin->getLogger()->warning("Language resource " . $defaultLang . " not found. Using default language resource by " . $this->plugin->getDescription()->getAuthors()[0]);
            $this->language = "en";
        }
        
        $this->plugin->saveResource("messages-" . $this->language . ".yml");
        $this->messages = new Config($this->plugin->getDataFolder() . "messages-" . $this->language . ".yml", Config::YAML, [
        ]);
        $this->plugin->getLogger()->info("Setting default language to '" . $defaultLang . "'");
        if(version_compare($this->getVersion(), $this->plugin->getPPVersion()) === -1)
        {
            $this->plugin->saveResource("messages-" . $this->language . ".yml", true);
            $this->messages = new Config($this->plugin->getDataFolder() . "messages-" . $this->language . ".yml", Config::YAML, [
            ]);
        }
    }
    
    public function reloadMessages()
    {
        $this->messages->reload();
    }    
}