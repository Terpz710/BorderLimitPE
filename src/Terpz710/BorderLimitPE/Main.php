<?php

namespace Terpz710\BorderLimitPE;

use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\math\Vector3;
use pocketmine\utils\Config;

class Main extends PluginBase implements Listener
{
    private array $worldConfigs = [];

    public function onEnable(): void
    {
        $this->saveDefaultConfig();
        $this->loadWorldConfigs();

        $this->getServer()->getPluginManager()->registerEvents($this, $this);
    }

    private function loadWorldConfigs(): void
    {
        $worlds = $this->getConfig()->get("worlds", []);

        foreach ($worlds as $worldName => $config) {
            $this->worldConfigs[$worldName] = new Config($this->getDataFolder() . "$worldName.yml", Config::YAML, $config);
        }
    }

    public function onMove(PlayerMoveEvent $event)
    {
        $player = $event->getPlayer();
        $worldName = $player->getworld()->getFolderName();

        if (!isset($this->worldConfigs[$worldName])) {
            return;
        }

        $config = $this->worldConfigs[$worldName];

        $x = $player->getPosition()->x;
        $z = $player->getPosition()->z;

        $motion = null;

        if ($x <= $config->get("min_x")) {
            $motion = new Vector3(+2, 1, 0);
        } elseif ($x >= $config->get("max_x")) {
            $motion = new Vector3(-2, 1, 0);
        } elseif ($z <= $config->get("min_z")) {
            $motion = new Vector3(0, 1, +2);
        } elseif ($z >= $config->get("max_z")) {
            $motion = new Vector3(0, 1, -2);
        }

        if ($motion !== null) {
            $player->setMotion($motion);
            if ($config->get("msg")) {
                $player->sendMessage($config->get("msg_"));
            }
        }
    }
}
