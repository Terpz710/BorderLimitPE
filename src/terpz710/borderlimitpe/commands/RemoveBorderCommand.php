<?php

declare(strict_types=1);

namespace terpz710\borderlimitpe\commands;

use pocketmine\command\CommandSender;

use pocketmine\player\Player;

use pocketmine\Server;

use terpz710\borderlimitpe\api\WorldBorderAPI;

use CortexPE\Commando\BaseCommand;
use CortexPE\Commando\args\RawStringArgument;

class RemoveBorderCommand extends BaseCommand {

    protected function prepare() : void{
        $this->setPermission("borderlimitpe.cmd");

        $this->registerArgument(0, new RawStringArgument("world", true));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args) : void{
        if (!$sender instanceof Player && !isset($args["world"])) {
            $sender->sendMessage("You must specify a world name when using this command from the console!");
            return;
        }

        $worldName = $args["world"] ?? $sender->getWorld()->getFolderName();
        $world = Server::getInstance()->getWorldManager()->getWorldByName($worldName);

        if ($world === null) {
            $sender->sendMessage("World §c" . $worldName . "§f does not exist or is not loaded!");
            return;
        }

        WorldBorderAPI::getInstance()->removeBorder($world);
        $sender->sendMessage("Successfully removed the border for world §e" . $worldName);
    }
}
