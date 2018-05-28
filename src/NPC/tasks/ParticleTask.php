<?php

declare(strict_types=1);

namespace NPC\tasks;

use pocketmine\entity\Entity;
use pocketmine\math\Vector3;
use pocketmine\level\particle\FlameParticle;
use pocketmine\scheduler\PluginTask;

use NPC\{
	Main, NPCHuman
};

class ParticleTask extends PluginTask{

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
			$level = $entity->getLevel();

			if($entity->isAlive()){
				for($yaw = 0; $yaw <= 10; $yaw += 0.5){
					$x = 0.5 * sin($yaw);
					$y = 0.5;
					$z = 0.5 * cos($yaw);
					$level->addParticle(new FlameParticle($entity->add($x, $y, $z)));
				}
			}
		}
	}
}