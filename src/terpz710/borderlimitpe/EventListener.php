<?php

declare(strict_types=1);

namespace terpz710\borderlimitpe;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerMoveEvent;
use pocketmine\event\block\BlockBreakEvent;

use pocketmine\utils\Config;

use pocketmine\math\Vector3;

use pocketmine\player\Player;

use pocketmine\Server;

use terpz710\borderlimitpe\api\WorldBorderAPI;

use terpz710\messages\Messages;

class EventListener implements Listener {

    public function onMove(PlayerMoveEvent $event) : void{
        $player = $event->getPlayer();
        $config = new Config(Loader::getInstance()->getDataFolder() . "messages.yml");

        if (Server::getInstance()->isOp($player->getName())) return;

        $border = WorldBorderAPI::getInstance()->getStoredBorder($player->getWorld());

        if ($border === null) return;

        $pos = $event->getTo();
        
        if ($pos->getX() < $border["min_x"] || $pos->getX() > $border["max_x"] || 
            $pos->getZ() < $border["min_z"] || $pos->getZ() > $border["max_z"]) {

            $event->cancel();
            $player->sendMessage((string) new Messages($config, "cannot-pass-border"));
        }
    }

    public function onBlockBreak(BlockBreakEvent $event) : void{
        $player = $event->getPlayer();
        $block = $event->getBlock();
        $pos = new Vector3($block->getPosition()->getX(), $block->getPosition()->getY(), $block->getPosition()->getZ());

        $borderAPI = WorldBorderAPI::getInstance();

        if ($borderAPI->isSettingBorder($player)) {
            $event->cancel();
            $borderAPI->setPoint($player, $pos);
        }
    }
}
