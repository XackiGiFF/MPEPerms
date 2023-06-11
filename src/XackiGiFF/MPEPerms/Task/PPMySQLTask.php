<?php

namespace XackiGiFF\MPEPerms\Task;

use XackiGiFF\MPEPerms\MPEPerms;

use pocketmine\scheduler\Task;

class PPMySQLTask extends Task
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

    private $db;

    private MPEPerms $plugin;

    /**
     * @param MPEPerms $plugin
     * @param \mysqli $db
     */
    public function __construct(MPEPerms $plugin, \mysqli $db)
    {
        parent::__construct($plugin);
        $this->db = $db;
    }

    public function onRun(): void
    {
        if($this->db->ping())
        {
            $this->plugin->getLogger()->debug("Connected to MySQLi Server");
        }
        else
        {
            $this->plugin->getLogger()->debug("[MySQL] Warning: " . $this->db->error);
        }
    }
}