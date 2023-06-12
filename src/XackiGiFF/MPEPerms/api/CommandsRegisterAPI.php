<?php

declare(strict_types=1);

namespace XackiGiFF\MPEPerms\api;

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

use XackiGiFF\MPEPerms\api\MPEPermsAPI;

use XackiGiFF\MPEPerms\MPEPerms;

final class CommandsRegisterAPI extends MPEPermsAPI
{
    public function __construct(protected MPEPerms $plugin){
	}

    public function registerCommands(): void{

		$commandMap = $this->plugin->getServer()->getCommandMap();

		//if($this->plugin->getNoeulAPI()->isNoeulEnabled())
			//$commandMap->register("MPEPerms", new MPSudo($this, "ppsudo", $this->plugin->getMessage("cmds.mpsudo.desc") ));
			// Лучше не включайте это, если не знаете, как пофикстить полное удаление всех прав у игрока после выхода из аккаунта.
		
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
	}
}