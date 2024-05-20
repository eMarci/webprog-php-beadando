<?php
include_once "jsonstorage.php";
include_once "util.php";

class Trade
{
    public $_id = null;
    public $sender_id;
    public $sender_card;
    public $target_id;
    public $target_card;

    public function __construct($sender_id = null, $sender_card = null, $target_id = null, $target_card = null)
    {
        $this->sender_id = $sender_id;
        $this->sender_card = $sender_card;
        $this->target_id = $target_id;
        $this->target_card = $target_card;
    }

    public static function from_array(array $arr): Trade
    {
        $instance = new Trade();
        $instance->_id = $arr["_id"] ?? null;
        $instance->sender_id = $arr["sender_id"] ?? null;
        $instance->sender_card = $arr["sender_card"] ?? null;
        $instance->target_id = $arr["target_id"] ?? null;
        $instance->target_card = $arr["target_card"] ?? null;
        return $instance;
    }

    public static function from_object(object $obj): Trade
    {
        return self::from_array((array) $obj);
    }
}

class TradeStorage
{
    private $storage;

    public function __construct()
    {
        $this->storage = new JsonStorage("data/trade-requests.json");
    }

    /**
     * Végimegy a objektumokon és mindet Trade-re castolja.
     * @param stdClass[]
     * @return Trade[]
     */
    private function convert(array $arr): array
    {
        return array_map([Trade::class, "from_object"], $arr);
    }

    /**
     * @return Trade[]
     */
    public function all(): array
    {
        return $this->convert($this->storage->all());
    }

    public function request(Trade $trade): string
    {
        return $this->storage->insert($trade);
    }

    /**
     * @return Trade[]
     */
    public function filter(callable $pred): array
    {
        return $this->convert($this->storage->filter($pred));
    }

    /**
     * @return Trade[]
     */
    public function get_sent(string $uid): array
    {
        return $this->filter(function ($trade) use ($uid) {
            return $trade->sender_id === $uid;
        });
    }

    /**
     * @return Trade[]
     */
    public function get_received(string $uid): array
    {
        return $this->filter(function ($trade) use ($uid) {
            return $trade->target_id === $uid;
        });
    }

    public function trade_exists_by_for(string $uid, string $cid): bool
    {
        return any($this->get_sent($uid), function ($req) use ($cid) {
            return $req->target_card === $cid;
        });
    }

    public function get_by_id(string $id): array
    {
        $results = array_filter($this->all(), function ($trade) use ($id) {
            return $trade->_id === $id;
        });

        if (count($results) === 1) {
            return (array) array_values($results)[0];
        }
        return [];
    }

    public function delete(string $id)
    {
        $this->storage->delete(function ($trade) use ($id) {
            return $trade->_id === $id;
        });
    }

    public function delete_where(callable $fn)
    {
        $this->storage->delete($fn);
    }
}
?>