<?php

declare(strict_types=1);

namespace Destiny;

use App\Account;
use Destiny\Milestones\MilestoneHandler;

/**
 * Class Destiny.
 */
class Destiny
{
    /**
     * @var DestinyClient
     */
    protected $client;

    /**
     * @var DestinyPlatform
     */
    protected $platform;

    public function __construct(DestinyClient $client, DestinyPlatform $platform)
    {
        $this->client = $client;
        $this->platform = $platform;
    }

    /**
     * @return Manifest
     */
    public function manifest() : Manifest
    {
        $result = $this->client->r($this->platform->manifest());

        return new Manifest($result);
    }

    /**
     * @param string $gamertag
     *
     * @return \Destiny\PlayerCollection
     */
    public function player($gamertag) : PlayerCollection
    {
        $result = $this->client->r($this->platform->searchDestinyPlayer($gamertag));

        return new PlayerCollection($gamertag, $result);
    }

    /**
     * @param Player $player
     *
     * @throws \DestinyNoClanException
     *
     * @return Group
     */
    public function groups(Player $player) : Group
    {
        $result = $this->client->r($this->platform->getGroups($player));

        if (isset($result['totalResults']) && $result['totalResults'] > 0) {
            return new Group($result['results'][0]['group']);
        }

        throw new \DestinyNoClanException('Could not locate clan for user: '.$player->displayName);
    }

    /**
     * @param Player $player
     *
     * @return Profile
     */
    public function profile(Player $player) : Profile
    {
        return \DB::transaction(function () use ($player) {

            /** @var Account $account */
            $account = Account::updateOrCreate([
                'membership_id'   => $player->membershipId,
                'membership_type' => $player->membershipType,
            ], [
                'name' => $player->displayName,
            ]);

            $result = $this->client->r($this->platform->getDestinyProfile($account));

            return new Profile($account, $result);
        });
    }

    /**
     * @return MilestoneHandler
     */
    public function publicMilestones() : MilestoneHandler
    {
        $milestones = $this->client->r($this->platform->getMilestones());

        return new MilestoneHandler(['milestones' => $milestones]);
    }

    /**
     * @param Group $group
     */
    public function clanMembers(Group $group)
    {
        $result = $this->client->r($this->platform->getClanMembers($group));
        dd($result);
    }
}
