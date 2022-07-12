<?php

namespace CustomSetting;

use pocketmine\form\Form;
use pocketmine\player\Player;
use pocketmine\utils\Config;

class SettingsForm implements Form {

	/** @var array */
    protected $data = [];
    
	/**
     * @param Config $config
     * @param Player $player
     */
    public function __construct(Config $config, Player $player) {
        $this->data["type"] = "custom_form";
        $this->data["title"] = $config->get("title");
        
        $icon = $config->get("icon");
		$iconType = filter_var($icon, FILTER_VALIDATE_URL) ? "url" : "path";
        $this->data["icon"] = [
        	"type" => $iconType,
    		"data" => $icon
		];
        
        $content = str_replace(
			["{name}", "{line}"],
			[$player->getName(), "\n"],
			$config->get("contents")
		);
		$this->data["content"][] = [
        	"type" => "label",
    		"text" => $content
		];
    }
    
    public function handleResponse(Player $player, $data): void{
    }
    
    public function jsonSerialize(){
        return $this->data;
    }
}