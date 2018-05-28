<?php

declare(strict_types=1);

namespace NPC;

use pocketmine\Player;
use pocketmine\entity\Entity;
use pocketmine\command\{
	Command, CommandSender
};
use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat;

class Main extends PluginBase{

	public function onEnable(){
		Entity::registerEntity(NPCHuman::class, true);
	}

	public function onCommand(CommandSender $sender, Command $cmd, string $label, array $args): bool{
		if(count($args) < 1){
			$sender->sendMessage("Usage: /npc <name>");
			return false;
		}

		$this->spawnNPC($sender, $args[0]);
		$sender->sendMessage(TextFormat::GREEN . "Spawned NPC: " . $args[0]);
		return true;
	}

	public function spawnNPC(Player $player, string $name){
		$nbt = Entity::createBaseNBT($player, null, 2, 2);
		$nbt->setTag($player->namedtag->getTag("Skin"));
		$npc = new NPCHuman($player->getLevel(), $nbt);
		$npc->setNameTag($name);
		$npc->setNameTagAlwaysVisible(true);
		$npc->spawnToAll();
	}
}