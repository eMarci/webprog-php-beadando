<?php
include_once "jsonstorage.php";

class Card
{
    public $_id = null;
    public $name;
    public $type;
    public $health;
    public $attack;
    public $defense;
    public $price;
    public $description;
    public $image;
    public $owner;

    public function __construct($name = null, $type = null, $health = null,
        $attack = null, $defense = null, $price = null,
        $description = null, $image = null, $owner = null)
    {
        $this->name = $name;
        $this->type = $type;
        $this->health = $health;
        $this->attack = $attack;
        $this->defense = $defense;
        $this->price = $price;
        $this->description = $description;
        $this->image = $image;
        $this->owner = $owner;
    }

    /**
     * @return Card
     */
    public static function from_array(array $arr): Card
    {
        $instance = new Card();
        $instance->_id = $arr["_id"] ?? null;
        $instance->name = $arr["name"] ?? null;
        $instance->type = $arr["type"] ?? null;
        $instance->health = $arr["health"] ?? null;
        $instance->attack = $arr["attack"] ?? null;
        $instance->defense = $arr["defense"] ?? null;
        $instance->price = $arr["price"] ?? null;
        $instance->description = $arr["description"] ?? null;
        $instance->image = $arr["image"] ?? null;
        $instance->owner = $arr["owner"] ?? null;
        return $instance;
    }

    public static function from_object(object $obj): Card
    {
        return self::from_array((array) $obj);
    }

    public static function basic(array $arr)
    {
        $type = "c-" . $arr["type"];
        $url = "details.php?id=" . $arr["_id"];
        return <<<HTML
            <div class="card">
                <img class="{$type}" src="{$arr['image']}" alt="Kép">
                <div class="card-info">
                    <h3><a href={$url}>{$arr["name"]}</a></h3>
                    <p><i>{$arr["type"]}</i></p>
                    <div class="stats">
                        <span><span class="icon">❤</span>
                            {$arr["health"]}
                        </span>
                        <span><span class="icon">⚔</span>
                            {$arr["attack"]}
                        </span>
                        <span><span class="icon">🛡</span>
                            {$arr["defense"]}
                        </span>
                    </div>
                </div>
            </div>
        HTML;
    }
}

class CardStorage
{
    private $storage;

    public function __construct()
    {
        $this->storage = new JsonStorage("data/cards.json");
    }

    public function convert(array $arr): array
    {
        return array_map([Card::class, 'from_object'], $arr);
    }

    public function all(): array
    {
        return $this->convert($this->storage->all());
    }

    public function add(Card $card): string
    {
        return $this->storage->insert($card);
    }

    public function get_by_type(string $type): array
    {
        return array_filter($this->all(), function ($card) use ($type) {
            return $card->type === $type;
        });
    }

    public function get_by_user($id): array
    {
        return array_filter($this->all(), function ($card) use ($id) {
            return $card->owner === $id;
        });
    }

    public function get_by_id($id): array
    {
        $results = array_filter($this->all(), function ($card) use ($id) {
            return $card->_id === $id;
        });
        if (count($results) === 1) {
            $card = (array) array_values($results)[0];
            return $card;
        }
        return [];
    }

    public function change_owner($cid, $uid)
    {
        $this->storage->update(function ($card) use ($cid) {
            return $card->_id === $cid;
        }, function ($card) use ($uid) {
            $card->owner = $uid;
        });
    }

    public function update($id, array $arr)
    {
        $this->storage->update(function ($card) use ($id) {
            return $card->_id === $id;
        }, function ($card) use ($arr) {
            foreach ($arr as $key => $val) {
                $card->$key = $val;
            }
        });
    }

    // public function delete(callable $pred)
    // {
    //     $this->storage->delete($pred);
    // }
}
?>