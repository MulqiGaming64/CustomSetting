<?php

namespace CustomSetting;

use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\event\server\DataPacketReceiveEvent;

use pocketmine\player\Player;
use pocketmine\network\mcpe\protocol\ServerSettingsResponsePacket;

class Main extends PluginBase implements Listener {

	public function onEnable(): void{
		$this->saveDefaultConfig();
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
    }
	
	/** Getting content and Icon from Config */
	public function getSetting(Player $player): string{
		// Title
		$title = $this->getConfig()->get("title");
		// Icon
		$icon = $this->getConfig()->get("icon");
		$iconType = filter_var($icon, FILTER_VALIDATE_URL) ? "url" : "path";
		// Contents
		$content = str_replace(
			["{name}", "{line}"],
			[$player->getName(), "\n"],
			$this->getConfig()->get("contents")
		);
		
		// ServerSettings
		$formData = [
    		"type" => "custom_form",
    		"title" => $title,
    		"icon" => [
    			"type" => $iconType,
    			"data" => $icon
    		],
    		"content" => [
    			[
    				"type" => "label",
    				"text" => $content
    			]
    		]
    	];
    
    	return json_encode($formData);
	}

	public function onReceive(DataPacketReceiveEvent $event): void{
		// Why no ServerSettingsRequestPacket?, I've tried but the settings don't show up
    	if(!$event->getOrigin()->getPlayer() instanceof Player) return;
    
    	$net = $event->getOrigin();
    	$player = $net->getPlayer();
    	$packet = ServerSettingsResponsePacket::create(5928, $this->getSetting($player));
    	$net->sendDataPacket($packet);
    }
}
