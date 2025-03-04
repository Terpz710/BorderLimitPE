<?php

declare(strict_types=1);

namespace terpz710\borderlimitpe\api;

use pocketmine\player\Player;

use pocketmine\math\Vector3;

use pocketmine\world\World;

use pocketmine\utils\SingletonTrait;

use terpz710\borderlimitpe\Loader;

use poggit\libasynql\DataConnector;
use poggit\libasynql\libasynql;

final class WorldBorderAPI {
    use SingletonTrait;

    protected Loader $plugin;

    protected DataConnector $db;

    protected array $borderSetup = [];

    public function __construct() {
        $this->plugin = Loader::getInstance();
    }

    public function init() : void{
        $this->db = libasynql::create($this->plugin, $this->plugin->getConfig()->get("database"), [
            "sqlite" => "database/sqlite.sql",
            "mysql" => "database/mysql.sql"
        ]);

        $this->db->executeGeneric("table.world_borders");
    }

    public function startBorderSetup(Player $player) : void{
        $this->borderSetup[$player->getUniqueId()->getBytes()] = [];
        $player->sendMessage("§aTap two points to set the world border!");
    }

    public function setPoint(Player $player, Vector3 $pos) : void{
        $id = $player->getUniqueId()->getBytes();

        if (!isset($this->borderSetup[$id])) {
            return;
        }

        if (!isset($this->borderSetup[$id][0])) {
            $this->borderSetup[$id][0] = $pos;
            $player->sendMessage("§aFirst point set at §e{$pos->getX()}, {$pos->getY()}, {$pos->getZ()}§a. Now tap the second point!");
        } else {
            $this->borderSetup[$id][1] = $pos;
            $this->saveBorder($player->getWorld(), $this->borderSetup[$id][0], $pos);
            unset($this->borderSetup[$id]);
            $player->sendMessage("§aWorld border set!");
        }
    }

    private function saveBorder(World $world, Vector3 $pos1, Vector3 $pos2) : void{
        $worldName = $world->getFolderName();

        $this->db->executeChange("world_borders.create", [
            "world" => $worldName,
            "min_x" => min($pos1->getX(), $pos2->getX()),
            "max_x" => max($pos1->getX(), $pos2->getX()),
            "min_z" => min($pos1->getZ(), $pos2->getZ()),
            "max_z" => max($pos1->getZ(), $pos2->getZ())
        ]);
    }

    public function getBorder(World $world, callable $callback) : void{
        $worldName = $world->getFolderName();

        $this->db->executeSelect("world_borders.get", ["world" => $worldName], function (array $rows) use ($callback) {
            if (!empty($rows)) {
                $callback($rows[0]);
            } else {
                $callback(null);
            }
        });
    }

    public function removeBorder(World $world) : void{
        $worldName = $world->getFolderName();

        $this->db->executeChange("world_borders.remove", ["world" => $worldName]);
    }

    public function close() : void{
        $this->db->close();
    }
}