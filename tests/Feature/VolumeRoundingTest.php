<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Helpers\VolumeHelper;
use App\Models\Donation;

class VolumeRoundingTest extends TestCase
{
    public function test_round_ml()
    {
        $this->assertSame(40, VolumeHelper::roundMl(41));
        $this->assertSame(40, VolumeHelper::roundMl(44));
        $this->assertSame(50, VolumeHelper::roundMl(45));
        $this->assertSame(50, VolumeHelper::roundMl(49));
        $this->assertSame(0, VolumeHelper::roundMl(null));
        $this->assertSame(0, VolumeHelper::roundMl(-5));
    }

    public function test_set_bag_volumes_rounds_and_sums()
    {
        $donation = new Donation();
        $donation->setBagVolumes([41, 44, 45, 49]);

        $this->assertEquals([40, 40, 50, 50], $donation->individual_bag_volumes);
        $this->assertEquals(4, $donation->number_of_bags);
        $this->assertEquals(180, (int)$donation->total_volume);
    }
}
