<?php

declare(strict_types=1);

namespace terpz710\borderlimitpe;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerMoveEvent;

use pocketmine\player\Player;

use pocketmine\Server;

use terpz710\borderlimitpe\api\WorldBorderAPI;

class EventListener implements Listener {

    public function onMove(PlayerMoveEvent $event) : void{
        $player = $event->getPlayer();

        if (Server::getInstance()->isOp($player->getName())) return;

        WorldBorderAPI::getInstance()->getBorder($player->getWorld(), function (?array $border) use ($event, $player) {

            if ($border === null) return;

            $pos = $player->getPosition();
            if ($pos->getX() < $border["min_x"] || $pos->getX() > $border["max_x"] || 
                $pos->getZ() < $border["min_z"] || $pos->getZ() > $border["max_z"]) {
                
                $event->cancel();
                $player->sendMessage("§cYou cannot leave the world border!");
            }
        });
    }
}
