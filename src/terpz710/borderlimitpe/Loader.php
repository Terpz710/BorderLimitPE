<?php

declare(strict_types=1);

namespace terpz710\borderlimitpe;

use pocketmine\plugin\PluginBase;

use terpz710\borderlimitpe\commands\CreateBorderCommand;
use terpz710\borderlimitpe\commands\RemoveBorderCommand;

use CortexPE\Commando\PacketHooker;

final class Loader extends PluginBase {

    protected static self $instance;

    protected function onLoad() : void{
        self::$instance = $this;
    }

    protected function onEnable() : void{
        $this->saveDefaultConfig();
        
        $this->getServer()->getPluginManager()->registerEvents(new EventListener(), $this);

        if (!PacketHooker::isRegistered()) {
            PacketHooker::register($this);
        }

        $this->getServer()->getCommandMap()->registerAll("BorderLimitPE", [
            new CreateBorderCommand($this, "createborder", "Initiate the creation of the world border"),
            new RemoveBorderCommand($this, "removeborder", "Removes the world border for the current or specified world")
        ]);
    }

    public static function getInstance() : self{
        return self::$instance;
    }
}
