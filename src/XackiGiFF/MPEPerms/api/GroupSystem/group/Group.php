<?php

namespace XackiGiFF\MPEPerms\api\GroupSystem\group;

use XackiGiFF\MPEPerms\api\MPEPermsAPI;
use XackiGiFF\MPEPerms\MPEPerms;


class Group {
	/*
		MPEPerms by XackiGiFF (Remake by @mpe_coders from PurePerms by #64FF00)

		╔═╗╔═╗╔═══╗╔═══╗     ╔═══╗╔═══╗╔═══╗╔═╗╔═╗╔═══╗
		║║╚╝║║║╔═╗║║╔══╝     ║╔═╗║║╔══╝║╔═╗║║║╚╝║║║╔═╗║
		║╔╗╔╗║║╚═╝║║╚══╗     ║╚═╝║║╚══╗║╚═╝║║╔╗╔╗║║╚══╗
		║║║║║║║╔══╝║╔══╝     ║╔══╝║╔══╝║╔╗╔╝║║║║║║╚══╗║
		║║║║║║║║───║╚══╗     ║║───║╚══╗║║║╚╗║║║║║║║╚═╝║
		╚╝╚╝╚╝╚╝───╚═══╝     ╚╝───╚═══╝╚╝╚═╝╚╝╚╝╚╝╚═══╝
	*/

	private $name;

	protected $protected;

	private $parents = [];

	public function __construct(protected MPEPerms $plugin, $name){
		$this->name = $name;
	}

	// This is for what?
	//
	// /**
	//  * @return string
	//  */
	// public function __toString(){
	// 	return $this->name;
	// }

	/**
	 * @param Group $group
	 *
	 * @return bool
	 */
	public function addParent(Group $group) {
		$teGroupData = $this->getData();

		if($this === $group || in_array($this->getName(), $group->getParentGroups()))
			return false;

		$teGroupData["inheritance"][] = $group->getName();

		$this->setData($teGroupData);

		$this->plugin->updatePlayersInGroup($this);

		return true;
	}

	/**
	 * @param $levelName
	 */
	public function createWorldData($levelName) {
		if(!isset($this->getData()["worlds"][$levelName])){
			$teGroupData = $this->getData();

			$teGroupData["worlds"][$levelName] = [
				"isDefault" => false,
				"permissions" => [
				]
			];

			$this->setData($teGroupData);
		}
	}

	/**
	 * @return mixed
	 */
	public function getAlias() {
		if($this->getNode("alias") === null)
			return $this->name;

		return $this->getNode("alias");
	}

	/**
	 * @return mixed
	 */
	public function getData() {
		return $this->plugin->getProvider()->getGroupData($this);
	}

	/**
	 * @param null $levelName
	 *
	 * @return array
	 */
	public function getGroupPermissions($levelName = null) {
		
		$permissions = $levelName !== null ? $this->getWorldData($levelName)["permissions"] : $this->getNode("permissions");

		if(!is_array($permissions)){
			$this->plugin->getLogger()->critical("Invalid 'permissions' node given to " . __METHOD__);

			return [];
		}

		/** @var Group $parentGroup */
		foreach($this->getParentGroups() as $parentGroup){
			$parentPermissions = $parentGroup->getGroupPermissions($levelName);

			if($parentPermissions === null)
				$parentPermissions = [];

			// Fixed by @mad-hon (https://github.com/mad-hon) / Tysm! :D
			$permissions = array_merge($parentPermissions, $permissions);
		}

		return $permissions;
	}

	/**
	 * @return mixed
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * @param $node
	 *
	 * @return null|mixed
	 */
	public function getNode($node) {
		if(!isset($this->getData()[$node])) return null;

		return $this->getData()[$node];
	}

	/**
	 * @return Group[]
	 */
	public function getParentGroups() {
		if($this->parents === []){
			if(!is_array($this->getNode("inheritance"))){
				$this->plugin->getLogger()->critical("Invalid 'inheritance' node given to " . __METHOD__);

				return [];
			}

			foreach($this->getNode("inheritance") as $parentGroupName){
				$parentGroup = $this->plugin->getGroup($parentGroupName);

				if($parentGroup !== null)
					$this->parents[] = $parentGroup;
			}
		}
		return $this->parents;
	}

	/**
	 * @param $levelName
	 *
	 * @return null
	 */
	public function getWorldData($levelName) {
		if($levelName === null)
			return null;

		$this->createWorldData($levelName);

		return $this->getData()["worlds"][$levelName];
	}

	/**
	 * @param $levelName
	 * @param $node
	 *
	 * @return null
	 */
	public function getWorldNode($levelName, $node) {
		if(!isset($this->getWorldData($levelName)[$node])) return null;

		return $this->getWorldData($levelName)[$node];
	}

	/**
	 * @param null $levelName
	 *
	 * @return bool
	 */
	public function isDefault($levelName = null) {
		if($levelName === null){
			return ($this->getNode("isDefault") === true);
		}else{
			return (isset($this->getWorldData($levelName)["isDefault"])) ? true : false;
		}
	}

	/**
	 * @param $node
	 */
	public function removeNode($node) {
		$teGroupData = $this->getData();

		if(isset($teGroupData[$node])){
			unset($teGroupData[$node]);

			$this->setData($teGroupData);
		}
	}

	/**
	 * @param Group $group
	 *
	 * @return bool
	 */
	public function removeParent(Group $group) {
		$teGroupData = $this->getData();

		$teGroupData["inheritance"] = array_diff($teGroupData["inheritance"], [$group->getName()]);

		$this->setData($teGroupData);

		$this->plugin->updatePlayersInGroup($this);

		return true;
	}

	/**
	 * @param $levelName
	 * @param $node
	 */
	public function removeWorldNode($levelName, $node) {
		$worldData = $this->getWorldData($levelName);

		if(isset($worldData[$node])){
			unset($worldData[$node]);

			$this->setWorldData($levelName, $worldData);
		}
	}

	/**
	 * @param array $data
	 */
	public function setData(array $data) {
		$this->plugin->getProvider()->setGroupData($this, $data);
	}

	/**
	 * @param null $levelName
	 */
	public function setDefault($levelName = null) {
		if($levelName === null){
			$this->setNode("isDefault", true);
		}else{
			$worldData = $this->getWorldData($levelName);

			$worldData["isDefault"] = true;

			$this->setWorldData($levelName, $worldData);
		}
	}
	
	/**
	 * @param string      $permission
	 * @param string|null $levelName
	 *
	 * @return bool
	 */
	public function setGroupPermission($permission, $levelName = null) {
		if($levelName == null){
			$teGroupData = $this->getData();

			$teGroupData["permissions"][] = $permission;

			$this->setData($teGroupData);
		}else{
			$worldData = $this->getWorldData($levelName);

			$worldData["permissions"][] = $permission;

			$this->setWorldData($levelName, $worldData);
		}

		$this->plugin->updatePlayersInGroup($this);

		return true;
	}

	/**
	 * @param $node
	 * @param $value
	 */
	public function setNode($node, $value) {
		$teGroupData = $this->getData();

		$teGroupData[$node] = $value;

		$this->setData($teGroupData);
	}

	/**
	 * @param       $levelName
	 * @param array $worldData
	 */
	public function setWorldData($levelName, array $worldData) {
		if(isset($this->getData()["worlds"][$levelName])){
			$teGroupData = $this->getData();

			$teGroupData["worlds"][$levelName] = $worldData;

			$this->setData($teGroupData);
		}
	}

	/**
	 * @param $levelName
	 * @param $node
	 * @param $value
	 */
	public function setWorldNode($levelName, $node, $value) {
		$worldData = $this->getWorldData($levelName);

		$worldData[$node] = $value;

		$this->setWorldData($levelName, $worldData);
	}

	public function sortPermissions() {
		$teGroupData = $this->getData();

		if(isset($teGroupData["permissions"])){
			$teGroupData["permissions"] = array_unique($teGroupData["permissions"]);

			sort($teGroupData["permissions"]);
		}

		$isMultiWorldPermsEnabled = $this->plugin->getAPI()->getConfigValue("enable-multiworld-perms");

		if($isMultiWorldPermsEnabled and isset($teGroupData["worlds"])){
			foreach($this->plugin->getServer()->getWorldManager()->getWorlds() as $level){
				$levelName = $level->getFolderName();

				if(isset($teGroupData["worlds"][$levelName])){
					$teGroupData["worlds"][$levelName]["permissions"] = array_unique($teGroupData["worlds"][$levelName]["permissions"]);

					sort($teGroupData["worlds"][$levelName]["permissions"]);
				}
			}
		}

		$this->setData($teGroupData);
	}

	/**
	 * @param      $permission
	 * @param null $levelName
	 *
	 * @return bool
	 */
	public function unsetGroupPermission($permission, $levelName = null) {
		if($levelName == null){
			$teGroupData = $this->getData();

			if(!in_array($permission, $teGroupData["permissions"])) return false;

			$teGroupData["permissions"] = array_diff($teGroupData["permissions"], [$permission]);

			$this->setData($teGroupData);
		}else{
			$worldData = $this->getWorldData($levelName);

			if(!in_array($permission, $worldData["permissions"])) return false;

			$worldData["permissions"] = array_diff($worldData["permissions"], [$permission]);

			$this->setWorldData($levelName, $worldData);
		}

		$this->plugin->updatePlayersInGroup($this);

		return true;
	}
}