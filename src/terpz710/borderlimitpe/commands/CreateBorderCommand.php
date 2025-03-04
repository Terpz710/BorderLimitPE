<?php

declare(strict_types=1);

namespace terpz710\borderlimitpe\commands;

use pocketmine\command\CommandSender;

use pocketmine\player\Player;

use terpz710\borderlimitpe\api\WorldBorderAPI;

use CortexPE\Commando\BaseCommand;

class CreateBorderCommand extends BaseCommand {

    protected function prepare() : void{
        $this->setPermission("borderlimitpe.cmd");
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{
        if (!$sender instanceof Player) {
            $sender->sendMessage("use this command in-game!");
            return;
        }

        if (!$this->testPermission($sender)) {
            return;
        }

        WorldBorderAPI::getInstance()->startBorderSetup($sender);
    }
}