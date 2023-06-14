<?php

declare(strict_types=1);

namespace XackiGiFF\MPEPerms\api\commands;

use XackiGiFF\MPEPerms\cmd\AddGroup;
use XackiGiFF\MPEPerms\cmd\AddParent;
use XackiGiFF\MPEPerms\cmd\DefGroup;
use XackiGiFF\MPEPerms\cmd\FPerms;
use XackiGiFF\MPEPerms\cmd\Groups;
use XackiGiFF\MPEPerms\cmd\GrpInfo;
use XackiGiFF\MPEPerms\cmd\ListGPerms;
use XackiGiFF\MPEPerms\cmd\ListUPerms;
use XackiGiFF\MPEPerms\cmd\MPInfo;
use XackiGiFF\MPEPerms\cmd\MPReload;
use XackiGiFF\MPEPerms\cmd\MPSudo;
use XackiGiFF\MPEPerms\cmd\RmGroup;
use XackiGiFF\MPEPerms\cmd\RmParent;
use XackiGiFF\MPEPerms\cmd\SetGPerm;
use XackiGiFF\MPEPerms\cmd\SetGroup;
use XackiGiFF\MPEPerms\cmd\SetUPerm;
use XackiGiFF\MPEPerms\cmd\UnsetGPerm;
use XackiGiFF\MPEPerms\cmd\UnsetUPerm;
use XackiGiFF\MPEPerms\cmd\UsrInfo;

use XackiGiFF\MPEPerms\MPEPerms;

final class CommandsRegisterAPI {
	/*
		MPEPerms by XackiGiFF (Remake by @mpe_coders from MPEPerms by #64FF00)

		╔═╗╔═╗╔═══╗╔═══╗     ╔═══╗╔═══╗╔═══╗╔═╗╔═╗╔═══╗
		║║╚╝║║║╔═╗║║╔══╝     ║╔═╗║║╔══╝║╔═╗║║║╚╝║║║╔═╗║
		║╔╗╔╗║║╚═╝║║╚══╗     ║╚═╝║║╚══╗║╚═╝║║╔╗╔╗║║╚══╗
		║║║║║║║╔══╝║╔══╝     ║╔══╝║╔══╝║╔╗╔╝║║║║║║╚══╗║
		║║║║║║║║───║╚══╗     ║║───║╚══╗║║║╚╗║║║║║║║╚═╝║
		╚╝╚╝╚╝╚╝───╚═══╝     ╚╝───╚═══╝╚╝╚═╝╚╝╚╝╚╝╚═══╝
	*/

    public function __construct(protected MPEPerms $plugin){
	}

	private $c_namespace = '\\XackiGiFF\\MPEPerms\\cmd\\';

	public function getCommands(): array{
		return array (
			"addgroup" => 
				array("class" => 'AddGroup',
					   "desc" => "cmds.addgroup.desc"),
			"defgroup" => 
				array("class" => 'DefGroup',
					   "desc" => "cmds.defgroup.desc"),
			"fperms"   => 
				array("class" => 'FPerms',
					   "desc" => "cmds.fperms.desc"),
			"groups"   => 
				array("class" => 'Groups',
					   "desc" => "cmds.groups.desc"),
			"ppinfo"   => 
				array("class" => 'MPInfo',
					   "desc" => "cmds.mpinfo.desc"),
	  		"delgroup" => 
				array("class" => 'RmGroup',
					   "desc" => "cmds.rmgroup.desc"),
			"setgroup" => 
				array("class" => 'SetGroup',
					   "desc" => "cmds.setgroup.desc"),
		);
	}

    public function registerCommands(): void{

		$commandMap = $this->plugin->getServer()->getCommandMap();

		foreach($this->getCommands() as $command => $keys){
			$commandMap->register("MPEPerms", new ($this->c_namespace . $keys['class']) ($this->plugin, $command, $this->plugin->getMessage($keys['desc']) ) );
		//if($this->plugin->getNoeulAPI()->isNoeulEnabled())
		//	$commandMap->register("MPEPerms", new MPSudo($this, "ppsudo", $this->plugin->getMessage("cmds.mpsudo.desc") ));
		}

		/* WTF?!
		$commandMap->register("MPEPerms", new AddGroup($this->plugin, "addgroup", $this->plugin->getMessage("cmds.addgroup.desc") ));
		//$commandMap->register("MPEPerms", new AddParent($this->plugin, "addparent", $this->plugin->getMessage("cmds.addparent.desc") ));
		$commandMap->register("MPEPerms", new DefGroup($this->plugin, "defgroup", $this->plugin->getMessage("cmds.defgroup.desc") ));
		$commandMap->register("MPEPerms", new FPerms($this->plugin, "fperms", $this->plugin->getMessage("cmds.fperms.desc") ));
		$commandMap->register("MPEPerms", new Groups($this->plugin, "groups", $this->plugin->getMessage("cmds.groups.desc") ));
		//$commandMap->register("MPEPerms", new GrpInfo($this->plugin, "grpinfo", $this->plugin->getMessage("cmds.grpinfo.desc") ));
		//$commandMap->register("MPEPerms", new ListGPerms($this->plugin, "listgperms", $this->plugin->getMessage("cmds.listgperms.desc") ));
		//$commandMap->register("MPEPerms", new ListUPerms($this->plugin, "listuperms", $this->plugin->getMessage("cmds.listuperms.desc") ));
		$commandMap->register("MPEPerms", new MPInfo($this->plugin, "ppinfo", $this->plugin->getMessage("cmds.mpinfo.desc") ));
		//$commandMap->register("MPEPerms", new MPReload($this->plugin, "ppreload", $this->plugin->getMessage("cmds.mpreload.desc") ));
		$commandMap->register("MPEPerms", new RmGroup($this->plugin, "rmgroup", $this->plugin->getMessage("cmds.rmgroup.desc") ));
		//$commandMap->register("MPEPerms", new RmParent($this->plugin, "rmparent", $this->plugin->getMessage("cmds.rmparent.desc") ));
		//$commandMap->register("MPEPerms", new SetGPerm($this->plugin, "setgperm", $this->plugin->getMessage("cmds.setgperm.desc") ));
		$commandMap->register("MPEPerms", new SetGroup($this->plugin, "setgroup", $this->plugin->getMessage("cmds.setgroup.desc") ));
		//$commandMap->register("MPEPerms", new SetUPerm($this->plugin, "setuperm", $this->plugin->getMessage("cmds.setuperm.desc") ));
		//$commandMap->register("MPEPerms", new UnsetGPerm($this->plugin, "unsetgperm", $this->plugin->getMessage("cmds.unsetgperm.desc") ));
		//$commandMap->register("MPEPerms", new UnsetUPerm($this->plugin, "unsetuperm", $this->plugin->getMessage("cmds.unsetuperm.desc") ));
		//$commandMap->register("MPEPerms", new UsrInfo($this->plugin, "usrinfo", $this->plugin->getMessage("cmds.usrinfo.desc") ));
		*/
	}
}