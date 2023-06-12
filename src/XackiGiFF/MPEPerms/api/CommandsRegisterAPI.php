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
		parent::__construct($plugin);
        $this->registerCommands();
	}

    public static function registerCommands(): void{

		$commandMap = self::getPlugin()->getServer()->getCommandMap();

		//if(self::getPlugin()->getNoeulAPI()->isNoeulEnabled())
			//$commandMap->register("MPEPerms", new MPSudo($this, "ppsudo", self::getPlugin()->getMessage("cmds.mpsudo.desc") ));
			// Лучше не включайте это, если не знаете, как пофикстить полное удаление всех прав у игрока после выхода из аккаунта.
		
		$commandMap->register("MPEPerms", new AddGroup(self::getPlugin(), "addgroup", self::getPlugin()->getMessage("cmds.addgroup.desc") ));
		//$commandMap->register("MPEPerms", new AddParent(self::getPlugin(), "addparent", self::getPlugin()->getMessage("cmds.addparent.desc") ));
		$commandMap->register("MPEPerms", new DefGroup(self::getPlugin(), "defgroup", self::getPlugin()->getMessage("cmds.defgroup.desc") ));
		$commandMap->register("MPEPerms", new FPerms(self::getPlugin(), "fperms", self::getPlugin()->getMessage("cmds.fperms.desc") ));
		$commandMap->register("MPEPerms", new Groups(self::getPlugin(), "groups", self::getPlugin()->getMessage("cmds.groups.desc") ));
		//$commandMap->register("MPEPerms", new GrpInfo(self::getPlugin(), "grpinfo", self::getPlugin()->getMessage("cmds.grpinfo.desc") ));
		//$commandMap->register("MPEPerms", new ListGPerms(self::getPlugin(), "listgperms", self::getPlugin()->getMessage("cmds.listgperms.desc") ));
		//$commandMap->register("MPEPerms", new ListUPerms(self::getPlugin(), "listuperms", self::getPlugin()->getMessage("cmds.listuperms.desc") ));
		$commandMap->register("MPEPerms", new MPInfo(self::getPlugin(), "ppinfo", self::getPlugin()->getMessage("cmds.mpinfo.desc") ));
		//$commandMap->register("MPEPerms", new MPReload(self::getPlugin(), "ppreload", self::getPlugin()->getMessage("cmds.mpreload.desc") ));
		$commandMap->register("MPEPerms", new RmGroup(self::getPlugin(), "rmgroup", self::getPlugin()->getMessage("cmds.rmgroup.desc") ));
		//$commandMap->register("MPEPerms", new RmParent(self::getPlugin(), "rmparent", self::getPlugin()->getMessage("cmds.rmparent.desc") ));
		//$commandMap->register("MPEPerms", new SetGPerm(self::getPlugin(), "setgperm", self::getPlugin()->getMessage("cmds.setgperm.desc") ));
		//$commandMap->register("MPEPerms", new SetGroup(self::getPlugin(), "setgroup", self::getPlugin()->getMessage("cmds.setgroup.desc") ));
		//$commandMap->register("MPEPerms", new SetUPerm(self::getPlugin(), "setuperm", self::getPlugin()->getMessage("cmds.setuperm.desc") ));
		//$commandMap->register("MPEPerms", new UnsetGPerm(self::getPlugin(), "unsetgperm", self::getPlugin()->getMessage("cmds.unsetgperm.desc") ));
		//$commandMap->register("MPEPerms", new UnsetUPerm(self::getPlugin(), "unsetuperm", self::getPlugin()->getMessage("cmds.unsetuperm.desc") ));
		//$commandMap->register("MPEPerms", new UsrInfo(self::getPlugin(), "usrinfo", self::getPlugin()->getMessage("cmds.usrinfo.desc") ));
	}
}