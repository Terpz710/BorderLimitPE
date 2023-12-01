<?php

namespace Terpz710\BorderLimitPE;

use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\plugin\PluginBase;
use pocketmine\event\Listener;
use pocketmine\math\Vector3;

class Main extends PluginBase implements Listener
{
    private array $worldConfigs = [];
    private array $messageSent = [];

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
            $this->worldConfigs[$worldName] = $config;
        }
    }

    public function onMove(PlayerMoveEvent $event)
    {
        $player = $event->getPlayer();
        $worldName = $player->getWorld()->getFolderName();

        if (!isset($this->worldConfigs[$worldName])) {
            return;
        }

        $config = $this->worldConfigs[$worldName];

        $x = $player->getPosition()->x;
        $z = $player->getPosition()->z;

        $motion = null;

        if ($x <= $config["min_x"]) {
            $motion = new Vector3(+2, 1, 0);
        } elseif ($x >= $config["max_x"]) {
            $motion = new Vector3(-2, 1, 0);
        } elseif ($z <= $config["min_z"]) {
            $motion = new Vector3(0, 1, +2);
        } elseif ($z >= $config["max_z"]) {
            $motion = new Vector3(0, 1, -2);
        }

        if ($motion !== null && !isset($this->messageSent[$player->getName()])) {
            $player->setMotion($motion);
            if ($config["msg"]) {
                $player->sendMessage($config["msg_"]);
            }
            $this->messageSent[$player->getName()] = true;
        } elseif ($motion === null) {
            unset($this->messageSent[$player->getName()]);
        }
    }
}
