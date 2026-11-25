<?php

declare(strict_types=1);

namespace RohaM\LBCreator\util;

use pocketmine\world\World;
use pocketmine\math\Vector3;
use pocketmine\world\particle\FloatingTextParticle;

class FloatingTextHelper {

    private static array $texts = [];

    public static function spawn(int $id, World $world, Vector3 $position, string $text): void {
        self::remove($id);
        
        $particle = new FloatingTextParticle($text, "");
        $world->addParticle($position, $particle);
        
        self::$texts[$id] = [
            'particle' => $particle,
            'world' => $world,
            'position' => $position,
            'text' => $text,
            'created' => time()
        ];
    }

    public static function update(int $id, string $newText): void {
        if (isset(self::$texts[$id])) {
            $data = self::$texts[$id];
            $data['particle']->setText($newText);
            $data['world']->addParticle($data['position'], $data['particle']);
            self::$texts[$id]['text'] = $newText;
            self::$texts[$id]['updated'] = time();
        }
    }

    public static function remove(int $id): void {
        if (isset(self::$texts[$id])) {
            $data = self::$texts[$id];
            // The SAME particle instance must be made invisible to despawn it
            $data['particle']->setInvisible();
            $data['world']->addParticle($data['position'], $data['particle']);
            unset(self::$texts[$id]);
        }
    }

    public static function exists(int $id): bool {
        return isset(self::$texts[$id]);
    }

    public static function get(int $id): ?array {
        return self::$texts[$id] ?? null;
    }

    public static function getAll(): array {
        return self::$texts;
    }

    public static function updatePosition(int $id, World $world, Vector3 $position): void {
        if (isset(self::$texts[$id])) {
            $data = self::$texts[$id];
            $data['particle']->setInvisible();
            $data['world']->addParticle($data['position'], $data['particle']);
            
            $newParticle = new FloatingTextParticle($data['text'], "");
            $world->addParticle($position, $newParticle);
            
            self::$texts[$id] = [
                'particle' => $newParticle,
                'world' => $world,
                'position' => $position,
                'text' => $data['text'],
                'created' => $data['created'] ?? time(),
                'updated' => time()
            ];
        }
    }
}