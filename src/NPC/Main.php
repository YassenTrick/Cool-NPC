<?php

declare(strict_types=1);

namespace NPC;

use pocketmine\Player;
use pocketmine\entity\Entity;
use pocketmine\event\entity\EntitySpawnEvent;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\Listener;
use pocketmine\command\{
	Command, CommandSender
};
use pocketmine\plugin\PluginBase;
use pocketmine\utils\TextFormat;
use pocketmine\utils\Config;

use NPC\tasks\NPCTask;

class Main extends PluginBase implements Listener{
	
	public function onEnable(){
		if(!is_dir($this->getDataFolder())){
			@mkdir($this->getDataFolder());
		}
		
		//Config stuff
		$this->saveResource("config.yml");
		$config = new Config($this->getDataFolder() . "config.yml", Config::YAML);
		if($config->get("sneak")){
			$this->getLogger()->info("Enabling sneaking...");
		}
		if($config->get("unsneak")){
			$this->getLogger()->info("Enabling un-sneaking...");
		}
		if($config->get("particles")){
			$this->getLogger()->info("Enabling particles...");
		}
		if($config->get("maths")){
			$this->getLogger()->info("Enabling maths...");
		}
		
		$this->getLogger()->info("Plugin enabled!");
		
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
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
	
	//Quick maths
	//Credits to FreeGamingHere
	public function onChat(PlayerChatEvent $event){
		$config = new Config($this->getDataFolder() . "config.yml", Config::YAML);
		if($config->get("maths")){
			$msg = $event->getMessage();
			$player = $event->getPlayer();
			$prefix = $config->get("prefix");
			if($msg[0] == $prefix){
				foreach($this->getServer()->getOnlinePlayers() as $p){
					if(!is_numeric($msg[1]) or !is_numeric($msg[3]) or $msg[2] !== "+" or $msg[2] !== "-" or $msg[2] !== "*" or $msg[2] !== "/"){
						$player->sendMessage(TextFormat::GREEN . "[Bot]" . TextFormat::RED . "Usage: " . $prefix . " {number} + or - or * or / {number}");
						$event->setCancelled();
						return;
					} else {
						if($msg[2] == "+"){
							$p->sendMessage(TextFormat::GREEN . "[Bot]" . TextFormat::BLUE . $msg[1] . " plus " . $msg[3] . " equals " . (int)$msg[1] + (int)$msg[3]);
							return;
						} elseif($msg[2] == "-"){
							$p->sendMessage(TextFormat::GREEN . "[Bot]" . TextFormat::BLUE . $msg[1] . " minus " . $msg[3] . " equals " . (int)$msg[1] - (int)$msg[3]);
							return;
						} elseif($msg[2] == "*"){
							$p->sendMessage(TextFormat::GREEN . "[Bot]" . TextFormat::BLUE . $msg[1] . " times " . $msg[3] . " equals " . (int)$msg[1] * (int)$msg[3]);
							return;
						} elseif($msg[2] == "/"){
							$p->sendMessage(TextFormat::GREEN . "[Bot]" . TextFormat::BLUE . $msg[1] . " devided by " . $msg[3] . " equals " . (int)$msg[1] / (int)$msg[3]);
							return;
						}
					}
					if($msg == $prefix . " 2 + 2 - 1"){
						$p->sendMessage(TextFormat::GREEN . "[Big Shaq]" . TextFormat::BLUE . "Two plus two is four, minus one that's three quick maths.");
						return;
					}
				}
			}
		} 
	}
	
	public function onEntitySpawn(EntitySpawnEvent $e){
		$entity = $e->getEntity();
		
		if($entity instanceof NPCHuman){
			$this->getServer()->getScheduler()->scheduleRepeatingTask(new NPCTask($this, $entity), 200);
		}
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
