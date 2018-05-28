<?php

declare(strict_types=1);

namespace NPC\tasks;

use pocketmine\entity\Entity;
use pocketmine\scheduler\PluginTask;

use NPC\{
	Main, NPCHuman
};

class SneakTask extends PluginTask{

	/** @var Main $plugin */
	/** @var Entity $entity */
	private $plugin, $entity;

	public function __construct(Main $plugin, Entity $entity){
		$this->plugin = $plugin;
		$this->entity = $entity;
		parent::__construct($plugin);
	}

	public function onRun(int $tick){
		$entity = $this->entity;

		if($entity instanceof NPCHuman){
			$entity->setSneaking(true);
		}
	}
}